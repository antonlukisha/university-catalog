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
        <h1>Добро пожаловать в Веб-приложение БД</h1>
        <nav>
            <a href="scripts/students.php">Студенты</a>
            <a href="scripts/groups.php">Группы</a>
            <a href="scripts/courses.php">Факультативы</a>
            <a href="scripts/faculties.php">Факультеты</a>
            <a href="leaders.php">Старосты</a>
        </nav>
        <div class="image-container">
            <img src="resurses/nsu.jpg" alt="NSU">
        </div>
        <input method="post" action="scripts/clear_tables.php" value="Очистить базу данных"/>
    </div>
</body>
</html>
