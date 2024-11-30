<?php
session_start();
require_once '../includes/db_connect.php';

/**
 * Get all courses.
 *
 * @param PDO $pdo
 * @return array
 */
 function getAllCourses(PDO $pdo): array
 {
     $query = "
         SELECT
          c.course_id,
          c.course_name,
          c.description,
          c.capacity,
          f.faculty_name
        FROM courses c
        JOIN faculties f ON c.faculty_id = f.faculty_id;
     ";
     $stmt = $pdo->prepare($query);
     $stmt->execute();
     return $stmt->fetchAll(PDO::FETCH_ASSOC);
 }

 /**
 * Check validation of student ID.
 *
 * @param int $student_id
 * @return bool
 */
function isStudentValid(PDO $pdo, int $student_id): bool {
    $query = "SELECT 1 FROM students WHERE student_id = :student_id LIMIT 1;";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['student_id' => $student_id]);
    return $stmt->fetchColumn() !== false;
}

/**
 * Add student to course.
 *
 * @param int $student_id
 * @param int $course_id
 * @return bool
 */
function addStudentToCourse(PDO $pdo, int $student_id, int $course_id): bool {
    $query = "INSERT INTO student_courses (student_id, course_id) VALUES (:student_id, :course_id);";
    $stmt = $pdo->prepare($query);
    return $stmt->execute(['student_id' => $student_id, 'course_id' => $course_id]);
}

/**
 * Check .
 *
 * @param int $student_id
 * @param int $course_id
 * @return bool
 */
function hasWroteStudentToCourse(PDO $pdo, int $student_id, int $course_id): bool {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM student_courses WHERE student_id = :student_id AND course_id = :course_id");
    $stmt->execute([':student_id' => $student_id, ':course_id' => $course_id]);
    return $stmt->fetchColumn();
}

 /*** Get all courses data***/
$courses = getAllCourses($pdo);
/*** Get role***/
$role = $_SESSION['role'] ?: 'USER';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'], $_POST['course_id'])) {
    $student_id = intval($_POST['student_id']);
    $course_id = intval($_POST['course_id']);

    if (isStudentValid($pdo, $student_id) && !hasWroteStudentToCourse($pdo, $student_id, $course_id)) {
        addStudentToCourse($pdo, $student_id, $course_id);
        $message = "Успешно";
        $courseID = $course_id;
    } else {
        $message = "Неверный ID";
        $courseID = $course_id;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Факультативы</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Факультативы</h1>
    <nav>
        <a href="../index.php">Домашняя страница</a> |
        <a href="students.php">Студенты</a> |
        <a href="groups.php">Группы</a>
    </nav>
    <br>

    <table border="1">
        <tr>
            <th>ID Факультатива</th>
            <th>Название</th>
            <th>Описание</th>
            <th>Вместимость</th>
            <th>Факультет</th>
            <th>Подробнее</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($courses as $course): ?>
            <tr>
                <td><?php echo htmlspecialchars($course['course_id']); ?></td>
                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                <td><?php echo htmlspecialchars(mb_strimwidth($course['description'], 0, 50, "...")); ?></td>
                <td><?php echo htmlspecialchars($course['capacity']); ?></td>
                <td><?php echo htmlspecialchars($course['faculty_name']); ?></td>
                <td>
                    <a href="course_detail.php?course_id=<?php echo urlencode($course['course_id']); ?>">Подробнее</a>
                </td>
                <td>
                    <form method="post" style="background: rgba(0, 0, 0, 0); box-shadow: none;">
                      <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                      <input type="text" name="student_id" placeholder="<?php if (isset($message) && $courseID == $course['course_id']): ?><?php echo htmlspecialchars($message); ?><?php else: ?>ID Студента<?php endif; ?>" required>
                      <button type="submit">Добавить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php if ($role !== 'USER' && $role !== 'STUDENT'): ?>
      <br>
      <a href="add_course.php">Добавить новый факультатив</a>
    <?php endif; ?>
    <a href="../index.php">Вернуться на домашнюю страницу</a>
    <?php if (isset($message)): ?>

    <?php endif; ?>
</body>
</html>
