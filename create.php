<?php
/**
 * PHP Tareas CRUD
 * Copyright (C) 2025  zer0-sh
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */
require_once 'config.php';
redirectIfNotLogged();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estado = $_POST['estado'] ?? 'pendiente';
    $tagsInput = trim($_POST['tags'] ?? '');
    $tags = array_filter(array_map('trim', explode(',', $tagsInput)));
    
    if ($titulo) {
        $data = loadData();
        $id = count($data['tareas']) + 1;
        $data['tareas'][] = [
            'id' => $id,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'estado' => $estado,
            'tags' => $tags,
            'created_at' => date('Y-m-d H:i:s')
        ];
        saveData($data);
        header('Location: index.php');
        exit;
    } else {
        $error = 'El título es obligatorio';
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
    <title>Nueva Tarea</title>
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
        .error { color: #ef4444; margin-bottom: 15px; }
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
                <h1>Nueva Tarea</h1>
                <div class="user"><?= htmlspecialchars($user) ?></div>
            </div>
            
            <div class="card">
                <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="titulo">Título *</label>
                        <input type="text" id="titulo" name="titulo" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="tags">Tags (separados por coma)</label>
                        <input type="text" id="tags" name="tags" placeholder="ej: trabajo, urgente, personal">
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value="pendiente">Pendiente</option>
                            <option value="completada">Completada</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
