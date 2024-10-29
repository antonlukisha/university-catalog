<?php 
require_once 'includes/db_connect.php';

if (!isset($_GET['student_id'])) {
    die("Не указан ID студента.");
}

$student_id = intval($_GET['student_id']);

$Q = "SELECT * FROM students WHERE student_id = :student_id;";
$stmt = $pdo->prepare($Q);
$stmt->execute(['student_id' => $student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Студент не найден.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $patronymic = $_POST['patronymic'];
    $birth_place = $_POST['birth_place'];
    $birth_date = $_POST['birth_date'];
    $phone = $_POST['phone'];
    $group_id = $_POST['group_id'];
    $average_grade = $_POST['average_grade'];

    $Q = "UPDATE students SET first_name = :first_name, last_name = :last_name, patronymic = :patronymic, 
          birth_place = :birth_place, birth_date = :birth_date, phone = :phone, 
          group_id = :group_id, average_grade = :average_grade 
          WHERE student_id = :student_id";
    $stmt = $pdo->prepare($Q);
    $stmt->execute([
        'first_name' => $first_name,
        'last_name' => $last_name,
        'patronymic' => $patronymic,
        'birth_place' => $birth_place,
        'birth_date' => $birth_date,
        'phone' => $phone,
        'group_id' => $group_id,
        'average_grade' => $average_grade,
        'student_id' => $student_id
    ]);

    $deleteQ = "DELETE FROM performances WHERE student_id = :student_id";
    $deleteStmt = $pdo->prepare($deleteQ);
    $deleteStmt->execute(['student_id' => $student_id]);

    // Добавляем новые оценки
    $insertQ = "INSERT INTO performances (student_id, grade) VALUES (:student_id, :grade)";
    $insertStmt = $pdo->prepare($insertQ);
    $insertStmt->execute(['student_id' => $student_id, 'grade' => $grade]);

    header("Location: student.php?student_id=" . urlencode($student_id));
    exit;
}

$Q_groups = "SELECT group_id, group_name FROM study_groups";
$stmt_groups = $pdo->prepare($Q_groups);
$stmt_groups->execute();
$groups = $stmt_groups->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Редактировать студента</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Редактировать студента</h1>
    <nav>
        <a href="students.php">Студенты</a> | 
        <a href="index.php">Домашняя страница</a>
    </nav>
    <form action="edit_student.php?student_id=<?php echo urlencode($student_id); ?>" method="POST">
        
        
        <label>Фамилия:</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required><br>

        <label>Имя:</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required><br>
        
        <label>Отчество:</label>
        <input type="text" name="patronymic" value="<?php echo htmlspecialchars($student['patronymic']); ?>"><br>
        
        <label>Место рождения:</label>
        <input type="text" name="birth_place" value="<?php echo htmlspecialchars($student['birth_place']); ?>"><br>
        
        <label>Дата рождения:</label>
        <input type="date" name="birth_date" value="<?php echo htmlspecialchars($student['birth_date']); ?>"><br>
        
        <label>Телефон:</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>"><br>
        
        <label>Группа:</label>
        <select name="group_id" required>
            <option value="">Выберите группу</option>
            <?php foreach ($groups as $group): ?>
                <option value="<?php echo $group['group_id']; ?>" <?php echo ($group['group_id'] == $student['group_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($group['group_name']); ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label>Средний балл:</label>
        <input type="number" name="average_grade" value="<?php echo htmlspecialchars($student['average_grade']); ?>" step="0.01"><br>
        
        <input type="submit" value="Сохранить изменения">
    </form>
</body>
</html>
