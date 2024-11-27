<?php
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

 /*** Get all courses data***/
 $courses = getAllCourses($pdo);
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
            </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <a href="add_course.php">Добавить новый факультатив</a>
</body>
</html>
