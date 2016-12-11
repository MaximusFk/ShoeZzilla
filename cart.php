<?php
require_once 'scripts/items/cart_db.php';

// Методы для работы пользователя с корзиной покупок, формат входа GET-REQUEST, формат выхода JSON

if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'GET') {
    $session_id = filter_input(INPUT_COOKIE, 'session_id');
    $method = filter_input(INPUT_GET, 'method');
    switch ($method) {
        case 'ADD':
            $sizes = json_decode(filter_input(INPUT_GET, 'sizes'));
            $all = 0;
            foreach ($sizes as $count) {
                $all = $all + $count;
            }
            $all_count = add_to_cart($session_id, $item_id, $sizes);
            $responce = array(
                'session_id' => $session_id,
                'item_id' => filter_input(INPUT_GET, 'item_id'),
                'count' => $all,
                'all_count' => $all_count
                    );
            echo json_encode($responce);
            break;
        default:
            break;
    }
    
}

