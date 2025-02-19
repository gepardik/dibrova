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

// Получаем список концертов
try {
    $conn = getDBConnection();
    $stmt = $conn->prepare('
        SELECT 
            id,
            title_ru,
            date,
            time,
            venue_ru,
            is_active
        FROM concerts 
        ORDER BY date DESC
    ');
    $stmt->execute();
    $concerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Управление концертами</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-success {
            background-color: #28a745;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .status {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 14px;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Управление концертами</h1>
        <div>
            <a href="concert-form.php" class="btn">Добавить концерт</a>
            <a href="index.php" class="btn" style="margin-left: 10px;">Назад</a>
        </div>
    </div>
    
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Дата</th>
                    <th>Время</th>
                    <th>Место</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($concerts)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Нет добавленных концертов</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($concerts as $concert): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($concert['title_ru']); ?></td>
                            <td><?php echo date('d.m.Y', strtotime($concert['date'])); ?></td>
                            <td><?php echo date('H:i', strtotime($concert['time'])); ?></td>
                            <td><?php echo htmlspecialchars($concert['venue_ru']); ?></td>
                            <td>
                                <span class="status <?php echo $concert['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $concert['is_active'] ? 'Активен' : 'Неактивен'; ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="concert-form.php?id=<?php echo $concert['id']; ?>" class="btn">
                                    Редактировать
                                </a>
                                <a href="concert-toggle.php?id=<?php echo $concert['id']; ?>" 
                                   class="btn <?php echo $concert['is_active'] ? 'btn-danger' : 'btn-success'; ?>"
                                   onclick="return confirm('Вы уверены?')">
                                    <?php echo $concert['is_active'] ? 'Деактивировать' : 'Активировать'; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 