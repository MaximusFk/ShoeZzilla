<?php
require_once './lib/Twig/Autoloader.php';
require_once './scripts/sql_query_exec.php';
Twig_Autoloader::register();

if(filter_has_var(INPUT_POST, 'email') && filter_has_var(INPUT_POST, 'password_hash')) {
    $email = filter_input(INPUT_POST, 'email');
    $hash = filter_input(INPUT_POST, 'password_hash');
    $session_id = create_session_id($email, $hash);
    if($session_id !== NULL) {
        setcookie("session_id", $session_id, time() + 60*60*24*7);
        header("Location: /");
    }
    else {
        $twig = new Twig_Loader_Filesystem('./templates');
        $twig->addPath('./info_pages');
        $twig = new Twig_Environment($twig);
        echo $twig->render('error_page.twig', array('text' => 'Неверно введен логин или пароль!'));
    }
}