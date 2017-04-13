<?php
//INFO: Структура папок статей
// /articles/view/##gen_name##/index.php, /articles/view/##gen_name##/content.xml, *.xml forbiden for all
require_once '../scripts/twig.php';

const VIEW = '.' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;

$loader = create_file_loader(['info_pages']);
$twig = create_twig($loader);

$views = scandir(VIEW);
$views = array_values(array_filter($views, function ($item) {
    return is_dir(VIEW . $item) && file_exists(VIEW . $item . DIRECTORY_SEPARATOR . 'index.php') && ($item !== '.' && $item !== '..');
}));

foreach ($views as $dir) {
    $filename = '.' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'content.xml';
    if(file_exists($filename)) {
        $xml = simplexml_load_file($filename);
        $title = $xml->article[0]->art_title;
        $text = substr( $xml->article[0]->text, 0, 500) . ' ...';
        $articles[] = ['title' => $title, 'text' => $text, 'url' => VIEW . $dir . DIRECTORY_SEPARATOR];
    }
}
echo $twig->render('articles_list.twig', ['articles' => $articles]);
