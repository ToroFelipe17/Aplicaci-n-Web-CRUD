<?php
// Iniciar sesión y proteger la página
session_start();
include 'includes/db.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Agregar una asignación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asignar'])) {
    $id_usuario = intval($_POST['id_usuario']);
    $id_recurso = intval($_POST['id_recurso']);

    // Validar que el recurso esté disponible
    $recursoCheck = $conn->query("SELECT estado FROM recursos WHERE id_recurso = $id_recurso");
    if ($recursoCheck->num_rows > 0) {
        $recurso = $recursoCheck->fetch_assoc();
        if ($recurso['estado'] === 'disponible') {
            // Insertar asignación
            $sql = "INSERT INTO asignaciones (id_usuario, id_recurso, fecha_asignacion) VALUES ($id_usuario, $id_recurso, NOW())";
            $updateRecurso = "UPDATE recursos SET estado = 'asignado' WHERE id_recurso = $id_recurso";

            if ($conn->query($sql) && $conn->query($updateRecurso)) {
                echo "<p class='success'>Asignación realizada exitosamente.</p>";
            } else {
                echo "<p class='error'>Error al realizar la asignación: " . $conn->error . "</p>";
            }
        } else {
            echo "<p class='error'>El recurso no está disponible para asignar.</p>";
        }
    } else {
        echo "<p class='error'>El recurso no existe.</p>";
    }
}

// Eliminar una asignación
if (isset($_GET['eliminar'])) {
    $id_asignacion = intval($_GET['eliminar']);
    $sql = "DELETE FROM asignaciones WHERE id_asignacion = $id_asignacion";
    if ($conn->query($sql)) {
        echo "<p class='success'>Asignación eliminada exitosamente.</p>";
    } else {
        echo "<p class='error'>Error al eliminar la asignación: " . $conn->error . "</p>";
    }
}

// Obtener todas las asignaciones
$asignaciones = $conn->query("
    SELECT a.id_asignacion, u.nombre AS usuario, r.nombre AS recurso, a.fecha_asignacion 
    FROM asignaciones a 
    JOIN usuarios u ON a.id_usuario = u.id_usuario 
    JOIN recursos r ON a.id_recurso = r.id_recurso
");

if (!$asignaciones) {
    die("<p class='error'>Error al obtener las asignaciones: " . $conn->error . "</p>");
}

// Obtener usuarios y recursos para el formulario
$usuarios = $conn->query("SELECT * FROM usuarios");
$recursos = $conn->query("SELECT * FROM recursos WHERE estado = 'disponible'");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Asignaciones</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header>
        <h1>Gestión de Asignaciones</h1>
        <a href="dashboard.php">Volver al Menu</Menu></a>
        <a href="logout.php">Cerrar Sesión</a>
    </header>

    <main>
        <section>
            <h2>Asignar Recurso</h2>
            <form method="POST" action="asignaciones.php">
                <label for="id_usuario">Usuario:</label>
                <select name="id_usuario" id="id_usuario" required>
                    <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                        <option value="<?php echo $usuario['id_usuario']; ?>">
                            <?php echo htmlspecialchars($usuario['nombre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="id_recurso">Recurso:</label>
                <select name="id_recurso" id="id_recurso" required>
                    <?php while ($recurso = $recursos->fetch_assoc()): ?>
                        <option value="<?php echo $recurso['id_recurso']; ?>">
                            <?php echo htmlspecialchars($recurso['nombre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit" name="asignar">Asignar</button>
            </form>
        </section>

        <section>
            <h2>Listado de Asignaciones</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Recurso</th>
                        <th>Fecha de Asignación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($asignaciones->num_rows > 0): ?>
                        <?php while ($row = $asignaciones->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id_asignacion']); ?></td>
                                <td><?php echo htmlspecialchars($row['usuario']); ?></td>
                                <td><?php echo htmlspecialchars($row['recurso']); ?></td>
                                <td><?php echo htmlspecialchars($row['fecha_asignacion']); ?></td>
                                <td>
                                    <a href="asignaciones.php?eliminar=<?php echo $row['id_asignacion']; ?>" 
                                       onclick="return confirm('¿Estás seguro de eliminar esta asignación?');">
                                       Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No hay asignaciones disponibles.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Gestión de Recursos</p>
    </footer>
</body>
</html>
<script src="js/scripts.js"></script>