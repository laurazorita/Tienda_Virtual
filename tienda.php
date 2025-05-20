<?php
    session_start(); // Asegúrate de iniciar la sesión en cada página

    require_once 'includes/conexion.php';

    // Inicializa el carrito si no existe
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Check if the user is logged in
    if (!isset($_SESSION['usuario']) && !isset($_SESSION['invitado'])) {
        header('Location: login.php');
        exit();
    }

    // Handle adding items to the cart
    if (isset($_GET['add'])) {
        $id_producto = $_GET['add'];

        // Usar prepared statements para prevenir inyección SQL
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        $producto = $stmt->fetch();

        if ($producto) {
            $_SESSION['carrito'][$id_producto] = [
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'imagen' => $producto['imagen'],
                'cantidad' => ($_SESSION['carrito'][$id_producto]['cantidad'] ?? 0) + 1 // Usar ?? para evitar errores si no existe
            ];
        }
        header('Location: tienda.php');
        exit();
    }

    // Obtener productos de la base de datos
    try {
        $productos = $pdo->query("SELECT * FROM productos")->fetchAll();
    } catch (PDOException $e) {
        die("Error al obtener productos: " . $e->getMessage());
    }

    ?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>tienda</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <style>
            body {
                font-family: 'Nunito', sans-serif;
                margin: 0;
                background-color: #f7f9fc;
                color: #3a3a3a;
                line-height: 1.7;
                padding-top: 20px;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .main-nav {
                background-color: #fff;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                margin-bottom: 30px;
                width: 100%;
            }

            .main-nav ul {
                list-style: none;
                padding: 15px 20px;
                margin: 0 auto;
                max-width: 1200px;
                display: flex;
                justify-content: flex-start;
                align-items: center;
            }

            .main-nav ul li {
                margin-right: 20px;
            }

            .main-nav ul li:last-child {
                margin-right: 0;
                margin-left: auto;
                /* Push the last items to the right */
                display: flex;
                align-items: center;
            }

            .main-nav ul li a {
                color: #3a3a3a;
                text-decoration: none;
                font-size: 1.1rem;
                font-weight: 500;
                transition: color 0.3s ease;
            }

            .main-nav ul li a:hover {
                color: #555;
            }

            .container-icon {
                position: relative;
                cursor: pointer;
                margin-left: 20px;
            }

            .icon-cart {
                width: 24px;
                height: 24px;
                fill: none;
                stroke: #3a3a3a;
                stroke-width: 1.5;
                vertical-align: middle;
            }

            .count-products {
                position: absolute;
                top: -8px;
                right: -8px;
                background-color: #63b3ed;
                color: white;
                border-radius: 50%;
                padding: 4px 6px;
                font-size: 0.8rem;
            }

            main {
                padding: 0 20px;
                max-width: 1200px;
                margin: 0 auto;
                width: 100%;
                display: flex;
                justify-content: center;
            }

            main>div {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                width: 100%;
            }

            .producto {
                background-color: #fff;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                transition: transform 0.2s ease-in-out;
            }

            .producto:hover {
                transform: translateY(-5px);
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            }

            .producto-imagen-container {
                width: 100%;
                height: 200px;
                overflow: hidden;
                border-bottom: 1px solid #e0e0e0;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .producto img {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
                display: block;
                transition: transform 0.3s ease-in-out;
            }

            .producto:hover img {
                transform: scale(1.03);
            }

            .producto-info {
                padding: 15px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                flex-grow: 1;
                width: 100%;
            }

            .producto-info h3 {
                margin-top: 0;
                margin-bottom: 8px;
                font-size: 1.2rem;
                color: #2d3748;
                font-weight: 600;
            }

            .producto-info p {
                margin-bottom: 12px;
                color: #718096;
                font-size: 0.9rem;
            }

            .producto-precio-accion {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 8px;
                width: 100%;
            }

            .producto-precio-accion p {
                margin: 0;
                font-weight: 700;
                color: #48bb78;
                font-size: 1rem;
            }

            .producto-precio-accion a {
                background-color: #4299e1;
                color: white;
                padding: 8px 12px;
                border-radius: 6px;
                text-decoration: none;
                font-size: 0.9rem;
                transition: background-color 0.3s ease;
            }

            .producto-precio-accion a:hover {
                background-color: #2b6cb0;
            }

            .logout-link {
                margin-left: 20px;
            }
        </style>
    </head>

    <body>
        <nav class="main-nav">
    <ul>
        <li><a href="tienda.php">Inicio</a></li>
        <li style="margin-left: auto;">
            <div class="container-icon">
                <a href="carrito.php" class="icon-cart-link">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon-cart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    <div class="count-products">
                        <span id="contador-productos">
                            <?php
                            $total_cantidad = 0;
                            if (isset($_SESSION['carrito'])) {
                                foreach ($_SESSION['carrito'] as $item) {
                                    $total_cantidad += $item['cantidad'];
                                }
                            }
                            echo $total_cantidad;
                            ?>
                        </span>
                    </div>
                </a>
            </div>
            <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario'] !== 'invitado') : ?>
                <a href="logout.php" class="logout-link">Cerrar sesión</a>
            <?php else : ?>
                <a href="login.php" class="logout-link">Iniciar sesión</a>
            <?php endif; ?>
        </li>
    </ul>
</nav>

        <main>
            <div>
                <?php if (isset($productos) && !empty($productos)) : ?>
                    <?php foreach ($productos as $producto) : ?>
                        <div class="producto">
                            <div class="producto-imagen-container">
                                <img src="img/<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" onerror="this.onerror=null; this.src='img/default.jpg'">
                            </div>
                            <div class="producto-info">
                                <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
                                <div class="producto-precio-accion">
                                    <p><?= number_format($producto['precio'], 2) ?>€</p>
                                    <a href="tienda.php?add=<?= $producto['id_producto'] ?>">Añadir</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>No hay productos disponibles.</p>
                <?php endif; ?>
            </div>
        </main>

    </body>

    </html>