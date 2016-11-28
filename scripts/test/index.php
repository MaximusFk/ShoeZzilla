<?php
require_once '../../lib/Twig/Autoloader.php';
Twig_Autoloader::register();
$twig = new Twig_Loader_Filesystem('../../shoes_cards');
$twig->addPath('../../templates');
$twig = new Twig_Environment($twig);
echo $twig->render('shoes_show.twig');
?>