<?php
require_once './scripts/twig.php';
require_once './scripts/items/cart_db.php';
require_once './scripts/database/sessions_db.php';
require_once './scripts/database/accounts_db.php';
$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
$loader = create_file_loader(['info_pages', 'templates/order']);
$twig = create_twig($loader);

if($method === 'GET' && (filter_has_var(INPUT_GET, 'order_id') || filter_has_var(INPUT_GET, 'access_code'))) {
    $order_id = filter_has_var(INPUT_GET, 'order_id') ? filter_input(INPUT_GET, 'order_id') : get_order_id(filter_input(INPUT_GET, 'access_code'));
    $order = get_order_by_id($order_id);
    if($order) {
        $entries = get_entries_array($order_id, Orders);
        foreach ($entries as &$key) {
            $key['size_data'] = json_decode($key['size_data'], true);
            ksort($key['size_data']);
            $key['node'] = get_item_by_id($key['item_id']);
        }
        echo $twig->render('order_view.twig', ['order_id' => $order_id, 'items' => $entries, 'order' => $order]);
    }
    else {
        echo $twig->render('error_page.twig', ['text' => 'Заказ не найден!']);
    }
}
else if($method === 'POST') {
    $session_id = filter_input(INPUT_COOKIE, 'session_id');
    $cart_id = get_cart_id($session_id, false);
    $name = filter_input(INPUT_POST, 'name');
    $surname = filter_input(INPUT_POST, 'surname');
    $email = filter_input(INPUT_POST, 'email');
    $phone = filter_input(INPUT_POST, 'phone_number');
    $city = filter_input(INPUT_POST, 'city');
    $office = filter_input(INPUT_POST, 'npoffice');
    $address = $city . ", " . $office;
    if($cart_id && $name && $surname && $email && $phone && $city && $office) {
        $user_id = get_account_id_by_session($session_id);
        $cart = get_cart($cart_id);
        $order_id = create_new_order($name, $surname, $email, $phone, $address, $cart['price_sum'], $user_id);
        set_new_parent($order_id, Orders, $cart_id, Carts);
        remove_cart($cart_id);
        $access = get_access_code($order_id);
        echo $twig->render('order_success.twig', ['order_id' => $order_id, 'access_code' => $access]);
    }
    else {
        echo $twig->render('error_page.twig', ['text' => 'Во время запроса произошла ошибка, пожалуйста, повторите еще раз!']);
    }
}
else {
    $session_id = filter_input(INPUT_COOKIE, 'session_id');
    $cart_id = get_cart_id($session_id, false);
    $cart = get_cart($cart_id);
    if($cart['item_count'] > 0) {
        $export = [
            'cart' => $cart
            ];
        if(linked_to_account($session_id)) {
            $export['user'] = get_account_info(get_account_id_by_session($session_id));
        }
        echo $twig->render('order_form.twig', $export);
    }
    else {
        echo $twig->render('error_page.twig', ['text' => 'Ваша корзина пуста!']);
    }
}
