<?php
$ruleMap = [
  'single' => '單一票',
  'multi_unique' => '多票（不可重複）',
  'multi-multi' => '多票（可重複）'
];

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.html");
    exit;
}

$categoryFile = __DIR__ . '/data/categories.json';
$categories = file_exists($categoryFile) ? json_decode(file_get_contents($categoryFile), true) : [];
?>
<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8" />
  <title>Admin 控制台</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <header class="bg-blue-700 text-white p-4 text-center text-xl font-semibold">
    管理介面
  </header>
  <main class="max-w-6xl mx-auto p-6 space-y-8">

    <section class="bg-white p-4 rounded shadow">
      <h2 class="text-lg font-bold mb-4">現有分類</h2>
      <table class="w-full table-auto border-collapse">
        <thead>
          <tr class="bg-gray-200">
            <th class="px-4 py-2 text-left">顯示名稱</th>
            <th class="px-4 py-2 text-left">資料夾</th>
            <th class="px-4 py-2 text-center">檔案數</th>
            <th class="px-4 py-2 text-left">投票規則</th>
            <th class="px-4 py-2 text-left">改名</th>
            <th class="px-4 py-2 text-center">刪除</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($categories as $cat): ?>
          <?php $folderPath = __DIR__ . '/Files/' . $cat['folder']; ?>
          <?php $fileCount = is_dir($folderPath) ? count(array_filter(scandir($folderPath), function ($f) use ($folderPath) {
              return is_file($folderPath . '/' . $f) && !in_array($f, ['.', '..']);
          })) : 0; ?>
          <tr class="border-t">
            <td class="px-4 py-2"><?php echo htmlspecialchars($cat['name']); ?></td>
            <td class="px-4 py-2"><?php echo htmlspecialchars($cat['folder']); ?></td>
            <td class="px-4 py-2 text-center"><?php echo $fileCount; ?></td>
            <td class="px-4 py-2"><?php echo htmlspecialchars($ruleMap[$cat['rule']] ?? $cat['rule']); ?></td>
            <td class="px-4 py-2 text-center">
              <form action="rename_category.php" method="POST" class="flex items-center space-x-2">
                <input type="hidden" name="folder" value="<?php echo htmlspecialchars($cat['folder']); ?>">
                <input type="text" name="new_name" placeholder="新分類名稱" class="border p-1 rounded text-sm" required>
                <button type="submit" class="bg-yellow-500 text-white text-sm px-2 py-1 rounded">改名</button>
              </form>
            </td>
            <td class="px-4 py-2 text-center">
              <form action="delete_category.php" method="POST" onsubmit="return confirm('確認要刪除此分類及其所有檔案？');">
                <input type="hidden" name="folder" value="<?php echo htmlspecialchars($cat['folder']); ?>">
                <button type="submit" class="bg-red-500 text-white text-sm px-3 py-1 rounded">刪除</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <section class="bg-white p-4 rounded shadow mt-6 max-w-xl mx-auto">
      <h2 class="text-lg font-bold mb-2">新增分類</h2>
      <form method="POST" action="create_category_folder.php">
        <div class="mb-2">
          <label>分類名稱：</label>
          <input type="text" name="name" class="border p-2 rounded w-full" required>
        </div>
        <div class="mb-2">
          <label>投票規則：</label>
          <select name="rule" class="border p-2 rounded w-full" required>
            <option value="single">每人一票</option>
            <option value="multi_unique">可投多票，不可重複</option>
            <option value="multi_repeat">可投多票，可重複</option>
          </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">建立分類</button>
      </form>
    </section>

    <section class="bg-white p-4 rounded shadow mt-6 max-w-xl mx-auto">
      <h2 class="text-lg font-bold mb-2">產生使用者代碼</h2>
      <form method="POST">
        <label class="block mb-2">數量：</label>
        <input type="number" name="count" value="5" class="border p-2 rounded w-32 mb-4">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">產生</button>
      </form>
      <?php
        $generated = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['count'])) {
            function generateCode($length = 6) {
                return substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', $length)), 0, $length);
            }
            $count = max(1, intval($_POST['count']));
            $userFile = __DIR__ . '/data/users.json';
            $existing = file_exists($userFile) ? json_decode(file_get_contents($userFile), true) : [];
            for ($i = 0; $i < $count; $i++) {
                do {
                    $code = generateCode();
                } while (array_key_exists($code, $existing));
                $existing[$code] = 'code';
                $generated[] = $code;
            }
            file_put_contents($userFile, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
      ?>
      <?php if (!empty($generated)): ?>
        <div class="mt-4">
          <h3 class="font-semibold">產生的代碼：</h3>
          <ul class="list-disc list-inside text-sm mt-2">
            <?php foreach ($generated as $code): ?>
              <li><code><?php echo $code; ?></code></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
