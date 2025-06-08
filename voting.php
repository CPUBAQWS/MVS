<?php session_start(); if (!isset($_SESSION['user_code'])) { header('Location: index.html'); exit; } ?>
<?php
$ruleMap = [
  'single' => '單一票',
  'multi_unique' => '多票（不可重複）',
  'multi_repeat' => '多票（可重複）'
];

$categoryFile = __DIR__ . '/data/categories.json';
$categories = file_exists($categoryFile) ? json_decode(file_get_contents($categoryFile), true) : [];

$categoryMap = [];
foreach ($categories as $cat) {
    $categoryMap[$cat['folder']] = $cat;
}

$dirs = array_filter(glob(__DIR__ . '/Files/*'), 'is_dir');
$available = [];

foreach ($dirs as $dir) {
    $folderName = basename($dir);
    $catData = $categoryMap[$folderName] ?? [];
    if (isset($catData['enabled']) && !$catData['enabled']) {
        continue; // skip disabled categories
    }
    $available[] = [
        'folder' => $folderName,
        'name' => $catData['name'] ?? $folderName,
        'rule' => $catData['rule'] ?? $catData['voting_rule'] ?? 'N/A',
        'count' => count(array_filter(scandir($dir), function($f) use ($dir) {
            return is_file("$dir/$f") && !in_array($f, ['.', '..']);
        }))
    ];
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <title>投票頁面</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

  <header class="bg-blue-600 text-white p-4 text-center text-xl font-semibold">
    家族投票系統
  </header>

  <main class="max-w-4xl mx-auto p-6 space-y-8">
    <section>
      <h2 class="text-lg font-bold mb-4">可用投票分類</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($available as $cat): ?>
          <div class="bg-white rounded shadow p-4">
            <h3 class="text-md font-semibold text-blue-800"><?php echo htmlspecialchars($cat['name']); ?></h3>
            <p class="text-sm text-gray-600 mb-1">投稿數量: <?php echo $cat['count']; ?> 個</p>
            <p class="text-sm text-gray-600 mb-2">規則: <?= $ruleMap[$cat['rule']] ?? $cat['rule'] ?></p>
            <a href="category.php?category=<?php echo urlencode($cat['folder']); ?>" class="text-blue-600 hover:underline">進入投票</a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section>
      <button onclick="document.getElementById('myVotes').classList.toggle('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded">
        查看我的投票
      </button>
      <div id="myVotes" class="hidden mt-4 bg-white p-4 rounded shadow">
        <h3 class="text-md font-bold mb-2">我的投票</h3>
        <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
          <li><strong>攝影獎:</strong> Summer.jpg / Fish.jpg</li>
          <li><strong>散文:</strong> Travel Notes</li>
          <li><strong>創意:</strong> Felting Work</li>
        </ul>
      </div>
    </section>
  </main>
</body>
</html>
