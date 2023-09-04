<?php

namespace App\Http\Controllers;

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

            // Agregar el ítem a la preferencia
            $preference->items = [$item];

            // Configurar el pagador
            $payer = new Payer();
            $payer->first_name = auth()->user()->firstname;
            $payer->last_name = auth()->user()->lastname;
            $payer->email = auth()->user()->email;
            $preference->payer = $payer;

            // Configurar la URL de notificación con el preference_id
            $notificationUrl = "https://7a64-2800-a4-2979-7500-f8b7-f854-e72b-835f.ngrok-free.app/store/webhook";
            $preference->notification_url = $notificationUrl;

            // Configurar las URLs de retorno
            $preference->back_urls = [
                "success" => route('store.success'),
                "failure" => route('store.error'),
                "pending" => route('store.pending'),
            ];

            // Crear una entrada de orden en tu base de datos
            $order = new Order();
            $order->user_id = auth()->user()->id;
            $order->product_id = $product->id;
            $order->order_unique_id = $this->generateUUID(new Order(), 'order_unique_id');
            $order->status = "pending";
            $order->save();

            $preference->metadata = ['order_uuid' => $order->order_unique_id];
            $preference->auto_return = 'approved';

            // Guardar la preferencia
            $preference->save();
            // Devolver la respuesta con la información de la preferencia
            return $this->successResponse([
                'global' => $preference->id,
                'init_point' => $preference->init_point,
                'sandbox_init_point' => $preference->sandbox_init_point,
            ]);
        } catch (Exception $e) {
            // Manejar errores
            return $this->errorResponse('Ocurrió un error al crear la orden de compra. '.$e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function success(Request $request)
    {
        $preferenceId = $request->input('preference_id');
        $order = Order::where('preference_id', $preferenceId)->first();
        if($order)
        {
            try
            {
                DB::beginTransaction();
                $order->status = 'completed';
                $order->save();

                if($order->status == 'completed')
                {
                    $order->user->role->role_id = $order->product->role_id;
                    $order->user->save();
                }
                DB::commit();

                return $this->successResponse([
                    'preference_id' => $order->preference_id,
                    'status' => $order->status,
                    'user' => auth()->user()
                ]);
            }
            catch(Exception $e)
            {
                DB::rollBack();
                return $this->errorResponse('Ocurrió un error al modificar el rol del usuario. ', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function error(Request $request)
    {
        $preferenceId = $request->input('preference_id');
        $order = Order::where('preference_id', $preferenceId)->first();

        if($order)
        {
            try
            {
                DB::beginTransaction();
                $order->status = 'failed';
                $order->save();
                DB::commit();

                return $this->successResponse($order);
            }
            catch(Exception $e)
            {
                DB::rollBack();
                return $this->errorResponse('Ocurrió un error al actualizar la órden.', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function pending()
    {
        return $this->successResponse('pending');
    }

    public function handleWebhook(Request $request)
    {
        if($request->input('action') === 'payment.created') 
        {
            $paymentId = $request->input('data')['id'];
            $payment = Payment::find_by_id($paymentId);
            if($payment && $request->input('type') === 'payment' && $payment->status == 'approved') {
                $order = Order::where('order_unique_id', $payment->metadata->order_uuid)                  
                    ->first();
                if(!$order)
                {
                    return $this->errorResponse('No se encontró la orden.', Response::HTTP_NOT_FOUND);
                }
                $order->update([
                    'status' => 'completed',
                ]); 

                SendUserPaymentConfirmation::dispatch($order);

                return $this->successResponse('Orden actualizada');
            }
        }
    }
}
