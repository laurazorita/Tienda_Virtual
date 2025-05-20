<?php
require_once 'includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (usuario, contraseña, nombre, apellidos, correo, fecha_nacimiento, genero) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$usuario, $contraseña, $_POST['nombre'], $_POST['apellidos'], $_POST['correo'], $_POST['fecha_nacimiento'], $_POST['genero']]);
        header('Location: login.php');
        exit();
    } catch (PDOException $e) {
        $error = "ERROR! Ya existe un usuario con ese nombre.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro</title>
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

        div {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 400px;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .error-message {
            color: #c0392b;
            margin-bottom: 15px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        input[type="date"],
        select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1rem;
        }

        select {
            appearance: none;
            background-color: #fff;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3E%3Cpath fill='%23777' d='M2 0L0 2h4zm0 5L0 3h4z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 8px 10px;
        }

        button[type="submit"] {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        button[type="submit"]:hover {
            background-color: #3498db;
        }

        p {
            margin-top: 20px;
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div>
        <h1>Registro</h1>
        <?php if (isset($error)): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="usuario" required placeholder="Usuario">
            <input type="password" name="contraseña" required placeholder="Contraseña">
            <input type="text" name="nombre" required placeholder="Nombre">
            <input type="text" name="apellidos" required placeholder="Apellidos">
            <input type="email" name="correo" required placeholder="Correo">
            <input type="date" name="fecha_nacimiento" required>
            <select name="genero" required>
                <option value="Hombre">Hombre</option>
                <option value="Mujer">Mujer</option>
                <option value="Otro">Otro</option>
            </select>
            <button type="submit">Registrarse</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    </div>
</body>

</html>