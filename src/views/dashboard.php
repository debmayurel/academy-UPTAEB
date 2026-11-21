<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7cfe0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            text-align: center;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            background: #da0071;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Bienvenido(a)</h1>
        <p>Has iniciado sesión como la bebita <strong><?= htmlspecialchars($currentUserEmail ?? $_SESSION['user_email'] ?? 'usuario', ENT_QUOTES, 'UTF-8'); ?></strong>.</p>
        <a href="/logout">Pulsa para huir bebita</a>
    </div>
</body>
</html>
