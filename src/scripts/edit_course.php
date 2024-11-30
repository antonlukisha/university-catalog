<?php
require_once '../includes/db_connect.php';

/**
 * Update course.
 *
 * @param PDO $pdo
 * @param array $params
 * @return null
 */
function updateCourse(PDO $pdo, $params) {
    $query = "
        UPDATE courses
        SET course_name = :course_name,
            description = :description,
            capacity = :capacity,
            faculty_id = :faculty_id
        WHERE course_id = :course_id
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
}

/**
 * Get course by ID.
 *
 * @param PDO $pdo
 * @param int $course_id
 * @return array|null
 */
function getCourseById(PDO $pdo, $course_id): ?array {
    $query = "SELECT * FROM courses WHERE course_id = :course_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':course_id' => $course_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get all faculties.
 *
 * @param PDO $pdo
 * @return array|null
 */
function getFaculties($pdo): ?array {
  $query = "SELECT faculty_id, faculty_name FROM faculties";
  $stmt = $pdo->prepare($query);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*** GET course data for editing ***/
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['course_id'])) {
    $course_id = (int)$_GET['course_id'];
    $course = getCourseById($pdo, $course_id);
    if (!$course) {
        echo "<p>Курс не найден.</p>";
        exit;
    }
    $faculties = getFaculties($pdo);
}
/*** POST updated course data ***/
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $course_id = $_POST['course_id'];
        $course_name = $_POST['course_name'];
        $description = $_POST['description'];
        $capacity = $_POST['capacity'];
        $faculty_id = $_POST['faculty_id'];

        $params = [
            ':course_id' => $course_id,
            ':course_name' => $course_name,
            ':description' => $description,
            ':capacity' => $capacity,
            ':faculty_id' => $faculty_id
        ];

        updateCourse($pdo, $params);
        header("Location: course_detail.php?course_id=" . urlencode($course_id));
        exit;
    } catch (PDOException $exception) {
        error_log("Update error: " . $exception->getMessage());
        echo "<p>Ошибка обновления данных курса. Попробуйте снова.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Редактировать факультатив</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Редактировать факультатив</h1>
    <nav>
        <a href="courses.php">Факультативы</a> |
        <a href="../index.php">Домашняя страница</a>
    </nav>
    <form action="edit_course.php" method="POST">
        <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">

        <label>Название факультатива:</label>
        <input type="text" name="course_name" value="<?php echo htmlspecialchars($course['course_name']); ?>" required><br>

        <label>Описание:</label>
        <textarea name="description"><?php echo htmlspecialchars($course['description']); ?></textarea><br>

        <label>Вместимость:</label>
        <input type="number" name="capacity" value="<?php echo $course['capacity']; ?>" required min="1"><br>

        <label>Факультет:</label>
        <select name="faculty_id" required>
            <option value="">Выберите факультет</option>
            <?php foreach ($faculties as $faculty): ?>
                <option value="<?php echo $faculty['faculty_id']; ?>"
                    <?php echo ($faculty['faculty_id'] == $course['faculty_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($faculty['faculty_name']); ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <input type="submit" value="Сохранить изменения">
    </form>
</body>
</html>
