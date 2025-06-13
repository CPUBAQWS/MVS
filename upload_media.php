<?php
require_once __DIR__ . '/inc/i18n.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folder = $_POST['folder'] ?? '';
    $uploadDir = __DIR__ . "/Files/" . basename($folder) . "/";

    if (!is_dir($uploadDir)) {
        echo json_encode(["success" => false, "message" => t('folder_not_exist')]);
        exit;
    }

    // Allow common image/video formats plus custom YouTube link files (.yt)
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'pdf', 'yt'];
    $blocked = ['exe', 'zip', 'msi'];

    if (!isset($_FILES['media'])) {
        echo json_encode(["success" => false, "message" => t('no_file_selected')]);
        exit;
    }

    $file = $_FILES['media'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $blocked)) {
        echo json_encode(["success" => false, "message" => t('blocked_extension')]);
        exit;
    }

    if (!in_array($ext, $allowed)) {
        echo json_encode(["success" => false, "message" => t('unsupported_type')]);
        exit;
    }

    $target = $uploadDir . basename($file['name']);
    if (move_uploaded_file($file['tmp_name'], $target)) {
        echo json_encode(["success" => true, "message" => t('upload_success')]);
    } else {
        echo json_encode(["success" => false, "message" => t('upload_failed')]);
    }
}
?>
