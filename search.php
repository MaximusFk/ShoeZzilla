<?php
//echo htmlspecialchars($_GET["find"]);
require_once 'lib/Twig/Autoloader.php';
require_once 'scripts/items/item_db.php';
Twig_Autoloader::register();
$twig = new Twig_Loader_Filesystem('templates');
$twig->addPath('info_pages');
$twig->addPath('shoes_cards');
$twig = new Twig_Environment($twig);
$founded = find_items($_GET['find']);
if(count($founded) > 0) {
    echo $twig->render('show_shoes_list_box.twig', array('items' => $founded));
} else {
    echo $twig->render('not_found.twig', array('str' => $_GET['find']));
}
?>