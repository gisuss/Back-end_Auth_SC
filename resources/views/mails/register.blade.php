@component('mail::message')
# ¡Saludos, {{$name}}!<br>
Te damos la bienvenida al Sistema de Gestión del Servicio Comunitario de la Facyt.<br>

Estás recibiendo este email porque se ha detectado un registro de nuevo usuario con tu dirección de correo electrónico.

Tus datos de inicio de sesión son los siguientes:
@component('mail::table')
| Usuario      | Contraseña      |
| :-------------: |:-------------:|
| {{$username}}     | {{$pass}}       |
@endcomponent

Ingresa al siguiente link para iniciar sesión
@component('mail::button', ['url' => $linkpage])
Inicio
@endcomponent

Y si llegas a olvidar tu contraseña, podrás recuperarla a través de este correo.

Saludos, y que estés bien,<br>
SGSC

<hr>
<br>

@component('mail::panel')
Esta dirección de correo electrónico no está monitorizada, por favor no interactúe con esta cuenta.
@endcomponent

@endcomponent
