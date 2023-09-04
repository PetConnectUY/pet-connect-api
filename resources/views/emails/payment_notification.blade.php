<!DOCTYPE html>
<html>
<head>
    <title>Orden Aprobada</title>
</head>
<body>
    <h1>Tu orden ha sido aprobada</h1>
    <p>¡Hola {{ $order->user->firstname }} {{$order->user->lastname}}!</p>
    <p>Tu orden #{{ $order->order_unique_id }} del product {{$order->product->name}} por el precio de {{$order->product->price}} ha sido aprobada.</p>
</body>
</html>