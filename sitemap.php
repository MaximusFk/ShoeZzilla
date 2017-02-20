<!DOCTYPE html>
<html>
    <head>
        <?php
            include './templates/head.html';
        ?>
    </head>
    <body>
        <section class="main">
            <?php
                include_once './scripts/twig.php';
                $loader = create_file_loader();
                $twig = create_twig($loader);
                echo $twig->render('header.html');
                echo $twig->render('navigation.html');
            ?>
            <div class="content content_block">
                <ul>
                    <li><a href="/">Главная</a></li>
                    <li><a href="/about/">О нас</a></li>
                    <li><a href="/contacts/">Контакты</a></li>
                    <li>
                        <a href="/shoes/catalog.php">Каталог</a>
                        <ul>
                            <?php
                                include_once './scripts/items/item_db.php';
                                $items = get_raw_items();
                                foreach ($items as $item) {
                                    echo '<li><a href="/shoes/catalog.php?item_id=' . $item['id'] . '">' . $item['display_name'] . '</a></li>';
                                }
                            ?>
                        </ul>
                    </li>
                    <li><a href="/delivery/">Доставка</a></li>
                </ul>

                
                
            </div>
        </section>
    </body>
</html>
