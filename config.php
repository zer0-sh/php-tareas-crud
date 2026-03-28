<?php
session_start();

define('DATA_FILE', __DIR__ . '/data.json');

function loadData() {
    if (!file_exists(DATA_FILE)) {
        return ['users' => [['username' => 'admin', 'password' => 'admin']], 'tareas' => [], 'tags' => []];
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
