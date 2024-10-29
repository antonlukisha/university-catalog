<?php
require_once 'includes/db_connect.php';

if (!isset($_GET['student_id'])) {
    die("Не указан ID студента.");
}

$student_id = intval($_GET['student_id']);

$Q = "SELECT * FROM students WHERE student_id = :student_id;";
$stmt = $pdo->prepare($Q);
$stmt->execute(['student_id' => $student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Студент не найден.");
}

$Q_group = "SELECT group_name FROM study_groups WHERE group_id = :group_id;";
$stmt_group = $pdo->prepare($Q_group);
$stmt_group->execute(['group_id' => $student['group_id']]);
$group = $stmt_group->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Студент: <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Листок студента</h1>
    <nav>
        <a href="students.php">Студенты</a> | 
        <a href="index.php">Домашняя страница</a>
    </nav>
    <br>
    <table border="1">
        <tr>
            <th>ID</th>
            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
        </tr>
        <tr>
            <th>Фамилия</th>
            <td><?php echo htmlspecialchars($student['last_name']); ?></td>
        </tr>
        <tr>
            <th>Имя</th>
            <td><?php echo htmlspecialchars($student['first_name']); ?></td>
        </tr>
        <tr>
            <th>Отчество</th>
            <td><?php echo htmlspecialchars($student['patronymic'] ?? '—'); ?></td>
        </tr>
        <tr>
            <th>Место рождения</th>
            <td><?php echo htmlspecialchars($student['birth_place'] ?? '—'); ?></td>
        </tr>
        <tr>
            <th>Дата рождения</th>
            <td><?php 
                if ($student['birth_date']) {
                    $months = [
                        1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
                        5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
                        9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
                    ];

                    $birthDate = DateTime::createFromFormat('Y-m-d', $student['birth_date']);
                    $day = $birthDate->format('d');
                    $month = $birthDate->format('n');
                    $year = $birthDate->format('Y');

                    echo "{$day} {$months[$month]} {$year}";
                } else {
                    echo '—';
                }
        ?></td>
        </tr>
        <tr>
            <th>Телефон</th>
            <td><?php echo htmlspecialchars($student['phone'] ?? '—'); ?></td>
        </tr>
        <tr>
            <th>Средний балл</th>
            <td><?php echo htmlspecialchars($student['average_grade'] ?? '—'); ?></td>
        </tr>
        <tr>
            <th>Группа</th>
            <td>
                <?php 
                if ($group) {
                    echo "<a href='group.php?group_id=" . urlencode($student['group_id']) . "'>" . htmlspecialchars($group['group_name']) . "</a>";
                } else {
                    echo '—';
                }
                ?>
            </td>
        </tr>
    </table>
    <br>
    <a href="students.php">Вернуться к списку студентов</a>
</body>
</html>
