<?php
require_once '../includes/db_connect.php';

/**
 * Get all students.
 *
 * @param PDO $pdo
 * @return array
 */
function getAllStudents(PDO $pdo): array
{
    $query = "SELECT student_id, last_name, first_name, patronymic FROM students;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*** Get all students data***/
$students = getAllStudents($pdo);

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Студенты</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Все студенты</h1>
    <nav>
        <a href="../index.php">Домашняя страница</a>
    </nav>
    <br>
    <?php if (!empty($students)): ?>
      <table border="1">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Фамилия</th>
                  <th>Имя</th>
                  <th>Отчество</th>
                  <th>Данные</th>
                  <th>Редактирование</th>
              </tr>
          </thead>
          <tbody>
            <?php foreach ($students as $student): ?>
                  <tr>
                      <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                      <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                      <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                      <td><?php echo htmlspecialchars($student['patronymic'] ?? '—'); ?></td>
                      <td>
                          <a href="student.php?student_id=<?php echo urlencode($student['student_id']); ?>">
                              Просмотр
                          </a>
                      </td>
                      <td>
                          <a href="../edit_student.php?student_id=<?php echo urlencode($student['student_id']); ?>">
                              Изменить
                          </a>
                      </td>
                  </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
    <?php else: ?>
        <p>Список студентов пуст.</p>
    <?php endif; ?>
    <br>
    <a href="../input_student.php">Добавить нового студента</a>

    <a href="../index.php">Вернуться на домашнюю страницу</a>
</body>
</html>
