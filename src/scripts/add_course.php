<?php
require_once '../includes/db_connect.php';

/**
 * Add course.
 *
 * @param PDO $pdo
 * @param map $params
 * @return null
 */
function addCourse(PDO $pdo, $params) {
  $query = "
    INSERT INTO courses (course_name, description, capacity, faculty_id)
        VALUES (:course_name, :description, :capacity, :faculty_id)
  ";
  $stmt = $pdo->prepare($query);
  $stmt->execute($params);
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

/*** POST course data ***/
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  try {
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];
    $capacity = $_POST['capacity'];
    $faculty_id = $_POST['faculty_id'];

    $params = [];
    $params[':course_name'] = $course_name;
    $params[':description'] = $description;
    $params[':capacity'] = $capacity;
    $params[':faculty_id'] = $faculty_id;

    addCourse($pdo, $params);

    header("Location: courses.php");
    exit;
  } catch (PDOException $exception) {
    error_log("Addition error: " . $exception->getMessage());
    echo "<p>Некорректные данные курса. Попробуйте снова.</p>";
  }
}

$faculties = getFaculties($pdo);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Добавить факультатив</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Добавить факультатив</h1>
    <nav>
        <a href="courses.php">Факультативы</a> |
        <a href="../index.php">Домашняя страница</a>
    </nav>
    <form action="add_course.php" method="POST">

        <label>Название факультатива:</label>
        <input type="text" name="course_name" required><br>

        <label>Описание:</label>
        <textarea name="description"></textarea><br>

        <label>Вместимость:</label>
        <input type="number" name="capacity" required min="1"><br>

        <label>Факультет:</label>
        <select name="faculty_id" required>
            <option value="">Выберите факультет</option>
            <?php foreach ($faculties as $faculty): ?>
                <option value="<?php echo $faculty['faculty_id']; ?>"><?php echo htmlspecialchars($faculty['faculty_name']); ?></option>
            <?php endforeach; ?>
        </select><br>

        <input type="submit" value="Добавить факультатив">
    </form>
</body>
</html>
