<!DOCTYPE html>
<html>
<head>
    <title>Orden Aprobada</title>
</head>
<body>
    <h1>Tu orden ha sido aprobada</h1>
    <p>¡Hola {{ $order->user->firstname }} {{$order->user->lastname}}!</p>
    <p>Tu orden con número {{ $order->preference_id }} ha sido aprobada.</p>
</body>
</html>