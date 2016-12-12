<?php
require_once 'scripts/items/cart_db.php';

// Методы для работы пользователя с корзиной покупок, формат входа GET-REQUEST, формат выхода JSON

if(filter_has_var(INPUT_COOKIE, 'session_id')) {
    $session_id = filter_input(INPUT_COOKIE, 'session_id');
}
else {
    $response = array('error' => "no_session_id");
    echo json_encode($response);
    return;
}

if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'GET') {
    $method = filter_input(INPUT_GET, 'method');
    switch ($method) {
        case 'ADD':
            $sizes = json_decode(filter_input(INPUT_GET, 'sizes'));
            $item_id = filter_input(INPUT_GET, 'item_id');
            $all = 0;
            foreach ($sizes as $count) {
                $all = $all + $count;
            }
            $cart_id = get_cart_id($session_id);
            $all_count = add_to_cart($cart_id, $item_id, $sizes);
            $responce = array(
                'session_id' => $session_id,
                'item_id' => $item_id,
                'count' => $all,
                'all_count' => $all_count,
                'cart_id' => $cart_id
                    );
            echo json_encode($responce);
            break;
        case 'GET_LIST':
            $cart_id = get_cart_id($session_id);
            $cart = get_cart($cart_id);
            $cart['items'] = get_entries($cart_id);
            echo json_encode($cart);
        default:
            break;
    }
    
}

