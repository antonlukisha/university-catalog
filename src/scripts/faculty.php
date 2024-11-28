<?php
require_once '../includes/db_connect.php';

/**
 * Get faculties data by ID.
 *
 * @param PDO $pdo
 * @param int $faculty_id
 * @return array|null
 */
 function getFacultyById(PDO $pdo, int $faculty_id): ?array {
     $query = "SELECT * FROM faculties WHERE faculty_id = :faculty_id;";
     $stmt = $pdo->prepare($query);
     $stmt->execute(['faculty_id' => $faculty_id]);
     return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get group by faculty ID.
 *
 * @param PDO $pdo
 * @param int $faculty_id
 * @return array|null
 */
 function getGroups(PDO $pdo, int $faculty_id): ?array {
     $query = "SELECT group_id, group_name FROM study_groups WHERE faculty_id = :faculty_id;";
     $stmt = $pdo->prepare($query);
     $stmt->execute(['faculty_id' => $faculty_id]);
     return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*** Check faculty's ID validation ***/
if (!isset($_GET['faculty_id'])) {
    die("Не указан ID факультета.");
}

$faculty_id = intval($_GET['faculty_id']);

/*** Get faculty's data ***/
$faculty = getFacultyById($pdo, $faculty_id);

if (!$faculty) {
    die("Факультет не найден.");
}

/*** Get grops ***/
$groups = getGroups($pdo, $faculty_id);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Факультет: <?php echo htmlspecialchars($faculty['faculty_name']); ?></title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Информация о факультете</h1>
    <nav>
        <a href="faculties.php">Факультеты</a> |
        <a href="../index.php">Домашняя страница</a>
    </nav>
    <br>
    <table border="1">
        <tr>
            <th>ID Факультета</th>
            <td><?php echo htmlspecialchars($faculty['faculty_id']); ?></td>
        </tr>
        <tr>
            <th>Название факультета</th>
            <td><?php echo htmlspecialchars($faculty['faculty_name']); ?></td>
        </tr>
    </table>

    <h2>Группы факультета</h2>
    <table border="1">
        <tr>
            <th>ID Группы</th>
            <th>Название группы</th>
        </tr>
        <?php if (!empty($groups)): ?>
            <?php foreach ($groups as $group): ?>
                <tr>
                    <td><?php echo htmlspecialchars($group['group_id']); ?></td>
                    <td><a href="group.php?group_id=<?php echo urlencode($group['group_id']); ?>"><?php echo htmlspecialchars($group['group_name']); ?></a></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2">На факультете нет групп.</td>
            </tr>
        <?php endif; ?>
    </table>
    <br>
    <a href="faculties.php">Вернуться к списку факультетов</a>
</body>
</html>
