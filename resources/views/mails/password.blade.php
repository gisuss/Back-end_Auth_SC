@component('mail::message')
# ¡Hola!<br>
Estás recibiendo este email porque se ha solicitado un cambio de contraseña para tu cuenta.

@component('mail::button', ['url' => $ruta])
Restablecer contraseña
@endcomponent

Este enlace para restablecer la contraseña caduca en 60 minutos.

Si no has solicitado un cambio de contraseña, puedes ignorar o eliminar este e-mail.

Saludos, y que estés bien,<br>
SGSC

<hr>
<br>

@component('mail::panel')
Esta dirección de correo electrónico no está monitorizada, por favor no interactúe con esta cuenta.
@endcomponent

@endcomponent
