<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Academy UPTAEB</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <div class="auth-container">
        <div class="left-panel">
            <h1>Login Beba</h1>
            <p>Sistema de gestión de usuarios.</p>
        </div>

        <div class="right-panel">
            <div class="box">
                <h2>Iniciar sesión</h2>
                <p class="subtitle">Ingresa tus credenciales para acceder al panel</p>

                <div id="login-alert" class="alert <?= !empty($error) ? 'alert-error show' : '' ?>">
                    <?= !empty($error) ? htmlspecialchars($error, ENT_QUOTES, 'UTF-8') : '' ?>
                </div>

                <form id="login-form" method="post" action="/login" novalidate>
                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" autocomplete="email" required>
                        <small class="field-error"></small>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
                        <small class="field-error"></small>
                    </div>

                    <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                </form>

                <div class="auth-links">
                    <a href="/forgot-password">¿Olvidaste tu contraseña?</a>
                    <span>¿No tienes cuenta? <a href="/register">Crear usuario</a></span>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/app.js"></script>
</body>
</html>
