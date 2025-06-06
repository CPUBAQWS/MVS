<?php
$catFile = __DIR__ . "/data/categories.json";
$categories = [];

if (file_exists($catFile)) {
  $categories = json_decode(file_get_contents($catFile), true) ?? [];
}

function getRuleLabel($rule) {
  switch ($rule) {
    case "1": return "每帳號僅限一票";
    case "2": return "多票，每項目只能投一次";
    case "3": return "多票，可對同項目多次投票";
    default: return "未知";
  }
}

echo '<div class="overflow-x-auto mt-6">';
echo '<table class="min-w-full text-sm border border-gray-300">';
echo '<thead class="bg-gray-200"><tr>';
echo '<th class="px-4 py-2 border">類別名稱</th>';
echo '<th class="px-4 py-2 border">資料夾</th>';
echo '<th class="px-4 py-2 border">檔案數</th>';
echo '<th class="px-4 py-2 border">投票規則</th>';
echo '</tr></thead><tbody>';

foreach ($categories as $cat) {
  $folderPath = __DIR__ . "/Files/" . $cat["folder"];
  $fileCount = 0;

  if (is_dir($folderPath)) {
    $files = array_diff(scandir($folderPath), ['.', '..']);
    $fileCount = count($files);
  }

  echo "<tr>";
  echo "<td class='border px-4 py-2'>" . htmlspecialchars($cat["name"]) . "</td>";
  echo "<td class='border px-4 py-2'>" . htmlspecialchars($cat["folder"]) . "</td>";
  echo "<td class='border px-4 py-2 text-center'>" . $fileCount . "</td>";
  echo "<td class='border px-4 py-2'>" . getRuleLabel($cat["rule"]) . "</td>";
  echo "</tr>";
}

echo '</tbody></table></div>';
?>
