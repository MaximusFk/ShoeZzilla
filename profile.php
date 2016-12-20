<?php
require_once './scripts/database/accounts_db.php';
require_once './scripts/database/sessions_db.php';
require_once './scripts/twig.php';

$command = filter_input(INPUT_GET, 'type');
$post = filter_input(INPUT_POST, 'type');

$session_id = filter_input(INPUT_COOKIE, 'session_id');

$loader = create_file_loader(['info_pages']);
$twig = create_twig($loader);

if($post === 'account_reg') {
    $name = filter_input(INPUT_POST, 'name');
    $surname = filter_input(INPUT_POST, 'surname');
    $phone = filter_input(INPUT_POST, 'phone');
    $email = filter_input(INPUT_POST, 'email');
    $hash = filter_input(INPUT_POST, 'password_hash');
    
    echo create_account($name, $surname, '', $email, $phone, $hash);
    return;
}
else if($post === 'get_name' && filter_has_var(INPUT_POST, 'session_id')) {
    $session_id = filter_input(INPUT_POST, 'session_id');
    $user_id = get_account_id_by_session($session_id);
    echo get_name($user_id);
}

switch ($command) {
    case 'login':
        if(!linked_to_account($session_id)) {
            echo $twig->render('login.twig');
        } else {
            header("Location: /profile");
        }
        break;
    case 'register':
        echo $twig->render('register.twig');
        break;
    case 'get_test_session_id':
        setcookie('session_id', 16, time() + (60 * 60));
        header("Location: /");
        break;
    default:
        echo $twig->render('not_found.twig');
}
