<?php
// Habilitar reporte de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Contraseñas de los usuarios
$contraseñas = [
    "Prueba123", // Contraseña para usuario 1
    "Prueba123", // Contraseña para usuario 2
    "Prueba123", // Contraseña para usuario 3
    "Prueba123"  // Contraseña para usuario 4
];

foreach ($contraseñas as $key => $contraseña) {
    $hash = password_hash($contraseña, PASSWORD_BCRYPT);
    echo "Contraseña " . ($key + 1) . ": $hash<br>";
}
?>
