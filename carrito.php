<?php
 require_once 'includes/conexion.php';
 
 // Permitir acceso si la sesión de usuario o de invitado está activa
 if (!isset($_SESSION['usuario']) && !isset($_SESSION['invitado'])) {
  header('Location: login.php');
  exit();
 }
 
 if (isset($_GET['eliminar'])) {
  unset($_SESSION['carrito'][$_GET['eliminar']]);
 }
 
 if (isset($_GET['vaciar'])) {
  $_SESSION['carrito'] = [];
 }
 
 $total = 0;
 
 // Función para redirigir al login si es invitado
 function check_guest_and_redirect() {
  if (isset($_SESSION['invitado'])) {
  header('Location: login.php?redirect=checkout'); // Redirige a login con parámetro 'redirect'
  exit();
  }
 }
 
 /*
 // Procesar el pago (simulado) o redirigir - ESTE BLOQUE LO VAMOS A ELIMINAR O COMENTAR
 if (isset($_GET['pagar'])) {
  check_guest_and_redirect();
  // Aquí iría el código para procesar el pago si el usuario está logueado
  // Por ahora, solo un mensaje
  echo "Pago procesado con éxito. ¡Gracias por tu compra!";
  exit();
 }
 */
 ?>
 <!DOCTYPE html>
 <html lang="es">
 <head>
  <meta charset="UTF-8">
  <title>Carrito de Compras</title>
  <style>
  body {
  font-family: 'Nunito', sans-serif;
  margin: 0;
  background-color: #f4f4f4;
  color: #333;
  line-height: 1.6;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-top: 20px;
  }
 
  .main-header {
  background-color: #28a745; /* Un verde más moderno */
  color: white;
  padding: 1rem 2rem;
  margin-bottom: 2rem;
  width: 100%;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }
 
  .header-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  }
 
  .site-title {
  font-size: 2rem;
  margin: 0;
  }
 
  .user-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
  }
 
  .user-name {
  font-size: 1rem;
  color: #eee;
  }
 
  .logout-link {
  text-decoration: none;
  color: white;
  background-color: #dc3545; /* Rojo para salir */
  padding: 0.5rem 1rem;
  border-radius: 0.25rem;
  font-size: 0.9rem;
  transition: background-color 0.3s ease;
  }
 
  .logout-link:hover {
  background-color: #c82333;
  }
 
  .cart-icon-wrapper {
  position: relative;
  }
 
  .icon-cart {
  width: 24px;
  height: 24px;
  fill: none;
  stroke: white;
  stroke-width: 2;
  vertical-align: middle;
  }
 
  .count-products {
  position: absolute;
  top: -0.5rem;
  right: -0.5rem;
  background-color: #ffc107; /* Amarillo para la cuenta */
  color: #333;
  border-radius: 50%;
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
  }
 
  .container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  background-color: #fff;
  border-radius: 0.5rem;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
  width: 95%;
  }
 
  h2 {
  color: #333;
  margin-bottom: 1.5rem;
  text-align: center;
  }
 
  .empty-cart {
  text-align: center;
  padding: 3rem;
  color: #777;
  font-size: 1.1rem;
  }
 
  .empty-cart a {
  color: #007bff;
  text-decoration: none;
  font-weight: bold;
  }
 
  .empty-cart a:hover {
  text-decoration: underline;
  }
 
  .cart-grid {
  display: grid;
  grid-template-columns: 100px 1fr auto auto auto auto; /* Añadido una columna más */
  gap: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #ddd;
  align-items: center;
  }
 
  .cart-grid:first-child {
  font-weight: bold;
  color: #555;
  padding-top: 0;
  }
 
  .cart-grid:last-child {
  border-bottom: none;
  padding-bottom: 0;
  }
 
  .item-image img {
  width: 100%;
  height: auto;
  border-radius: 0.25rem;
  box-shadow: 0 0.1rem 0.3rem rgba(0, 0, 0, 0.05);
  }
 
  .item-details h3 {
  font-size: 1rem;
  margin-bottom: 0.5rem;
  color: #333;
  }
 
  .item-details p {
  color: #777;
  font-size: 0.9rem;
  margin-bottom: 0.25rem;
  }
 
  .item-price {
  font-weight: bold;
  color: #555;
  text-align: right; /* Alinea el precio a la derecha */
  }
 
  .item-quantity {
  color: #555;
  text-align: center; /* Centra la cantidad */
  }
 
  .item-subtotal {
  font-weight: bold;
  color: #333;
  text-align: right; /* Alinea el subtotal a la derecha */
  }
 
  .item-remove a {
  color: #dc3545;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.3s ease;
  }
 
  .item-remove a:hover {
  color: #c82333;
  }
 
  .cart-summary {
  margin-top: 2rem;
  text-align: right;
  font-weight: bold;
  font-size: 1.2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #ddd;
  }
 
  .summary-buttons {
  margin-top: 1rem;
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  }
 
  .summary-buttons a {
  padding: 0.75rem 1.5rem;
  border-radius: 0.25rem;
  text-decoration: none;
  color: white;
  background-color: #007bff;
  transition: background-color 0.3s ease;
  font-size: 1rem;
  }
 
  .summary-buttons a:hover {
  background-color: #0056b3;
  }
 
  .continue-shopping {
  background-color: #6c757d !important;
  }
 
  .continue-shopping:hover {
  background-color: #545b62 !important;
  }
 
  .vaciar-carrito {
  margin-top: 1.5rem;
  text-align: center;
  }
 
  .vaciar-carrito a {
  color: #dc3545;
  text-decoration: none;
  font-size: 1rem;
  transition: color 0.3s ease;
  }
 
  .vaciar-carrito a:hover {
  text-decoration: underline;
  }
  </style>
 </head>
 <body>
  <header class="main-header">
  <div class="header-container">
  <h1 class="site-title">Carrito</h1>
  <div class="user-actions">
  <?php if (isset($_SESSION['usuario'])): ?>
  <span class="user-name"><?= htmlspecialchars($_SESSION['usuario']) ?></span>
  <a href="logout.php" class="logout-link">Salir</a>
  <?php elseif (isset($_SESSION['invitado'])): ?>
  <span class="user-name">Invitado</span>
  <a href="logout.php" class="logout-link">Salir</a>
  <?php endif; ?>
  </div>
  </div>
  </header>
  <div class="container">
  <h2>Tu Carrito de Compras</h2>
  <?php if (empty($_SESSION['carrito'])): ?>
  <div class="empty-cart">
  <p>Tu carrito está vacío.</p>
  <a href="tienda.php">Seguir comprando</a>
  </div>
  <?php else: ?>
  <div class="cart-grid">
  <div>Imagen</div>
  <div>Producto</div>
  <div style="text-align: right;">Precio</div>
  <div style="text-align: center;">Cantidad</div>
  <div style="text-align: right;">Subtotal</div>
  <div>Acción</div>
  </div>
  <?php foreach ($_SESSION['carrito'] as $id => $item):
  $subtotal = $item['precio'] * $item['cantidad'];
  $total += $subtotal;
  ?>
  <div class="cart-grid cart-item">
  <div class="item-image">
  <img src="img/<?= htmlspecialchars($item['imagen']) ?>" alt="<?= htmlspecialchars($item['nombre']) ?>" onerror="this.src='img/default.jpg'">
  </div>
  <div class="item-details">
  <h3><?= htmlspecialchars($item['nombre']) ?></h3>
  </div>
  <div class="item-price"><?= number_format($item['precio'], 2) ?> €</div>
  <div class="item-quantity"><?= $item['cantidad'] ?></div>
  <div class="item-subtotal"><?= number_format($subtotal, 2) ?> €</div>
  <div class="item-remove">
  <a href="carrito.php?eliminar=<?= $id ?>">Eliminar</a>
  </div>
  </div>
  <?php endforeach; ?>
 
  <div class="cart-summary">
  Total: <?= number_format($total, 2) ?>€
  <div class="summary-buttons">
  <a href="tienda.php" class="continue-shopping">Seguir comprando</a>
  <?php if (isset($_SESSION['invitado'])): ?>
  <a href="login.php?redirect=checkout">Pagar</a>
  <?php else: ?>
  <a href="checkout.php">Pagar</a> <?php endif; ?>
  </div>
  </div>
 
  <div class="vaciar-carrito">
  <a href="carrito.php?vaciar=1" onclick="return confirm('¿Estás seguro de que quieres vaciar el carrito?')">Vaciar carrito</a>
  </div>
  <?php endif; ?>
  </div>
 </body>
 </html>