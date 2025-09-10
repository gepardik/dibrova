<?php
require_once '../config.php';

// Проверяем авторизацию
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Получаем статистику
try {
    $conn = getDBConnection();
    
    // Количество предстоящих концертов
    $upcomingStmt = $conn->prepare('SELECT COUNT(*) FROM concerts WHERE date >= CURDATE() AND is_active = TRUE');
    $upcomingStmt->execute();
    $upcomingCount = $upcomingStmt->fetchColumn();

    // Количество непрочитанных сообщений
    $messagesStmt = $conn->prepare('SELECT COUNT(*) FROM contact_messages WHERE is_read = FALSE');
    $messagesStmt->execute();
    $unreadMessages = $messagesStmt->fetchColumn();
    
    // Количество альбомов
    $albumsStmt = $conn->prepare('SELECT COUNT(*) FROM albums');
    $albumsStmt->execute();
    $albumsCount = $albumsStmt->fetchColumn();
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель | DIBROVA</title>
    <base href="/admin/">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #D9D9D9;
        }
        .header {
            background-color: #1C2824;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 1.5rem;
        }
        .header a {
            color: white;
            text-decoration: none;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .stat-card h3 {
            margin: 0 0 1rem 0;
            color: #1C2824;
        }
        .stat-card p {
            margin: 0;
            font-size: 2rem;
            color: #536C63;
        }
        .actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        .action-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .action-card a {
            display: inline-block;
            padding: 1rem 2rem;
            background-color: #536C63;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 1rem;
        }
        .action-card a:hover {
            background-color: #3f524a;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>Админ-панель DIBROVA</h1>
        <a href="/admin/logout.php">Выйти</a>
    </header>

    <div class="container">
        <div class="stats">
            <div class="stat-card">
                <h3>Предстоящие концерты</h3>
                <p><?php echo $upcomingCount; ?></p>
            </div>
            <div class="stat-card">
                <h3>Непрочитанные сообщения</h3>
                <p><?php echo $unreadMessages; ?></p>
            </div>
            <div class="stat-card">
                <h3>Фотоальбомы</h3>
                <p><?php echo $albumsCount; ?></p>
            </div>
        </div>

        <div class="actions">
            <div class="action-card">
                <h3>Концерты</h3>
                <p>Управление концертами</p>
                <a href="/admin/concerts.php">Перейти →</a>
            </div>
            <div class="action-card">
                <h3>Сообщения</h3>
                <p>Просмотр сообщений</p>
                <a href="/admin/messages.php">Перейти →</a>
            </div>
            <div class="action-card">
                <h3>Галерея</h3>
                <p>Управление альбомами</p>
                <a href="/admin/albums.php">Перейти →</a>
            </div>
        </div>
    </div>
</body>
</html> 