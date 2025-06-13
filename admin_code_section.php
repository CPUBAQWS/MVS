<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

function generateCode($length = 6) {
    return substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', $length)), 0, $length);
}

$generated = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $count = max(1, intval($_POST['count'] ?? 1));
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

<!-- Admin section for generating codes -->
<section class="bg-white p-4 rounded shadow mt-6 max-w-xl mx-auto">
  <h2 class="text-lg font-bold mb-2">Generate Access Codes</h2>
  <form method="POST">
    <label class="block mb-2">How many codes?</label>
    <input type="number" name="count" value="5" class="border p-2 rounded w-32 mb-4">
    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Generate</button>
  </form>

  <?php if (!empty($generated)): ?>
    <div class="mt-4">
      <h3 class="font-semibold">New Codes:</h3>
      <ul class="list-disc list-inside text-sm mt-2">
        <?php foreach ($generated as $code): ?>
          <li><code><?php echo $code; ?></code></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
</section>
