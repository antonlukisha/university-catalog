<?php 
require_once 'includes/db_connect.php';

$groupFilter = isset($_GET['group']) ? $_GET['group'] : '';
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'last_name'; 
$sortOrder = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'asc' : 'desc'; 

$Q = "SELECT s.student_id, s.first_name, s.last_name, g.group_name 
      FROM students s
      JOIN leaders l ON s.student_id = l.student_id
      JOIN study_groups g ON s.group_id = g.group_id";

if ($groupFilter) {
    $Q .= " WHERE g.group_name = :groupFilter";
}

$Q .= " ORDER BY $sortColumn $sortOrder";

$stmt = $pdo->prepare($Q);

if ($groupFilter) {
    $stmt->bindParam(':groupFilter', $groupFilter, PDO::PARAM_STR);
}

$stmt->execute();
$leaders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$groupsQuery = "SELECT group_name FROM study_groups";
$groupsStmt = $pdo->prepare($groupsQuery);
$groupsStmt->execute();
$groups = $groupsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Список старост</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Список старост</h1>
    <nav>
        <a href="index.php">Домашняя страница</a>
    </nav>

    
    <a class="filter-toggle-btn" onclick="toggleFilter()">Показать/скрыть фильтр</a>
    <br>
    <div class="filter-form" id="filterForm">
        <form method="GET" action="">
            <label for="group">Фильтр по группе:</label>
            <select name="group" id="group">
                <option value="">Все группы</option>
                <?php foreach ($groups as $group): ?>
                    <option value="<?php echo htmlspecialchars($group['group_name']); ?>" <?php if ($group['group_name'] === $groupFilter) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($group['group_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="sort">Сортировать по:</label>
            <select name="sort" id="sort">
                <option value="last_name" <?php if ($sortColumn === 'last_name') echo 'selected'; ?>>Фамилии</option>
                <option value="first_name" <?php if ($sortColumn === 'first_name') echo 'selected'; ?>>Имени</option>
            </select>

            <label for="order">Порядок:</label>
            <select name="order" id="order">
                <option value="asc" <?php if ($sortOrder === 'asc') echo 'selected'; ?>>По возрастанию</option>
                <option value="desc" <?php if ($sortOrder === 'desc') echo 'selected'; ?>>По убыванию</option>
            </select>

            <input type="submit" value="Применить">
        </form>
    </div>

    <br>
    <table>
        <tr>
            <th>ID</th>
            <th><a href="?sort=last_name&order=<?php echo $sortOrder === 'asc' ? 'desc' : 'asc'; ?>">Фамилия</a></th>
            <th><a href="?sort=first_name&order=<?php echo $sortOrder === 'asc' ? 'desc' : 'asc'; ?>">Имя</a></th>
            <th>Группа</th>
            <th class="center">Данные</th>
        </tr>
        <?php foreach ($leaders as $leader): ?>
            <tr>
                <td><?php echo htmlspecialchars($leader['student_id']); ?></td>
                <td><?php echo htmlspecialchars($leader['last_name']); ?></td>
                <td><?php echo htmlspecialchars($leader['first_name']); ?></td>
                <td><?php echo htmlspecialchars($leader['group_name']); ?></td>
                <td class="center"><a href="student.php?student_id=<?php echo urlencode($leader['student_id']); ?>">Просмотр</a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
        function toggleFilter() {
            var filterForm = document.getElementById('filterForm');
            if (filterForm.style.display === 'none' || filterForm.style.display === '') {
                filterForm.style.display = 'block';
            } else {
                filterForm.style.display = 'none';
            }
        }
    </script>
    <br>
    <a href="faculties.php">Вернуться к списку факультетов</a>
</body>
</html>
