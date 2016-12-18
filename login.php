<?php
require_once './scripts/database/sessions_db.php';
require_once './scripts/database/accounts_db.php';
require_once './scripts/twig.php';

if(filter_has_var(INPUT_POST, 'email') && filter_has_var(INPUT_POST, 'password_hash')) {
    $email = filter_input(INPUT_POST, 'email');
    $hash = filter_input(INPUT_POST, 'password_hash');
    $user_id = get_account_id($email, $hash);
    if($user_id !== NULL) {
        if(! (filter_has_var(INPUT_COOKIE, 'session_id') && set_account_id_for_session(filter_input(INPUT_COOKIE, 'session_id'), $user_id))) {
            $session_id = create_session($user_id);
            setcookie("session_id", $session_id, time() + 60*60*24*7);
        }
        header("Location: /");
    }
    else {
        $loader = create_file_loader(['info_pages']);
        $twig = create_twig($loader);
        echo $twig->render('error_page.twig', array('text' => 'Неверно введен логин или пароль!'));
    }
}