<?php

namespace App\Http\Controllers;

use App\Classes\OrderStatus;
use App\Jobs\SendUserPaymentConfirmation;
use App\Models\Order;
use App\Models\Product;
use App\Traits\ApiResponser;
use App\Traits\UUID;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use MercadoPago\Item;
use MercadoPago\Payer;
use MercadoPago\Payment;
use MercadoPago\Preference;
use MercadoPago\SDK;

class MercadoPagoController extends Controller
{
    use ApiResponser, UUID;

    public function __construct()
    {
        SDK::setAccessToken(config('services.mercadopago.token'));
        SDK::setPublicKey(config('services.mercadopago.key'));
    }

    public function createOrder($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return $this->errorResponse('No se encontró el producto.', Response::HTTP_NOT_FOUND);
        }

        try {
            $preference = new Preference();

            // Configurar los ítems de la preferencia
            $item = new Item();
            $item->id = $product->id;
            $item->title = $product->name;
            $item->description = $product->description;
            $item->picture_url = asset('storage/products/' . $product->image);
            $item->quantity = 1;
            $item->currency_id = "UYU";
            $item->unit_price = $product->price;

            $preference->items = [$item];

            $payer = new Payer();
            $payer->first_name = auth()->user()->firstname;
            $payer->last_name = auth()->user()->lastname;
            $payer->email = auth()->user()->email;
            $preference->payer = $payer;

            $notificationUrl = "https://7a64-2800-a4-2979-7500-f8b7-f854-e72b-835f.ngrok-free.app/store/webhook";
            $preference->notification_url = $notificationUrl;

            $preference->back_urls = [
                "success" => env('FRONTEND_URL')."purchase?sv_status=success",
                "failure" => env('FRONTEND_URL')."purchase?sv_status=failture",
                "pending" => env('FRONTEND_URL')."purchase?sv_status=pending",
            ];

            $order = new Order();
            $order->user_id = auth()->user()->id;
            $order->product_id = $product->id;
            $order->status = OrderStatus::PENDING;
            $order->save();

            $preference->metadata = ['order_uuid' => $order->uuid];
            $preference->auto_return = 'approved';

            $preference->save();
            return $this->successResponse([
                'global' => $preference->id,
                'init_point' => $preference->init_point,
                'sandbox_init_point' => $preference->sandbox_init_point,
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Ocurrió un error al crear la orden de compra. '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function handleWebhook(Request $request)
    {
        if($request->input('action') === 'payment.created') 
        {
            $paymentId = $request->input('data')['id'];
            $payment = Payment::find_by_id($paymentId);
            if($payment && $request->input('type') === 'payment' && $payment->status == 'approved') {
                $order = Order::where('uuid', $payment->metadata->order_uuid)                  
                    ->first();
                if(!$order)
                {
                    return $this->errorResponse('No se encontró la orden.', Response::HTTP_NOT_FOUND);
                }
                $order->status = OrderStatus::PROCESSING;
                $order->payment_id =$payment->id;
                $order->save(); 

                SendUserPaymentConfirmation::dispatch($order);

                return $this->successResponse('Orden actualizada');
            }
        }
    }
}
