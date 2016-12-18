<?php
require_once __DIR__ . '/../lib/Twig/Autoloader.php';
require_once __DIR__ . '/database/db_connect.php';
require_once __DIR__ . '/database/sessions_db.php';
Twig_Autoloader::register();

function create_file_loader(array $path = null) {
    $loader = new Twig_Loader_Filesystem(__DIR__ . '/../templates/');
    foreach ($path as $p) {
        $loader->addPath(__DIR__ . '/../' . $p);
    }
    return $loader;
}

function create_twig($loader) {
    $twig = new Twig_Environment($loader);
    $twig->addFunction('get_session_id', new Twig_SimpleFunction('get_session_id', function () { return filter_input(INPUT_COOKIE, 'session_id'); }));
    $twig->addFunction('get_cart_entry_count', new Twig_SimpleFunction('get_cart_entry_count', function ($session_id) { 
        $sql = get_db();
        $result = $sql->query("SELECT item_count FROM " . Carts . " WHERE session_id='$session_id'");
        $count = $result && $result->num_rows !== 0 ? $result->fetch_assoc()['item_count'] : null;
        $sql->close();
        $result->free();
        return $count;
    }));
    return $twig;
}

// Эта часть кода запускает сессию если ее еще нет

if(filter_has_var(INPUT_COOKIE, 'session_id')) {
    //...
}
else {
    $session_id = create_session();
    setcookie("session_id", $session_id, time() + 60*60*24*7);
}

