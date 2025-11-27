<?php
require_once 'config.php';

// Получаем все категории с оборудованием
$categoriesQuery = $pdo->query("
    SELECT c.id, c.name, c.slug 
    FROM categories c 
    WHERE EXISTS (SELECT 1 FROM equipment e WHERE e.category_id = c.id)
    ORDER BY c.id
");
$categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);

// Для каждой категории получаем оборудование
$equipmentByCategory = [];
foreach ($categories as $category) {
    $equipmentQuery = $pdo->prepare("
        SELECT id, name, slug, brand, model, image_url 
        FROM equipment 
        WHERE category_id = ? 
        ORDER BY name
    ");
    $equipmentQuery->execute([$category['id']]);
    $equipmentByCategory[$category['slug']] = $equipmentQuery->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аренда оборудования для мероприятий</title>
    <link rel="icon" href="img/logo.png" type="image/png">
    <link rel="apple-touch-icon" href="img/logo.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Все ваши существующие стили остаются без изменений */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            color: #000000;
            line-height: 1.6;
            background-color: #f9f9f9;
            padding-top: 70px;
            background-color: #5E5858;
        }
        
        /* Стили для карточек товаров */
        .prod-col {
            padding: 10px;
            margin-bottom: 20px;
        }
        
        .product-bg {
            height: 200px;
            width: 250px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            position: relative;
            border-radius: 4px;
            transition: all 0.3s ease;
            display: flex;
            align-items: flex-end;
            background-color: #f5f5f5;
            cursor: pointer;
        }
        
        .s-black::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 50%);
            border-radius: 4px;
        }
        
        .new-prod-head {
            position: relative;
            width: 100%;
            padding: 15px;
            z-index: 1;
        }
        
        .brand-in {
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .new-prod-title {
            color: #fff;
            font-size: 14px;
            font-weight: 400;
            margin: 0;
            line-height: 1.3;
        }
        
        .product-bg:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .section-title {
            margin: 40px 0 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #444;
            color: white;
        }
        
        .row-flex-wrap {
            display: flex;
            flex-wrap: wrap;
            margin-right: -10px;
            margin-left: -10px;
        }

        /* Остальные ваши стили... */
    </style>
</head>
<body>
    <!-- Ваш хедер остается без изменений -->
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

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-1 col-sm-1 none-mob"></div>
            <div class="col-sm-10 col-md-10 col-xs-12">
                <?php foreach ($categories as $category): ?>
                    <?php if (!empty($equipmentByCategory[$category['slug']])): ?>
                        <section id="<?= $category['slug'] ?>">
                            <h2 class="section-title"><?= htmlspecialchars($category['name']) ?></h2>
                            <div class="row row-flex-wrap">
                                <?php foreach ($equipmentByCategory[$category['slug']] as $item): ?>
                                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 prod-col">
                                        <div class="s-black product-bg" 
                                             style="background-image: url('<?= htmlspecialchars($item['image_url']) ?>');"
                                             onclick="window.location.href='equipment-detail.php?slug=<?= $item['slug'] ?>'">
                                            <div class="new-prod-head">
                                                <?php if ($item['brand']): ?>
                                                    <div class="brand-in"><?= htmlspecialchars($item['brand']) ?></div>
                                                <?php endif; ?>
                                                <div class="title new-prod-title"><?= htmlspecialchars($item['name']) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="col-lg-1 col-sm-1 none-mob"></div>
        </div>
    </div>

    <!-- Футер остается без изменений -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Контакты</h3>
                    <p>Уфа, Ленина 70</p>
                    <p>+7 (993) 068-58-05</p>
                    <p>S7t.pro@yandex.ru</p>
                </div>
                <div class="footer-section">
                    <h3>Соцсети</h3>
                    <div class="social-icons">
                        <a href="https://vk.com/zvukpodkluch?t2fs=01cdc6cbb572e7546e_3" target="_blank">
                            <i class="fab fa-vk"></i>
                        </a>
                        <a href="https://www.instagram.com/s.event.pro/">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Ваш существующий JavaScript код для меню
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