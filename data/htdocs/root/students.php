<?php
require_once 'includes/db_connect.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Студенты</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Все студенты</h1>
    <nav>
        <a href="index.php">Домашняя страница</a>
    </nav>
    <br>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Отчество</th>
                <th>Данные</th>
                <th>Редактирование</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $Q = "SELECT student_id, last_name, first_name, patronymic FROM students;";
            $stmt = $pdo->prepare($Q);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['patronymic']) . "</td>";
                echo "<td><a href='student.php?student_id=" . urlencode($row['student_id']) . "'>Просмотр</a></td>";
                echo "<td><a href='edit_student.php?student_id=" . urlencode($row['student_id']) . "'>Изменить</a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <br>
    <a href="input_student.php">Добавить нового студента</a>

    <a href="index.php">Вернуться на домашнюю страницу</a>
</body>
</html>
