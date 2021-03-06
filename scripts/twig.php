<?php
require_once __DIR__ . '/../lib/Twig/Autoloader.php';
require_once __DIR__ . '/database/db_connect.php';
require_once __DIR__ . '/database/accounts_db.php';
require_once __DIR__ . '/database/sessions_db.php';
Twig_Autoloader::register();

const NOVA_POSHTA_ACCESS = '98f0f33429e0aca503cf27732aae9205';

function create_file_loader(array $path = null) {
    $loader = new Twig_Loader_Filesystem(__DIR__ . '/../templates/');
    foreach ($path as $p) {
        $loader->addPath(__DIR__ . '/../' . $p);
    }
    return $loader;
}

function create_file_loader_m(array $path = null) {
    $loader = new Twig_Loader_Filesystem(__DIR__ . '/../mobile/templates/');
    foreach ($path as $p) {
        $loader->addPath(__DIR__ . '/../mobile/' . $p);
    }
    return $loader;
}

function create_twig($loader) {
    $twig = new Twig_Environment($loader);
    $twig->addFunction('get_session_id', new Twig_SimpleFunction('get_session_id', function () { return filter_input(INPUT_COOKIE, 'session_id'); }));
    $twig->addFunction('is_admin', new Twig_SimpleFunction('is_admin', "is_admin"));
    $twig->addFunction('tr', new Twig_SimpleFunction('tr', function ($key) {
        $sql = get_db();
        $sql->query("SET NAMES utf8");
        $result = $sql->query("SELECT ru_RU FROM _Locale_ WHERE str_key='$key'");
        return $result && $result->num_rows !== 0 ? $result->fetch_assoc()["ru_RU"] : "";
    }));
    $twig->addFunction('get_cart_entry_count', new Twig_SimpleFunction('get_cart_entry_count', function ($session_id) { 
        $sql = get_db();
        $result = $sql->query("SELECT item_count FROM " . Carts . " WHERE session_id='$session_id'");
        $count = $result && $result->num_rows !== 0 ? $result->fetch_assoc()['item_count'] : 0;
        $sql->close();
        $result->free();
        return $count;
    }));
    $twig->addFilter('array_sum', new Twig_SimpleFilter('array_sum', array_sum));
    return $twig;
}

// Эта часть кода запускает сессию если ее еще нет

if(filter_has_var(INPUT_COOKIE, 'session_id')) {
    //...
}
else {
    $session_id = create_session();
    setcookie("session_id", $session_id, time() + 60*60*24*7, "/");
}

