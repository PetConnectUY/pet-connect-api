<?php

namespace App\Http\Controllers;

use App\Http\Requests\PetImage\PutRequest;
use App\Http\Requests\PetImage\StoreRequest;
use App\Models\PetImage;
use App\Traits\ApiResponser;
use App\Traits\File;
use App\Traits\Image;
use Exception;
use Illuminate\Http\Response;

class PetImageController extends Controller
{
    use ApiResponser, File, Image;

    public function store(StoreRequest $request)
    {
        $image = new PetImage;
        $image->pet_id = $request->validated('pet_id');

        if($request->has('image'))
        {
            $imgName = $this->generateFileUniqueName(new PetImage, 'name');
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
        
        return $this->successResponse($this->jsonResponse($image));
    }

    public function update(PutRequest $request, $id)
    {
        $image = PetImage::find($id);
        if(!$image)
        {
            return $this->errorResponse('No se encontró la imagen.', Response::HTTP_NOT_FOUND);
        }

        $image->update([
            'cover_image' => $request->validated('cover_image'),
        ]);

        return $this->successResponse($this->jsonResponse($image));
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

        return $this->successResponse($this->jsonResponse($image));
    }


    private function jsonResponse($data)
    {
        return [
            'id' => $data->id,
            'pet_id' => $data->pet_id,
            'name' => $data->name,
            'cover_image' => $data->cover_image
        ];
    }
}
