<?php
require_once '../config.php';

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
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление концертами | DIBROVA</title>
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
        .actions {
            margin-bottom: 2rem;
        }
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: #536C63;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn:hover {
            background-color: #3f524a;
        }
        .concerts-table {
            width: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .concerts-table th,
        .concerts-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .concerts-table th {
            background-color: #f8f8f8;
            font-weight: 600;
            color: #1C2824;
        }
        .status {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
        }
        .status-active {
            background-color: #e6f4ea;
            color: #1e7e34;
        }
        .status-inactive {
            background-color: #feeced;
            color: #dc3545;
        }
        .action-links a {
            color: #536C63;
            text-decoration: none;
            margin-right: 1rem;
        }
        .action-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>Управление концертами</h1>
        <a href="index.php">Вернуться в админ-панель</a>
    </header>

    <div class="container">
        <div class="actions">
            <a href="concert-form.php" class="btn">Добавить концерт</a>
        </div>

        <table class="concerts-table">
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
                        <td class="action-links">
                            <a href="concert-form.php?id=<?php echo $concert['id']; ?>">Редактировать</a>
                            <a href="concert-toggle.php?id=<?php echo $concert['id']; ?>" 
                               onclick="return confirm('Вы уверены?')">
                                <?php echo $concert['is_active'] ? 'Деактивировать' : 'Активировать'; ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($concerts)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Нет добавленных концертов</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 