<?php
session_start();
require_once '../includes/db_connect.php';

/**
 * Get grop's data by ID.
 *
 * @param PDO $pdo
 * @param int $group_id
 * @return array|null
 */
function getGroupById(PDO $pdo, int $group_id): ?array {
    $query = "SELECT * FROM study_groups WHERE group_id = :group_id;";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['group_id' => $group_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

/**
 * Get faculty name by ID.
 *
 * @param PDO $pdo
 * @param int|null
 * @return string|null
 */
function getFacultyNameById(PDO $pdo, ?int $faculty_id): ?string {
    if (!$faculty_id) {
        return null;
    }
    $query = "SELECT faculty_name FROM faculties WHERE faculty_id = :faculty_id;";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['faculty_id' => $faculty_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['faculty_name'] ?? null;
}

/**
 * Get leader's of group data.
 *
 * @param PDO $pdo
 * @param int|null $leader_id
 * @return array|null
 */
function getLeader(PDO $pdo, ?int $leader_id): ?array {
    if (!$leader_id) {
        return null;
    }
    $query = "
      SELECT
        s.first_name,
        s.last_name,
        s.patronymic,
        l.student_id
      FROM leaders l
      INNER JOIN students s ON l.student_id = s.student_id
      WHERE l.leader_id = :leader_id;
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['leader_id' => $leader_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

/**
 * Get group's student.
 *
 * @param PDO $pdo
 * @param int $group_id
 * @return array
 */
function getStudentsByGroup(PDO $pdo, int $group_id): array {
    $query = "
      SELECT
        student_id,
        first_name,
        last_name,
        patronymic
      FROM students WHERE group_id = :group_id;
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['group_id' => $group_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*** Check group's ID ***/
if (!isset($_GET['group_id'])) {
    die("Не указан ID группы.");
}

$group_id = intval($_GET['group_id']);

/*** Get data ***/
$group = getGroupById($pdo, $group_id);
if (!$group) {
    die("Группа не найдена.");
}

$facultyName = getFacultyNameById($pdo, $group['faculty_id']);
$leaderInfo = getLeader($pdo, $group['leader_id']);
$students = getStudentsByGroup($pdo, $group_id);
/*** Get role***/
$role = $_SESSION['role'] ?: 'USER';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Группа: <?php echo htmlspecialchars($group['group_name']); ?></title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Информация о группе</h1>
    <nav>
        <a href="groups.php">Группы</a> |
        <a href="../index.php">Домашняя страница</a>
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
            <td><?php echo htmlspecialchars($facultyName ?? '—'); ?></td>
        </tr>
        <tr>
            <th>Староста группы</th>
            <td>
                <?php if ($leaderInfo): ?>
                    <a href="student.php?student_id=<?php echo urlencode($leaderInfo['student_id']); ?>">
                        <?php echo htmlspecialchars($leaderInfo['last_name'] . ' ' . $leaderInfo['first_name'] . ' ' . $leaderInfo['patronymic']); ?>
                    </a>
                <?php else: ?>
                    —
                <?php endif; ?>
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
                    <td>
                        <a href="student.php?student_id=<?php echo urlencode($student['student_id']); ?>">Просмотр</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">В группе нет студентов.</td>
            </tr>
        <?php endif; ?>
    </table>
    <?php if ($role === 'ADMIN' || $role === 'TEACHER'): ?>
      <br>
      <h2>Установить старосту группы</h2>
      <form action="set_leader.php" method="POST">
          <input type="hidden" name="group_id" value="<?php echo htmlspecialchars($group_id); ?>">
          <label>
            Выберите студента:
          </label>
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
    <?php endif; ?>
    <br>
    <a href="groups.php">Вернуться к списку групп</a>
</body>
</html>
