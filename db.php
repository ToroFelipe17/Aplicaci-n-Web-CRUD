<?php
// Detalles de conexión a la base de datos
$host = "localhost";
$username = "root"; 
$password = "********"; 
$dbname = "gestion_recursos";

// Crear la conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión Fallida: " . $conn->connect_error);
}

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar'])) {
    // Escapar los valores del formulario
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_BCRYPT); // Cifrar contraseña
    $rol = $conn->real_escape_string($_POST['rol']);

    // Validar si el correo ya existe
    $verificarCorreo = $conn->query("SELECT correo FROM usuarios WHERE correo = '$correo'");
    if ($verificarCorreo->num_rows > 0) {
        echo "El correo ya está registrado.";
    } else {
        // Insertar usuario en la base de datos
        $sql = "INSERT INTO usuarios (nombre, correo, contraseña, rol) VALUES ('$nombre', '$correo', '$contraseña', '$rol')";
        if ($conn->query($sql)) {
            echo "Registro exitoso. Ahora puedes iniciar sesión.";
            header("Location: login.php");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>
