<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folder = $_POST['folder'] ?? '';
    $uploadDir = __DIR__ . "/Files/" . basename($folder) . "/";

    if (!is_dir($uploadDir)) {
        echo json_encode(["success" => false, "message" => "資料夾不存在"]);
        exit;
    }

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'pdf'];
    $blocked = ['exe', 'zip', 'msi'];

    if (!isset($_FILES['media'])) {
        echo json_encode(["success" => false, "message" => "未選擇檔案"]);
        exit;
    }

    $file = $_FILES['media'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $blocked)) {
        echo json_encode(["success" => false, "message" => "禁止上傳此副檔名"]);
        exit;
    }

    if (!in_array($ext, $allowed)) {
        echo json_encode(["success" => false, "message" => "不支援的檔案類型"]);
        exit;
    }

    $target = $uploadDir . basename($file['name']);
    if (move_uploaded_file($file['tmp_name'], $target)) {
        echo json_encode(["success" => true, "message" => "成功上傳"]);
    } else {
        echo json_encode(["success" => false, "message" => "上傳失敗"]);
    }
}
?>
