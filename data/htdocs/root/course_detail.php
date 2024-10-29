<?php
require_once 'includes/db_connect.php';

if (!isset($_GET['course_id'])) {
    die("Не указан ID факультатива.");
}

$course_id = intval($_GET['course_id']);

$Q = "
    SELECT c.*, f.faculty_name
    FROM courses c
    JOIN faculties f ON c.faculty_id = f.faculty_id
    WHERE c.course_id = :course_id;
";
$stmt = $pdo->prepare($Q);
$stmt->execute(['course_id' => $course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die("Факультатив не найден.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($course['course_name']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Факультатив: <?php echo htmlspecialchars($course['course_name']); ?></h1>
    <nav>
        <a href="courses.php">Вернуться к списку факультативов</a> |
        <a href="index.php">Домашняя страница</a>
    </nav>
    <br>

    <table border="1">
        <tr>
            <th>ID Факультатива</th>
            <td><?php echo htmlspecialchars($course['course_id']); ?></td>
        </tr>
        <tr>
            <th>Название</th>
            <td><?php echo htmlspecialchars($course['course_name']); ?></td>
        </tr>
        <tr>
            <th>Описание</th>
            <td><?php echo htmlspecialchars($course['description']); ?></td>
        </tr>
        <tr>
            <th>Вместимость</th>
            <td><?php echo htmlspecialchars($course['capacity']); ?></td>
        </tr>
        <tr>
            <th>Факультет</th>
            <td><?php echo htmlspecialchars($course['faculty_name']); ?></td>
        </tr>
    </table>
    <br>
    <a href="courses.php">Вернуться к списку факультативов</a>
</body>
</html>
