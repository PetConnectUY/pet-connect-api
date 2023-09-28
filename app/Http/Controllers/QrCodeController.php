<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Traits\ApiResponser;
use App\Traits\UUID;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeLibrary;

class QrCodeController extends Controller
{
    use ApiResponser, UUID;

    public function generate(Request $request)
    {
        try {
            DB::beginTransaction();

            $token = $this->generateUUID(new QrCode(), 'token');

            $qrCodeUrl = 'http://localhost:4200/pets/'.$token;
            $image = QrCodeLibrary::format('png')
                ->size(256)
                ->generate($qrCodeUrl);

            Storage::put(env('QR_IMAGES_FOLDER').$token.'.png', $image);
            $fileName = $token .'.png';
            $qrImageUrl = asset('storage'.env('QR_IMAGES_FOLDER').$fileName);

            $qrCode = QrCode::create([
                'token' => $token,
                'image_url' => $qrImageUrl,
            ]);

            DB::commit();

            return $this->successResponse($qrCode);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Ocurrió un error al generar el código qr. '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function generateQrImage(Request $request)
    {
        $token = $request->input('token');

        if(!$token)
        {
            return $this->errorResponse('Token no proporcionado.', Response::HTTP_BAD_REQUEST);
        }

        $qrCode = QrCode::where('token', $token)
            ->first();

        if(is_null($qrCode))
        {
            return $this->errorResponse('No se encontró el código qr.', Response::HTTP_NOT_FOUND);
        }

        $qrCodeUrl = env('FRONTEND_URL').'pets/'.$token;

        $image = QrCodeLibrary::format('png')
            ->size(256)
            ->generate($qrCodeUrl);

        
        $imageExists = Storage::fileExists(env('QR_IMAGES_FOLDER').$token.'.png');

        if(!$imageExists)
        {
            Storage::put(env('QR_IMAGES_FOLDER').$token.'.png', $image);   
        }

        $fileName = $token .'.png';
        $qrImageUrl = asset('storage'.env('QR_IMAGES_FOLDER').$fileName);

        return $this->successResponse([
            'image_url' => $qrImageUrl
        ]);
    }

}
