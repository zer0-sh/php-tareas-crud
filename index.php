<?php
require_once 'config.php';
initDB();

$stmt = getDB()->query("SELECT * FROM tareas ORDER BY created_at DESC");
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tareas CRUD</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { margin-bottom: 20px; color: #333; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-edit { background: #ffc107; color: #333; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-success { background: #28a745; color: white; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; }
        .estado { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .pendiente { background: #ffc107; color: #333; }
        .completada { background: #28a745; color: white; }
        .actions { display: flex; gap: 8px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 500; }
        input, textarea, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h1>Mis Tareas</h1>
        <a href="create.php" class="btn btn-primary">Nueva Tarea</a>
        <br><br>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tareas as $tarea): ?>
                <tr>
                    <td><?= $tarea['id'] ?></td>
                    <td><?= htmlspecialchars($tarea['titulo']) ?></td>
                    <td><?= htmlspecialchars($tarea['descripcion']) ?></td>
                    <td>
                        <span class="estado <?= $tarea['estado'] ?>">
                            <?= $tarea['estado'] ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="edit.php?id=<?= $tarea['id'] ?>" class="btn btn-edit">Editar</a>
                        <a href="delete.php?id=<?= $tarea['id'] ?>" class="btn btn-delete" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($tareas)): ?>
                <tr><td colspan="5" style="text-align:center">No hay tareas</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
