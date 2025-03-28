<?php
$host = "sql113.infinityfree.com"; 
$username = "if0_37918179"; 
$password = "0258Pipe01"; 
$dbname = "if0_37918179_gestion_recursos";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "Conexión Exitosa a la Base de Datos";
}
?>
