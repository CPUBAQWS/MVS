<?php
require_once __DIR__ . '/inc/i18n.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folder = $_POST['folder'] ?? '';
    $file = $_POST['file'] ?? '';

    $target = __DIR__ . "/Files/" . basename($folder) . "/" . basename($file);

    if (file_exists($target)) {
        unlink($target);
        echo json_encode(["success" => true, "message" => t('file_deleted')]);
    } else {
        echo json_encode(["success" => false, "message" => t('file_not_found')]);
    }
}
?>
