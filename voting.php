<?php
session_start();
if (!isset($_SESSION['user_code'])) { header('Location: index.php'); exit; }
require_once __DIR__ . '/inc/i18n.php';
$ruleMap = [
  'single' => t('one_vote'),
  'multi_unique' => t('multi_unique')
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
        'max_votes' => isset($catData['max_votes']) ? intval($catData['max_votes']) : null,
        'allow_vote' => $catData['allow_vote'] ?? true,
        'count' => count(array_filter(scandir($dir), function($f) use ($dir) {
            return is_file("$dir/$f") && !in_array($f, ['.', '..']);
        }))
    ];
}
$langAttr = get_lang();
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($langAttr); ?>">
<head>
  <meta charset="UTF-8">
  <title><?php echo t('voting_page_title'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

  <header class="bg-blue-600 text-white p-4 text-center text-xl font-semibold">
    <?php echo t('family_voting_system'); ?>
  </header>
  <nav class="max-w-4xl mx-auto mt-4 px-4">
    <a href="index.php" class="text-blue-600 hover:underline">&larr; <?php echo t('back_home'); ?></a>
  </nav>

  <main class="max-w-4xl mx-auto p-6 space-y-8">
    <section>
      <h2 class="text-lg font-bold mb-4"><?php echo t('available_categories'); ?></h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($available as $cat): ?>
          <div class="bg-white rounded shadow p-4">
            <h3 class="text-md font-semibold text-black"><?php echo htmlspecialchars($cat['name']); ?></h3>
            <p class="text-sm text-gray-600 mb-1"><?php echo sprintf(t('submissions_count'), $cat['count']); ?></p>
            <?php
              $ruleLabel = $ruleMap[$cat['rule']] ?? $cat['rule'];
              if ($cat['rule'] === 'multi_unique' && isset($cat['max_votes'])) {
                  $ruleLabel .= ' (' . sprintf(t('max_votes_format'), intval($cat['max_votes'])) . ')';
              }
            ?>
            <p class="text-sm text-gray-600 mb-2"><?php echo sprintf(t('rule_label'), htmlspecialchars($ruleLabel)); ?></p>
            <p class="text-xs mb-2 <?php echo $cat['allow_vote'] ? 'text-green-600' : 'text-red-600'; ?>">
              <?php echo $cat['allow_vote'] ? t('voting_enabled') : t('view_only'); ?>
            </p>
            <a href="category.php?category=<?php echo urlencode($cat['folder']); ?>" class="text-blue-600 font-bold hover:underline"><?php echo t('enter_voting'); ?></a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

  </main>
</body>
</html>
