<?php
require_once 'config.php';
redirectIfNotLogged();

$type = $_GET['type'] ?? 'kanban';
$data = loadData();
$tareas = $data['tareas'] ?? [];
$theme = getTheme();
$t = $themes[$theme];
$user = $_SESSION['user'];

$title = ['kanban' => 'Kanban', 'lista' => 'Lista', 'calendario' => 'Calendario'][$type] ?? 'Kanban';

$pendientes = array_filter($tareas, fn($t) => $t['estado'] === 'pendiente');
$completadas = array_filter($tareas, fn($t) => $t['estado'] === 'completada');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Tareas</title>
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
        .kanban-board {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding-bottom: 20px;
        }
        .kanban-column {
            min-width: 300px;
            background: <?= $t['card'] ?>;
            border-radius: 12px;
            padding: 20px;
        }
        .column-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid <?= $t['primary'] ?>;
        }
        .column-title.pending { border-color: #f59e0b; }
        .column-title.completed { border-color: #22c55e; }
        .task-card {
            background: <?= $t['bg'] ?>;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .task-title { font-weight: 500; margin-bottom: 8px; }
        .task-desc { font-size: 13px; color: #888; margin-bottom: 8px; }
        .task-tags { display: flex; flex-wrap: wrap; gap: 4px; }
        .tag {
            padding: 2px 8px;
            background: <?= $t['primary'] ?>;
            color: white;
            border-radius: 10px;
            font-size: 11px;
        }
        .empty { color: #666; font-style: italic; }
        .list-item {
            background: <?= $t['card'] ?>;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .list-title { font-weight: 500; }
        .list-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        .list-status.pendiente { background: #f59e0b; color: #333; }
        .list-status.completada { background: #22c55e; color: white; }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }
        .calendar-header {
            text-align: center;
            font-weight: 600;
            color: #888;
            padding: 10px;
        }
        .calendar-day {
            background: <?= $t['card'] ?>;
            padding: 10px;
            border-radius: 8px;
            min-height: 80px;
            font-size: 14px;
        }
        .calendar-day.today { border: 2px solid <?= $t['primary'] ?>; }
        .calendar-task {
            font-size: 11px;
            background: <?= $t['primary'] ?>;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            margin-top: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
                <a href="index.php" class="menu-item">Tareas</a>
            </div>
            
            <div class="menu-section">
                <div class="menu-title">Tableros</div>
                <div class="submenu">
                    <a href="board.php?type=kanban" class="menu-item <?= $type === 'kanban' ? 'active' : '' ?>">Kanban</a>
                    <a href="board.php?type=lista" class="menu-item <?= $type === 'lista' ? 'active' : '' ?>">Lista</a>
                    <a href="board.php?type=calendario" class="menu-item <?= $type === 'calendario' ? 'active' : '' ?>">Calendario</a>
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
                <h1><?= $title ?></h1>
                <div class="user"><?= htmlspecialchars($user) ?></div>
            </div>
            
            <?php if ($type === 'kanban'): ?>
            <div class="kanban-board">
                <div class="kanban-column">
                    <div class="column-title pending">Pendientes (<?= count($pendientes) ?>)</div>
                    <?php if (empty($pendientes)): ?>
                    <p class="empty">Sin tareas</p>
                    <?php else: ?>
                    <?php foreach ($pendientes as $tarea): ?>
                    <div class="task-card">
                        <div class="task-title"><?= htmlspecialchars($tarea['titulo']) ?></div>
                        <div class="task-desc"><?= htmlspecialchars($tarea['descripcion']) ?></div>
                        <?php if (!empty($tarea['tags'])): ?>
                        <div class="task-tags">
                            <?php foreach ($tarea['tags'] as $tag): ?>
                            <span class="tag"><?= htmlspecialchars($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="kanban-column">
                    <div class="column-title completed">Completadas (<?= count($completadas) ?>)</div>
                    <?php if (empty($completadas)): ?>
                    <p class="empty">Sin tareas</p>
                    <?php else: ?>
                    <?php foreach ($completadas as $tarea): ?>
                    <div class="task-card">
                        <div class="task-title"><?= htmlspecialchars($tarea['titulo']) ?></div>
                        <div class="task-desc"><?= htmlspecialchars($tarea['descripcion']) ?></div>
                        <?php if (!empty($tarea['tags'])): ?>
                        <div class="task-tags">
                            <?php foreach ($tarea['tags'] as $tag): ?>
                            <span class="tag"><?= htmlspecialchars($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php elseif ($type === 'lista'): ?>
            <?php foreach ($tareas as $tarea): ?>
            <div class="list-item">
                <div>
                    <div class="list-title"><?= htmlspecialchars($tarea['titulo']) ?></div>
                    <div class="task-desc"><?= htmlspecialchars($tarea['descripcion']) ?></div>
                </div>
                <span class="list-status <?= $tarea['estado'] ?>"><?= $tarea['estado'] ?></span>
            </div>
            <?php endforeach; ?>
            <?php if (empty($tareas)): ?>
            <p class="empty">No hay tareas</p>
            <?php endif; ?>
            
            <?php elseif ($type === 'calendario'): ?>
            <div class="calendar-grid">
                <div class="calendar-header">Dom</div>
                <div class="calendar-header">Lun</div>
                <div class="calendar-header">Mar</div>
                <div class="calendar-header">Mié</div>
                <div class="calendar-header">Jue</div>
                <div class="calendar-header">Vie</div>
                <div class="calendar-header">Sáb</div>
                <?php
                $daysInMonth = date('t');
                $firstDay = date('w', mktime(0, 0, 0, date('n'), 1));
                $today = date('j');
                
                for ($i = 0; $i < $firstDay; $i++): ?>
                <div class="calendar-day"></div>
                <?php endfor; ?>
                
                <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
                <div class="calendar-day <?= $day == $today ? 'today' : '' ?>">
                    <strong><?= $day ?></strong>
                    <?php foreach ($tareas as $tarea): 
                        $taskDay = date('j', strtotime($tarea['created_at']));
                        if ($taskDay == $day): ?>
                        <div class="calendar-task"><?= htmlspecialchars($tarea['titulo']) ?></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
            
            <footer style="margin-top: 40px; text-align: center; color: #666; font-size: 14px;">
                © <?= date('Y') ?> <a href="https://github.com/zer0-sh" target="_blank" style="color: <?= $t['primary'] ?>; text-decoration: none;">zer0-sh</a> - Licensed under GPL
            </footer>
        </div>
    </div>
</body>
</html>
