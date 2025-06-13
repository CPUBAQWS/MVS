<?php
session_start();
require_once __DIR__ . '/inc/i18n.php';
$ruleMap = [
  'single' => t('one_vote'),
  'multi_unique' => t('multi_unique')
];
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

$categoryFile = __DIR__ . '/data/categories.json';
$categories = file_exists($categoryFile) ? json_decode(file_get_contents($categoryFile), true) : [];
$langAttr = get_lang();
$languages = array_map(function($f){return basename($f,'.php');}, glob(__DIR__.'/lang/*.php'));
$langNames = ['en' => t('english'), 'zh' => t('chinese')];
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($langAttr); ?>">
<head>
  <meta charset="UTF-8" />
  <title><?php echo t('admin_dashboard'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <header class="bg-blue-700 text-white p-4 text-center text-xl font-semibold">
    <?php echo t('admin_interface'); ?>
  </header>
  <nav class="max-w-6xl mx-auto mt-4 px-4 flex justify-between items-center">
    <a href="index.php" class="text-blue-600 hover:underline">&larr; <?php echo t('back_home'); ?></a>
    <div>
      <label class="mr-2"><?php echo t('language'); ?></label>
      <select id="langSelect" class="border p-1 rounded">
        <?php foreach ($languages as $code): ?>
          <option value="<?php echo $code; ?>" <?php echo $code === $langAttr ? 'selected' : ''; ?>><?php echo htmlspecialchars($langNames[$code] ?? $code); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </nav>
  <main class="max-w-6xl mx-auto p-6 space-y-8">

    <section class="bg-white p-4 rounded shadow">
      <h2 class="text-lg font-bold mb-4"><?php echo t('existing_categories'); ?></h2>
      <table class="w-full table-auto border-collapse">
        <thead>
          <tr class="bg-gray-200">
            <th class="px-4 py-2 text-left"><?php echo t('display_name'); ?></th>
            <th class="px-4 py-2 text-left"><?php echo t('folder'); ?></th>
            <th class="px-4 py-2 text-center"><?php echo t('file_count'); ?></th>
            <th class="px-4 py-2 text-left"><?php echo t('voting_rule'); ?></th>
            <th class="px-4 py-2 text-center"><?php echo t('max_votes'); ?></th>
            <th class="px-4 py-2 text-center"><?php echo t('voting_status'); ?></th>
            <th class="px-4 py-2 text-left"><?php echo t('rename'); ?></th>
            <th class="px-4 py-2 text-center"><?php echo t('delete'); ?></th>
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
            <td class="px-4 py-2">
              <?php echo htmlspecialchars($ruleMap[$cat['rule']] ?? $cat['rule']); ?>
            </td>
            <td class="px-4 py-2 text-center">
              <?php
                if ($cat['rule'] === 'single') {
                    echo t('one_vote');
                } elseif ($cat['rule'] === 'multi_unique' && isset($cat['max_votes'])) {
                    echo sprintf(t('max_votes_format'), intval($cat['max_votes']));
                } else {
                    echo '—';
                }
              ?>
            </td>
            <td class="px-4 py-2 text-center">
              <?php $allow = $cat['allow_vote'] ?? true; ?>
              <div class="space-y-1">
                <div class="text-sm"><?php echo $allow ? t('voting_enabled') : t('view_only'); ?></div>
                <form action="toggle_vote.php" method="POST">
                  <input type="hidden" name="folder" value="<?php echo htmlspecialchars($cat['folder']); ?>">
                  <button type="submit" class="bg-blue-500 text-white text-xs px-2 py-1 rounded"><?php echo t('toggle'); ?></button>
                </form>
              </div>
            </td>
            <td class="px-4 py-2 text-center">
              <form action="rename_category.php" method="POST" class="flex items-center space-x-2">
                <input type="hidden" name="folder" value="<?php echo htmlspecialchars($cat['folder']); ?>">
                <input type="text" name="new_name" placeholder="<?php echo t('new_category_name'); ?>" class="border p-1 rounded text-sm" required>
                <button type="submit" class="bg-yellow-500 text-white text-sm px-2 py-1 rounded"><?php echo t('rename'); ?></button>
              </form>
            </td>
            <td class="px-4 py-2 text-center">
              <form action="delete_category.php" method="POST" onsubmit="return confirm('<?php echo t('delete'); ?>?');">
                <input type="hidden" name="folder" value="<?php echo htmlspecialchars($cat['folder']); ?>">
                <button type="submit" class="bg-red-500 text-white text-sm px-3 py-1 rounded"><?php echo t('delete'); ?></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <section class="bg-white p-4 rounded shadow mt-6 max-w-xl mx-auto">
      <h2 class="text-lg font-bold mb-2"><?php echo t('add_category'); ?></h2>
      <form method="POST" action="create_category_folder.php">
        <div class="mb-2">
          <label><?php echo t('category_name'); ?></label>
          <input type="text" name="name" class="border p-2 rounded w-full" required>
        </div>
        <div class="mb-2">
          <label><?php echo t('select_rule'); ?></label>
          <select name="rule" id="ruleSelect" class="border p-2 rounded w-full" required>
            <option value="single"><?php echo t('one_vote'); ?></option>
            <option value="multi_unique"><?php echo t('multi_unique'); ?></option>
          </select>
        </div>
        <div class="mb-2" id="maxVotesWrapper" style="display:none;">
          <label><?php echo t('max_votes_label'); ?></label>
          <input type="number" name="max_votes" id="maxVotes" value="3" min="1" class="border p-2 rounded w-full">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded"><?php echo t('create_category'); ?></button>
      </form>
    </section>

    <section class="bg-white p-4 rounded shadow mt-6 max-w-xl mx-auto">
      <h2 class="text-lg font-bold mb-2"><?php echo t('generate_codes'); ?></h2>
      <form method="POST">
        <label class="block mb-2"><?php echo t('quantity'); ?></label>
        <input type="number" name="count" value="5" class="border p-2 rounded w-32 mb-4">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded"><?php echo t('generate'); ?></button>
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
          <h3 class="font-semibold"><?php echo t('generated_codes'); ?></h3>
          <ul class="list-disc list-inside text-sm mt-2">
            <?php foreach ($generated as $code): ?>
              <li><code><?php echo $code; ?></code></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </section>

    <section class="bg-white p-4 rounded shadow mt-6 max-w-xl mx-auto">
      <h2 class="text-lg font-bold mb-2"><?php echo t('download_report'); ?></h2>
      <p class="text-sm text-gray-600 mb-4"><?php echo t('download_report_desc'); ?></p>
      <a href="download_report.php" class="bg-blue-600 text-white px-4 py-2 rounded"><?php echo t('download'); ?></a>
    </section>
  </main>
  <script>
    const ruleSelect = document.getElementById('ruleSelect');
    const maxWrapper = document.getElementById('maxVotesWrapper');
    function toggleMaxField() {
      maxWrapper.style.display = ruleSelect.value === 'multi_unique' ? 'block' : 'none';
    }
    ruleSelect.addEventListener('change', toggleMaxField);
    toggleMaxField();

    document.getElementById('langSelect').addEventListener('change', function(e) {
      fetch('set_lang.php?lang=' + encodeURIComponent(e.target.value))
        .then(() => location.reload());
    });
  </script>
</body>
</html>
