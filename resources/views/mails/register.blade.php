@component('mail::message')
# ¡Saludos!<br>
Te damos la bienvenida al Sistema de Gestión del Servicio Comunitario de la Facyt.<br>

Estás recibiendo este email porque se ha detectado un registro de nuevo usuario con tu dirección de Correo electrónico.

Tus datos de inicio de sesión son los siguientes:
@component('mail::table')
| Username      | Password      |
| ------------- |:-------------:|
| {{$username}}     | {{$cedula}}       |
@endcomponent

Lo primero que debes hacer es confirmar tu correo electrónico haciendo click en el siguiente enlace:

@component('mail::button', ['url' => $emailConfirmationUrl])
Verificar Email
@endcomponent

De esta forma podremos estar en contacto.

Y si llegas a olvidar tu contraseña, la podrás recuperar a través de este correo.

Saludos, y que estés bien,<br>
{{ config('app.name') }}

<hr>
<br>

@component('mail::panel')
Esta dirección de correo electrónico no está monitorizada, por favor no interactúe con esta cuenta.
@endcomponent

@endcomponent
