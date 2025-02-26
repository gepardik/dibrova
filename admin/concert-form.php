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

$concert = [
    'id' => null,
    'title_ru' => '',
    'title_et' => '',
    'title_en' => '',
    'title_uk' => '',
    'description_ru' => '',
    'description_et' => '',
    'description_en' => '',
    'description_uk' => '',
    'date' => '',
    'time' => '',
    'venue_ru' => '',
    'venue_et' => '',
    'venue_en' => '',
    'venue_uk' => '',
    'price' => '',
    'ticket_link' => '',
    'image_path' => '',
    'is_active' => true
];

$isEdit = false;
$error = '';
$success = '';

// Если это редактирование, загружаем данные концерта
if (isset($_GET['id'])) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare('SELECT * FROM concerts WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $loadedConcert = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($loadedConcert) {
            $concert = $loadedConcert;
            $isEdit = true;
        }
    } catch (PDOException $e) {
        $error = 'Ошибка загрузки данных концерта';
    }
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Загрузка изображения
    $uploadedImage = $_FILES['image'] ?? null;
    $imagePath = $concert['image_path'];

    if ($uploadedImage && $uploadedImage['size'] > 0) {
        $targetDir = "../uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $imageFileType = strtolower(pathinfo($uploadedImage['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $imageFileType;
        $targetFile = $targetDir . $newFileName;

        if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png") {
            $error = "Разрешены только JPG, JPEG и PNG файлы.";
        } elseif ($uploadedImage['size'] > 5000000) {
            $error = "Файл слишком большой. Максимальный размер 5MB.";
        } elseif (move_uploaded_file($uploadedImage['tmp_name'], $targetFile)) {
            $imagePath = "uploads/" . $newFileName;
        } else {
            $error = "Ошибка загрузки файла.";
        }
    }

    if (empty($error)) {
        try {
            $conn = getDBConnection();
            
            $params = [
                'title_ru' => $_POST['title_ru'],
                'title_et' => $_POST['title_et'],
                'title_en' => $_POST['title_en'],
                'title_uk' => $_POST['title_uk'],
                'description_ru' => $_POST['description_ru'],
                'description_et' => $_POST['description_et'],
                'description_en' => $_POST['description_en'],
                'description_uk' => $_POST['description_uk'],
                'date' => $_POST['date'],
                'time' => $_POST['time'],
                'venue_ru' => $_POST['venue_ru'],
                'venue_et' => $_POST['venue_et'],
                'venue_en' => $_POST['venue_en'],
                'venue_uk' => $_POST['venue_uk'],
                'price' => $_POST['price'],
                'ticket_link' => $_POST['ticket_link'],
                'image_path' => $imagePath,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if ($isEdit) {
                $sql = "UPDATE concerts SET 
                    title_ru = :title_ru,
                    title_et = :title_et,
                    title_en = :title_en,
                    title_uk = :title_uk,
                    description_ru = :description_ru,
                    description_et = :description_et,
                    description_en = :description_en,
                    description_uk = :description_uk,
                    date = :date,
                    time = :time,
                    venue_ru = :venue_ru,
                    venue_et = :venue_et,
                    venue_en = :venue_en,
                    venue_uk = :venue_uk,
                    price = :price,
                    ticket_link = :ticket_link,
                    image_path = :image_path,
                    is_active = :is_active
                    WHERE id = :id";
                $params['id'] = $concert['id'];
            } else {
                $sql = "INSERT INTO concerts (
                    title_ru, title_et, title_en, title_uk,
                    description_ru, description_et, description_en, description_uk,
                    date, time,
                    venue_ru, venue_et, venue_en, venue_uk,
                    price, ticket_link, image_path, is_active
                ) VALUES (
                    :title_ru, :title_et, :title_en, :title_uk,
                    :description_ru, :description_et, :description_en, :description_uk,
                    :date, :time,
                    :venue_ru, :venue_et, :venue_en, :venue_uk,
                    :price, :ticket_link, :image_path, :is_active
                )";
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            header('Location: concerts.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Ошибка сохранения данных: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $isEdit ? 'Редактирование' : 'Добавление'; ?> концерта</title>
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
        .form-group {
            margin-bottom: 15px;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="date"],
        input[type="time"],
        input[type="url"],
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
            resize: vertical;
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
        .btn-secondary {
            background-color: #6c757d;
        }
        .error {
            color: #dc3545;
            margin-bottom: 15px;
        }
        .success {
            color: #28a745;
            margin-bottom: 15px;
        }
        .language-label {
            display: inline-block;
            padding: 2px 6px;
            background-color: #e9ecef;
            border-radius: 3px;
            font-size: 12px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo $isEdit ? 'Редактирование' : 'Добавление'; ?> концерта</h1>
        <a href="concerts.php" class="btn btn-secondary">Назад</a>
    </div>
    
    <div class="content">
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <h3>Название концерта</h3>
            <div class="form-row">
                <div class="form-group">
                    <span class="language-label">RU</span>
                    <input type="text" name="title_ru" value="<?php echo htmlspecialchars($concert['title_ru']); ?>" required>
                </div>
                <div class="form-group">
                    <span class="language-label">ET</span>
                    <input type="text" name="title_et" value="<?php echo htmlspecialchars($concert['title_et']); ?>" required>
                </div>
                <div class="form-group">
                    <span class="language-label">EN</span>
                    <input type="text" name="title_en" value="<?php echo htmlspecialchars($concert['title_en']); ?>" required>
                </div>
                <div class="form-group">
                    <span class="language-label">UK</span>
                    <input type="text" name="title_uk" value="<?php echo htmlspecialchars($concert['title_uk']); ?>" required>
                </div>
            </div>

            <h3>Описание</h3>
            <div class="form-row">
                <div class="form-group">
                    <span class="language-label">RU</span>
                    <textarea name="description_ru"><?php echo htmlspecialchars($concert['description_ru']); ?></textarea>
                </div>
                <div class="form-group">
                    <span class="language-label">ET</span>
                    <textarea name="description_et"><?php echo htmlspecialchars($concert['description_et']); ?></textarea>
                </div>
                <div class="form-group">
                    <span class="language-label">EN</span>
                    <textarea name="description_en"><?php echo htmlspecialchars($concert['description_en']); ?></textarea>
                </div>
                <div class="form-group">
                    <span class="language-label">UK</span>
                    <textarea name="description_uk"><?php echo htmlspecialchars($concert['description_uk']); ?></textarea>
                </div>
            </div>

            <h3>Место проведения</h3>
            <div class="form-row">
                <div class="form-group">
                    <span class="language-label">RU</span>
                    <input type="text" name="venue_ru" value="<?php echo htmlspecialchars($concert['venue_ru']); ?>" required>
                </div>
                <div class="form-group">
                    <span class="language-label">ET</span>
                    <input type="text" name="venue_et" value="<?php echo htmlspecialchars($concert['venue_et']); ?>" required>
                </div>
                <div class="form-group">
                    <span class="language-label">EN</span>
                    <input type="text" name="venue_en" value="<?php echo htmlspecialchars($concert['venue_en']); ?>" required>
                </div>
                <div class="form-group">
                    <span class="language-label">UK</span>
                    <input type="text" name="venue_uk" value="<?php echo htmlspecialchars($concert['venue_uk']); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Дата</label>
                    <input type="date" name="date" value="<?php echo htmlspecialchars($concert['date']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Время</label>
                    <input type="time" name="time" value="<?php echo htmlspecialchars($concert['time']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Цена</label>
                    <input type="text" name="price" value="<?php echo htmlspecialchars($concert['price']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Ссылка на билеты</label>
                    <input type="url" name="ticket_link" value="<?php echo htmlspecialchars($concert['ticket_link']); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Афиша</label>
                    <input type="file" name="image" accept="image/jpeg,image/png">
                    <?php if ($concert['image_path']): ?>
                        <p>Текущее изображение: <?php echo htmlspecialchars($concert['image_path']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" <?php echo $concert['is_active'] ? 'checked' : ''; ?>>
                        Активен
                    </label>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn">Сохранить</button>
                <a href="concerts.php" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html> 