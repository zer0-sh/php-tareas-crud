<?php
require_once 'config.php';
redirectIfNotLogged();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$data = loadData();
$tarea = null;
foreach ($data['tareas'] as $t) {
    if ($t['id'] == $id) {
        $tarea = $t;
        break;
    }
}

if (!$tarea) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estado = $_POST['estado'] ?? 'pendiente';
    $tagsInput = trim($_POST['tags'] ?? '');
    $tags = array_filter(array_map('trim', explode(',', $tagsInput)));
    
    if ($titulo) {
        foreach ($data['tareas'] as &$t) {
            if ($t['id'] == $id) {
                $t['titulo'] = $titulo;
                $t['descripcion'] = $descripcion;
                $t['estado'] = $estado;
                $t['tags'] = $tags;
                break;
            }
        }
        saveData($data);
        header('Location: index.php');
        exit;
    }
}

$theme = getTheme();
$t = $themes[$theme];
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea</title>
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
        .submenu { padding-left: 15px; display: flex; gap: 8px; flex-wrap: wrap; }
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
        .btn-secondary { background: #666; color: white; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 500; }
        input, textarea, select { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #333; 
            border-radius: 6px; 
            background: <?= $t['bg'] ?>;
            color: <?= $t['text'] ?>;
            font-size: 14px;
        }
        .card { 
            background: <?= $t['card'] ?>; 
            padding: 30px; 
            border-radius: 12px; 
            max-width: 500px;
        }
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
                    <a href="#" class="menu-item">Kanban</a>
                    <a href="#" class="menu-item">Lista</a>
                    <a href="#" class="menu-item">Calendario</a>
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
                <h1>Editar Tarea</h1>
                <div class="user"><?= htmlspecialchars($user) ?></div>
            </div>
            
            <div class="card">
                <form method="POST">
                    <div class="form-group">
                        <label for="titulo">Título *</label>
                        <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($tarea['titulo']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($tarea['descripcion']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="tags">Tags (separados por coma)</label>
                        <input type="text" id="tags" name="tags" value="<?= htmlspecialchars(implode(', ', $tarea['tags'] ?? [])) ?>" placeholder="ej: trabajo, urgente, personal">
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value="pendiente" <?= $tarea['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="completada" <?= $tarea['estado'] === 'completada' ? 'selected' : '' ?>>Completada</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
