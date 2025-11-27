<?php
require_once 'config.php';

if (!isset($_GET['slug'])) {
    header('Location: arenda.php');
    exit;
}

$slug = $_GET['slug'];
$stmt = $pdo->prepare("
    SELECT e.*, c.name as category_name 
    FROM equipment e 
    LEFT JOIN categories c ON e.category_id = c.id 
    WHERE e.slug = ?
");
$stmt->execute([$slug]);
$equipment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$equipment) {
    header('Location: arenda.php');
    exit;
}

// Декодируем спецификации из JSON
$specifications = $equipment['specifications'] ? json_decode($equipment['specifications'], true) : [];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($equipment['name']) ?> - Аренда оборудования</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #5E5858;
            color: white;
            padding-top: 80px;
        }
        
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px;
            transition: background 0.3s;
        }
        
        .back-button:hover {
            background: #0056b3;
        }
        
        .equipment-detail {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            margin-bottom: 40px;
        }
        
        .detail-header {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            margin-bottom: 30px;
        }
        
        .equipment-image {
            flex: 1;
            min-width: 300px;
            max-width: 500px;
        }
        
        .equipment-image img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .equipment-info {
            flex: 2;
            min-width: 300px;
        }
        
        .equipment-title {
            font-size: 2.5em;
            margin-bottom: 10px;
            color: white;
        }
        
        .equipment-brand {
            font-size: 1.5em;
            color: #ccc;
            margin-bottom: 20px;
        }
        
        .equipment-price {
            font-size: 2em;
            color: #4CAF50;
            margin: 20px 0;
        }
        
        .specifications {
            background: rgba(0,0,0,0.3);
            padding: 25px;
            border-radius: 8px;
            margin: 25px 0;
        }
        
        .specifications h3 {
            margin-bottom: 15px;
            color: #fff;
        }
        
        .spec-list {
            list-style: none;
        }
        
        .spec-item {
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: space-between;
        }
        
        .spec-name {
            font-weight: bold;
            color: #ccc;
        }
        
        .spec-value {
            color: white;
        }
        
        .description {
            line-height: 1.6;
            font-size: 1.1em;
            margin: 25px 0;
        }
        
        @media (max-width: 768px) {
            .detail-header {
                flex-direction: column;
            }
            
            .equipment-title {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <!-- Хедер (такой же как в arenda.php) -->
    <header class="header">
        <div class="header-container">
            <a href="index.html" class="logo">
                <img src="img/logo.png" alt="Звук под ключ" class="logo-img">
            </a>
            
            <nav class="nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="#" class="nav-link">Проекты</a>
                    </li>
                    <li class="nav-item">
                        <a href="index.html#services" class="nav-link">Услуги</a>
                    </li>
                    <li class="nav-item">
                        <div class="nav-link dropdown-toggle">Оборудование <i class="fas fa-chevron-down dropdown-icon"></i></div>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="arenda.php" class="dropdown-link">Звуковое</a></li>
                            <li class="dropdown-item"><a href="svet.html" class="dropdown-link">Световое</a></li>
                            <li class="dropdown-item"><a href="led.html" class="dropdown-link">Видео</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">О компании</a>
                    </li>
                    <li class="nav-item">
                        <a href="https://taplink.cc/s_event_rental" class="nav-link">Контакты</a>
                    </li>
                </ul>
            </nav>
            
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>

    <a href="arenda.php" class="back-button">
        <i class="fas fa-arrow-left"></i> Назад к каталогу
    </a>

    <div class="equipment-detail">
        <div class="detail-header">
            <div class="equipment-image">
                <img src="<?= htmlspecialchars($equipment['image_url']) ?>" alt="<?= htmlspecialchars($equipment['name']) ?>">
            </div>
            <div class="equipment-info">
                <h1 class="equipment-title"><?= htmlspecialchars($equipment['name']) ?></h1>
                <?php if ($equipment['brand']): ?>
                    <div class="equipment-brand">Производитель: <?= htmlspecialchars($equipment['brand']) ?></div>
                <?php endif; ?>
                <?php if ($equipment['model']): ?>
                    <div class="equipment-model">Модель: <?= htmlspecialchars($equipment['model']) ?></div>
                <?php endif; ?>
                
                <?php if ($equipment['price']): ?>
                    <div class="equipment-price"><?= number_format($equipment['price'], 2, ',', ' ') ?> руб./сутки</div>
                <?php endif; ?>
                
                <div class="description">
                    <h3>Описание</h3>
                    <p><?= nl2br(htmlspecialchars($equipment['description'] ?: 'Описание временно отсутствует')) ?></p>
                </div>
            </div>
        </div>
        
        <?php if (!empty($specifications)): ?>
        <div class="specifications">
            <h3>Технические характеристики</h3>
            <ul class="spec-list">
                <?php foreach ($specifications as $key => $value): ?>
                    <li class="spec-item">
                        <span class="spec-name"><?= htmlspecialchars($key) ?>:</span>
                        <span class="spec-value"><?= htmlspecialchars($value) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // JavaScript для меню (такой же как в arenda.php)
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
        
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const parentItem = this.parentElement;
                const dropdownMenu = this.nextElementSibling;
                
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu !== dropdownMenu) {
                        menu.classList.remove('active');
                        menu.previousElementSibling.parentElement.classList.remove('active');
                    }
                });
                
                parentItem.classList.toggle('active');
                dropdownMenu.classList.toggle('active');
                
                if (window.innerWidth <= 768) {
                    dropdownMenu.classList.toggle('show');
                }
            });
        });
        
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.nav-item')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('active');
                    menu.previousElementSibling.parentElement.classList.remove('active');
                });
            }
        });
        
        const menuToggle = document.querySelector('.menu-toggle');
        const nav = document.querySelector('.nav');
        
        menuToggle.addEventListener('click', () => {
            nav.classList.toggle('active');
        });
    </script>
</body>
</html>