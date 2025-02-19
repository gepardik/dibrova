<?php
require_once '../config.php';

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

            $success = 'Концерт успешно ' . ($isEdit ? 'обновлен' : 'добавлен');
            if (!$isEdit) {
                // Очищаем форму после успешного добавления
                $concert = array_fill_keys(array_keys($concert), '');
                $concert['is_active'] = true;
            }
        } catch (PDOException $e) {
            $error = 'Ошибка сохранения данных';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Редактирование' : 'Добавление'; ?> концерта | DIBROVA</title>
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
        input[type="date"],
        input[type="time"],
        textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .checkbox-group input[type="checkbox"] {
            width: auto;
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
        .language-label {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            background-color: #e9ecef;
            border-radius: 4px;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1><?php echo $isEdit ? 'Редактирование' : 'Добавление'; ?> концерта</h1>
        <a href="concerts.php">Вернуться к списку</a>
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
                <!-- Названия -->
                <div class="form-group">
                    <div class="form-row">
                        <div>
                            <span class="language-label">RU</span>
                            <label for="title_ru">Название (Русский)</label>
                            <input type="text" id="title_ru" name="title_ru" required
                                   value="<?php echo htmlspecialchars($concert['title_ru']); ?>">
                        </div>
                        <div>
                            <span class="language-label">ET</span>
                            <label for="title_et">Название (Эстонский)</label>
                            <input type="text" id="title_et" name="title_et" required
                                   value="<?php echo htmlspecialchars($concert['title_et']); ?>">
                        </div>
                        <div>
                            <span class="language-label">EN</span>
                            <label for="title_en">Название (Английский)</label>
                            <input type="text" id="title_en" name="title_en" required
                                   value="<?php echo htmlspecialchars($concert['title_en']); ?>">
                        </div>
                        <div>
                            <span class="language-label">UK</span>
                            <label for="title_uk">Название (Украинский)</label>
                            <input type="text" id="title_uk" name="title_uk" required
                                   value="<?php echo htmlspecialchars($concert['title_uk']); ?>">
                        </div>
                    </div>
                </div>

                <!-- Описания -->
                <div class="form-group">
                    <div class="form-row">
                        <div>
                            <span class="language-label">RU</span>
                            <label for="description_ru">Описание (Русский)</label>
                            <textarea id="description_ru" name="description_ru"><?php echo htmlspecialchars($concert['description_ru']); ?></textarea>
                        </div>
                        <div>
                            <span class="language-label">ET</span>
                            <label for="description_et">Описание (Эстонский)</label>
                            <textarea id="description_et" name="description_et"><?php echo htmlspecialchars($concert['description_et']); ?></textarea>
                        </div>
                        <div>
                            <span class="language-label">EN</span>
                            <label for="description_en">Описание (Английский)</label>
                            <textarea id="description_en" name="description_en"><?php echo htmlspecialchars($concert['description_en']); ?></textarea>
                        </div>
                        <div>
                            <span class="language-label">UK</span>
                            <label for="description_uk">Описание (Украинский)</label>
                            <textarea id="description_uk" name="description_uk"><?php echo htmlspecialchars($concert['description_uk']); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Дата и время -->
                <div class="form-group">
                    <div class="form-row">
                        <div>
                            <label for="date">Дата</label>
                            <input type="date" id="date" name="date" required
                                   value="<?php echo htmlspecialchars($concert['date']); ?>">
                        </div>
                        <div>
                            <label for="time">Время</label>
                            <input type="time" id="time" name="time" required
                                   value="<?php echo htmlspecialchars($concert['time']); ?>">
                        </div>
                    </div>
                </div>

                <!-- Места проведения -->
                <div class="form-group">
                    <div class="form-row">
                        <div>
                            <span class="language-label">RU</span>
                            <label for="venue_ru">Место проведения (Русский)</label>
                            <input type="text" id="venue_ru" name="venue_ru" required
                                   value="<?php echo htmlspecialchars($concert['venue_ru']); ?>">
                        </div>
                        <div>
                            <span class="language-label">ET</span>
                            <label for="venue_et">Место проведения (Эстонский)</label>
                            <input type="text" id="venue_et" name="venue_et" required
                                   value="<?php echo htmlspecialchars($concert['venue_et']); ?>">
                        </div>
                        <div>
                            <span class="language-label">EN</span>
                            <label for="venue_en">Место проведения (Английский)</label>
                            <input type="text" id="venue_en" name="venue_en" required
                                   value="<?php echo htmlspecialchars($concert['venue_en']); ?>">
                        </div>
                        <div>
                            <span class="language-label">UK</span>
                            <label for="venue_uk">Место проведения (Украинский)</label>
                            <input type="text" id="venue_uk" name="venue_uk" required
                                   value="<?php echo htmlspecialchars($concert['venue_uk']); ?>">
                        </div>
                    </div>
                </div>

                <!-- Цена и ссылка на билеты -->
                <div class="form-group">
                    <div class="form-row">
                        <div>
                            <label for="price">Цена</label>
                            <input type="text" id="price" name="price" required
                                   value="<?php echo htmlspecialchars($concert['price']); ?>">
                        </div>
                        <div>
                            <label for="ticket_link">Ссылка на билеты</label>
                            <input type="text" id="ticket_link" name="ticket_link"
                                   value="<?php echo htmlspecialchars($concert['ticket_link']); ?>">
                        </div>
                    </div>
                </div>

                <!-- Изображение -->
                <div class="form-group">
                    <label for="image">Афиша</label>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png">
                    <?php if ($concert['image_path']): ?>
                        <p>Текущее изображение: <?php echo htmlspecialchars($concert['image_path']); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Статус -->
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active"
                               <?php echo $concert['is_active'] ? 'checked' : ''; ?>>
                        <label for="is_active">Активен</label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Сохранить</button>
                    <a href="concerts.php" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 