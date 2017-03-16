<?php
require_once './scripts/twig.php';
require_once './scripts/items/item_db.php';
$loader = create_file_loader(['info_pages']);
$twig = create_twig($loader);
$hits = get_items_by_tag_raw("hit");
$new = get_items_by_tag_raw("novelty");
echo $twig->render('main_page.twig', ['hits' => $hits, 'novelty' => $new]);