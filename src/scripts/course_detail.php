<?php
session_start();
require_once '../includes/db_connect.php';

/**
 * Get course data by ID.
 *
 * @param PDO $pdo
 * @param int $course_id
 * @return array|null
 */
function getCourseById(PDO $pdo, int $course_id): ?array {
    $query = "
        SELECT c.*, f.faculty_name
        FROM courses c
        JOIN faculties f ON c.faculty_id = f.faculty_id
        WHERE c.course_id = :course_id;
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['course_id' => $course_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Remove student from course.
 *
 * @param PDO $pdo
 * @param int $student_id
 * @param int $course_id
 * @return bool
 */
function removeStudent(PDO $pdo, int $student_id, int $course_id): bool {
    $query = "DELETE FROM student_courses WHERE student_id = :student_id AND course_id = :course_id;";
    $stmt = $pdo->prepare($query);
    return $stmt->execute(['student_id' => $student_id, 'course_id' => $course_id]);
}

/**
 * Get students by course ID.
 *
 * @param PDO $pdo
 * @param int $course_id
 * @return array|null
 */
function getStudentByCourseID(PDO $pdo, int $course_id): ?array {
  $query = "
      SELECT s.student_id, s.first_name, s.last_name, s.patronymic
      FROM students s
      JOIN student_courses sc ON s.student_id = sc.student_id
      WHERE sc.course_id = :course_id;
  ";
  $stmt = $pdo->prepare($query);
  $stmt->execute(['course_id' => $course_id]);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*** Check course's ID validation ***/
if (!isset($_GET['course_id'])) {
    die("Не указан ID факультатива.");
}

$course_id = intval($_GET['course_id']);

/*** Get course ***/
$course = getCourseById($pdo, $course_id);

if (!$course) {
    die("Факультатив не найден.");
}

/*** Removing a course ***/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $student_id = intval($_POST['student_id']);
    removeStudent($pdo, $student_id, $course_id);
}

$students = getStudentByCourseID($pdo, $course_id);
/*** Get role***/
$role = $_SESSION['role'] ?: 'USER';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($course['course_name']); ?></title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Факультатив: <?php echo htmlspecialchars($course['course_name']); ?></h1>
    <nav>
        <a href="courses.php">Вернуться к списку факультативов</a> |
        <a href="../index.php">Домашняя страница</a>
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
    <h2>Студенты на факультативе</h2>
    <table border="1">
        <tr>
            <th>ID Студента</th>
            <th>ФИО</th>
            <?php if ($role !== 'STUDENT'): ?>
              <th>Действия</th>
            <?php endif; ?>
        </tr>
        <?php foreach ($students as $student): ?>
            <tr>
                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                <td><?php echo htmlspecialchars("{$student['first_name']} {$student['last_name']} {$student['patronymic']}"); ?></td>
                <?php if ($role !== 'STUDENT'): ?>
                  <td>
                    <form method="post" style="background: rgba(0, 0, 0, 0); box-shadow: none; font-size: 16px; padding: 5px 10px;">
                        <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                        <button type="submit" name="action" value="remove" style="font-size: 16px; padding: 5px 10px;">Удалить</button>
                    </form>
                  </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <a href="courses.php">Вернуться к списку факультативов</a>
    <?php if ($role === 'TEACHER' || $role === 'ADMIN'): ?>
      <a href="edit_course.php?course_id=<?php echo urlencode($course['course_id']); ?>">
        Изменить данные
      </a>
    <?php endif; ?>
</body>
</html>
