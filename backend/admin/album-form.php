<?php
require_once '../config.php';

// Проверяем авторизацию
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

$album = [
    'id' => null,
    'title_en' => '',
    'title_uk' => '',
    'title_ru' => '',
    'cover_path' => ''
];

// Если это редактирование существующего альбома
if (isset($_GET['id'])) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT * FROM albums WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $album = $result;
        }
    } catch (PDOException $e) {
        die('Database error: ' . $e->getMessage());
    }
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title_en = $_POST['title_en'] ?? '';
    $title_uk = $_POST['title_uk'] ?? '';
    $title_ru = $_POST['title_ru'] ?? '';
    
    // Загрузка обложки
    $cover_path = $album['cover_path'];
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/gallery/covers/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['cover']['tmp_name'], $upload_path)) {
            // Если это редактирование и была старая обложка, удаляем её
            if ($cover_path && file_exists('../../' . $cover_path)) {
                unlink('../../' . $cover_path);
            }
            $cover_path = 'uploads/gallery/covers/' . $new_filename;
        }
    }
    
    try {
        $conn = getDBConnection();
        if ($album['id']) {
            // Обновление существующего альбома
            $stmt = $conn->prepare("
                UPDATE albums 
                SET title_en = ?, title_uk = ?, title_ru = ?, cover_path = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$title_en, $title_uk, $title_ru, $cover_path, $album['id']]);
        } else {
            // Создание нового альбома
            $stmt = $conn->prepare("
                INSERT INTO albums (title_en, title_uk, title_ru, cover_path, created_at, updated_at)
                VALUES (?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([$title_en, $title_uk, $title_ru, $cover_path]);
        }
        
        header('Location: albums.php');
        exit;
    } catch (PDOException $e) {
        die('Database error: ' . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $album['id'] ? 'Редактировать' : 'Добавить'; ?> альбом - DIBROVA Admin</title>
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
            max-width: 800px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
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
        .current-cover {
            margin-top: 10px;
        }
        .current-cover img {
            max-width: 200px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1><?php echo $album['id'] ? 'Редактировать' : 'Добавить'; ?> альбом</h1>
        <a href="albums.php" class="btn" style="background-color: #6c757d;">← Назад</a>
    </div>
    
    <div class="admin-content">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title_en">Название (EN)</label>
                <input type="text" id="title_en" name="title_en" value="<?php echo htmlspecialchars($album['title_en']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="title_uk">Название (UK)</label>
                <input type="text" id="title_uk" name="title_uk" value="<?php echo htmlspecialchars($album['title_uk']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="title_ru">Название (RU)</label>
                <input type="text" id="title_ru" name="title_ru" value="<?php echo htmlspecialchars($album['title_ru']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="cover">Обложка альбома</label>
                <input type="file" id="cover" name="cover" accept="image/*">
                <?php if ($album['cover_path']): ?>
                    <div class="current-cover">
                        <p>Текущая обложка:</p>
                        <img src="/<?php echo htmlspecialchars($album['cover_path']); ?>" alt="Обложка альбома">
                    </div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn">Сохранить</button>
        </form>
    </div>
</body>
</html> 