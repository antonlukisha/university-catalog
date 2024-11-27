<?php
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tables = ['students', 'study_groups', 'courses', 'faculties', 'leaders'];
    $errors = [];

    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        foreach ($tables as $table) {
            $stmt = $pdo->prepare("TRUNCATE TABLE $table;");
            $stmt->execute();
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
        echo "Таблицы успешно очищены.";
    } catch (PDOException $exception) {
        $errors[] = "Ошибка при очистке таблицы: " . $exception->getMessage();
    }

    if (!empty($errors)) {
        echo "<p>Возникли ошибки:</p><ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
    }
} else {
    echo "Неверный метод доступа.";
}
?>
<br>
<a href="../index.php">Вернуться на главную страницу</a>
