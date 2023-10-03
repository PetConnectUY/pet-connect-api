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
            <img src="img" alt="Logo de Pet Connect">
            <h1>Alguien quiere ponerse en contacto contigo</h1>
        </div>
        <div class="content">
            <p>¡Hola {{$activation->user->firstname}},</p>
            <p>Te notificamos que alguien escaneó la chapita de {{$activation->pet->name}}</p>
            <p>El número que se nos proporcionó es <b>{{$pet_found->phone}}</b></p>

            <p>¡Esperamos que puedas encontrar a {{$activation->pet->name}}!</p>
        </div>
        <div class="footer">
            <p><a href="https://www.petconnect.com/" class="btn">Visita nuestra web</a></p>
        </div>
    </div>
</body>
</html>