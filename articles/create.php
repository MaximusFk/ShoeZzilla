<?php
$xmlstr = <<<'XML'
<?xml version='1.0' encoding='utf-8' standalone='yes'?>
<articles>
</articles>
XML;
$phpviewtempl = <<<'PHP'
<?php
ini_set("include_path", '/home/aorynqco/php:' . ini_get("include_path") );
require_once '../../../scripts/twig.php';
require_once '../../../scripts/BBCode.php';
$loader = create_file_loader(['info_pages']);
$twig = create_twig($loader);
$twig->addFilter(new Twig_SimpleFilter('parse_bb', decode_bb_a));
$xml = simplexml_load_file('./content.xml');
echo $twig->render('show_article.twig', ['title' => $xml->article[0]->art_title, 'text' => $xml->article[0]->text]);
PHP;
require_once '../scripts/utilites/filter_functions.php';
const VIEWS = './view/';
if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    if(filter_has_var(INPUT_POST, 'title') && filter_has_var(INPUT_POST, 'text')) {
        $title = filter_input(INPUT_POST, 'title');
        $url = str2url($title);
        $text = filter_input(INPUT_POST, 'text');
        $user_id = filter_has_var(INPUT_POST, 'user_id') ? filter_input(INPUT_POST, 'user_id') : -1;
        $uid = filter_has_var(INPUT_POST, 'id') ? filter_input(INPUT_POST, 'id') : uniqid('art_');
        $url .= ('_' . $uid);
        if(file_exists('data.json')) {
            $json = file_get_contents('./data.json');
            $json = json_decode($json);
        }
        if ($json && ($index = find_index_where($json, 'id', $uid)) !== false) {
            $ex = $json[$index];
            if (is_dir(VIEWS . DIRECTORY_SEPARATOR . $ex['path']) && $ex['path'] !== $url) {
                rename(VIEWS . $ex['path'], VIEWS . $url);
                $ex['path'] = $url;
                $json[$index] = $ex;
            }
        } else {
            $json[] = ['id' => $uid, 'path' => $url, 'owner' => $user_id];
        }
        mkdir(VIEWS . $url . DIRECTORY_SEPARATOR);
        file_put_contents('data.json', json_encode($json));
        $xml = new SimpleXMLElement($xmlstr);
        $art = $xml->addChild('article');
        $art->addChild('art_title', $title);
        $art->addChild('text', $text);
        $xml->saveXML(VIEWS . $url . DIRECTORY_SEPARATOR . 'content.xml');
        file_put_contents(VIEWS . $url . DIRECTORY_SEPARATOR . 'index.php', $phpviewtempl);
        
        header('Location: /articles/view/' . $url . DIRECTORY_SEPARATOR);
    }
}
else {
    require '../scripts/twig.php';
    $loader = create_file_loader(['info_pages']);
    $twig = create_twig($loader);
    echo $twig->render('edit_article.twig');
}
