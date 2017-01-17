<?php
require_once './scripts/twig.php';
require_once './scripts/items/cart_db.php';
require_once './scripts/database/sessions_db.php';
require_once './scripts/database/accounts_db.php';
require_once './scripts/utilites/filter_functions.php';
require_once './scripts/novaposhta/cities.php';
require_once './scripts/novaposhta/warehouses.php';
$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
$loader = create_file_loader(['info_pages', 'templates/order']);
$twig = create_twig($loader);

function convert_entries($entries) {
    foreach ($entries as &$key) {
        $key['size_data'] = json_decode($key['size_data'], true);
        ksort($key['size_data']);
        $key['node'] = get_item_by_id($key['item_id']);
    }
    return $entries;
}

function get_address_from_delivery_data($delivery_data) {
    if($delivery_data['type'] === 'novaposhta'){
        $city = find_where(get_cities_object()['data'], 'Ref', $delivery_data['cityUUID']);
        $office = find_where(get_warehouses_object(NULL)['data'], 'Ref', $delivery_data['officeUUID']);
        return $city['DescriptionRu'] . ", " . $office['DescriptionRu'] . "[{$delivery_data['type']}]";
    }
}
if($method === 'GET' && filter_has_var(INPUT_GET, 'access_code') && filter_input(INPUT_GET, 'set_access_status') === 'confirm') {
    $order_id = get_order_id(filter_input(INPUT_GET, 'access_code'));
    update_order($order_id, ['status' => 'WAIT_TO_SEND']);
    echo 'Статус заказа обновлен';
}
else if($method === 'GET' && (filter_has_var(INPUT_GET, 'order_id') || filter_has_var(INPUT_GET, 'access_code'))) {
    $order_id = filter_has_var(INPUT_GET, 'order_id') ? filter_input(INPUT_GET, 'order_id') : get_order_id(filter_input(INPUT_GET, 'access_code'));
    $order = get_order_by_id($order_id);
    if($order) {
        $order['address'] = get_address_from_delivery_data(json_decode($order['delivery_data'], true));
        $entries = convert_entries(get_entries_array($order_id, Orders));
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
    $delivery_data = ['type' => 'novaposhta', 'cityUUID' => $city, 'officeUUID' => $office];
    if($cart_id && $name && $surname && $email && $phone && $city && $office) {
        $user_id = get_account_id_by_session($session_id);
        $cart = get_cart($cart_id);
        $order_id = create_new_order($name, $surname, $email, $phone, json_encode($delivery_data), $cart['price_sum'], $user_id);
        $order = get_order_by_id($order_id);
        $order['address'] = get_address_from_delivery_data(json_decode($order['delivery_data'], true));
        set_new_parent($order_id, Orders, $cart_id, Carts);
        remove_cart($cart_id);
        $access = get_access_code($order_id);
        
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';
        // Дополнительные заголовки
        $headers[] = "To: $name <$email>";
        $headers[] = 'From: ShoeZzilla <admin@shoezzilla.zzz.com.ua>';
        if(mail($email, "Ваш заказ был принят", 
                $twig->render('order_mail.twig', ['order' => $order, 'access_code' => $access, 'items' => convert_entries(get_entries_array($order_id, Orders))]),
                implode("\r\n", $headers))) {
            echo $twig->render('order_success.twig', ['order_id' => $order_id, 'access_code' => $access]);
        }
        else {
            echo 'Error';
        }
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
        $cities = json_decode(get_cities_json(), true)['data'];
        $export['cities'] = filter($cities, ['DescriptionRu', 'Ref']);
        echo $twig->render('order_form.twig', $export);
    }
    else {
        echo $twig->render('error_page.twig', ['text' => 'Ваша корзина пуста!']);
    }
}
