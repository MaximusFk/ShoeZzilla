<?php
require_once './lib/Twig/Autoloader.php';
require_once './scripts/sql_query_exec.php';
Twig_Autoloader::register();

$command = filter_input(INPUT_GET, 'type');
$post = filter_input(INPUT_POST, 'type');

$loader = new Twig_Loader_Filesystem('./templates');
$loader->addPath('./info_pages');
$twig = new Twig_Environment($loader);

if($post === 'account_reg') {
    $name = filter_input(INPUT_POST, 'name');
    $surname = filter_input(INPUT_POST, 'surname');
    $phone = filter_input(INPUT_POST, 'phone');
    $email = filter_input(INPUT_POST, 'email');
    $hash = filter_input(INPUT_POST, 'password_hash');
    
    echo create_account($name, $surname, $email, $phone, $hash);
    return;
}
else if($post === 'get_name' && filter_has_var(INPUT_POST, 'session_id')) {
    $session_id = filter_input(INPUT_POST, 'session_id');
    
    echo get_userinfo_by_session_id($session_id)['name'];
}

switch ($command) {
    case 'login':
        if(!filter_has_var(INPUT_COOKIE, 'session_id') || !equals_session_id(filter_input(INPUT_COOKIE, 'session_id'))) {
            echo $twig->render('login.twig');
        } else {
            header("Location: /");
        }
        break;
    case 'register':
        echo $twig->render('register.twig');
        break;
    default:
        echo $twig->render('not_found.twig');
}
