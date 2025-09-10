<?php
require_once '../config.php';

// Проверяем авторизацию
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

$album = [
    'id' => null,
    'title_ru' => '',
    'title_et' => '',
    'title_en' => '',
    'title_uk' => '',
    'cover_path' => ''
];

$isEdit = false;
$error = '';
$success = '';

// Если это редактирование, загружаем данные альбома
if (isset($_GET['id'])) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare('SELECT * FROM albums WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $loadedAlbum = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($loadedAlbum) {
            $album = $loadedAlbum;
            $isEdit = true;
        }
    } catch (PDOException $e) {
        $error = 'Ошибка загрузки данных альбома';
    }
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Загрузка обложки
    $uploadedCover = $_FILES['cover'] ?? null;
    $coverPath = $album['cover_path'];

    if ($uploadedCover && $uploadedCover['size'] > 0) {
        $targetDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . "/uploads/gallery/covers/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $imageFileType = strtolower(pathinfo($uploadedCover['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $imageFileType;
        $targetFile = $targetDir . $newFileName;

        // Debug information
        error_log("Processing file upload:");
        error_log("Target directory: " . $targetDir);
        error_log("New filename: " . $newFileName);
        error_log("Target file: " . $targetFile);

        if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png") {
            $error = "Разрешены только JPG, JPEG и PNG файлы.";
        } elseif ($uploadedCover['size'] > 5000000) {
            $error = "Файл слишком большой. Максимальный размер 5MB.";
        } elseif (move_uploaded_file($uploadedCover['tmp_name'], $targetFile)) {
            // Если это редактирование и была старая обложка, удаляем её
            if ($coverPath && file_exists("../" . $coverPath)) {
                unlink("../" . $coverPath);
            }
            // Сохраняем путь относительно корня сайта
            $coverPath = "uploads/gallery/covers/" . $newFileName;
            error_log("File uploaded successfully. Cover path set to: " . $coverPath);
        } else {
            $error = "Ошибка загрузки файла.";
            error_log("Failed to move uploaded file. Upload error code: " . $uploadedCover['error']);
        }
    }

    if (empty($error)) {
        try {
            $conn = getDBConnection();
            
            // Debug information
            error_log("Preparing to save album data:");
            error_log("Cover path before save: " . $coverPath);
            
            if ($isEdit) {
                $sql = "UPDATE albums SET 
                    title_ru = :title_ru,
                    title_et = :title_et,
                    title_en = :title_en,
                    title_uk = :title_uk,
                    cover_path = :cover_path,
                    updated_at = :updated_at
                    WHERE id = :id";
                $params = [
                    'title_ru' => $_POST['title_ru'],
                    'title_et' => $_POST['title_et'],
                    'title_en' => $_POST['title_en'],
                    'title_uk' => $_POST['title_uk'],
                    'cover_path' => $coverPath,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'id' => $album['id']
                ];
            } else {
                $sql = "INSERT INTO albums (
                    title_ru, title_et, title_en, title_uk,
                    cover_path, created_at, updated_at
                ) VALUES (
                    :title_ru, :title_et, :title_en, :title_uk,
                    :cover_path, :created_at, :updated_at
                )";
                $params = [
                    'title_ru' => $_POST['title_ru'],
                    'title_et' => $_POST['title_et'],
                    'title_en' => $_POST['title_en'],
                    'title_uk' => $_POST['title_uk'],
                    'cover_path' => $coverPath,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }

            // Debug information
            error_log("Executing SQL query:");
            error_log($sql);

            $stmt = $conn->prepare($sql);
            if (!$stmt->execute($params)) {
                error_log("Database error: " . implode(", ", $stmt->errorInfo()));
                throw new PDOException("Failed to execute query");
            }

            $success = 'Альбом успешно ' . ($isEdit ? 'обновлен' : 'создан');
            
            // Debug information after successful save
            error_log("Album saved successfully. " . ($isEdit ? "Updated" : "Created new") . " album.");
            
            if (!$isEdit) {
                // Очищаем форму после успешного добавления
                $album = array_fill_keys(array_keys($album), '');
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $error = 'Ошибка сохранения данных: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Редактирование' : 'Создание'; ?> альбома | DIBROVA</title>
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
        input[type="file"] {
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
    </style>
</head>
<body>
    <header class="header">
        <h1><?php echo $isEdit ? 'Редактирование' : 'Создание'; ?> альбома</h1>
        <a href="/admin/albums.php">← Назад</a>
    </header>

    <div class="container">
        <div class="form-container">
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="cover">Обложка альбома</label>
                    <input type="file" id="cover" name="cover" accept="image/jpeg,image/png">
                    <?php if ($album['cover_path']): ?>
                        <div class="current-cover">
                            <p>Текущая обложка:</p>
                            <img src="/<?php echo htmlspecialchars($album['cover_path']); ?>" alt="Обложка альбома">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <div class="form-row">
                        <div>
                            <span class="language-label">RU</span>
                            <label for="title_ru">Название на русском</label>
                            <input type="text" id="title_ru" name="title_ru" value="<?php echo htmlspecialchars($album['title_ru']); ?>" required>
                        </div>
                        <div>
                            <span class="language-label">ET</span>
                            <label for="title_et">Название на эстонском</label>
                            <input type="text" id="title_et" name="title_et" value="<?php echo htmlspecialchars($album['title_et']); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-row">
                        <div>
                            <span class="language-label">EN</span>
                            <label for="title_en">Название на английском</label>
                            <input type="text" id="title_en" name="title_en" value="<?php echo htmlspecialchars($album['title_en']); ?>" required>
                        </div>
                        <div>
                            <span class="language-label">UK</span>
                            <label for="title_uk">Название на украинском</label>
                            <input type="text" id="title_uk" name="title_uk" value="<?php echo htmlspecialchars($album['title_uk']); ?>" required>
                        </div>
                    </div>
                </div>

                <div>
                    <button type="submit" class="btn">Сохранить</button>
                    <a href="/admin/albums.php" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 