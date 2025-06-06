<?php
header('Content-Type: application/json');

$path = __DIR__ . '/data/categories.json';
$method = $_SERVER['REQUEST_METHOD'];

function load_json($file) {
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}

function save_json($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$categories = load_json($path);

if ($method === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add') {
        $id = $_POST['id'];
        $categories[$id] = [
            'name' => $_POST['name'],
            'folder' => $_POST['folder'],
            'voting_rule' => $_POST['voting_rule'],
            'enabled' => $_POST['enabled'] === 'true'
        ];
        save_json($path, $categories);
        echo json_encode(['status' => 'added']);
        exit();
    }

    if ($action === 'delete') {
        $id = $_POST['id'];
        unset($categories[$id]);
        save_json($path, $categories);
        echo json_encode(['status' => 'deleted']);
        exit();
    }

    if ($action === 'toggle') {
        $id = $_POST['id'];
        $categories[$id]['enabled'] = !$categories[$id]['enabled'];
        save_json($path, $categories);
        echo json_encode(['status' => 'toggled']);
        exit();
    }
}
echo json_encode(['error' => 'Invalid request']);
