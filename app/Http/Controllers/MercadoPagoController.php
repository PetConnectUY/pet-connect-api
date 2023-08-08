<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoController extends Controller
{
    use ApiResponser;

    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
    }

    public function createPreference(Request $request, $productId)
    {
        $product = Product::find($productId);
        if(is_null($product))
        {
            return $this->errorResponse('No se encontró el producto.', Response::HTTP_NOT_FOUND);
        }
        json_encode($request->token);
        $paymentRequest = [
            'transaction_amount' => $product->price,
            'description' => $product->name,
            'payment_method_id' => $request->selectedPaymentMethod,
            'token' => [
                'cardExpirationMonth' => $request->token['cardExpirationMonth'],
                'cardholderName' => $request->token['cardholderName'],
                'cardExpirationYear' => $request->token['cardExpirationYear'],
                'securityCode' => $request->token['securityCode'],
                'cardNumber' => $request->token['cardNumber']
            ],
            'payer' => [
                'first_name' => auth()->user()->firstname,
                'last_name' => auth()->user()->lastname,
                'email' => auth()->user()->email,
                'address' => [
                    'street_name' => auth()->user()->address
                ],
            ],
            'additional_info' => [
                'items' => [
                    [
                        'id' => $product->id,
                        'title' => $product->name,
                        'description' => $product->description,
                        'quantity' => 1,
                        'unit_price' => $product->price,
                    ],
                ],
            ],
        ];

        try
        {
            $payment = new PaymentClient();
            $createdPayment = $payment->create($paymentRequest);
            dd($createdPayment);
        }
        catch(MPApiException $e) {
            dd($e->getApiResponse());
        }
    }
}
