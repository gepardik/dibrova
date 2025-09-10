<?php
require_once '../config.php';

// Проверяем авторизацию
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Получаем список альбомов
try {
    $conn = getDBConnection();
    $stmt = $conn->prepare("
        SELECT 
            a.*,
            (SELECT COUNT(*) FROM photos WHERE album_id = a.id) as photos_count,
            (SELECT COUNT(*) FROM videos WHERE album_id = a.id) as videos_count
        FROM albums a
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Управление альбомами - DIBROVA Admin</title>
    <base href="/admin/">
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
        .btn {
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
        .albums-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .albums-table th,
        .albums-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .albums-table th {
            background-color: #f8f9fa;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>Управление альбомами</h1>
        <div>
            <a href="/admin/index.php" class="btn" style="background-color: #6c757d;">← Назад</a>
            <a href="/admin/album-form.php" class="btn">+ Добавить альбом</a>
        </div>
    </div>
    
    <div class="admin-content">
        <?php if (empty($albums)): ?>
            <p>Альбомов пока нет.</p>
        <?php else: ?>
            <table class="albums-table">
                <thead>
                    <tr>
                        <th>Название (EN)</th>
                        <th>Название (UK)</th>
                        <th>Фото</th>
                        <th>Видео</th>
                        <th>Дата создания</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($albums as $album): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($album['title_en']); ?></td>
                            <td><?php echo htmlspecialchars($album['title_uk']); ?></td>
                            <td><?php echo $album['photos_count']; ?></td>
                            <td><?php echo $album['videos_count']; ?></td>
                            <td><?php echo date('d.m.Y', strtotime($album['created_at'])); ?></td>
                            <td class="action-buttons">
                                <a href="/admin/album-photos.php?id=<?php echo $album['id']; ?>" class="btn">Фото</a>
                                <a href="/admin/album-videos.php?id=<?php echo $album['id']; ?>" class="btn">Видео</a>
                                <a href="/admin/album-form.php?id=<?php echo $album['id']; ?>" class="btn">Изменить</a>
                                <a href="/admin/album-delete.php?id=<?php echo $album['id']; ?>" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить этот альбом?');">Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html> 