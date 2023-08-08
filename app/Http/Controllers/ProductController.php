<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Traits\ApiResponser;
use App\Traits\Image;
use App\Traits\UUID;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use ApiResponser, UUID, Image;
    private $publicPath = 'app/public';

    public function index() {
        $products = Product::get();

        $products->each(function($product) {
            $product->image = asset('storage/products/' . $product->image);
        });

        return $this->successResponse($products);
    }

    public function store(ProductRequest $request)
    {
        try
        {
            DB::beginTransaction();
            $product = new Product();
            
            $product->name = $request->validated('name');
            $product->price = $request->validated('price');
            $product->description = $request->validated('description');
            if($request->has('image'))
            {
                $imgName = $this->generateUUID(new Product, 'image');
                $imgExtension = $request->file('image')->getClientOriginalExtension();
                $this->createImages($request->file('image'), env('PRODUCTS_IMAGES_FOLDER'), $imgName, $imgExtension);
                $product->image = $imgName.'.'.$imgExtension;
            }
            $product->save();
            DB::commit();

            return $this->successResponse($product);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return $this->errorResponse('Ocurrió un error al crear el producto. '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if(is_null($product))
        {
            return $this->errorResponse('No se encontró el producto.', Response::HTTP_NOT_FOUND);
        }
        try
        {
            DB::beginTransaction();
            $this->deleteImages(env('PRODUCTS_IMAGES_FOLDER'), $product->image);
            $product->delete();
            DB::commit();

            return $this->successResponse($product);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return $this->errorResponse('Ocurrió un error al borrar el producto. '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
