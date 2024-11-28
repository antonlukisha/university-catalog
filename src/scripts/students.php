<?php
session_start();
require_once '../includes/db_connect.php';

/**
 * Get all students.
 *
 * @param PDO $pdo
 * @param string $sort_column
 * @param string $sort_order
 * @return array|null
 */
function getAllStudents(PDO $pdo, $sort_column, $sort_order): ?array {
  $query = "SELECT student_id, last_name, first_name, patronymic FROM students";
  $query .= " ORDER BY $sort_column $sort_order";
  $stmt = $pdo->prepare($query);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*** Change filtered parameters ***/
$sort_column = $_GET['sort'] ?? 'student_id';
$sort_order = ($_GET['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

/*** Get all students data***/
$students = getAllStudents($pdo, $sort_column, $sort_order);
/*** Get role***/
$role = $_SESSION['role'] ?: 'USER';
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
    <a class="filter-toggle-btn" onclick="toggleFilter()">Показать/скрыть фильтр</a>
    <br>
    <div class="filter-form" id="filterForm">
        <form method="GET" action="">
            <label for="sort">Сортировать по:</label>
            <select name="sort" id="sort">
                <option value="last_name" <?= $sort_column === 'last_name' ? 'selected' : ''; ?>>Фамилии</option>
                <option value="first_name" <?= $sort_column === 'first_name' ? 'selected' : ''; ?>>Имени</option>
                <option value="patronymic" <?= $sort_column === 'first_name' ? 'selected' : ''; ?>>Отчеству</option>
            </select>

            <label for="order">Порядок:</label>
            <select name="order" id="order">
                <option value="asc" <?= $sort_order === 'asc' ? 'selected' : ''; ?>>По возрастанию</option>
                <option value="desc" <?= $sort_order === 'desc' ? 'selected' : ''; ?>>По убыванию</option>
            </select>

            <input type="submit" value="Применить">
        </form>
    </div>
    <br>
      <table border="1">
          <thead>
              <tr>
                  <th>ID</th>
                  <th><a style="color: white;" href="?sort=last_name&order=<?= $sort_order === 'asc' ? 'desc' : 'asc'; ?>">Фамилия</th>
                  <th><a style="color: white;" href="?sort=first_name&order=<?= $sort_order === 'asc' ? 'desc' : 'asc'; ?>">Имя</th>
                  <th><a style="color: white;" href="?sort=patronymic&order=<?= $sort_order === 'asc' ? 'desc' : 'asc'; ?>">Отчество</th>
                  <?php if ($role !== 'USER'): ?>
                    <th>Данные</th>
                  <?php endif; ?>
                  <?php if ($role === 'ADMIN'): ?>
                    <th>Редактирование</th>
                  <?php endif; ?>
              </tr>
          </thead>
          <tbody>
            <?php foreach ($students as $student): ?>
                  <tr>
                      <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                      <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                      <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                      <td><?php echo htmlspecialchars($student['patronymic'] ?? '—'); ?></td>
                      <?php if ($role !== 'USER'): ?>
                        <td>
                            <a href="student.php?student_id=<?php echo urlencode($student['student_id']); ?>">
                                Просмотр
                            </a>
                        </td>
                      <?php endif; ?>
                      <?php if ($role === 'ADMIN'): ?>
                        <td>
                            <a href="edit_student.php?student_id=<?php echo urlencode($student['student_id']); ?>">
                                Изменить
                            </a>
                        </td>
                      <?php endif; ?>
                  </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
    <br>
    <?php if ($role === 'ADMIN'): ?>
    <a href="input_student.php">Добавить нового студента</a>
    <?php endif; ?>
    <script>
        function toggleFilter() {
            const filterForm = document.getElementById('filterForm');
            filterForm.style.display = filterForm.style.display === 'block' ? 'none' : 'block';
        }
    </script>
    <a href="../index.php">Вернуться на домашнюю страницу</a>
</body>
</html>
