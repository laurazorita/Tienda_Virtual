<?php
require_once 'includes/conexion.php';

if (isset($_SESSION['usuario']) || isset($_SESSION['invitado'])) {
    header('Location: tienda.php');
    exit();
}

if (isset($_GET['guest'])) {
    $_SESSION['invitado'] = true;
    header('Location: tienda.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $contraseña = $_POST['contraseña'] ?? '';

    if ($usuario === 'invitado' && $contraseña === 'invitado') {
        $_SESSION['invitado'] = true;
        header('Location: tienda.php');
        exit();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        $user = $stmt->fetch();

        if ($user && password_verify($contraseña, $user['contraseña'])) {
            $_SESSION['usuario'] = $user['usuario'];
            header('Location: tienda.php');
            exit();
        } else {
            $error = "ERROR! Usuario o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
       body {
            font-family: 'Nunito', sans-serif;
            background-color: #f7f9fc;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        h1 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #34495e;
        }

        .error-message {
            color: #e74c3c;
            margin-bottom: 1rem;
            text-align: center;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        input[type="text"],
        input[type="password"] {
            padding: 0.75rem;
            border: 1px solid #bdc3c7;
            border-radius: 0.375rem;
            font-size: 1rem;
        }

        button[type="submit"],
        .guest-login {
            padding: 0.75rem 1rem;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            font-size: 1rem;
            text-align: center;
            text-decoration: none;
        }

        button[type="submit"]:hover,
        .guest-login:hover {
            background-color: #2980b9;
        }

        .guest-login {
            display: block;
            margin-top: 1rem;
        }

        p {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #7f8c8d;
        }

        a {
            color: #3498db;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        <?php if (isset($error)): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST" class="login-form">
            <input type="text" name="usuario" required placeholder="Usuario">
            <input type="password" name="contraseña" required placeholder="Contraseña">
            <button type="submit">Entrar</button>
        </form>
        <a href="login.php?guest=1" class="guest-login">Entrar como Invitado</a>
        <p>¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
    </div>

</body>
</html>