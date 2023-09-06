<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu pedido de [nombre del producto] ha sido confirmado</title>

    <!-- Estilos CSS -->
    <style>
        body {
            background-color: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ccc;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ccc;
        }

        img {
            max-width: 100%;
        }

        .container {
            width: 600px;
            margin: 0 auto;
        }

        .header {
            background-color: #F9B348;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
        }

        .footer {
            background-color: #f5f5f5;
            color: #000000;
            padding: 20px;
            text-align: center;
        }

        .btn {
            text-decoration: none;
            background-color: #F9B348;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn:hover {
            color: #fff;
            background-color: #f8a629;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://www.petconnect.com/assets/img/logo.png" alt="Logo de Pet Connect">
            <h1>Tu pedido de [nombre del producto] ha sido confirmado</h1>
        </div>
        <div class="content">
            <p>¡Hola [nombre del cliente],</p>
            <p>Te informamos que tu pedido de [nombre del producto] ha sido confirmado.</p>
            <p>Estamos muy contentos de que hayas elegido Pet Connect para tu compra. Tu pedido se está procesando y te mantendremos informado sobre el progreso del envío.</p>
            <p>Aquí hay un resumen de tu pedido:</p>
            <table>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                </tr>
                <tr>
                    <td>{{ $order->product->name }}</td>
                    <td>${{$order->product->price}}</td>
                </tr>
            </table>
            <p>¡Esperamos que disfrutes de tu compra!</p>
            <p><a href="https://www.petconnect.com/" class="btn">Visita nuestra web</a></p>
        </div>
        <div class="footer">
            <p>Pet Connect</p>
        </div>
    </div>
</body>
</html>