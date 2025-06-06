<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folder = $_POST['folder'] ?? '';
    $file = $_POST['file'] ?? '';

    $target = __DIR__ . "/Files/" . basename($folder) . "/" . basename($file);

    if (file_exists($target)) {
        unlink($target);
        echo json_encode(["success" => true, "message" => "已刪除"]);
    } else {
        echo json_encode(["success" => false, "message" => "找不到檔案"]);
    }
}
?>
