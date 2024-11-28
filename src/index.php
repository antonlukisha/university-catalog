<?php
/*** Upload Context ***/
session_start();
if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'USER';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role'])) {
    $_SESSION['role'] = $_POST['role'];
}

$role = $_SESSION['role'];
$role_name = [
    'USER' => 'Пользователь',
    'ADMIN' => 'Администратор',
    'STUDENT' => 'Студент',
    'TEACHER' => 'Учитель'
];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Веб-приложение БД</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" href="resurses/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="resurses/favicon.ico" type="image/x-icon">
    <link rel="icon" href="resurses/favicon-32x32.png" sizes="32x32" type="image/png">
    <link rel="icon" href="resurses/favicon-16x16.png" sizes="16x16" type="image/png">
</head>
<body>
    <div class="container">
        <br>
        <h1>Добро пожаловать в Веб-приложение БД</h1>
        <form action="" method="post" class="invisible-form">
          <label for="role-select">Выберите роль:</label>
            <select name="role" id="role-select" onchange="this.form.submit()">
              <option value="USER" <?= $role === 'USER' ? 'selected' : '' ?>>Пользователь</option>
              <option value="ADMIN" <?= $role === 'ADMIN' ? 'selected' : '' ?>>Администратор</option>
              <option value="STUDENT" <?= $role === 'STUDENT' ? 'selected' : '' ?>>Студент</option>
              <option value="TEACHER" <?= $role === 'TEACHER' ? 'selected' : '' ?>>Учитель</option>
          </select>
        </form>
        <p>Текущая роль: <strong><?= $role_name[htmlspecialchars($role)] ?></strong></p>
        <br>
        <?php if ($role === 'ADMIN'): ?>
          <nav>
              <a href="scripts/students.php">Студенты</a>
              <a href="scripts/groups.php">Группы</a>
              <a href="scripts/courses.php">Факультативы</a>
              <a href="scripts/faculties.php">Факультеты</a>
              <a href="scripts/leaders.php">Старосты</a>
          </nav>
        <?php elseif ($role === 'STUDENT'): ?>
          <nav>
              <a href="scripts/students.php">Студенты</a>
              <a href="scripts/groups.php">Группы</a>
              <a href="scripts/courses.php">Факультативы</a>
              <a href="scripts/faculties.php">Факультеты</a>
          </nav>
        <?php elseif ($role === 'TEACHER'): ?>
          <nav>
            <a href="scripts/students.php">Студенты</a>
            <a href="scripts/groups.php">Группы</a>
            <a href="scripts/courses.php">Факультативы</a>
            <a href="scripts/faculties.php">Факультеты</a>
            <a href="scripts/leaders.php">Старосты</a>
          </nav>
        <?php else: ?>
          <nav>
              <a href="scripts/groups.php">Группы</a>
              <a href="scripts/faculties.php">Факультеты</a>
          </nav>
        <?php endif; ?>
        <div class="image-container">
            <img src="resurses/nsu.jpg" alt="NSU">
        </div>
        <?php if ($role === 'ADMIN'): ?>
          <div class="flex-container">
            <form class="invisible-form" action="scripts/clear.php" method="post">
              <input type="submit" value="Очистить базу данных">
            </form>
            <form class="invisible-form" action="scripts/upload_test_data.php" method="post">
              <input type="submit" value="Загрузить тестовые данные">
            </form>
          </div>
        <?php endif; ?>
        <br>
    </div>
</body>
</html>
