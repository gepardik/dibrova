<?php
require_once '../config.php';

// Проверяем авторизацию
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Проверяем ID альбома
if (!isset($_GET['id'])) {
    header('Location: albums.php');
    exit;
}

$album_id = $_GET['id'];

// Получаем информацию об альбоме
try {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM albums WHERE id = ?");
    $stmt->execute([$album_id]);
    $album = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$album) {
        header('Location: albums.php');
        exit;
    }
    
    // Получаем видео альбома
    $stmt = $conn->prepare("SELECT * FROM videos WHERE album_id = ? ORDER BY position");
    $stmt->execute([$album_id]);
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}

// Обработка добавления видео
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $youtube_url = $_POST['youtube_url'] ?? '';
    $title_en = $_POST['title_en'] ?? '';
    $title_uk = $_POST['title_uk'] ?? '';
    $title_ru = $_POST['title_ru'] ?? '';
    
    // Извлекаем ID видео из URL YouTube
    $video_id = '';
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $youtube_url, $matches)) {
        $video_id = $matches[1];
    }
    
    if ($video_id) {
        try {
            // Получаем максимальную позицию
            $stmt = $conn->prepare("SELECT MAX(position) as max_pos FROM videos WHERE album_id = ?");
            $stmt->execute([$album_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $position = ($result['max_pos'] ?? 0) + 1;
            
            // Сохраняем видео
            $stmt = $conn->prepare("
                INSERT INTO videos (album_id, youtube_id, title_en, title_uk, title_ru, position, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$album_id, $video_id, $title_en, $title_uk, $title_ru, $position]);
            
            header('Location: album-videos.php?id=' . $album_id);
            exit;
        } catch (PDOException $e) {
            die('Database error: ' . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Видео альбома - DIBROVA Admin</title>
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
        .videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .video-item {
            position: relative;
        }
        .video-preview {
            width: 100%;
            padding-top: 56.25%; /* 16:9 aspect ratio */
            position: relative;
            background: #000;
            border-radius: 4px;
            overflow: hidden;
        }
        .video-preview iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        .video-title {
            margin: 10px 0;
            font-weight: bold;
        }
        .video-actions {
            display: flex;
            gap: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .add-video-form {
            max-width: 600px;
            margin: 0 auto 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>Видео альбома "<?php echo htmlspecialchars($album['title_' . DEFAULT_LANGUAGE]); ?>"</h1>
        <a href="albums.php" class="btn" style="background-color: #6c757d;">← Назад</a>
    </div>
    
    <div class="admin-content">
        <form method="POST" class="add-video-form">
            <h2>Добавить видео</h2>
            <div class="form-group">
                <label for="youtube_url">URL видео на YouTube</label>
                <input type="text" id="youtube_url" name="youtube_url" required 
                       placeholder="https://www.youtube.com/watch?v=...">
            </div>
            
            <div class="form-group">
                <label for="title_en">Название (EN)</label>
                <input type="text" id="title_en" name="title_en" required>
            </div>
            
            <div class="form-group">
                <label for="title_uk">Название (UK)</label>
                <input type="text" id="title_uk" name="title_uk" required>
            </div>
            
            <div class="form-group">
                <label for="title_ru">Название (RU)</label>
                <input type="text" id="title_ru" name="title_ru" required>
            </div>
            
            <button type="submit" class="btn">Добавить видео</button>
        </form>
        
        <?php if (empty($videos)): ?>
            <p>В альбоме пока нет видео.</p>
        <?php else: ?>
            <div class="videos-grid">
                <?php foreach ($videos as $video): ?>
                    <div class="video-item">
                        <div class="video-preview">
                            <iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($video['youtube_id']); ?>" 
                                    allowfullscreen></iframe>
                        </div>
                        <div class="video-title">
                            <?php echo htmlspecialchars($video['title_' . DEFAULT_LANGUAGE]); ?>
                        </div>
                        <div class="video-actions">
                            <a href="video-delete.php?id=<?php echo $video['id']; ?>&album_id=<?php echo $album_id; ?>" 
                               class="btn" 
                               style="background-color: #dc3545;"
                               onclick="return confirm('Вы уверены, что хотите удалить это видео?')">Удалить</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 