<?php
require_once '../config.php';

// Проверяем авторизацию
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Проверяем наличие album_id
if (!isset($_GET['album_id'])) {
    header('Location: albums.php');
    exit;
}

$video = [
    'id' => null,
    'album_id' => $_GET['album_id'],
    'youtube_id' => '',
    'title_ru' => '',
    'title_et' => '',
    'title_en' => '',
    'title_uk' => '',
    'position' => 0
];

$isEdit = false;
$error = '';
$success = '';

// Если это редактирование, загружаем данные видео
if (isset($_GET['id'])) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare('SELECT * FROM videos WHERE id = ? AND album_id = ?');
        $stmt->execute([$_GET['id'], $_GET['album_id']]);
        $loadedVideo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($loadedVideo) {
            $video = $loadedVideo;
            $isEdit = true;
        }
    } catch (PDOException $e) {
        $error = 'Ошибка загрузки данных видео';
    }
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn = getDBConnection();
        
        $params = [
            'album_id' => $video['album_id'],
            'youtube_id' => $_POST['youtube_id'],
            'title_ru' => $_POST['title_ru'],
            'title_et' => $_POST['title_et'],
            'title_en' => $_POST['title_en'],
            'title_uk' => $_POST['title_uk'],
            'position' => $_POST['position'],
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($isEdit) {
            $sql = "UPDATE videos SET 
                youtube_id = :youtube_id,
                title_ru = :title_ru,
                title_et = :title_et,
                title_en = :title_en,
                title_uk = :title_uk,
                position = :position
                WHERE id = :id AND album_id = :album_id";
            $params['id'] = $video['id'];
        } else {
            $sql = "INSERT INTO videos (
                album_id, youtube_id,
                title_ru, title_et, title_en, title_uk,
                position, created_at
            ) VALUES (
                :album_id, :youtube_id,
                :title_ru, :title_et, :title_en, :title_uk,
                :position, :created_at
            )";
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        $success = 'Видео успешно ' . ($isEdit ? 'обновлено' : 'добавлено');
        if (!$isEdit) {
            // Очищаем форму после успешного добавления
            $video['youtube_id'] = '';
            $video['title_ru'] = '';
            $video['title_et'] = '';
            $video['title_en'] = '';
            $video['title_uk'] = '';
        }
    } catch (PDOException $e) {
        $error = 'Ошибка сохранения данных';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Редактирование' : 'Добавление'; ?> видео | DIBROVA</title>
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
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #1C2824;
            font-weight: 500;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .language-label {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            background-color: #e9ecef;
            border-radius: 4px;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
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
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .error {
            color: #dc3545;
            margin-bottom: 1rem;
        }
        .success {
            color: #28a745;
            margin-bottom: 1rem;
        }
        .youtube-preview {
            margin-top: 1rem;
            aspect-ratio: 16/9;
            max-width: 100%;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            overflow: hidden;
        }
        .youtube-preview iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1><?php echo $isEdit ? 'Редактирование' : 'Добавление'; ?> видео</h1>
        <a href="album-videos.php?id=<?php echo htmlspecialchars($video['album_id']); ?>">Вернуться к списку</a>
    </header>

    <div class="container">
        <div class="form-container">
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST">
                <!-- YouTube ID -->
                <div class="form-group">
                    <label for="youtube_id">ID видео на YouTube</label>
                    <input type="text" id="youtube_id" name="youtube_id" required
                           value="<?php echo htmlspecialchars($video['youtube_id']); ?>"
                           placeholder="Например: dQw4w9WgXcQ">
                    <?php if ($video['youtube_id']): ?>
                        <div class="youtube-preview">
                            <iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($video['youtube_id']); ?>"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Названия -->
                <div class="form-group">
                    <div class="form-row">
                        <div>
                            <span class="language-label">RU</span>
                            <label for="title_ru">Название (Русский)</label>
                            <input type="text" id="title_ru" name="title_ru" required
                                   value="<?php echo htmlspecialchars($video['title_ru']); ?>">
                        </div>
                        <div>
                            <span class="language-label">ET</span>
                            <label for="title_et">Название (Эстонский)</label>
                            <input type="text" id="title_et" name="title_et" required
                                   value="<?php echo htmlspecialchars($video['title_et']); ?>">
                        </div>
                        <div>
                            <span class="language-label">EN</span>
                            <label for="title_en">Название (Английский)</label>
                            <input type="text" id="title_en" name="title_en" required
                                   value="<?php echo htmlspecialchars($video['title_en']); ?>">
                        </div>
                        <div>
                            <span class="language-label">UK</span>
                            <label for="title_uk">Название (Украинский)</label>
                            <input type="text" id="title_uk" name="title_uk" required
                                   value="<?php echo htmlspecialchars($video['title_uk']); ?>">
                        </div>
                    </div>
                </div>

                <!-- Позиция -->
                <div class="form-group">
                    <label for="position">Позиция</label>
                    <input type="number" id="position" name="position" required
                           value="<?php echo htmlspecialchars($video['position']); ?>"
                           min="0" step="1">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Сохранить</button>
                    <a href="album-videos.php?id=<?php echo htmlspecialchars($video['album_id']); ?>" 
                       class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 