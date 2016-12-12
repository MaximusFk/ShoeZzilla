<?php
require_once 'item.php';

define('Password', 'maxime51mk');
define('Login', 'maximusfk');
define('current_db', 'maximusfk');
define('Accounts', 'accounts');
define('Models', 'models_test');

function get_db($name = current_db) {
	return new mysqli('localhost', Login, Password, $name);
}

function construct_item_from_array(array $array) {
    $item = new ShoesItem();
    if(array_key_exists('id', $array)) { $item->ID = $array['id']; }
    if(array_key_exists('name', $array)) { $item->name = $array['name']; }
    if(array_key_exists('color', $array)) { $item->color = strtolower($array['color']); }
    if(array_key_exists('display_name', $array)) { $item->display_name = $array['display_name']; }
    if(array_key_exists('category', $array)) { $item->category = $array['category']; }
    if(array_key_exists('image_path', $array)) { $item->img_code = $array['image_path']; }
    if(array_key_exists('image_head', $array)) { $item->img_main = $array['image_head']; }
    if(array_key_exists('size_min', $array)) { $item->min_size = $array['size_min']; }
    if(array_key_exists('size_max', $array)) { $item->max_size = $array['size_max']; }
    if(array_key_exists('price_retail', $array)) { $item->price = $array['price_retail']; }
    if(array_key_exists('url', $array)) { $item->url = $array['url']; }
    return $item;
}

function get_item_by_id($id) {
        $sql = get_db(current_db);
        $query = "SELECT * FROM _Shoes_ WHERE id='$id' AND hide=0";
        if($result = $sql->query($query)) {
            if($result->num_rows !== 0) {
                $actor = $result->fetch_assoc();
                return construct_item_from_array($actor);
            }
        }
}

function get_items_by_category($category) {
    $sql = get_db(current_db);
    $result = $sql->query("SELECT * FROM _Shoes_ WHERE category='$category'");
    if($result) {
        $pos = 0;
        while ($actor = $result->fetch_assoc()) {
            $items[$pos] = construct_item_from_array($actor);
            $pos++;
        }
        return $items;
    }
}

function create_item($shoesItem) {
    if($shoesItem) {
        $db = get_db(current_db);
        $result = $db->query("INSERT INTO _Shoes_ (name, color, price_retail, category, size_min, size_max) VALUES "
                . "('$shoesItem->name', '$shoesItem->color', '$shoesItem->price', '$shoesItem->category', '$shoesItem->min_size', '$shoesItem->max_size')");
        $shoesItem->ID = $db->insert_id;
        return $result ? true : false;
    }
    return false;
}

function get_item_url($id) {
    $sql = get_db(current_db);
    $query = "SELECT url FROM _Shoes_ WHERE id='$id'";
    $result = $sql->query($query);
    if($result && $result->num_rows !== 0) {
        $actor = $result->fetch_assoc();
        return $actor['url'];
    } else {
        return NULL;
    }
}

function get_item_price_retail($id) {
    $sql = get_db(current_db);
    $query = "SELECT price_retail FROM _Shoes_ WHERE id='$id'";
    $result = $sql->query($query);
    if($result && $result->num_rows !== 0) {
        $actor = $result->fetch_assoc();
        return $actor['price_retail'];
    } else {
        return NULL;
    }
}

function get_items_by_name($name) {
    $sql = get_db(current_db);
    $query = "SELECT * FROM _Shoes_ WHERE name = '$name' AND hide=0";
    if($result = $sql->query($query)) {
        $pos = 0;
        $items;
        while ($actor = $result->fetch_assoc()) {
            $item = new ShoesItem();
            $item->name = $actor['name'];
            $item->color = $actor['color'];
            $item->price = $actor['price_retail'];
            $item->img_code = $actor['image_path'];
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
    $query = "SELECT * FROM _Shoes_ WHERE concat(name, color, price_retail, display_name, brand, gender) LIKE '%$str%'";
    if($result = $sql->query($query)) {
        $pos = 0;
        $items = NULL;
        while ($actor = $result->fetch_assoc()) {
            $item = construct_item_from_array($actor);
            $items[$pos] = $item;
            $pos++;
        }
        return $items;
    }
}

function get_items() {
    $sql = get_db(current_db);
    $query = "SELECT * FROM _Shoes_ WHERE hide=0";
    if($result = $sql->query($query)) {
        $pos = 0;
        $items = NULL;
        while ($actor = $result->fetch_assoc()) {
            $item = new ShoesItem();
            $item->name = $actor['name'];
            $item->color = $actor['color'];
            $item->price = $actor['price_retail'];
            $item->img_code = $actor['image_path'];
            $item->img_main = $actor['image_head'];
            $item->ID = $actor['id'];
            $items[$pos] = $item;
            $pos++;
        }
        return $items;
    }
}


/* ShoesEntry block */

function has_entry($cart_id, $item_id) {
    $sql = get_db(current_db);
    $result = $sql->query("SELECT item_id FROM _ShoesEntries_ WHERE parent_id='$cart_id' AND parent_table='_Carts_' AND item_id='$item_id'");
    return $result->num_rows;
}

function get_entry_from_cart($cart_id, $item_id) {
    $sql = get_db(current_db);
    $result = $sql->query("SELECT * FROM _ShoesEntries_ WHERE parent_id='$cart_id' AND parent_table='_Carts_' AND item_id='$item_id'");
    return $result ? $result->fetch_assec() : null;
}

function update_entry_in_cart($cart_id, $item_id, $count, $info) {
    $sql = get_db(current_db);
    $sql->query("UPDATE _ShoesEntries_ SET (item_id,parent_id,parent_table,count,info) VALUES ('$item_id','$cart_id','_Carts_','$count','$info')");
    return $sql->field_count !== 0;
}

function add_entry_for_cart($cart_id, $item_id, $count, $info) {
    $sql = get_db(current_db);
    $sql->query("INSERT INTO _ShoesEntries_ (item_id,parent_id,parent_table,count,info) VALUES ('$item_id','$cart_id','_Carts_','$count','$info')");
    return $sql->field_count !== 0;
}

function remove_entry_from_cart($cart_id, $item_id) {
    $sql = get_db(current_db);
    $sql->query("DELETE ");
}

function get_entries($cart_id) {
    $sql = get_db(current_db);
    $result = $sql->query("SELECT item_id, count, info FROM _Carts_ WHERE parent_id='$cart_id' AND parent_table='_Carts_'");
    if($result) {
        $pos = 0;
        while($actor = $result->fetch_assoc()) {
            $list[$pos] = $actor;
            $pos++;
        }
        return $list;
    }
    return null;
}