<?php
header('Content-Type: application/json');
$path = __DIR__ . '/data/categories.json';
if (file_exists($path)) {
    echo file_get_contents($path);
} else {
    echo json_encode([]);
}
