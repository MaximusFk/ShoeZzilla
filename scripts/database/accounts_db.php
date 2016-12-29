<?php
require_once 'db_connect.php';

function create_account($name, $surname, $secondname, $email, $phone, $password_hash) {
    $sql = get_db();
    $date = date("Y-m-d");
    if(!$sql->query("INSERT INTO " . Accounts . " (name, surname, secondname, email, phone, password_hash, register_date)"
            . " VALUES ('$name','$surname','$secondname','$email','$phone','$password_hash','$date')")) {
        return $sql->insert_id;
    }
    else {
        return null;
    }
}

function equals_account($email, $password_hash) {
    $sql = get_db();
    $result = $sql->query("SELECT id FROM " . Accounts . " WHERE email='$email' AND password_hash='$password_hash'");
    return $result && $result->num_rows !== 0;
}

function get_account_id($email, $password_hash) {
    $sql = get_db();
    $result = $sql->query("SELECT id FROM " . Accounts . " WHERE email='$email' AND password_hash='$password_hash'");
    return $result && $result->num_rows !== 0 ? $result->fetch_assoc()['id'] : null;
}

function get_name($id) {
    $sql = get_db();
    $result = $sql->query("SELECT name FROM " . Accounts . " WHERE id='$id'");
    return $result && $result->num_rows !== 0 ? $result->fetch_assoc()['name'] : null;
}

function get_account_info($id) {
    $sql = get_db();
    $result = $sql->query("SELECT name, surname, secondname, phone, email FROM " . Accounts . " WHERE id='$id'");
    return $result ? $result->fetch_assoc() : null;
}