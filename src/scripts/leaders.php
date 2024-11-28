<?php
require_once '../includes/db_connect.php';


/**
 * Get liders with sort and filter.
 *
 * @param PDO $pdo
 * @param string|null $groupFilter
 * @param string $sortColumn
 * @param string $sortOrder
 * @return array|null
 */
function getLeaders($pdo, $groupFilter, $sortColumn, $sortOrder): ?array  {
    $query = "
      SELECT
        s.student_id,
        s.first_name,
        s.last_name,
        g.group_name
      FROM students s
      JOIN leaders l ON s.student_id = l.student_id
      JOIN study_groups g ON s.group_id = g.group_id
    ";

    if ($groupFilter) {
        $query .= " WHERE g.group_name = :groupFilter";
    }

    $query .= " ORDER BY $sortColumn $sortOrder";

    $stmt = $pdo->prepare($query);
    if ($groupFilter) {
        $stmt->bindParam(':groupFilter', $groupFilter, PDO::PARAM_STR);
    }
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get all groups.
 *
 * @param PDO $pdo
 * @return array
 */
function getGroups($pdo): array {
    $stmt = $pdo->prepare("SELECT group_name FROM study_groups");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*** Change filtered parameters ***/
$groupFilter = $_GET['group'] ?? '';
$sortColumn = $_GET['sort'] ?? 'last_name';
$sortOrder = ($_GET['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

/*** Get data ***/
$leaders = getLeaders($pdo, $groupFilter, $sortColumn, $sortOrder);
$groups = getGroups($pdo);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Список старост</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1>Список старост</h1>
    <nav>
        <a href="../index.php">Домашняя страница</a>
    </nav>

    <a class="filter-toggle-btn" onclick="toggleFilter()">Показать/скрыть фильтр</a>
    <br>
    <div class="filter-form" id="filterForm">
        <form method="GET" action="">
            <label for="group">Фильтр по группе:</label>
            <select name="group" id="group">
                <option value="">Все группы</option>
                <?php foreach ($groups as $group): ?>
                    <option value="<?= htmlspecialchars($group['group_name']); ?>" <?= $group['group_name'] === $groupFilter ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($group['group_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="sort">Сортировать по:</label>
            <select name="sort" id="sort">
                <option value="last_name" <?= $sortColumn === 'last_name' ? 'selected' : ''; ?>>Фамилии</option>
                <option value="first_name" <?= $sortColumn === 'first_name' ? 'selected' : ''; ?>>Имени</option>
            </select>

            <label for="order">Порядок:</label>
            <select name="order" id="order">
                <option value="asc" <?= $sortOrder === 'asc' ? 'selected' : ''; ?>>По возрастанию</option>
                <option value="desc" <?= $sortOrder === 'desc' ? 'selected' : ''; ?>>По убыванию</option>
            </select>

            <input type="submit" value="Применить">
        </form>
    </div>

    <br>
    <table>
        <tr>
            <th>ID</th>
            <th><a style="color: white;" href="?sort=last_name&order=<?= $sortOrder === 'asc' ? 'desc' : 'asc'; ?>">Фамилия</a></th>
            <th><a style="color: white;" href="?sort=first_name&order=<?= $sortOrder === 'asc' ? 'desc' : 'asc'; ?>">Имя</a></th>
            <th>Группа</th>
            <th class="center">Данные</th>
        </tr>
        <?php foreach ($leaders as $leader): ?>
            <tr>
                <td><?= htmlspecialchars($leader['student_id']); ?></td>
                <td><?= htmlspecialchars($leader['last_name']); ?></td>
                <td><?= htmlspecialchars($leader['first_name']); ?></td>
                <td><?= htmlspecialchars($leader['group_name']); ?></td>
                <td class="center"><a href="student.php?student_id=<?= urlencode($leader['student_id']); ?>">Просмотр</a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
        function toggleFilter() {
            const filterForm = document.getElementById('filterForm');
            filterForm.style.display = filterForm.style.display === 'block' ? 'none' : 'block';
        }
    </script>
    <br>
    <a href="faculties.php">Вернуться к списку факультетов</a>
</body>
</html>
