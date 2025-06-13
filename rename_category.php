<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['folder'], $_POST['new_name'])) {
    $folder = $_POST['folder'];
    $newName = trim($_POST['new_name']);
    $file = __DIR__ . '/data/categories.json';

    if (!file_exists($file)) {
        header("Location: admin.php");
        exit;
    }

    $categories = json_decode(file_get_contents($file), true);
    foreach ($categories as &$category) {
        if ($category['folder'] === $folder) {
            $category['name'] = $newName;
            break;
        }
    }

    file_put_contents($file, json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

header("Location: admin.php");
exit;
?>
