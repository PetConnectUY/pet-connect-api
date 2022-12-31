# Find my pet API

Find my pet es una API creada para la creación de perfiles para que los usuarios puedan ver la información de sus mascotas registradas así como alguna información de los dueños.

## Instalación

Dentro del directorio que desea ejecutar utilize 
```bash
git clone https://github.com/MarkOsBab/find-my-pet-api.git
```

Si no tiene instalado Docker puede descargarlo desde [aqui](https://docs.docker.com/desktop/)

Puedes levantar los contenedores utilizando el comando
```bash
docker-compose up
```

- find-my-pet-api-app: Es la aplicacion desarrollada con Laravel.

- find-my-pet-api-nginx: Servidor web que expone la api para su acceso en el puerto 8200.

- find-my-pet-api-mysql: Servicio de MySQL para la base de datos que utiliza el puerto 33699.

- find-my-pet-api-phpmyadmin: Aplicación para la gestión de la base de datos en el puerto 7800.

Todos los puertos se pueden modificar en [docker-compose.yml](https://github.com/MarkOsBab/find-my-pet-api/blob/main/docker-compose.yml)

Actualiza los paquetes ejecutando el comando dentro del contenedor de la aplicación correspondiente a la API
```bash
docker-compose exec app php composer update
```
De ser necesario, modifica la configuración correspondiente al entorno local .env.

Podrás acceder a la API desde la siguiente dirección  ```http://localhost:8601/```.

## Route List

| Method | Endopint | Request | Controller@function |
| ------------- | ------------- | ------------- | ------------- |
| POST | /api/users | <li>firstname</li><li>lastname</li><li>username</li><li>email</li><li>password</li><li>phone</li><li>address</li> | UserController@store |
| POST | /api/users/{id} | <li>firstname</li><li>lastname</li><li>phone</li><li>address</li> | UserController@update |
| DELETE | /api/users/{id} | none | UserController@destroy | 
| POST | /api/auth/login | <li>username</li><li>password</li> | AuthController@login |
| POST | /api/auth/logout | <li>Authorization Bearer token in header</li> | AuthController@logout |
| POST | /api/auth/refresh | <li>Authorization Bearer token in header</li> | AuthController@refresh |