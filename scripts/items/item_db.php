<?php
require_once 'item.php';

define('Password', 'maxime51mk');
define('Login', 'maximusfk');
define('current_db', 'maximusfk');
define('Accounts', 'accounts');
define('Models', 'models_test');

function get_db($name) {
	return new mysqli('localhost', Login, Password, $name);
}

function get_item_by_id($id) {
        $sql = get_db(current_db);
        $query = "SELECT name, color, price, img_code FROM models_test WHERE id='$id' AND hidden=0";
        if($result = $sql->query($query)) {
            if($result->num_rows !== 0) {
                $actor = $result->fetch_assoc();
                $item = new ShoesItem();
                $item->name = $actor['name'];
                $item->color = $actor['color'];
                $item->price = $actor['price'];
                $item->img_code = $actor['img_code'];
		$item->ID = $id;
                return $item;
            }
        }
}

function get_item_url($id) {
    $sql = get_db(current_db);
    $query = "SELECT url FROM models_test WHERE id='$id'";
    $result = $sql->query($query);
    if($result && $result->num_rows !== 0) {
        $actor = $result->fetch_assoc();
        return $actor['url'];
    } else {
        return NULL;
    }
}

function get_items_by_name($name) {
    $sql = get_db(current_db);
    $query = "SELECT * FROM models_test WHERE name = '$name' AND hidden=0";
    if($result = $sql->query($query)) {
        $pos = 0;
        $items;
        while ($actor = $result->fetch_assoc()) {
            $item = new ShoesItem();
            $item->name = $actor['name'];
            $item->color = $actor['color'];
            $item->price = $actor['price'];
            $item->img_code = $actor['img_code'];
            $item->ID = $actor['id'];
            $items[$pos] = $item;
            $pos++;
        }
        return $items;
    }
}

function find_items($str) {
    $sql = get_db(current_db);
    if(!$str) {
        return array();
    }
    $query = "SELECT * FROM models_test WHERE concat(name, color, price) LIKE '%$str%'";
    if($result = $sql->query($query)) {
        $pos = 0;
        $items;
        while ($actor = $result->fetch_assoc()) {
            $item = new ShoesItem();
            $item->name = $actor['name'];
            $item->color = $actor['color'];
            $item->price = $actor['price'];
            $item->img_code = $actor['img_code'];
			$item->ID = $actor['id'];
            $items[$pos] = $item;
            $pos++;
        }
        return $items;
    }
}

function get_items() {
    $sql = get_db(current_db);
    $query = "SELECT * FROM models_test WHERE hidden=0";
    if($result = $sql->query($query)) {
        $pos = 0;
        $items;
        while ($actor = $result->fetch_assoc()) {
            $item = new ShoesItem();
            $item->name = $actor['name'];
            $item->color = $actor['color'];
            $item->price = $actor['price'];
            $item->img_code = $actor['img_code'];
            $item->ID = $actor['id'];
            $items[$pos] = $item;
            $pos++;
        }
        return $items;
    }
}
?>