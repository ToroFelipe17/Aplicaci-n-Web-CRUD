<?php 
// Iniciar la sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

// Incluir la conexión a la base de datos
include 'includes/db.php';

// Obtener el nombre, id y rol del usuario
$id_usuario = $_SESSION['id_usuario'];
$nombre = $_SESSION['nombre'];
$rol = $_SESSION['rol'];

// Si es un usuario, obtener los recursos asignados
$recursos_asignados = [];
if ($rol === 'usuario') {
    $sql = "SELECT r.nombre, r.descripcion, r.estado 
            FROM asignaciones a
            JOIN recursos r ON a.id_recurso = r.id_recurso
            WHERE a.id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $recursos_asignados[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header>
        <h1>Bienvenido, <?php echo htmlspecialchars($nombre); ?></h1>
        <p>Rol: <?php echo htmlspecialchars($rol); ?></p>
        <a href="logout.php">Cerrar Sesión</a>
    </header>

    <main>
        <?php if ($rol === 'admin'): ?>
            <section>
                <h2>Gestión de Recursos</h2>
                <p><a href="recursos.php">Ver y gestionar recursos</a></p>
            </section>

            <section>
                <h2>Gestión de Asignaciones</h2>
                <p><a href="asignaciones.php">Ver y gestionar asignaciones</a></p>
            </section>
        <?php else: ?>
            <section>
                <h2>Tus Recursos</h2>
                <?php if (count($recursos_asignados) > 0): ?>
                    <ul>
                        <?php foreach ($recursos_asignados as $recurso): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($recurso['nombre']); ?></strong><br>
                                Descripción: <?php echo htmlspecialchars($recurso['descripcion']); ?><br>
                                Estado: <?php echo htmlspecialchars($recurso['estado']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No tienes recursos asignados.</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Gestión de Recursos</p>
    </footer>
</body>
</html>
<script src="js/scripts.js"></script>
