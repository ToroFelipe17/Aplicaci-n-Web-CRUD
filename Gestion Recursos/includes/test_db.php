<?php
$host = "------.infinityfree.com"; 
$username = "-------"; 
$password = "******"; 
$dbname = "-------_gestion_recursos";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "Conexión Exitosa a la Base de Datos";
}
?>
