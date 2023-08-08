<?php

namespace App\Http\Controllers;

use App\Mail\OrderApproved;
use App\Models\Order;
use App\Models\Product;
use App\Models\UserRole;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use MercadoPago\Item;
use MercadoPago\Payer;
use MercadoPago\Preference;
use MercadoPago\SDK;

class MercadoPagoController extends Controller
{
    use ApiResponser;

    public function __construct()
    {
        SDK::setAccessToken('TEST-2088029280433887-080715-31b1b9090038305c366ffd833e8d3557-1444734222');
    }

    public function createOrder($productId)
    {
        $product = Product::find($productId);
        if(!$product)
        {
            return $this->errorResponse('No se encontró el producto.', Response::HTTP_NOT_FOUND);
        }

        $preference = new Preference();
        $item = new Item();

        try
        {
            DB::beginTransaction();

            $item->id = $product->id;
            $item->title = $product->name;
            $item->description = $product->description;
            $item->picture_url = asset('storage/products/' . $product->image);
            $item->quantity = 1;
            $item->currency_id = "UYU";
            $item->unit_price = $product->price;

            $payer = new Payer();
            $payer->first_name = auth()->user()->firstname;
            $payer->last_name = auth()->user()->lastname;
            $payer->email = auth()->user()->email;

            $preference->auto_return = "approved";
            $preference->items = array($item);
            $preference->payer = $payer;
            $preference->notification_url = 'https://86e1-2800-a4-29b8-6800-147c-6fa2-4eaa-b877.ngrok-free.app/store/webhook';
            $preference->back_urls = [
                "success" => route('store.success'),
                "failure" => route('store.error'),
                "pending" => route('store.pending'),
            ];

            $preference->save();

            $order = new Order();
            $order->user_id = auth()->user()->id;
            $order->product_id = $product->id;
            $order->preference_id = $preference->id;
            $order->status = "pending";
            $order->save();
            DB::commit();

            return $this->successResponse([
                'preference_id' => $preference->id,
                'init_point' => $preference->init_point,
                'sandbox_init_point' => $preference->sandbox_init_point,
            ]);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            return $this->errorResponse('Ocurrió un error al crear la orden de compra.', Response::HTTP_INTERNAL_SERVER_ERROR);
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

                return $this->successResponse(auth()->user());
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

        return $this->successResponse('error');
    }

    public function pending()
    {
        return $this->successResponse('pending');
    }

    public function handleWebhook(Request $request)
    {
        $preference = Preference::find_by_id($request->input('preference_id'));
        if ($preference) {
            switch ($request->input('status')) {
                case 'approved':
                    $this->handlePaymentApproved($request);
                    break;
                case 'pending':
                    $this->handlePaymentPending($request);
                    break;
                case 'cancelled':
                    $this->handlePaymentCancelled($request);
                    break;
                default:
                    break;
            }
        }

        //return $this->successResponse(true);
    }

    private function handlePaymentApproved($data)
    {
        $preferenceId = $data->input('preference_id');
        $order = Order::where('preference_id', $preferenceId)->first();

        if ($order) {
            $order->status = 'completed';
            $order->save();
            Mail::to($order->user->email)->send(new OrderApproved($order));
        }
    }

    private function handlePaymentPending($data)
    {
        $preferenceId = $data['data']['id'];
        $order = Order::where('preference_id', $preferenceId)->first();

        if ($order) {
            // Actualiza el estado de la orden a "pendiente", envía notificaciones, etc.
            $order->status = 'pending';
            $order->save();
        }
    }

    private function handlePaymentCancelled($data)
    {
        $preferenceId = $data['data']['id'];
        $order = Order::where('preference_id', $preferenceId)->first();

        if ($order) {
            $order->status = 'cancelled';
            $order->save();
        }
    }
}
