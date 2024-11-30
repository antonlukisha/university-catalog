<?php
session_start();
require_once '../includes/db_connect.php';

/**
 * Get group's data with faculty and leader.
 *
 * @param PDO $pdo
 * @return array|null
 */
function getGroups(PDO $pdo): ?array {
    $query = "
        SELECT
            g.group_id,
            g.group_name,
            f.faculty_name,
            CONCAT(s.last_name, ' ', s.first_name) AS leader_name
        FROM study_groups g
        LEFT JOIN faculties f ON g.faculty_id = f.faculty_id
        LEFT JOIN leaders l ON g.leader_id = l.leader_id
        LEFT JOIN students s ON l.student_id = s.student_id;
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*** Get all groups data***/
$groups = getGroups($pdo);
/*** Get role***/
$role = $_SESSION['role'] ?: 'USER';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Группы</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Все группы</h1>
    <nav>
        <a href="../index.php">Домашняя страница</a>
        <?php if ($role !== 'USER'): ?> |
         <a href="students.php">Студенты</a>
        <?php endif; ?>
    </nav>
    <br>
      <table border="1">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Название группы</th>
                  <th>Факультет</th>
                  <?php if ($role !== 'USER'): ?>
                    <th>Староста</th>
                    <th>Действия</th>
                  <?php endif; ?>
              </tr>
          </thead>
          <tbody>
            <?php foreach ($groups as $group): ?>
              <tr>
                  <td><?php echo htmlspecialchars($group['group_id']); ?></td>
                  <td><?php echo htmlspecialchars($group['group_name']); ?></td>
                  <td><?php echo htmlspecialchars($group['faculty_name']); ?></td>
                  <?php if ($role !== 'USER'): ?>
                  <td><?php echo htmlspecialchars($group['leader_name'] ?? '—'); ?></td>
                    <td>
                        <a href="group.php?group_id=<?php echo urlencode($group['group_id']); ?>">
                            Просмотр
                        </a>
                    </td>
                  <?php endif; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
      </table>
    <br>
    <a href="../index.php">Вернуться на домашнюю страницу</a>
</body>
</html>
