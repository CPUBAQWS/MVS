<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $folder = $_POST["folder"] ?? "";
    $dataFile = __DIR__ . "/data/categories.json";

require_once __DIR__ . '/inc/i18n.php';
    if (!$folder || !file_exists($dataFile)) {
        echo json_encode(["success" => false, "message" => t('missing_folder_data')]);
        exit;
    }

    $categories = json_decode(file_get_contents($dataFile), true);
    $categories = array_filter($categories, function($cat) use ($folder) {
        return $cat["folder"] !== $folder;
    });

    file_put_contents($dataFile, json_encode(array_values($categories), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $dir = __DIR__ . "/Files/" . $folder;
    if (is_dir($dir)) {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            unlink("$dir/$file");
        }
        rmdir($dir);
    }

    echo json_encode(["success" => true, "message" => t('category_delete_success')]);
} else {
    echo json_encode(["success" => false, "message" => t('invalid_request')]);
}
header("Location: admin.php");
exit;
?>
