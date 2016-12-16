<?php
require_once '../scripts/items/cart_db.php';
require_once '../scripts/database/sessions_db.php';
require_once '../lib/Twig/Autoloader.php';
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('../templates');
$enviroment = new Twig_Environment($loader);
if(filter_has_var(INPUT_COOKIE, 'session_id')) {
    $session_id = filter_input(INPUT_COOKIE, 'session_id');
    if(equals_session($session_id)) {
        $cart_id = get_cart_id($session_id, false);
        if($cart_id) {
            $cart = get_cart($cart_id);
            $items = get_entries($cart_id);
            foreach ($items as &$key) {
                $key['info'] = json_decode($key['info'], true);
                $key['node'] = get_item_by_id($key['item_id']);
            }
            echo $enviroment->render('shopcart.twig', array('cart' => $cart, 'items' => $items));
            return;
        }
    }
}
echo $enviroment->render('shopcart.twig');

