<?php
session_start();

define('DATA_FILE', __DIR__ . '/data.json');

function loadData() {
    if (!file_exists(DATA_FILE)) {
        $initialData = [
            'users' => [['username' => 'admin', 'password' => 'admin']],
            'tareas' => [
                ['id' => 1, 'titulo' => 'Completar documentación API', 'descripcion' => '撰写API文档并发布', 'estado' => 'completada', 'tags' => ['trabajo', 'documentación'], 'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))],
                ['id' => 2, 'titulo' => 'Revisar pull requests', 'descripcion' => 'Revisar PRs pendientes del equipo', 'estado' => 'pendiente', 'tags' => ['trabajo', 'urgente'], 'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))],
                ['id' => 3, 'titulo' => 'Configurar CI/CD', 'descripcion' => 'Setup GitHub Actions para deploy', 'estado' => 'completada', 'tags' => ['devops'], 'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))],
                ['id' => 4, 'titulo' => 'Reunión de equipo', 'descripcion' => 'Weekly standup meeting', 'estado' => 'pendiente', 'tags' => ['reunión'], 'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))],
                ['id' => 5, 'titulo' => 'Estudiar TypeScript', 'descripcion' => 'Aprender TypeScript avanzado', 'estado' => 'pendiente', 'tags' => ['personal', 'estudio'], 'created_at' => date('Y-m-d H:i:s', strtotime('-6 hours'))],
                ['id' => 6, 'titulo' => 'Optimizar base de datos', 'descripcion' => 'Agregar índices y optimizar queries', 'estado' => 'completada', 'tags' => ['trabajo', 'base de datos'], 'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))],
                ['id' => 7, 'titulo' => 'Actualizar dependencias', 'descripcion' => 'Actualizar npm packages', 'estado' => 'pendiente', 'tags' => ['trabajo', 'mantenimiento'], 'created_at' => date('Y-m-d H:i:s', strtotime('-4 hours'))],
                ['id' => 8, 'titulo' => 'Hacer backup', 'descripcion' => 'Backup de base de datos de producción', 'estado' => 'completada', 'tags' => ['devops', 'urgente'], 'created_at' => date('Y-m-d H:i:s', strtotime('-12 hours'))],
                ['id' => 9, 'titulo' => 'Practicar ejercicios de código', 'descripcion' => 'LeetCode daily challenge', 'estado' => 'pendiente', 'tags' => ['personal', 'estudio'], 'created_at' => date('Y-m-d H:i:s', strtotime('-30 minutes'))],
                ['id' => 10, 'titulo' => 'Escribir tests unitarios', 'descripcion' => 'Aumentar coverage a 80%', 'estado' => 'pendiente', 'tags' => ['trabajo', 'testing'], 'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))],
            ],
            'tags' => ['trabajo', 'personal', 'urgente', 'devops', 'estudio', 'reunión', 'documentación', 'mantenimiento', 'testing', 'base de datos']
        ];
        saveData($initialData);
        return $initialData;
    }
    return json_decode(file_get_contents(DATA_FILE), true);
}

function saveData($data) {
    file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT));
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function redirectIfNotLogged() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getStats() {
    $data = loadData();
    $tareas = $data['tareas'] ?? [];
    $total = count($tareas);
    $completadas = count(array_filter($tareas, fn($t) => $t['estado'] === 'completada'));
    $pendientes = $total - $completadas;
    
    $tagStats = [];
    foreach ($tareas as $tarea) {
        if (!empty($tarea['tags'])) {
            foreach ($tarea['tags'] as $tag) {
                if (!isset($tagStats[$tag])) $tagStats[$tag] = 0;
                $tagStats[$tag]++;
            }
        }
    }
    
    return [
        'total' => $total,
        'completadas' => $completadas,
        'pendientes' => $pendientes,
        'tagStats' => $tagStats
    ];
}

function getTheme() {
    return $_COOKIE['theme'] ?? 'dark-purple';
}

$themes = [
    'dark-purple' => ['name' => 'Purple', 'primary' => '#8b5cf6', 'bg' => '#0f0f1a', 'card' => '#1a1a2e', 'text' => '#e5e5e5'],
    'dark-blue' => ['name' => 'Blue', 'primary' => '#3b82f6', 'bg' => '#0a0a0f', 'card' => '#111827', 'text' => '#e5e5e5'],
    'dark-green' => ['name' => 'Green', 'primary' => '#22c55e', 'bg' => '#0a0f0a', 'card' => '#142420', 'text' => '#e5e5e5'],
    'dark-red' => ['name' => 'Red', 'primary' => '#ef4444', 'bg' => '#0f0a0a', 'card' => '#2a1111', 'text' => '#e5e5e5'],
    'dark-orange' => ['name' => 'Orange', 'primary' => '#f97316', 'bg' => '#0f0a05', 'card' => '#2a1a10', 'text' => '#e5e5e5'],
];
