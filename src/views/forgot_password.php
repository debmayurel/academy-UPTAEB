<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña | Academy UPTAEB</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <div class="auth-container">
        <div class="left-panel">
            <h1>Recuperación</h1>
            <p>Recuperación de contraseña.</p>
        </div>

        <div class="right-panel">
            <div class="box">
                <h2>Recuperar contraseña</h2>
                <p class="subtitle">Ingresa tu cuenta y define una nueva contraseña</p>

                <div id="recovery-alert" class="alert"></div>

                <form id="reset-form" novalidate>
                    <div class="form-group">
                        <label for="reset-email">Correo registrado</label>
                        <input type="email" id="reset-email" name="email" placeholder="correo@ejemplo.com" autocomplete="email" required>
                        <small class="field-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="reset-password">Nueva contraseña</label>
                        <input type="password" id="reset-password" name="password" placeholder="Mínimo 6 caracteres, letras y números" autocomplete="new-password" required>
                        <small class="field-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="reset-password-confirmation">Confirmar nueva contraseña</label>
                        <input type="password" id="reset-password-confirmation" name="password_confirmation" placeholder="Repite la nueva contraseña" autocomplete="new-password" required>
                        <small class="field-error"></small>
                    </div>

                    <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
                </form>

                <div class="auth-links">
                    <a href="/login">Volver a iniciar sesión</a>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/app.js"></script>
</body>
</html>
