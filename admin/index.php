<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('../config.php');

// Проверяем авторизацию
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Обработка выхода
if (isset($_GET['logout'])) {
    session_destroy();
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
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Админ-панель</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .admin-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .logout-btn {
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .section {
            margin-bottom: 20px;
        }
        h2 {
            color: #333;
        }
        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>Админ-панель DIBROVA</h1>
        <a href="?logout" class="logout-btn">Выйти</a>
    </div>
    
    <div class="admin-content">
        <div class="section">
            <h2>Управление концертами</h2>
            <p>Всего предстоящих концертов: <?php echo $upcomingCount; ?></p>
            <a href="concerts.php" class="btn">Управлять концертами</a>
        </div>
        
        <div class="section">
            <h2>Сообщения</h2>
            <p>Непрочитанных сообщений: <?php echo $unreadMessages; ?></p>
            <a href="messages.php" class="btn">Просмотреть сообщения</a>
        </div>
    </div>
</body>
</html> 