<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            display: grid;
            grid-template-columns: 1.1fr 1.4fr;
            min-height: 100vh;
        }

        .left-panel {
            background: #f7cfe0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }

        .left-panel h1 {
            font-size: 3rem;
            color: #5a1a38;
            text-align: center;
            margin: 0;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .right-panel {
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .box {
            width: 100%;
            max-width: 420px;
            padding: 30px 25px;
        }

        .box h2 {
            margin-top: 0;
            margin-bottom: 25px;
            text-align: center;
            color: #333;
            font-size: 2rem;
        }

        .error {
            color: #b91c1c;
            background: #fee2e2;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 18px;
            border: 1px solid #d6d6d6;
            border-radius: 8px;
            font-size: 1rem;
        }

        button {
            padding: 12px;
            background: #ef5ca8;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }

            .left-panel {
                min-height: 260px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <h1>Login Beba</h1>
        </div>

        <div class="right-panel">
            <div class="box">
                <h2>Login</h2>

                <?php if (!empty($error)): ?>
                    <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <form method="post" action="/login">
                    <label for="email">Correo</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>

                    <button type="submit">Iniciar sesión</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
