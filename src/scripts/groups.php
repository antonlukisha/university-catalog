<?php
require_once '../includes/db_connect.php';

/**
 * Get group's data with faculty and leader.
 *
 * @param PDO $pdo
 * @return array
 */
function getGroups(PDO $pdo): array {
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
    </nav>
    <br>
    <?php if (!empty($groups)): ?>
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
            <?php foreach ($groups as $group): ?>
              <tr>
                  <td><?php echo htmlspecialchars($group['group_id']); ?></td>
                  <td><?php echo htmlspecialchars($group['group_name']); ?></td>
                  <td><?php echo htmlspecialchars($group['faculty_name']); ?></td>
                  <td><?php echo htmlspecialchars($group['leader_name'] ?? '—'); ?></td>
                  <td>
                      <a href="group.php?group_id=<?php echo urlencode($group['group_id']); ?>">
                          Просмотр
                      </a>
                  </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
      </table>
    <?php else: ?>
        <p>Список групп пуст.</p>
    <?php endif; ?>
    <br>
    <a href="../index.php">Вернуться на домашнюю страницу</a>
</body>
</html>
