<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Requisitos para correr el proyecto

- Contar con un entorno de desarrollo de PHP y MySQL como [Xampp](https://www.apachefriends.org/es/index.html).
- Es necesario tener en el sistema operativo [composer de manera global](https://www.youtube.com/watch?v=lPabQsgHvu0).
- Tener instalado y configurado **[GIT](https://www.youtube.com/watch?v=wHh3IgJvXcE)**.

## 1. Clonar el repositorio del proyecto en Laravel

Para clonar el proyecto abre una terminal o consola de comandos y escribe la siguiente nomenclatura:
```bash
git clone https://github.com/gisuss/Back-end_Auth_SC.git
```

## 2. Instalar dependencias del proyecto

Cuando guardas tu proyecto Laravel en un repositorio GIT, en el archivo **.gitignore** se excluye la carpeta vendor que es donde están las librerías que usa tu proyecto, es por eso que se debe correr en la terminal una instrucción que tome del archivo **composer.json** todas las referencias de las librerías que deben estar instaladas en tu proyecto.

Ingresa desde la terminal a la carpeta del proyecto y escribe:
```bash
composer install
```
Este comando instalará todas las librerías que están declaradas para el proyecto.

### 3. Generar archivo .env

Por seguridad el archivo **.env** está excluido del repositorio, para generar uno nuevo se toma como plantilla el archivo **.env.example** para copiar este archivo en uno nuevo escribe en tu terminal:
```bash
cp .env.example .env
```

## 4. Generar Key

Para que el proyecto en Laravel corra sin problemas es necesario generar una key de seguirdad, para ello en tu terminal corre el siguiente comando:
```bash
php artisan key:generate
```
Esta key nueva se agregará a tu archivo **.env**

## 5. Crear base de datos y configurar el archivo .env

Nuestro proyecto de Laravel funciona haciendo consultas a una base de datos entonces tienes que crear una nueva base de datos, pero primero hay que configurar el archivo **.env** ubicado en la raíz del proyecto, esto con el fin de que la api se conecte con nuestra base de datos. La configuración debe estar como sigue:
```PHP
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crudsc_sanctum
DB_USERNAME=root
DB_PASSWORD=
```
Posteriormente, desde tu administrador de base de datos (phpmyadmin si usas Xampp). Crea una nueva base de datos vacía con el nombre del proyecto dado anteriormente en el archivo **.env**:
```bash
DB_DATABASE=crudsc_sanctum
```

## 6. Correr migraciones y seeds

Nuestro proyecto cuenta con seeders para poblar ciertas tablas en la base de datos como usuarios, roles y permisos por lo cual debemos escribir en la terminal:
```bash
php artisan migrate --seed
```
O si ya tienes la base de datos y deseas correr una actualizacion de las migraciones, entonces usa el siguiente comando:
```bash
php artisan migrate:fresh --seed
```

Al ejecutar el comando anterior, verificar en tu gestor de base de datos que en la tabla **users** contenga los datos del usuario admin. A continuación te indico los datos para el inicio de sesión usando el usuario Admin:
```PHP
username: admin
password: 12345678
```

## 7. Correr el Servidor

Ponemos acorrer el proyecto escribiendo en la terminal el siguiente comando:
```bash
php artisan serve
```
Al ejecutar el comando anterior, y si todo sale bien, la terminal arrojará la siguiente respuesta:
```bash
Starting Laravel development server: http://127.0.0.1:8000
[Tue Aug 30 14:13:51 2022] PHP 7.4.27 Development Server (http://127.0.0.1:8000) started
```
Con lo cual, todo estará bien y proseguimos con los endpoints.

## 8. Endpoints

A continuacion muestro los endpoints configurados hasta el momento los cuales se encuentran en la ruta **routes/api.php**:
```PHP
Route::post('register', [UserController::class, 'register']);
Route::post('login', [SCAuthController::class, 'login']);
Route::put('verify-email', [UserController::class, 'verifyuseremail']);
Route::post('forgot-password', [newForgotPasswordController::class, 'forgotPassword']);
Route::put('reset-password', [newResetPasswordController::class, 'resetPassword']);

Route::group( ['middleware' => ['auth:sanctum']], function() {
    Route::get('user-profile', [UserController::class, 'userProfile']);
    Route::put('edit-user-profile/{id}', [UserController::class, 'edituserProfile']);
    Route::delete('delete-user/{id}', [UserController::class, 'deleteUser']);
    Route::put('change-password', [UserController::class, 'changePassword']);
    Route::post('logout', [SCAuthController::class, 'logout']);
    Route::get('refresh-token', [SCAuthController::class, 'refresh']);
});
```
Cada endpoint responde a las urls dadas a continuacion como ejemplos:
```PHP
http://127.0.0.1:8000/api/register
http://127.0.0.1:8000/api/login
http://127.0.0.1:8000/api/user-profile
http://127.0.0.1:8000/api/edit-user-profile/1
http://127.0.0.1:8000/api/logout
```

## 9. Configuración de correo de pruebas [Mailtrap.io](https://mailtrap.io/home)

Ingresan al sitio [Mailtrap.io](https://mailtrap.io/home) y se crean una cuenta (pueden logearse usando su cuenta de github), luego seleccionan "Laravel 7+" en la pestaña "SMTP Settings" > "Integrations" y copian al portapapeles la configuración de su servidor de correos de pruebas:
```PHP
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=xxxxxxxxxxx
MAIL_PASSWORD=xxxxxxxxxxx
MAIL_ENCRYPTION=tls
```
**OJO: Esto es solo un ejemplo**

Se dirigen al archivo **.env** ubicado en la raíz del proyecto y en la sección de "MAIL" reemplazan la configuración por defecto con la configuración que copiaron al portapapeles en el paso anterior. Listo, su servidor de pruebas está listo para gestionar solicitudes de emails.

## 10. Limpiar caché de rutas y configuraciones previas

Si tras clonar el repositorio (luego de alguna actualización) te da errores relacionados con caché, ejecutar en la terminal los siguientes comandos (uno despues del otro):

```PHP
php artisan config:clear
```
Para limpiar la config cache

```PHP
php artisan route:clear
```
Para limpiar la route cache

```PHP
php artisan cache:clear
```
Para limpiar la normal cache