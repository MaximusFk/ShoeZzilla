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


function add_to_cart($cart_id, $item_id, array $sizes) {
    $cart = get_cart($cart_id);
    if($cart) {
        $count = $cart['item_count'];
        $sum = $cart['price_sum'];
        $price = get_item_price_retail($item_id);
        $item_count = 0;
        foreach ($sizes as $value) {
            $count = $count + $value;
            $item_count += $value;
            $sum += $price * $value;
        }
        update_cart($cart_id, $count, $sum);
        if(has_entry($cart_id, $item_id)) {
            $item = get_entry_from_cart($cart_id, $item_id);
            $item_count += $item['count'];
            $info = json_encode(sum_size_data($sizes, json_decode($item['size_data'], true)));
            update_entry_in_cart($cart_id, $item_id, $item_count, $info);
        }
        else {
            add_entry_for_cart($cart_id, $item_id, $item_count, json_encode($sizes));
        }
        return $count;
    }
}

function remove_from_cart($cart_id, $item_id, array $sizes) {
    $cart = get_cart($cart_id);
    if($cart) {
        
    }
}

function get_cart($cart_id) {
    $sql = get_db(current_db);
    $result = $sql->query("SELECT price_sum, item_count FROM _Carts_ WHERE id=$cart_id");
    return $result && $result->num_rows !== 0 ? $result->fetch_assoc() : null;
}

function get_cart_entry_count($cart_id) {
    $cart = get_cart($cart_id);
    return $cart ? $cart['item_count'] : 0;
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

function sum_size_data(array $info1, array $info2) {
    foreach ($info2 as $key => $value) {
        if(array_key_exists($key, $info1)) {
            $info1["$key"] += $value;
        }
        else {
            $info1["$key"] = $value;
        }
    }
    return $info1;
}

function delete_size_data(array $info1, array $info2) {
    foreach ($info2 as $key => $value) {
        if(array_key_exists($key, $info1)) {
            $info1["$key"] -= $value;
            if($info1["$key"] === 0) {
                unset($info1["$key"]);
            }
        }
    }
    return $info1;
}
