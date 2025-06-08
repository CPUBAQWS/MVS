<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.html");
    exit;
}

$name = $_POST['name'] ?? '';
$rule = $_POST['rule'] ?? '';
$maxVotes = isset($_POST['max_votes']) ? max(1, intval($_POST['max_votes'])) : 1;

if (!$name || !$rule) {
    echo "<script>alert('Missing category name or rule'); window.location.href = 'admin.php';</script>";
    exit;
}

$folder = '';
do {
    $folder = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5);
} while (file_exists(__DIR__ . "/Files/$folder"));

mkdir(__DIR__ . "/Files/$folder", 0777, true);

$catFile = __DIR__ . "/data/categories.json";
$categories = file_exists($catFile) ? json_decode(file_get_contents($catFile), true) : [];

$cat = ['name' => $name, 'folder' => $folder, 'rule' => $rule];
if ($rule === 'multi_unique') {
    $cat['max_votes'] = $maxVotes;
}
$categories[] = $cat;
$categories[] = ['name' => $name, 'folder' => $folder, 'rule' => $rule, 'enabled' => true];
file_put_contents($catFile, json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Redirect using JavaScript
echo "<script>window.location.href = 'admin.php';</script>";
exit;
?>
