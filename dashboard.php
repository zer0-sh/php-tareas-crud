<?php
require_once 'config.php';
redirectIfNotLogged();

$stats = getStats();
$theme = getTheme();
$t = $themes[$theme];
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Tareas</title>
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
        .main { flex: 1; padding: 30px; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .user { color: #888; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: <?= $t['card'] ?>;
            padding: 25px;
            border-radius: 12px;
        }
        .stat-value { font-size: 36px; font-weight: bold; color: <?= $t['primary'] ?>; }
        .stat-label { color: #888; margin-top: 5px; }
        .chart-card {
            background: <?= $t['card'] ?>;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .chart-title { margin-bottom: 20px; font-size: 18px; }
        .bar-container { margin-bottom: 15px; }
        .bar-label { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 14px; }
        .bar { 
            height: 24px; 
            background: #333; 
            border-radius: 4px; 
            overflow: hidden;
        }
        .bar-fill { 
            height: 100%; 
            background: <?= $t['primary'] ?>; 
            transition: width 0.3s;
        }
        .bar-fill.completed { background: #22c55e; }
        .bar-fill.pending { background: #f59e0b; }
        .tags-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .tag-stat {
            background: <?= $t['bg'] ?>;
            padding: 10px 15px;
            border-radius: 20px;
            font-size: 14px;
        }
        .tag-stat span { color: <?= $t['primary'] ?>; font-weight: bold; }
        .empty { color: #666; font-style: italic; }
    </style>
</head>
<body>
    <div class="layout">
        <div class="sidebar">
            <div class="logo">Tareas App</div>
            
            <div class="menu-section">
                <div class="menu-title">General</div>
                <a href="dashboard.php" class="menu-item active">Dashboard</a>
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
                    <a href="theme.php" class="menu-item">Temas</a>
                </div>
            </div>
            
            <div style="margin-top: auto;">
                <a href="logout.php" class="menu-item">Cerrar Sesión</a>
            </div>
        </div>
        
        <div class="main">
            <div class="header">
                <h1>Dashboard</h1>
                <div class="user">Bienvenido, <?= htmlspecialchars($user) ?></div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?= $stats['total'] ?></div>
                    <div class="stat-label">Total de Tareas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= $stats['completadas'] ?></div>
                    <div class="stat-label">Completadas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= $stats['pendientes'] ?></div>
                    <div class="stat-label">Pendientes</div>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="chart-title">Progreso</div>
                <?php if ($stats['total'] > 0): ?>
                <?php $pct = round(($stats['completadas'] / $stats['total']) * 100); ?>
                <div class="bar-container">
                    <div class="bar-label">
                        <span>Completadas</span>
                        <span><?= $stats['completadas'] ?> (<?= $pct ?>%)</span>
                    </div>
                    <div class="bar">
                        <div class="bar-fill completed" style="width: <?= $pct ?>%"></div>
                    </div>
                </div>
                <?php $pct2 = round(($stats['pendientes'] / $stats['total']) * 100); ?>
                <div class="bar-container">
                    <div class="bar-label">
                        <span>Pendientes</span>
                        <span><?= $stats['pendientes'] ?> (<?= $pct2 ?>%)</span>
                    </div>
                    <div class="bar">
                        <div class="bar-fill pending" style="width: <?= $pct2 ?>%"></div>
                    </div>
                </div>
                <?php else: ?>
                <p class="empty">No hay tareas aún</p>
                <?php endif; ?>
            </div>
            
            <div class="chart-card">
                <div class="chart-title">Estadísticas por Tags</div>
                <?php if (!empty($stats['tagStats'])): ?>
                <div class="tags-grid">
                    <?php foreach ($stats['tagStats'] as $tag => $count): ?>
                    <div class="tag-stat"><?= htmlspecialchars($tag) ?>: <span><?= $count ?></span></div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="empty">No hay tags asignados</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
