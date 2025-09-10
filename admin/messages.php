<?php
require_once '../config.php';

// Проверяем авторизацию
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Обработка отметки сообщения как прочитанного
if (isset($_POST['mark_read']) && isset($_POST['message_id'])) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare('UPDATE contact_messages SET is_read = TRUE WHERE id = ?');
        $stmt->execute([$_POST['message_id']]);
    } catch (PDOException $e) {
        // Игнорируем ошибку
    }
}

// Получаем список сообщений
try {
    $conn = getDBConnection();
    $stmt = $conn->prepare('
        SELECT * FROM contact_messages 
        ORDER BY created_at DESC
    ');
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сообщения | DIBROVA</title>
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
        .message-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            padding: 1.5rem;
        }
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        .message-info h3 {
            margin: 0 0 0.5rem 0;
            color: #1C2824;
        }
        .message-info p {
            margin: 0;
            color: #666;
            font-size: 0.875rem;
        }
        .message-status {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
        }
        .status-unread {
            background-color: #feeced;
            color: #dc3545;
        }
        .status-read {
            background-color: #e6f4ea;
            color: #1e7e34;
        }
        .message-content {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }
        .message-actions {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
            display: flex;
            gap: 1rem;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #536C63;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
        }
        .btn:hover {
            background-color: #3f524a;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>Сообщения</h1>
        <a href="index.php">Вернуться в админ-панель</a>
    </header>

    <div class="container">
        <?php foreach ($messages as $message): ?>
            <div class="message-card">
                <div class="message-header">
                    <div class="message-info">
                        <h3><?php echo htmlspecialchars($message['name']); ?></h3>
                        <p>Email: <?php echo htmlspecialchars($message['email']); ?></p>
                        <p>Дата: <?php echo date('d.m.Y H:i', strtotime($message['created_at'])); ?></p>
                    </div>
                    <span class="message-status <?php echo $message['is_read'] ? 'status-read' : 'status-unread'; ?>">
                        <?php echo $message['is_read'] ? 'Прочитано' : 'Не прочитано'; ?>
                    </span>
                </div>
                <div class="message-content">
                    <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                </div>
                <div class="message-actions">
                    <?php if (!$message['is_read']): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                            <button type="submit" name="mark_read" class="btn">Отметить как прочитанное</button>
                        </form>
                    <?php endif; ?>
                    <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" class="btn btn-secondary">
                        Ответить по email
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($messages)): ?>
            <p style="text-align: center;">Нет сообщений</p>
        <?php endif; ?>
    </div>
</body>
</html> 