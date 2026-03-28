<?php
require_once 'config.php';
redirectIfNotLogged();

$id = $_GET['id'] ?? null;
if ($id) {
    $data = loadData();
    $data['tareas'] = array_filter($data['tareas'], fn($t) => $t['id'] != $id);
    saveData($data);
}

header('Location: index.php');
exit;
