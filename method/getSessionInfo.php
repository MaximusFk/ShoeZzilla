<?php
require_once '../scripts/database/sessions_db.php';
require_once '../scripts/database/accounts_db.php';
require_once '../scripts/items/cart_db.php';

if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'GET') {
    $session_id = filter_input(INPUT_GET, 'session_id');
    if(equals_session($session_id)) {
        $user_id = get_account_id_by_session($session_id);
        $name = get_name($user_id);
        if($name) {
            $response['user_name'] = $name;
        }
        $cart_id = get_cart_id($session_id, false);
        $response['cart_item_count'] = get_cart_entry_count($cart_id);
        $response['session_id'] = $session_id;
    }
    else {
        $response['session_id'] = create_session();
    }
    echo json_encode($response);
}