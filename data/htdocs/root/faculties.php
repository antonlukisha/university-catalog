<?php
require_once 'includes/db_connect.php';

$Q = "SELECT * FROM faculties;";
$stmt = $pdo->prepare($Q);
$stmt->execute();
$faculties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Факультеты</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Список факультетов</h1>
    <nav>
        <a href="index.php">Домашняя страница</a>
    </nav>
    <br>
    <table border="1">
        <tr>
            <th>ID Факультета</th>
            <th>Название факультета</th>
        </tr>
        <?php foreach ($faculties as $faculty): ?>
            <tr>
                <td><?php echo htmlspecialchars($faculty['faculty_id']); ?></td>
                <td><a href="faculty.php?faculty_id=<?php echo urlencode($faculty['faculty_id']); ?>"><?php echo htmlspecialchars($faculty['faculty_name']); ?></a></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <a href="index.php">Вернуться на домашнюю страницу</a>
</body>
</html>
