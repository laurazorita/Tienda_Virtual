<?php
 require_once 'includes/conexion.php';
 //session_start(); 
 
 // Verificar si es invitado
 if (isset($_SESSION['invitado'])) {
  header('Location: login.php?redirect=checkout');
  exit();
 }
 
 // Verificar sesión de usuario y carrito
 if (!isset($_SESSION['usuario'])) {
  header('Location: login.php');
  exit();
 }
 
 if (empty($_SESSION['carrito'])) {
  header('Location: carrito.php');
  exit();
 }
 
 $error = null; 
 $compra_exitosa = false; 
 
 // Procesar el pago
 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (empty($_POST['numero_tarjeta']) || empty($_POST['fecha_expiracion']) || empty($_POST['cvv'])) {
  $error = "Por favor complete todos los campos del formulario de pago";
  } else {
  // Redirigir a la página de procesamiento de pago
  header('Location: procesar_pago.php');
  exit();
  }
 }
 
 // Verificar si la compra fue exitosa (viene de procesar_pago.php)
 if (isset($_GET['success']) && $_GET['success'] == 1) {
  $compra_exitosa = true;
  $_SESSION['carrito'] = []; // Limpiar el carrito después del pago
 }
 
 // Calcular total
 $total = array_reduce(
  $_SESSION['carrito'],
  fn($carry, $item) => $carry + ($item['precio'] * $item['cantidad']),
  0
 );
 ?>
 
 <!DOCTYPE html>
 <html lang="es">
 <head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <style>
  body {
  font-family: 'Nunito', sans-serif;
  margin: 0;
  background-color: #f4f4f4;
  color: #333;
  line-height: 1.6;
  }
 
  .main-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  }
 
  .header {
  background-color: #28a745;
  color: white;
  padding: 1rem 2rem;
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  }
 
  .user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  }
 
  .logout-link {
  color: white;
  text-decoration: none;
  background-color: #dc3545;
  padding: 0.5rem 1rem;
  border-radius: 0.25rem;
  }
 
  .checkout-container {
  display: flex;
  gap: 2rem;
  }
 
  .order-summary, .payment-details {
  background: white;
  padding: 2rem;
  border-radius: 0.5rem;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
  }
 
  .order-summary {
  flex: 1;
  }
 
  .payment-details {
  flex: 1;
  }
 
  .item-row {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 0;
  border-bottom: 1px solid #eee;
  }
 
  .total-row {
  font-weight: bold;
  font-size: 1.2rem;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 2px solid #ddd;
  }
 
  .form-group {
  margin-bottom: 1rem;
  }
 
  label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: bold;
  }
 
  input[type="text"] {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 0.25rem;
  font-size: 1rem;
  }
 
  .form-row {
  display: flex;
  gap: 1rem;
  }
 
  button[type="submit"] {
  background-color: #28a745;
  color: white;
  border: none;
  padding: 1rem 2rem;
  border-radius: 0.25rem;
  font-size: 1.1rem;
  cursor: pointer;
  width: 100%;
  margin-top: 1rem;
  }
 
  .error-message {
  color: #dc3545;
  margin-bottom: 1rem;
  padding: 0.75rem;
  background-color: #f8d7da;
  border-radius: 0.25rem;
  }
 
  .success-message {
  background-color: #d4edda;
  color: #155724;
  padding: 1rem;
  border-radius: 0.25rem;
  margin-bottom: 2rem;
  text-align: center;
  }
  </style>
 </head>
 <body>
  <div class="header">
  <h1>Finalizar Compra</h1>
  <div class="user-info">
  <span><?= htmlspecialchars($_SESSION['usuario']) ?></span>
  <a href="logout.php" class="logout-link">Salir</a>
  </div>
  </div>
 
  <div class="main-container">
  <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
  <div class="success-message">
  <h2>¡Compra exitosa!</h2>
  <p>Gracias por tu compra. Tu pedido ha sido procesado correctamente.</p>
  <a href="tienda.php">Volver a la tienda</a>
  </div>
  <?php else: ?>
  <?php if ($error): ?>
  <div class="error-message"><?= $error ?></div>
  <?php endif; ?>
 
  <div class="checkout-container">
  <div class="order-summary">
  <h2>Resumen de tu pedido</h2>
  <?php foreach ($_SESSION['carrito'] as $id => $item): ?>
  <div class="item-row">
  <span><?= htmlspecialchars($item['nombre']) ?> (x<?= $item['cantidad'] ?>)</span>
  <span><?= number_format($item['precio'] * $item['cantidad'], 2) ?>€</span>
  </div>
  <?php endforeach; ?>
  <div class="total-row">
  <span>Total:</span>
  <span><?= number_format($total, 2) ?>€</span>
  </div>
  </div>
 
  <div class="payment-details">
  <h2>Datos de pago</h2>
  <form method="POST">
  <div class="form-group">
  <label for="numero_tarjeta">Número de tarjeta</label>
  <input type="text" id="numero_tarjeta" name="numero_tarjeta" required placeholder="1234 5678 9012 3456">
  </div>
 
  <div class="form-row">
  <div class="form-group">
  <label for="fecha_expiracion">Fecha de expiración</label>
  <input type="text" id="fecha_expiracion" name="fecha_expiracion" required placeholder="MM/AA">
  </div>
  <div class="form-group">
  <label for="cvv">CVV</label>
  <input type="text" id="cvv" name="cvv" required placeholder="123">
  </div>
  </div>
 
  <button type="submit">
  Confirmar pago de <?= number_format($total, 2) ?>€
  </button>
  </form>
  </div>
  </div>
  <?php endif; ?>
  </div>
 </body>
 </html>