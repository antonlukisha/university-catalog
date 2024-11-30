<?php
session_start();
require_once '../includes/db_connect.php';

/**
 * Get students data by ID.
 *
 * @param PDO $pdo
 * @param int $student_id
 * @return array|null
 */
function getStudentById(PDO $pdo, int $student_id): ?array
{
    $query = "SELECT * FROM students WHERE student_id = :student_id;";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['student_id' => $student_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


/**
 * Get courses by student ID.
 *
 * @param PDO $pdo
 * @param int $student_id
 * @return array|null
 */
function getCourseByStudentID(PDO $pdo, int $student_id): ?array {
  $query = "
      SELECT c.course_id, c.course_name
      FROM courses c
      JOIN student_courses sc ON c.course_id = sc.course_id
      WHERE sc.student_id = :student_id;
  ";
  $stmt = $pdo->prepare($query);
  $stmt->execute(['student_id' => $student_id]);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Remove course from student.
 *
 * @param PDO $pdo
 * @param int $student_id
 * @param int $course_id
 * @return bool
 */
function removeCourse(PDO $pdo, int $student_id, int $course_id): bool {
    $query = "DELETE FROM student_courses WHERE student_id = :student_id AND course_id = :course_id;";
    $stmt = $pdo->prepare($query);
    return $stmt->execute(['student_id' => $student_id, 'course_id' => $course_id]);
}

/**
 * Get group name by ID.
 *
 * @param PDO $pdo
 * @param int $group_id
 * @return string|null
 */
function getGroupNameById(PDO $pdo, int $group_id): ?string
{
    $query = "SELECT group_name FROM study_groups WHERE group_id = :group_id;";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['group_id' => $group_id]);
    $group = $stmt->fetch(PDO::FETCH_ASSOC);
    return $group['group_name'] ?? null;
}

/*** Check student's ID validation ***/
if (!isset($_GET['student_id']) || !ctype_digit($_GET['student_id'])) {
    die("Некоректный ID студента.");
}

$student_id = intval($_GET['student_id']);

/*** Get student's data ***/
$student = getStudentById($pdo, $student_id);
if (!$student) {
    die("Студент не найден.");
}

/*** Removing a course ***/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $course_id = intval($_POST['course_id']);
    removeCourse($pdo, $student_id, $course_id);
}

$courses = getCourseByStudentID($pdo, $student_id);

/*** Get student's group ***/
$group_name = $student['group_id'] ? getGroupNameById($pdo, $student['group_id']) : null;
/*** Get role***/
$role = $_SESSION['role'] ?: 'USER';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Студент: <?php echo htmlspecialchars("{$student['first_name']} {$student['last_name']}"); ?></title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Листок студента</h1>
    <nav>
        <a href="students.php">Студенты</a> |
        <a href="../index.php">Домашняя страница</a>
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
            <td>
                <?php
                if ($student['birth_date']) {
                    $months = [
                        1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
                        5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
                        9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
                    ];
                    $birthDate = DateTime::createFromFormat('Y-m-d', $student['birth_date']);
                    echo sprintf(
                        '%d %s %d',
                        $birthDate->format('d'),
                        $months[$birthDate->format('n')],
                        $birthDate->format('Y')
                    );
                } else {
                    echo '—';
                }
                ?>
            </td>
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
                <?php if ($group_name): ?>
                    <a href="group.php?group_id=<?php echo urlencode($student['group_id']); ?>">
                        <?php echo htmlspecialchars($group_name); ?>
                    </a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>
    </table>
    <br>
    <?php if ($role === 'STUDENT' || $role === 'ADMIN'): ?>
      <h2>Факультативы</h2>
      <table border="1">
          <tr>
              <th>Название</th>
              <th>Действия</th>
          </tr>
          <?php foreach ($courses as $course): ?>
              <tr>
                  <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                  <td>
                    <form method="post" style="background: rgba(0, 0, 0, 0); box-shadow: none; font-size: 16px; padding: 5px 10px;">
                        <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                        <button type="submit" name="action" value="remove" style="font-size: 16px; padding: 5px 10px;">Удалить</button>
                    </form>
                  </td>
              </tr>
          <?php endforeach; ?>
      </table>
    <?php endif; ?>
    <br>
    <a href="students.php">Вернуться к списку студентов</a>
    <?php if ($role === 'STUDENT' || $role === 'ADMIN'): ?>
      <a href="edit_student.php?student_id=<?php echo urlencode($student['student_id']); ?>">
        Изменить данные
      </a>
    <?php endif; ?>
</body>
</html>
