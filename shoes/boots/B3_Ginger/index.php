<?php
require_once '../../../scripts/items/item.php';
require_once '../../../scripts/items/item_db.php';
require_once '../../../lib/Twig/Autoloader.php';
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('../../../templates');
$loader->addPath('../../../info_pages');
$loader->addPath('../../../shoes_cards');
$twig = new Twig_Environment($loader);
echo $twig->render('shoes_show.twig', array('item' => get_item_by_id(9)));
?>
