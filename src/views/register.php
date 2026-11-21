<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear usuario | Academy UPTAEB</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <div class="auth-container">
        <div class="left-panel">
            <h1>Registro</h1>
            <p>Crea una nueva cuenta para ingresar al panel y gestionar usuarios.</p>
        </div>

        <div class="right-panel">
            <div class="box">
                <h2>Crear usuario</h2>
                <p class="subtitle">Completa los datos para registrarte</p>

                <div id="register-alert" class="alert <?= !empty($error) ? 'alert-error show' : '' ?>">
                    <?= !empty($error) ? htmlspecialchars($error, ENT_QUOTES, 'UTF-8') : '' ?>
                </div>

                <form id="register-form" method="post" action="/register" novalidate>
                    <div class="form-group">
                        <label for="name">Nombre completo</label>
                        <input type="text" id="name" name="name" placeholder="Ej. María Pérez" autocomplete="name" required>
                        <small class="field-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" autocomplete="email" required>
                        <small class="field-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres, letras y números" autocomplete="new-password" required>
                        <small class="field-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirmar contraseña</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repite tu contraseña" autocomplete="new-password" required>
                        <small class="field-error"></small>
                    </div>

                    <button type="submit" class="btn btn-primary">Crear cuenta</button>
                </form>

                <div class="auth-links">
                    <span>¿Ya tienes cuenta? <a href="/login">Volver al login</a></span>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/app.js"></script>
</body>
</html>
