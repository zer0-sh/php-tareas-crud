<?php
require_once 'config.php';
redirectIfNotLogged();

$data = loadData();
$tareas = $data['tareas'] ?? [];
$theme = getTheme();
$t = $themes[$theme];
$user = $_SESSION['user'];

usort($tareas, fn($a, $b) => strtotime($b['created_at'] ?? time()) - strtotime($a['created_at'] ?? time()));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tareas - Tareas</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; 
            background: <?= $t['bg'] ?>; 
            color: <?= $t['text'] ?>; 
            min-height: 100vh;
        }
        .layout { display: flex; min-height: 100vh; }
        .sidebar {
            width: 250px;
            background: <?= $t['card'] ?>;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .logo { 
            font-size: 20px; 
            font-weight: bold; 
            color: <?= $t['primary'] ?>; 
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #333;
        }
        .menu-item {
            padding: 12px 15px;
            color: <?= $t['text'] ?>;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 5px;
            transition: background 0.2s;
        }
        .menu-item:hover, .menu-item.active { 
            background: <?= $t['primary'] ?>; 
        }
        .menu-section { margin-bottom: 20px; }
        .menu-title {
            font-size: 11px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 10px;
            padding-left: 15px;
        }
        .submenu { padding-left: 15px; display: flex; gap: 8px; flex-wrap: wrap; margin-top: 5px; }
        .submenu .menu-item { font-size: 13px; padding: 8px 12px; margin-bottom: 0; background: rgba(255,255,255,0.05); }
        .submenu .menu-item { font-size: 14px; }
        .main { flex: 1; padding: 30px; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .user { color: #888; }
        .btn { 
            padding: 10px 20px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            text-decoration: none; 
            display: inline-block;
            font-size: 14px;
            font-weight: 500;
        }
        .btn-primary { background: <?= $t['primary'] ?>; color: white; }
        .btn-edit { background: #f59e0b; color: #333; }
        .btn-delete { background: #ef4444; color: white; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            background: <?= $t['card'] ?>; 
            border-radius: 12px; 
            overflow: hidden; 
        }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #333; }
        th { background: <?= $t['bg'] ?>; font-weight: 600; font-size: 12px; text-transform: uppercase; color: #666; }
        .estado { 
            padding: 4px 10px; 
            border-radius: 20px; 
            font-size: 12px; 
            display: inline-block;
        }
        .pendiente { background: #f59e0b; color: #333; }
        .completada { background: #22c55e; color: white; }
        .actions { display: flex; gap: 8px; }
        .tag {
            display: inline-block;
            padding: 3px 8px;
            background: <?= $t['primary'] ?>;
            color: white;
            border-radius: 12px;
            font-size: 11px;
            margin-right: 4px;
            margin-bottom: 4px;
        }
        .empty { text-align: center; color: #666; padding: 40px; }
    </style>
</head>
<body>
    <div class="layout">
        <div class="sidebar">
            <div class="logo">Tareas App</div>
            
            <div class="menu-section">
                <div class="menu-title">General</div>
                <a href="dashboard.php" class="menu-item">Dashboard</a>
                <a href="index.php" class="menu-item active">Tareas</a>
            </div>
            
            <div class="menu-section">
                <div class="menu-title">Tableros</div>
                <div class="submenu">
                    <a href="board.php?type=kanban" class="menu-item">Kanban</a>
                    <a href="board.php?type=lista" class="menu-item">Lista</a>
                    <a href="board.php?type=calendario" class="menu-item">Calendario</a>
                </div>
            </div>
            
            <div class="menu-section">
                <div class="menu-title">Personalización</div>
                <div class="submenu">
                    <a href="theme.php" class="menu-item">Temas</a>
                </div>
            </div>
            
            <div style="margin-top: auto;">
                <a href="logout.php" class="menu-item">Cerrar Sesión</a>
            </div>
        </div>
        
        <div class="main">
            <div class="header">
                <h1>Mis Tareas</h1>
                <div class="user"><?= htmlspecialchars($user) ?></div>
            </div>
            
            <a href="create.php" class="btn btn-primary">Nueva Tarea</a>
            <br><br>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Tags</th>
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
                            <?php if (!empty($tarea['tags'])): ?>
                                <?php foreach ($tarea['tags'] as $tag): ?>
                                    <span class="tag"><?= htmlspecialchars($tag) ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="estado <?= $tarea['estado'] ?>">
                                <?= $tarea['estado'] ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="edit.php?id=<?= $tarea['id'] ?>" class="btn btn-edit btn-sm">Editar</a>
                            <a href="delete.php?id=<?= $tarea['id'] ?>" class="btn btn-delete btn-sm" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($tareas)): ?>
                    <tr><td colspan="6" class="empty">No hay tareas</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <footer style="margin-top: 40px; text-align: center; color: #666; font-size: 14px;">
                © <?= date('Y') ?> <a href="https://github.com/zer0-sh" target="_blank" style="color: <?= $t['primary'] ?>; text-decoration: none;">zer0-sh</a> - Licensed under GPL
            </footer>
        </div>
    </div>
</body>
</html>
