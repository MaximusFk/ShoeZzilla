<?php
require_once '../scripts/items/item_db.php';
require_once '../lib/Twig/Autoloader.php';
Twig_Autoloader::register();

$twig = new Twig_Loader_Filesystem('../templates');
$twig->addPath('../info_pages');
$twig->addPath('../shoes_cards');
$twig = new Twig_Environment($twig);
echo $twig->render('show_shoes_list_box.twig', array('items' => get_items()));