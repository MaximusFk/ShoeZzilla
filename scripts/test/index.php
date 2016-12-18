<?php
require_once '../twig.php';
$loader = create_file_loader(['shoes_cards']);
$twig = create_twig($loader);
echo $twig->render('shoes_show.twig');
?>