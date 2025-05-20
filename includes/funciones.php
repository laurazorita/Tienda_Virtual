<?php

function sanitizar($dato) {
    return htmlspecialchars(trim($dato));
}

function manejarErrores($mensaje) {
    $_SESSION['error'] = $mensaje;
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
?>