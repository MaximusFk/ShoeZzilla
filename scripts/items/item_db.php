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

function construct_item_from_array(array $array) {
    $item = new ShoesItem();
    if(array_key_exists('id', $array)) { $item->ID = $array['id']; }
    if(array_key_exists('name', $array)) { $item->name = $array['name']; }
    if(array_key_exists('color', $array)) { $item->color = $array['color']; }
    if(array_key_exists('display_name', $array)) { $item->display_name = $array['display_name']; }
    if(array_key_exists('category', $array)) { $item->category = $array['category']; }
    if(array_key_exists('img_code', $array)) { $item->img_code = $array['img_code']; }
    if(array_key_exists('img_main', $array)) { $item->img_main = $array['img_main']; }
    if(array_key_exists('size_min', $array)) { $item->min_size = $array['size_min']; }
    if(array_key_exists('size_max', $array)) { $item->max_size = $array['size_max']; }
    if(array_key_exists('price', $array)) { $item->price = $array['price']; }
    if(array_key_exists('url', $array)) { $item->url = $array['url']; }
    return $item;
}

function get_item_by_id($id) {
        $sql = get_db(current_db);
        $query = "SELECT * FROM models_test WHERE id='$id' AND hidden=0";
        if($result = $sql->query($query)) {
            if($result->num_rows !== 0) {
                $actor = $result->fetch_assoc();
                $item = new ShoesItem();
                $item->name = $actor['name'];
                $item->color = $actor['color'];
                $item->price = $actor['price'];
                $item->img_code = $actor['img_code'];
                $item->img_main = $actor['img_main'];
                $item->min_size = $actor['size_min'];
                $item->max_size = $actor['size_max'];
		$item->ID = $id;
                return $item;
            }
        }
}

function get_items_by_category($category) {
    $sql = get_db(current_db);
    $result = $sql->query("SELECT * FROM models_test WHERE category='$category'");
    if($result) {
        $pos = 0;
        while ($actor = $result->fetch_assoc()) {
            $items[$pos] = construct_item_from_array($actor);
            $pos++;
        }
        return $items;
    }
}

function create_item_entry($shoesItem) {
    if($shoesItem) {
        $db = get_db(current_db);
        $result = $db->query("INSERT INTO models_test (name, color, price, category, size_min, size_max, url) VALUES "
                . "('$shoesItem->name', '$shoesItem->color', '$shoesItem->price', '$shoesItem->category', '$shoesItem->min_size', '$shoesItem->max_size', '$shoesItem->url')");
        $nid = $db->query("SELECT last_insert_id()");
        $shoesItem->ID = $nid->fetch_assoc()["last_insert_id()"];
        return $result ? true : false;
    }
    return false;
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
        $items = NULL;
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
        $items = NULL;
        while ($actor = $result->fetch_assoc()) {
            $item = new ShoesItem();
            $item->name = $actor['name'];
            $item->color = $actor['color'];
            $item->price = $actor['price'];
            $item->img_code = $actor['img_code'];
            $item->img_main = $actor['img_main'];
            $item->ID = $actor['id'];
            $items[$pos] = $item;
            $pos++;
        }
        return $items;
    }
}
?>