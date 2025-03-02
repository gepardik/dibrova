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
    
    // Получаем фотографии альбома
    $stmt = $conn->prepare("SELECT * FROM photos WHERE album_id = ? ORDER BY position");
    $stmt->execute([$album_id]);
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}

// Обработка загрузки фотографий
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photos'])) {
    $upload_dir = '../uploads/gallery/';
    $dirs = ['original', 'medium', 'thumbnails'];
    
    // Создаем директории, если они не существуют
    foreach ($dirs as $dir) {
        if (!file_exists($upload_dir . $dir)) {
            mkdir($upload_dir . $dir, 0777, true);
        }
    }
    
    // Получаем максимальную позицию
    $stmt = $conn->prepare("SELECT MAX(position) as max_pos FROM photos WHERE album_id = ?");
    $stmt->execute([$album_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $position = ($result['max_pos'] ?? 0) + 1;
    
    // Обрабатываем каждую загруженную фотографию
    foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
            $file_extension = strtolower(pathinfo($_FILES['photos']['name'][$key], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            
            // Пути для разных размеров
            $original_path = $upload_dir . 'original/' . $new_filename;
            $medium_path = $upload_dir . 'medium/' . $new_filename;
            $thumb_path = $upload_dir . 'thumbnails/' . $new_filename;
            
            // Загружаем оригинал
            if (move_uploaded_file($tmp_name, $original_path)) {
                // Создаем средний размер (800px по большей стороне)
                resizeImage($original_path, $medium_path, 800);
                
                // Создаем миниатюру (200px по большей стороне)
                resizeImage($original_path, $thumb_path, 200);
                
                // Сохраняем в базу с ведущим слешем
                $stmt = $conn->prepare("
                    INSERT INTO photos (album_id, original_path, medium_path, thumbnail_path, position, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $album_id,
                    'uploads/gallery/original/' . $new_filename,
                    'uploads/gallery/medium/' . $new_filename,
                    'uploads/gallery/thumbnails/' . $new_filename,
                    $position++
                ]);
            }
        }
    }
    
    header('Location: album-photos.php?id=' . $album_id);
    exit;
}

// Функция для изменения размера изображения
function resizeImage($source_path, $target_path, $max_size) {
    list($width, $height) = getimagesize($source_path);
    
    // Вычисляем новые размеры
    if ($width > $height) {
        $new_width = $max_size;
        $new_height = floor($height * ($max_size / $width));
    } else {
        $new_height = $max_size;
        $new_width = floor($width * ($max_size / $height));
    }
    
    // Создаем новое изображение
    $source = imagecreatefromstring(file_get_contents($source_path));
    $target = imagecreatetruecolor($new_width, $new_height);
    
    // Сохраняем прозрачность для PNG
    if (pathinfo($source_path, PATHINFO_EXTENSION) === 'png') {
        imagealphablending($target, false);
        imagesavealpha($target, true);
    }
    
    // Изменяем размер
    imagecopyresampled($target, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    // Сохраняем изображение
    $extension = strtolower(pathinfo($source_path, PATHINFO_EXTENSION));
    if ($extension === 'jpg' || $extension === 'jpeg') {
        imagejpeg($target, $target_path, 90);
    } elseif ($extension === 'png') {
        imagepng($target, $target_path, 9);
    }
    
    // Освобождаем память
    imagedestroy($source);
    imagedestroy($target);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Фотографии альбома | DIBROVA</title>
    <base href="/admin/">
    <link rel="stylesheet" href="/photoswipe/photoswipe.css">
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
        .photos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .photo-item {
            position: relative;
            cursor: pointer;
        }
        .photo-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }
        .photo-actions {
            position: absolute;
            top: 5px;
            right: 5px;
            display: flex;
            gap: 5px;
        }
        .photo-actions button {
            padding: 5px 10px;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .upload-form {
            margin-bottom: 20px;
        }
        #photos {
            display: none;
        }
        .upload-label {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            padding: 20px;
            box-sizing: border-box;
        }
        .modal-content {
            max-width: 90%;
            max-height: 90vh;
            margin: auto;
            display: block;
            position: relative;
            top: 50%;
            transform: translateY(-50%);
        }
        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            color: #fff;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
        }
        .modal img {
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
        }
        .pswp {
            --pswp-bg: rgba(0, 0, 0, 0.9);
            --pswp-placeholder-bg: #222;
            position: fixed;
            z-index: 9999;
        }
        
        .photo-item a {
            display: block;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>Фотографии альбома: <?php echo htmlspecialchars($album['title_ru']); ?></h1>
        <a href="/admin/albums.php">← Назад</a>
    </header>

    <div class="container">
        <div class="upload-section">
            <h2>Загрузить фотографии</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="photos[]" multiple accept="image/jpeg,image/png" required>
                <button type="submit" class="btn">Загрузить</button>
            </form>
        </div>

        <div class="photos-grid" id="gallery">
            <?php foreach ($photos as $photo): ?>
                <div class="photo-item">
                    <a href="/<?php echo htmlspecialchars($photo['original_path']); ?>"
                       data-pswp-width="<?php list($width, $height) = getimagesize($_SERVER['DOCUMENT_ROOT'] . '/' . $photo['original_path']); echo $width; ?>"
                       data-pswp-height="<?php echo $height; ?>">
                        <img src="/<?php echo htmlspecialchars($photo['medium_path']); ?>" alt="Фото">
                    </a>
                    <div class="photo-actions">
                        <a href="/admin/photo-delete.php?id=<?php echo $photo['id']; ?>&album_id=<?php echo $album_id; ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Вы уверены, что хотите удалить это фото?');">Удалить</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- PhotoSwipe template -->
    <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="pswp__bg"></div>
        <div class="pswp__scroll-wrap">
            <div class="pswp__container">
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
            </div>
            <div class="pswp__ui pswp__ui--hidden">
                <div class="pswp__top-bar">
                    <div class="pswp__counter"></div>
                    <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                    <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                    <div class="pswp__preloader">
                        <div class="pswp__preloader__icn">
                            <div class="pswp__preloader__cut">
                                <div class="pswp__preloader__donut"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
                <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
                <div class="pswp__caption">
                    <div class="pswp__caption__center"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="/photoswipe/umd/photoswipe.umd.js"></script>
    <script src="/photoswipe/umd/photoswipe-lightbox.umd.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lightbox = new PhotoSwipeLightbox({
                gallery: '#gallery',
                children: 'a',
                pswpModule: PhotoSwipe,
                
                // Basic options
                showHideAnimationType: 'fade',
                showAnimationDuration: 300,
                hideAnimationDuration: 300,
                
                // UI Elements
                arrowKeys: true,
                imageClickAction: 'zoom',
                tapAction: 'toggle-controls',
                
                // Show UI controls
                showPrevNextButtons: true,
                showZoomButton: true,
                showCounter: true,
                
                // Background opacity
                bgOpacity: 0.9,
                
                // Padding
                padding: { top: 20, bottom: 20, left: 20, right: 20 },
                
                // Error message
                errorMsg: 'Изображение не может быть загружено'
            });

            // Handle image loading
            lightbox.on('contentLoad', (e) => {
                const { content } = e;
                if (content.type === 'image') {
                    const img = new Image();
                    img.onload = () => {
                        content.width = img.naturalWidth;
                        content.height = img.naturalHeight;
                    };
                    img.src = content.data.src;
                }
            });

            lightbox.init();
        });
    </script>
</body>
</html> 