<?php
// Iniciar la sesión
session_start();

// Incluir conexión a la base de datos y proteger acceso
include 'includes/db.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

// Verificar si el usuario es administrador
if ($_SESSION['rol'] !== 'admin') {
    echo "No tienes permisos para acceder a esta página.";
    exit;
}

// Función reutilizable para ejecutar SQL y manejar redirecciones
function ejecutarSQL($conn, $sql, $mensajeExito) {
    if ($conn->query($sql)) {
        header("Location: recursos.php?mensaje=$mensajeExito");
        exit;
    } else {
        echo "<p class='error'>Error: " . $conn->error . "</p>";
    }
}

// Agregar un recurso (si se envió el formulario)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $nombre = trim($conn->real_escape_string($_POST['nombre']));
    $descripcion = trim($conn->real_escape_string($_POST['descripcion']));
    $estado = trim($conn->real_escape_string($_POST['estado']));

    if (!empty($nombre) && !empty($descripcion) && ($estado === 'disponible' || $estado === 'asignado')) {
        $sql = "INSERT INTO recursos (nombre, descripcion, estado) VALUES ('$nombre', '$descripcion', '$estado')";
        ejecutarSQL($conn, $sql, "agregado");
    } else {
        echo "<p class='error'>Por favor, completa todos los campos correctamente.</p>";
    }
}

// Editar un recurso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $id_recurso = intval($_POST['id_recurso']);
    $nombre = trim($conn->real_escape_string($_POST['nombre']));
    $descripcion = trim($conn->real_escape_string($_POST['descripcion']));
    $estado = trim($conn->real_escape_string($_POST['estado']));

    if (!empty($nombre) && !empty($descripcion) && ($estado === 'disponible' || $estado === 'asignado')) {
        $sql = "UPDATE recursos SET nombre = '$nombre', descripcion = '$descripcion', estado = '$estado' WHERE id_recurso = $id_recurso";
        ejecutarSQL($conn, $sql, "editado");
    } else {
        echo "<p class='error'>Por favor, completa todos los campos correctamente.</p>";
    }
}

// Eliminar un recurso
if (isset($_GET['eliminar'])) {
    $id_recurso = intval($_GET['eliminar']);
    $sql = "DELETE FROM recursos WHERE id_recurso = $id_recurso";
    ejecutarSQL($conn, $sql, "eliminado");
}

// Obtener todos los recursos
$recursos = $conn->query("SELECT * FROM recursos");

if (!$recursos) {
    die("<p class='error'>Error al obtener los recursos: " . $conn->error . "</p>");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Recursos</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header>
        <h1>Gestión de Recursos</h1>
        <a href="dashboard.php">Volver al menu</a>
        <a href="logout.php">Cerrar Sesión</a>
    </header>

    <main>
        <section>
            <h2>Agregar Nuevo Recurso</h2>
            <form method="POST" action="recursos.php">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" required>

                <label for="descripcion">Descripción:</label>
                <textarea name="descripcion" id="descripcion" required></textarea>

                <label for="estado">Estado:</label>
                <select name="estado" id="estado" required>
                    <option value="disponible">Disponible</option>
                    <option value="asignado">Asignado</option>
                </select>

                <button type="submit" name="agregar">Agregar Recurso</button>
            </form>
        </section>

        <section>
            <h2>Listado de Recursos</h2>
            <!-- Mostrar mensajes de éxito -->
            <?php if (isset($_GET['mensaje'])): ?>
                <p class="success">
                    <?php
                    if ($_GET['mensaje'] === 'agregado') echo "Recurso agregado exitosamente.";
                    if ($_GET['mensaje'] === 'editado') echo "Recurso editado exitosamente.";
                    if ($_GET['mensaje'] === 'eliminado') echo "Recurso eliminado exitosamente.";
                    ?>
                </p>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recursos->num_rows > 0): ?>
                        <?php while ($row = $recursos->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id_recurso']); ?></td>
                                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                                <td><?php echo htmlspecialchars($row['estado']); ?></td>
                                <td>
                                    <!-- Formulario para editar recurso -->
                                    <form method="POST" action="recursos.php" style="display:inline-block;">
                                        <input type="hidden" name="id_recurso" value="<?php echo $row['id_recurso']; ?>">
                                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($row['nombre']); ?>" required>
                                        <input type="text" name="descripcion" value="<?php echo htmlspecialchars($row['descripcion']); ?>" required>
                                        <select name="estado" required>
                                            <option value="disponible" <?php echo $row['estado'] === 'disponible' ? 'selected' : ''; ?>>Disponible</option>
                                            <option value="asignado" <?php echo $row['estado'] === 'asignado' ? 'selected' : ''; ?>>Asignado</option>
                                        </select>
                                        <button type="submit" name="editar">Guardar</button>
                                    </form>

                                    <!-- Enlace para eliminar recurso -->
                                    <a href="recursos.php?eliminar=<?php echo $row['id_recurso']; ?>" 
                                       onclick="return confirm('¿Estás seguro de eliminar este recurso?');">
                                       Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No hay recursos disponibles.</td>
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
