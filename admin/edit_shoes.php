<?php
require_once '../scripts/twig.php';
require_once '../scripts/items/item_db.php';

$loader = create_file_loader(["info_pages"]);
$twig = create_twig($loader);

$colors = get_enum_values(Models, 'color');
$seasons = get_enum_values(Models, 'season');
$categories = get_enum_values(Models, 'category');
$genders = get_enum_values(Models, 'gender');

if(filter_has_var(INPUT_GET, 'item_id') && filter_has_var(INPUT_GET, 'data')) {
    $item_id = filter_input(INPUT_GET, 'item_id');
    echo update_item($item_id, json_decode(filter_input(INPUT_GET, 'data'), true)) ? "Object updated" : 0;
}
else if(filter_has_var(INPUT_GET, 'item_id')) {
    $item = get_item_by_id_raw(filter_input(INPUT_GET, 'item_id'));
    echo $twig->render('edit_shoes.twig', [
    'item' => $item,
    'colors' => $colors,
    'seasons' => $seasons,
    'categories' => $categories,
    'genders' => $genders
    ]);
}
else if(filter_has_var(INPUT_GET, 'data')) {
    if(($newid = insert_item(json_decode(filter_input(INPUT_GET, 'data'), true))) !== 0) {
        header("Location: /admin/edit_shoes.php?item_id=$newid");
    }
    else {
        $data = filter_input(INPUT_GET, 'data');
        echo $twig->render("error_page.twig", ['text' => "При добавлении произошла ошибка!"]);
    }
}
else {
    echo $twig->render('edit_shoes.twig', [
    'colors' => $colors,
    'seasons' => $seasons,
    'categories' => $categories,
    'genders' => $genders
    ]);
}