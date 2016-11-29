<?php

define('Password', 'maxime51mk');
define('Login', 'maximusfk');
define('current_db', 'maximusfk');
define('Accounts', 'accounts');
define('Models', 'models_test');

function get_db($name) {
	return new mysqli('localhost', Login, Password, $name);
}

function get_item_pagepath_by_id($item_id) {
	$db = get_db(current_db);
	$query = "SELECT url FROM Models WHERE id='$item_id'";
        $result = $db->query($query);
	if($result) {
		if($result->num_rows !== 0) {
			$actor = $result->fetch_assoc();
			return $actor['url'];
		}
	}
	return "";
}

function create_account($name, $surname, $email, $phone, $hash) {
    $db = get_db(current_db);
    $result = $db->query("SELECT * FROM accounts WHERE email='$email'");
    if($result->num_rows > 0) {
        return false;
    } else {
        $query = "INSERT INTO accounts (name,surname,email,phone,password_hash) VALUES ('$name','$surname','$email','$phone','$hash')";
        if($db->query($query)) {
            return true;
        } else {
            return false;
        }
    }
}

function get_session_id($email, $hash) {
    $db = get_db(current_db);
    $result = $db->query("SELECT session_id FROM accounts WHERE email='$email' AND password_hash='$hash'");
    if($result && $result->num_rows !== 0) {
        $actor = $result->retch_assoc();
        return $actor['session_id'];
    }
    return "";
}

function get_userinfo_by_session_id($session_id) {
    $db = get_db(current_db);
    $result = $db->query("SELECT name, surname, email, phone FROM accounts WHERE session_id='$session_id'");
    if($result && $result->num_rows !== 0) {
        return $result->fetch_assoc();
    } else {
        return NULL;
    }
}

function equals_session_id($session_id) {
    $db = get_db(current_db);
    $result = $db->query("SELECT session_id FROM accounts WHERE session_id=$session_id");
    return $result->num_rows !== 0;
}

function create_session_id($email, $hash) {
    $db = get_db(current_db);
    $result = $db->query("SELECT * FROM accounts WHERE email='$email' AND password_hash='$hash'");
    if($result && $result->num_rows !== 0) {
        $magic_number = random_int(0, PHP_INT_MAX);
        $db->query("UPDATE accounts SET session_id='$magic_number' WHERE email='$email'");
        return $magic_number;
    } else {
        return NULL;
    }
}