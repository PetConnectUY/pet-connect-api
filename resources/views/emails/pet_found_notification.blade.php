<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Estilos CSS -->
    <style>
        body {
            background-color: #ffffff;
            font-size: 1.6em;
        }

        a {
            color: #fff;
        }

        span {
            font-weight: bold;
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
            color: #fff;
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
            transition: .5s color;
        }

        .btn:hover {
            color: #fff;
            background-color: #f8a629;
        }

        .subtitle {
            font-size: 1.4em;
        }

        .text {
            font-size: 1.2em;
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
            <p class="subtitle">¡Hola {{$activation->user->firstname}}!</p>
            <p class="subtitle">Te notificamos que alguien escaneó la chapita de {{$activation->pet->name}}</p>
            <p class="text">El número de teléfono que proporcionó @if ($pet_found->firstname) <span>{{$pet_found->firstname}}</span>@endif es <span>{{$pet_found->phone}}</span></p>
            @if ($pet_found->email)
                <p class="text">Además se proporcionó un email de contacto para que puedas comunicarte en caso de que el número de teléfono falle <span>{{$pet_found->email}}</span></p>
            @endif

            <p class="text">¡Esperamos que puedas encontrar a {{$activation->pet->name}}!</p>
        </div>
        <div class="footer">
            <p>
                <a href="#" class="btn">Visita nuestra web</a>
            </p>
        </div>
    </div>
</body>
</html>