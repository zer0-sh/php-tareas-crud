<?php
require_once 'config.php';
redirectIfNotLogged();

$theme = getTheme();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theme'])) {
    setcookie('theme', $_POST['theme'], time() + (86400 * 30), '/');
    header('Location: theme.php');
    exit;
}
$t = $themes[$theme];
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personalización - Tareas</title>
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
        .theme-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .theme-card {
            background: <?= $t['card'] ?>;
            padding: 20px;
            border-radius: 12px;
            cursor: pointer;
            border: 3px solid transparent;
            transition: border-color 0.2s;
        }
        .theme-card.selected {
            border-color: <?= $t['primary'] ?>;
        }
        .theme-preview {
            height: 60px;
            border-radius: 8px;
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
        }
        .theme-preview .preview-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 20px;
            width: 60%;
        }
        .theme-name { font-weight: 600; text-align: center; }
    </style>
</head>
<body>
    <div class="layout">
        <div class="sidebar">
            <div class="logo">Tareas App</div>
            
            <div class="menu-section">
                <div class="menu-title">General</div>
                <a href="dashboard.php" class="menu-item">Dashboard</a>
                <a href="index.php" class="menu-item">Tareas</a>
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
                    <a href="theme.php" class="menu-item active">Temas</a>
                </div>
            </div>
            
            <div style="margin-top: auto;">
                <a href="logout.php" class="menu-item">Cerrar Sesión</a>
            </div>
        </div>
        
        <div class="main">
            <div class="header">
                <h1>Personalización - Temas</h1>
                <div class="user"><?= htmlspecialchars($user) ?></div>
            </div>
            
            <div class="theme-grid">
                <?php foreach ($themes as $key => $value): ?>
                <form method="POST" class="theme-card <?= $key === $theme ? 'selected' : '' ?>">
                    <input type="hidden" name="theme" value="<?= $key ?>">
                    <button type="submit" style="background: none; border: none; cursor: pointer; width: 100%;">
                        <div class="theme-preview" style="background: <?= $value['bg'] ?>">
                            <div class="preview-bar" style="background: <?= $value['primary'] ?>"></div>
                        </div>
                        <div class="theme-name" style="color: <?= $t['text'] ?>"><?= $value['name'] ?></div>
                    </button>
                </form>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
