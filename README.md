# Jafet Osorio - Technical Test

Prueba tecnica para puesto de programador Full Stack.

## Requisitos

Asegúrate de tener las siguientes versiones de PHP y npm instaladas en tu entorno de desarrollo:

- PHP versión 8.2.2
- npm versión 9.4.2

## Instalación

Sigue estos pasos para instalar y configurar el proyecto:

1. Clona este repositorio en tu máquina local.
2. Ejecuta el comando `npm install` para instalar las dependencias.
3. Ejecuta el comando `composer install` para instalar las dependencias de PHP.
4. Configura el archivo `.env` con las variables de entorno necesarias.
    - Copia el archivo `.env.example` y renómbralo como `.env`.
    - Abre el archivo `.env` y configura las siguientes variables de entorno.
        - `BROADCAST_DRIVER=pusher` Driver de transmisión en tiempo real (para funcionalidad del chat).
        - `MAIL_DRIVER=smtp`: Driver de envío de correo.
        - `QUEUE_CONNECTION=database`: Conexión de cola.
5. Ejecuta el comando `php artisan migrate` para migrar la base de datos.
6. Ejecuta el comando `php artisan queue:work` para iniciar la cola de Laravel.
7. Ejecuta el comando `npm run dev` para compilar los assets.

## Uso

Una vez realizado los pasos de instalación restaria registrar cuentas de usuario e ir creando Posts para probar las funcionalidades solicitadas.

El proyecto ya tiene registrado la tarea diaria de envio de chats por correo, pero se puede probar la funcionalidad ejecutando `php artisan app:send-daily-chats `


## Pruebas de correo

Este proyecto utiliza Mailtrap (link: [https://mailtrap.io/](https://mailtrap.io/)) como servicio de prueba de correo electrónico. Asegúrate de configurar las variables de entorno de correo electrónico con los valores correspondientes proporcionados por Mailtrap para garantizar el correcto envío y recepción de correos electrónicos.

## Integración con Pusher

Este proyecto utiliza Pusher (link: [https://pusher.com/](https://pusher.com/)) como servicio de mensajería en tiempo real. Asegúrate de configurar las credenciales de Pusher en el archivo `.env` para aprovechar esta funcionalidad.


