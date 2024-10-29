<?php
require_once 'includes/db_connect.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Группы</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Все группы</h1>
    <nav>
        <a href="index.php">Домашняя страница</a>
    </nav>
    <br>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название группы</th>
                <th>Факультет</th>
                <th>Староста</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $Q = "SELECT g.group_id, g.group_name, f.faculty_name, CONCAT(s.last_name, ' ', s.first_name) AS leader_name 
                  FROM study_groups g
                  LEFT JOIN faculties f ON g.faculty_id = f.faculty_id
                  LEFT JOIN leaders l ON g.leader_id = l.leader_id
                  LEFT JOIN students s ON l.student_id = s.student_id;";
            $stmt = $pdo->prepare($Q);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['group_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['group_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['faculty_name'] ?? '—') . "</td>";
                echo "<td>" . htmlspecialchars($row['leader_name'] ?? '—') . "</td>";
                echo "<td><a href='group.php?group_id=" . urlencode($row['group_id']) . "'>Просмотр</a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <br>
    <a href="index.php">Вернуться на домашнюю страницу</a>
</body>
</html>
