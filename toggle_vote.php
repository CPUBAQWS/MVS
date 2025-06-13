<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['folder'])) {
    $folder = $_POST['folder'];
    $file = __DIR__ . '/data/categories.json';
    if (file_exists($file)) {
        $categories = json_decode(file_get_contents($file), true);
        foreach ($categories as &$cat) {
            if (($cat['folder'] ?? '') === $folder) {
                $cat['allow_vote'] = !($cat['allow_vote'] ?? true);
                break;
            }
        }
        file_put_contents($file, json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

header('Location: admin.php');
exit;
?>
