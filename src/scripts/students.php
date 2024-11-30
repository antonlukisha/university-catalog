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

/**
 * Delete a student by ID.
 *
 * @param PDO $pdo
 * @param int $student_id
 * @return bool
 */
function deleteStudent(PDO $pdo, int $student_id): bool {
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("DELETE FROM students WHERE student_id = :student_id");
        $stmt->execute(['student_id' => $student_id]);

        $pdo->commit();
        return true;
    } catch (Exception $exception) {
        $pdo->rollBack();
        return false;
    }
}

/*** Delete student ***/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_student_id'])) {
    $student_id = intval($_POST['delete_student_id']);
    if (deleteStudent($pdo, $student_id)) {
      $message = "Успешно";
    } else {
      $message = "Ошибка";
    }
}


/*** Change filtered parameters ***/
$sort_column = $_GET['sort'] ?? 'student_id';
$sort_order = ($_GET['order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

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
        <?php if ($role !== 'USER'): ?> |
         <a href="groups.php">Группы</a>
        <?php endif; ?>
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
                    <th>Удаление</th>
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
                          <td>
                            <form method="post" style="padding: 0; background: rgba(0, 0, 0, 0); box-shadow: none;" onsubmit="return confirm('Вы уверены, что хотите удалить этого студента?');">
                                <input type="hidden" name="delete_student_id" value="<?php echo $student['student_id']; ?>">
                                <button type="submit" style="font-size: 16px; padding: 5px 10px;">Удалить</button>
                            </form>
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
