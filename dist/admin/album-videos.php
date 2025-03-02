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
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Видео альбома | DIBROVA</title>
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
    <header class="header">
        <h1>Видео альбома: <?php echo htmlspecialchars($album['title_ru']); ?></h1>
        <a href="/admin/albums.php">← Назад</a>
    </header>

    <div class="container">
        <div class="form-section">
            <h2>Добавить видео</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="youtube_url">Ссылка на YouTube видео</label>
                    <input type="text" id="youtube_url" name="youtube_url" required>
                </div>
                <div class="form-group">
                    <div class="form-row">
                        <div>
                            <span class="language-label">RU</span>
                            <label for="title_ru">Название на русском</label>
                            <input type="text" id="title_ru" name="title_ru" required>
                        </div>
                        <div>
                            <span class="language-label">ET</span>
                            <label for="title_et">Название на эстонском</label>
                            <input type="text" id="title_et" name="title_et" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-row">
                        <div>
                            <span class="language-label">EN</span>
                            <label for="title_en">Название на английском</label>
                            <input type="text" id="title_en" name="title_en" required>
                        </div>
                        <div>
                            <span class="language-label">UK</span>
                            <label for="title_uk">Название на украинском</label>
                            <input type="text" id="title_uk" name="title_uk" required>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn">Добавить</button>
            </form>
        </div>

        <div class="videos-grid">
            <?php foreach ($videos as $video): ?>
                <div class="video-item">
                    <div class="video-preview">
                        <img src="https://img.youtube.com/vi/<?php echo htmlspecialchars($video['youtube_id']); ?>/mqdefault.jpg" alt="Превью видео">
                    </div>
                    <div class="video-info">
                        <h3><?php echo htmlspecialchars($video['title_ru']); ?></h3>
                        <div class="video-actions">
                            <a href="https://www.youtube.com/watch?v=<?php echo htmlspecialchars($video['youtube_id']); ?>" target="_blank" class="btn">Смотреть</a>
                            <a href="/admin/video-delete.php?id=<?php echo $video['id']; ?>&album_id=<?php echo $album_id; ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('Вы уверены, что хотите удалить это видео?');">Удалить</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html> 