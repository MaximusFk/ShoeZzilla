<?php
require_once '../scripts/twig.php';

$loader = create_file_loader(['info_pages']);
$twig = create_twig($loader);

echo $twig->render('delivery.twig');

