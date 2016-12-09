<?php
require_once '../scripts/items/item_db.php';
require_once '../lib/Twig/Autoloader.php';
Twig_Autoloader::register();

if($_SERVER['REQUEST_METHOD'] === 'GET') {
    $loader = new Twig_Loader_Filesystem("../templates");
    $loader->addPath("../shoes_cards");
    $loader->addPath('../info_pages');
    $twig = new Twig_Environment($loader);
    if(filter_has_var(INPUT_GET, 'category')) {
        $category = filter_input(INPUT_GET, 'category');
        $items = $category === 'all' ? get_items() : get_items_by_category($category);
        echo $twig->render('show_shoes_list_box.twig', array("items" => $items));
    }
    else if(filter_has_var(INPUT_GET, 'item_id')) {
        $item_id = filter_input(INPUT_GET, 'item_id');
        $item = get_item_by_id($item_id);
        if($item) {
            echo $twig->render('shoes_show.twig', array("item" => $item));
        }
        else {
            echo $twig->render('error_page.twig', array("text" => "Страница не найдена!"));
        }
    }
}
else {
    die('Ошибка!');
}