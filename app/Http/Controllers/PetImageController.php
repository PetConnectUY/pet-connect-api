<?php

namespace App\Http\Controllers;

use App\Http\Requests\PetImage\PutRequest;
use App\Http\Requests\PetImage\StoreRequest;
use App\Models\PetImage;
use App\Traits\ApiResponser;
use App\Traits\Image;
use App\Traits\UUID;
use Exception;
use Illuminate\Http\Response;

class PetImageController extends Controller
{
    use ApiResponser, UUID, Image;

    public function store(StoreRequest $request)
    {
        $image = new PetImage;
        $image->pet_id = $request->validated('pet_id');

        if($request->has('image'))
        {
            $imgName = $this->generateUUID(new PetImage, 'name');
            $imgExtension = $request->file('image')->getClientOriginalExtension();
            try
            {
                $this->createImages($request->file('image'), env('PET_IMAGES_FOLDER'), $imgName, $imgExtension);
                $image->name = $imgName.'.'.$imgExtension;
            }
            catch(Exception $e)
            {
                return $this->errorResponse('Ocurrió un error al subir la imagen. Excepción: '. $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        $image->cover_image = $request->validated('cover_image');
        $image->save();
        
        return $this->successResponse($image);
    }

    public function update(PutRequest $request, $id)
    {
        $image = PetImage::find($id);
        if(!$image)
        {
            return $this->errorResponse('No se encontró la imagen.', Response::HTTP_NOT_FOUND);
        }

        if($request->has('image'))
        {
            $imgName = $this->generateUUID(new PetImage, 'name');
            $imgExtension = $request->file('image')->getClientOriginalExtension();
            try
            {
                $this->createImages($request->file('image'), env('PET_IMAGES_FOLDER'), $imgName, $imgExtension);
                $oldimgName = $image->name;
                $this->deleteImages(env('PET_IMAGES_FOLDER'), $oldimgName);
                $image->name = $imgName.'.'.$imgExtension;
                $image->cover_image = 1;
            }
            catch(Exception $e)
            {
                return $this->errorResponse('Ocurrió un error al subir la imagen. Excepción: '. $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            $image->name = $image->name;
        }
        $image->save();

        return $this->successResponse($image);
    }

    public function destroy($id)
    {
        $image = PetImage::find($id);
        if(!$image)
        {
            return $this->errorResponse('No se encontró la imagen.', Response::HTTP_NOT_FOUND);
        }

        $this->deleteImages(env('PET_IMAGES_FOLDER'), $image->name);
        $image->delete();

        return $this->successResponse($image);
    }

    public function getImage($id)
    {
        $image = PetImage::find($id);
        if(!$image)
        {
            return $this->errorResponse('No se encontró la imagen.', Response::HTTP_NOT_FOUND);
        }

        $imageUrl = asset('storage/'.env('PET_IMAGES_FOLDER') . $image->name);

        return $this->successResponse($imageUrl);
    }
}
