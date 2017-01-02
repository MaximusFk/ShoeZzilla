<?php
require_once "db_connect.php";
const STATUS_AWAIT_CONFIRM = 'AWAIT_CONFIRM';
const STATUS_WAIT_TO_SEND = 'WAIT_TO_SEND';
const STATUS_SENT = 'SENT';
const STATUS_AWAIT_PAY = 'AWAIT_PAY';
const STATUS_DONE = 'DONE';
const STATUS_CANCELED = 'CANCELED';

function create_new_order($name, $surname, $email, $phone, $address, $price, $user_id = null) {
    $sql = get_db();
    $date = date("Y-m-d H:i:s");
    $access = random_int(0, PHP_INT_MAX);
    $user = $user_id ? "'$user_id'" : "NULL";
    $query = "INSERT INTO " . Orders . " (name, surname, email, phone, address, date, status, price_sum, access_code, user_id) "
            . "VALUES ('$name', '$surname', '$email', '$phone', '$address', '$date', '" . STATUS_AWAIT_CONFIRM . "', '$price', '$access', $user)";
    $sql->query($query);
    return $sql->insert_id;
}

function get_access_code($order_id) {
    $sql = get_db();
    $result = $sql->query("SELECT access_code FROM " . Orders . " WHERE id='$order_id'");
    return $result ? $result->fetch_assoc()['access_code'] : -1;
}

function get_order_id($access_code) {
    $sql = get_db();
    $result = $sql->query("SELECT id FROM " . Orders . " WHERE access_code='$access_code'");
    return $result ? $result->fetch_assoc()['id'] : -1;
}

function get_orders_for_user($user_id) {
    $sql = get_db();
    $result = $sql->query("SELECT * FROM " . Orders . " WHERE user_id='$user_id'");
    if($result && $result->num_rows !== 0) {
        $array = [];
        while (($assoc = $result->fetch_assoc())) {
            array_push($array, $assoc);
        }
    }
    return $array;
}

function get_order_by_id($order_id) {
    $sql = get_db();
    $result = $sql->query("SELECT * FROM " . Orders . " WHERE id='$order_id'");
    return $result ? $result->fetch_assoc() : null;
}

function get_order_by_access_code($access_code) {
    $sql = get_db();
    $result = $sql->query("SELECT * FROM " . Orders . " WHERE access_code='$access_code'");
    return $result ? $result->fetch_assoc() : null;
}