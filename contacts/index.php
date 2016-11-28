<?php
require_once '../lib/Twig/Autoloader.php';
Twig_Autoloader::register();

$twig = new Twig_Loader_Filesystem('../templates');
$twig->addPath('../info_pages');
$twig = new Twig_Environment($twig);
echo $twig->render('contacts.twig');
?>