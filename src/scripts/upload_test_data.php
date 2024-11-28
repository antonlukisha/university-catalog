<?php
require_once '../includes/db_connect.php';

/**
 * Upload test data to db.
 *
 * @param PDO $pdo
 * @param array $queries
 * @return null
 */
function uploadToDB(PDO $pdo, $queries) {
  foreach ($queries as $query) {
    if (!empty($query)) {
      $pdo->exec($query);
    }
  }
}

/*** POST upload test data ***/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $file = '../test/data_set.sql';

    if (file_exists($file)) {
      // Read file and -> queries
      $sqlContent = file_get_contents($file);
      $queries = array_filter(array_map('trim', explode(';', $sqlContent)));
      try {
          uploadToDB($pdo, $queries);
          echo "Тестовые данные успешно добавлены.";
      } catch (PDOException $exception) {
          error_log("Upload error: " . $exception->getMessage());
          echo "Ошибка при загрузке тестовых данных.";
      }
    } else {
        echo "Не удалось загрузить файл.";
    }
} else {
    echo "Ошибка доступа.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Загрузка тестовых данных</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <br>
  <a href="../index.php">Вернуться на главную страницу</a>
</body>
</html>
