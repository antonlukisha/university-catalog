<?php
require_once 'includes/db_connect.php';

if (!isset($_GET['group_id'])) {
    die("Не указан ID группы.");
}

$group_id = intval($_GET['group_id']);

$Q = "SELECT * FROM study_groups WHERE group_id = :group_id;";
$stmt = $pdo->prepare($Q);
$stmt->execute(['group_id' => $group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    die("Группа не найдена.");
}

$Q_faculty = "SELECT faculty_name FROM faculties WHERE faculty_id = :faculty_id;";
$stmt_faculty = $pdo->prepare($Q_faculty);
$stmt_faculty->execute(['faculty_id' => $group['faculty_id']]);
$faculty = $stmt_faculty->fetch(PDO::FETCH_ASSOC);

$leader_info = null;
if ($group['leader_id']) {
    $Q_leader = "SELECT student_id FROM leaders WHERE leader_id = :leader_id;";
    $stmt_leader = $pdo->prepare($Q_leader);
    $stmt_leader->execute(['leader_id' => $group['leader_id']]);
    $leader = $stmt_leader->fetch(PDO::FETCH_ASSOC);

    if ($leader) {
        $Q_student_name = "SELECT first_name, last_name, patronymic FROM students WHERE student_id = :student_id;";
        $stmt_student_name = $pdo->prepare($Q_student_name);
        $stmt_student_name->execute(['student_id' => $leader['student_id']]);
        $leader_info = $stmt_student_name->fetch(PDO::FETCH_ASSOC);
    }
}


$Q_students = "SELECT student_id, first_name, last_name, patronymic FROM students WHERE group_id = :group_id;";
$stmt_students = $pdo->prepare($Q_students);
$stmt_students->execute(['group_id' => $group_id]);
$students = $stmt_students->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Группа: <?php echo htmlspecialchars($group['group_name']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Информация о группе</h1>
    <nav>
        <a href="groups.php">Группы</a> | 
        <a href="index.php">Домашняя страница</a>
    </nav>
    <br>
    <table border="1">
        <tr>
            <th>ID Группы</th>
            <td><?php echo htmlspecialchars($group['group_id']); ?></td>
        </tr>
        <tr>
            <th>Название группы</th>
            <td><?php echo htmlspecialchars($group['group_name']); ?></td>
        </tr>
        <tr>
            <th>Факультет</th>
            <td>
                <?php 
                if ($faculty) {
                    echo htmlspecialchars($faculty['faculty_name']);
                } else {
                    echo '—';
                }
                ?>
            </td>
        </tr>
        <tr>
    <th>Староста группы</th>
        <td>
            <?php 
            if ($leader_info) {
                echo "<a href='student.php?student_id=" . urlencode($leader['student_id']) . "'>" . htmlspecialchars($leader_info['last_name'] . ' ' . $leader_info['first_name'] . ' ' . $leader_info['patronymic']) . "</a>";
            } else {
                echo '—';
            }
            ?>
        </td>
    </tr>

    </table>
    
    <h2>Список студентов</h2>
    <table border="1">
        <tr>
            <th>ID Студента</th>
            <th>Фамилия</th>
            <th>Имя</th>
            <th>Отчество</th>
            <th>Действия</th>
        </tr>
        <?php if (!empty($students)): ?>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                    <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['patronymic']); ?></td>
                    <td><a href="student.php?student_id=<?php echo urlencode($student['student_id']); ?>">Просмотр</a></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">В группе нет студентов.</td>
            </tr>
        <?php endif; ?>
    </table>
    
    <br>
    <h2>Установить старосту группы</h2>
    <form action="set_leader.php" method="POST">
        <input type="hidden" name="group_id" value="<?php echo htmlspecialchars($group_id); ?>">
        <label>Выберите студента:</label>
        <select name="leader_id" required>
            <option value="">Нет старосты</option>
            <?php foreach ($students as $student): ?>
                <option value="<?php echo $student['student_id']; ?>" <?php echo ($student['student_id'] == $group['leader_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($student['last_name'] . ' ' . $student['first_name'] . ' ' . $student['patronymic']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Сделать старостой">
    </form>

    <br>

    <a href="groups.php">Вернуться к списку групп</a>
</body>
</html>
