<?php
// Incluir conexión a la base de datos
include 'includes/db.php';

// Habilitar reporte de errores y escribir en un log
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

// Inicializar mensaje para mostrar errores o éxitos
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Capturar datos del formulario
        $nombre = trim($_POST['nombre'] ?? "");
        $correo = trim($_POST['correo'] ?? "");
        $contraseña = $_POST['contraseña'] ?? "";
        $rol = $_POST['rol'] ?? "";

        // Validar que no haya campos vacíos
        if (empty($nombre) || empty($correo) || empty($contraseña) || empty($rol)) {
            $mensaje = "<p style='color: red;'>Todos los campos son obligatorios.</p>";
            throw new Exception("Campos vacíos en el formulario.");
        }

        // Validar formato del correo electrónico
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $mensaje = "<p style='color: red;'>Formato de correo inválido.</p>";
            throw new Exception("Formato inválido de correo: $correo");
        }

        // Validar si el correo ya existe
        $stmt = $conn->prepare("SELECT correo FROM usuarios WHERE correo = ?");
        if (!$stmt) throw new Exception("Error en la consulta SELECT: " . $conn->error);

        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $mensaje = "<p style='color: red;'>El correo ya está registrado.</p>";
            throw new Exception("Correo duplicado: $correo");
        } else {
            // Cifrar contraseña
            $contraseña_cifrada = password_hash($contraseña, PASSWORD_BCRYPT);

            // Insertar usuario en la base de datos
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contraseña, rol) VALUES (?, ?, ?, ?)");
            if (!$stmt) throw new Exception("Error en la consulta INSERT: " . $conn->error);

            $stmt->bind_param("ssss", $nombre, $correo, $contraseña_cifrada, $rol);

            if ($stmt->execute()) {
                // Redirigir al inicio de sesión
                header("Location: login.php");
                exit;
            } else {
                $mensaje = "<p style='color: red;'>Error al registrar: " . htmlspecialchars($conn->error) . "</p>";
                throw new Exception("Error al ejecutar INSERT: " . $conn->error);
            }
        }
    } catch (Exception $e) {
        // Guardar error en archivo log
        error_log($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container">
        <h2>Registro de Usuario</h2>

        <!-- Mostrar mensaje de éxito o error -->
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="correo">Correo Electrónico:</label>
            <input type="email" name="correo" id="correo" required>

            <label for="contraseña">Contraseña:</label>
            <input type="password" name="contraseña" id="contraseña" required>

            <label for="rol">Rol:</label>
            <select name="rol" id="rol" required>
                <option value="usuario">Usuario</option>
                <option value="admin">Administrador</option>
            </select>

            <button type="submit" name="registrar">Registrar</button>
        </form>
    </div>
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> Universidad Autónoma. Todos los derechos son reservados.</p>
    </footer>
</body>
</html>
<script src="js/scripts.js"></script>
