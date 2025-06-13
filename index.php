<?php
session_start();
require_once __DIR__.'/inc/i18n.php';
$langAttr = get_lang();
$languages = array_map(function($f){return basename($f,'.php');}, glob(__DIR__.'/lang/*.php'));
$langNames = ['en' => t('english'), 'zh' => t('chinese')];
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($langAttr); ?>">
<head>
  <meta charset="UTF-8" />
  <title><?php echo t('login_title'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <div class="flex flex-col items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow max-w-md w-full space-y-6">
      <div class="text-right">
        <label class="mr-2"><?php echo t('language'); ?></label>
        <select id="langSelect" class="border p-1 rounded">
          <?php foreach ($languages as $code): ?>
            <option value="<?php echo $code; ?>" <?php echo $code === $langAttr ? 'selected' : ''; ?>><?php echo htmlspecialchars($langNames[$code] ?? $code); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <h1 class="text-xl font-bold mb-2 text-center"><?php echo t('user_login'); ?></h1>
        <form id="userForm">
          <input type="text" name="code" placeholder="<?php echo t('enter_access_code'); ?>" required class="w-full border p-2 mb-4 rounded">
          <button type="submit" class="bg-green-600 text-white px-4 py-2 w-full rounded"><?php echo t('start_voting'); ?></button>
        </form>
      </div>
      <hr class="my-4 border-gray-300">
      <details class="border rounded">
        <summary class="text-xl font-bold mb-2 text-center py-2 cursor-pointer select-none"><?php echo t('admin_login'); ?></summary>
        <div class="p-2">
          <form id="adminForm">
            <input type="text" name="username" placeholder="<?php echo t('admin_username'); ?>" required class="w-full border p-2 mb-2 rounded">
            <input type="password" name="password" placeholder="<?php echo t('password'); ?>" required class="w-full border p-2 mb-4 rounded">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 w-full rounded"><?php echo t('login_admin'); ?></button>
          </form>
        </div>
      </details>
      <p id="status" class="mt-2 text-center text-sm text-red-600"></p>
    </div>
  </div>

  <script>
    document.getElementById('adminForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(e.target);
      fetch('auth.php', {
        method: 'POST',
        body: formData,
      })
      .then(res => res.text())
      .then(text => {
        if (text === 'admin') {
          window.location.href = 'admin.php';
        } else {
          document.getElementById('status').textContent = <?php echo json_encode(t('admin_error')); ?>;
        }
      });
    });

    document.getElementById('userForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(e.target);
      fetch('auth.php', {
        method: 'POST',
        body: formData,
      })
      .then(res => res.text())
      .then(text => {
        if (text !== 'invalid' && text !== 'admin') {
          window.location.href = 'voting.php';
        } else {
          document.getElementById('status').textContent = <?php echo json_encode(t('code_error')); ?>;
        }
      });
    });

    document.getElementById('langSelect').addEventListener('change', function(e) {
      fetch('set_lang.php?lang=' + encodeURIComponent(e.target.value))
        .then(() => location.reload());
    });
  </script>
</body>
</html>
