<?php
require_once 'scripts/items/item_db.php';
require_once './scripts/twig.php';
$loader = create_file_loader(['info_pages', 'shoes_cards']);
$twig = create_twig($loader);
$founded = find_items(filter_input(INPUT_GET, 'find'));
if(count($founded) > 0) {
    echo $twig->render('show_shoes_list_box.twig', ['items' => $founded, 'str' => filter_input(INPUT_GET, 'find')]);
} else {
    echo $twig->render('not_found.twig', ['str' => filter_input(INPUT_GET, 'find')]);
}
?>