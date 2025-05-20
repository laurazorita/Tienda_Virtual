<?php
    session_start();

    sleep(2);

    $pago_exitoso = true;

    if ($pago_exitoso) {
    $_SESSION['carrito'] = [];


        header('Location: checkout.php?success=1');
    exit();
    } else {
    
        echo "Error al procesar el pago.";
    }
    ?>