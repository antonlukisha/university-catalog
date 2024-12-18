<?php
session_start();
require_once '../includes/db_connect.php';

/**
 * Get all faculties.
 *
 * @param PDO $pdo
 * @return array|null
 */
 function getAllFaculties(PDO $pdo): ?array {
     $query = "SELECT * FROM faculties;";
     $stmt = $pdo->prepare($query);
     $stmt->execute();
     return $stmt->fetchAll(PDO::FETCH_ASSOC);
 }

 /*** Get all faculties data***/
$faculties = getAllFaculties($pdo);
 /*** Get role***/
 $role = $_SESSION['role'] ?: 'USER';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Факультеты</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Список факультетов</h1>
    <nav>
        <a href="../index.php">Домашняя страница</a>
        <?php if ($role !== 'USER'): ?> |
         <a href="students.php">Студенты</a> |
         <a href="groups.php">Группы</a>
        <?php endif; ?>
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
                <td>
                  <?php if ($role !== 'USER'): ?>
                    <a href="faculty.php?faculty_id=<?php echo urlencode($faculty['faculty_id']); ?>">
                      <?php echo htmlspecialchars($faculty['faculty_name']); ?>
                    </a>
                  <?php else: ?>
                    <?php echo htmlspecialchars($faculty['faculty_name']); ?>
                  <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <a href="../index.php">Вернуться на домашнюю страницу</a>
</body>
</html>
