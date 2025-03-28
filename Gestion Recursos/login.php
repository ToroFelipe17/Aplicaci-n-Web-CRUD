<?php
// Iniciar la sesión
session_start();

// Incluir la conexión a la base de datos
include 'includes/db.php';

// Habilitar reporte de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Asegurarse de que la conexión a MySQL use UTF-8
$conn->set_charset("utf8mb4");

// Inicializar variable de error
$error = "";

// Procesar el formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['iniciar_sesion'])) {
    // Capturar y limpiar los datos ingresados
    $correo = trim($_POST['correo'] ?? "");
    $contraseña = $_POST['contraseña'] ?? "";

    // Validar que no haya campos vacíos
    if (empty($correo) || empty($contraseña)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        // Preparar la consulta para evitar inyección SQL
        $stmt = $conn->prepare("SELECT id_usuario, nombre, correo, contraseña, rol FROM usuarios WHERE correo = ?");
        if ($stmt === false) {
            $error = "Error en la preparación de la consulta: " . htmlspecialchars($conn->error);
        } else {
            $stmt->bind_param("s", $correo);
            $stmt->execute();
            $resultado = $stmt->get_result();

            // Verificar si se encontró un usuario con el correo
            if ($resultado->num_rows > 0) {
                $usuario = $resultado->fetch_assoc();

                // Verificar la contraseña cifrada
                if (password_verify($contraseña, $usuario['contraseña'])) {
                    // Iniciar sesión y guardar datos del usuario
                    $_SESSION['id_usuario'] = $usuario['id_usuario'];
                    $_SESSION['nombre'] = $usuario['nombre'];
                    $_SESSION['correo'] = $usuario['correo'];
                    $_SESSION['rol'] = $usuario['rol'];

                    // Redirigir al dashboard
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error = "Contraseña incorrecta.";
                }
            } else {
                $error = "No se encontró una cuenta con ese correo.";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header>
        <nav class="nav container">
            <h1>Gestor de Recursos Autónoma</h1>
        </nav>
    </header>

    <main class="container">
        <div class="login-container">
            <h1>Iniciar Sesión</h1>
            
            <?php if (!empty($error)): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <label for="correo">Correo:</label>
                <input type="email" name="correo" id="correo" required>

                <label for="contraseña">Contraseña:</label>
                <input type="password" name="contraseña" id="contraseña" required>

                <button type="submit" name="iniciar_sesion">Iniciar Sesión</button>
            </form>

            <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a>.</p>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> Universidad Autónoma. Todos los derechos son reservados.</p>
    </footer>
</body>
</html>
<script src="js/scripts.js"></script>
