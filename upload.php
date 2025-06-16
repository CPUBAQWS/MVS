<?php
session_start();
if (!isset($_SESSION['user_code'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/inc/i18n.php';

$categoryFile = __DIR__ . '/data/categories.json';
$categories = file_exists($categoryFile) ? json_decode(file_get_contents($categoryFile), true) : [];
$folders = array_column($categories, 'folder');

$allowed = ['jpg','jpeg','png','gif','mp4','mov','pdf','txt','yt'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category']) && isset($_FILES['files'])) {
    $cat = $_POST['category'];
    if (in_array($cat, $folders)) {
        do {
            $suffix = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5);
            $destDir = __DIR__ . "/Files/{$cat}_$suffix";
        } while (file_exists($destDir));
        mkdir($destDir, 0777, true);
        foreach ($_FILES['files']['tmp_name'] as $idx => $tmp) {
            if ($_FILES['files']['error'][$idx] !== UPLOAD_ERR_OK) continue;
            $name = basename($_FILES['files']['name'][$idx]);
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) continue;
            move_uploaded_file($tmp, "$destDir/$name");
        }
        $message = t('upload_success');
    }
}

$langAttr = get_lang();
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($langAttr); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars(t('submit_files')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-4 text-gray-800">
<div class="max-w-md mx-auto bg-white p-4 rounded shadow">
    <?php if ($message): ?>
        <p class="text-green-600 mb-4 text-center"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <label class="block text-sm">
            <span class="mr-2"><?php echo t('category_name'); ?></span>
            <select name="category" class="border p-1 rounded w-full">
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['folder']); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <input type="file" name="files[]" multiple class="border p-2 w-full rounded">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full"><?php echo htmlspecialchars(t('submit_files')); ?></button>
    </form>
</div>
</body>
</html>
