<?php
require_once 'config.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = getDB()->prepare("DELETE FROM tareas WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: index.php');
exit;
