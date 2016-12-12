<?php
require_once 'item_db.php';
require_once __DIR__ . "/../database/orders_db.php";
/*1*
 * Description of cart_db
 *
 * @author maximusfk
 */

function update_cart($cart_id, $item_count, $price_sum) {
    $sql = get_db(current_db);
    $sql->query("UPDATE _Carts_ SET price_sum='$price_sum', item_count='$item_count' WHERE id='$cart_id'");
    return $sql->field_count !== 0;
}


function add_to_cart($cart_id, $item_id, $sizes) {
    $cart = get_cart($cart_id);
    if($cart && has_entry($cart_id, $item_id)) {
        $item = get_entry_from_cart($cart_id, $item_id);
        $item_count = $item['count'];
    }
    if($cart) {
        $count = $cart['item_count'];
        $sum = $cart['price_sum'];
        $price = get_item_price_retail($item_id);
        $item_count = 0;
        foreach ($sizes as $c) {
            $count = $count + $c;
            $item_count += $c;
            $sum += $price * $c;
        }
        update_cart($cart_id, $count, $sum);
        add_entry_for_cart($cart_id, $item_id, $item_count, json_encode($sizes));
        return $count;
    }
}

function get_cart($cart_id) {
    $sql = get_db(current_db);
    $result = $sql->query("SELECT price_sum, item_count FROM _Carts_ WHERE id=$cart_id");
    return $result && $result->num_rows !== 0 ? $result->fetch_assoc() : null;
}

function get_cart_id($session_id, $create = true) {
    $sql = get_db(current_db);
    $result = $sql->query("SELECT id FROM _Carts_ WHERE session_id=$session_id");
    if($result && $result->num_rows !== 0) {
        return $result->fetch_assoc()['id'];
    }
    else if($create) {
        $result = $sql->query("INSERT INTO _Carts_ (session_id) VALUES ('$session_id')");
        return $result? $sql->insert_id : false;
    }
    else {
        return null;
    }
}
