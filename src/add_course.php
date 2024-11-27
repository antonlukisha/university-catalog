<?php 
require_once 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];
    $capacity = $_POST['capacity'];
    $faculty_id = $_POST['faculty_id'];

    $Q = "INSERT INTO courses (course_name, description, capacity, faculty_id) 
          VALUES (:course_name, :description, :capacity, :faculty_id)";
    $stmt = $pdo->prepare($Q);
    $stmt->execute([
        'course_name' => $course_name,
        'description' => $description,
        'capacity' => $capacity,
        'faculty_id' => $faculty_id,
    ]);

    header("Location: courses.php");
    exit;
}

$Q_faculties = "SELECT faculty_id, faculty_name FROM faculties";
$stmt_faculties = $pdo->prepare($Q_faculties);
$stmt_faculties->execute();
$faculties = $stmt_faculties->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Добавить факультатив</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Добавить факультатив</h1>
    <nav>
        <a href="courses.php">Факультативы</a> | 
        <a href="index.php">Домашняя страница</a>
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
