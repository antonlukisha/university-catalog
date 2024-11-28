<?php
require_once '../includes/db_connect.php';

/**
 * Clear db's data.
 *
 * @param PDO $pdo
 * @param array $tables
 * @return null
 */
function clearDB(PDO $pdo, $tables) {
  foreach ($tables as $table) {
      $stmt = $pdo->prepare("TRUNCATE TABLE $table;");
      $stmt->execute();
  }
}

/*** POST clear all data ***/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tables = ['students', 'study_groups', 'courses', 'faculties', 'leaders'];

    try {
      //Keys off (for correct truncate)
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

        clearDB($pdo, $tables);

        //Keys on
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
        echo "Таблицы успешно очищены.";
    } catch (PDOException $exception) {
        error_log("Clear error: " . $exception->getMessage());
        echo "Ошибка при очистке таблиц";
    }
} else {
    echo "Неверный метод доступа.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Очистка данных</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
  <br>
  <a href="../index.php">Вернуться на главную страницу</a>
</body>
</html>
