<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; text-align: center;">
    
    <h2>¡Bienvenido a Aventones, {{ $user->nombre }}!</h2>

    <p>Gracias por registrarte. Para activar tu cuenta, haz clic en el siguiente botón:</p>

    <p>
        <a href="{{ url('/activate/' . $user->activation_token) }}"
           style="
               display: inline-block;
               background-color: #0ea5e9; /* Cambia el color aquí */
               color: white;
               padding: 12px 24px;
               font-size: 16px;
               text-decoration: none;
               border-radius: 6px;
               font-weight: bold;
           ">
            Activar mi cuenta
        </a>
    </p>

    <p style="color: #6b7280;">Si no te registraste, ignora este mensaje.</p>
</body>
</html>