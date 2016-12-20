<?php
require_once 'item.php';
require_once __DIR__ . '/../database/db_connect.php';

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
    if(array_key_exists('size_data', $array)) { $item->size_data = json_decode($array['size_data'], true); }
    if(array_key_exists('url', $array)) { $item->url = $array['url']; }
    return $item;
}

function construct_entry_from_array(array $array) {
    $entry = new ShoesEntry();
    if(array_key_exists('item_id', $array)) { $entry->item_id = $array['item_id']; }
    if(array_key_exists('count', $array)) { $entry->count = $array['count']; }
    if(array_key_exists('size_data', $array)) { $entry->size_data = json_decode($array['size_data']); }
    return $entry;
}

function get_item_by_id($id) {
        $sql = get_db(current_db);
        $query = "SELECT * FROM " . Models . " WHERE id='$id' AND hide=0";
        if($result = $sql->query($query)) {
            if($result->num_rows !== 0) {
                $actor = $result->fetch_assoc();
                return construct_item_from_array($actor);
            }
        }
}

function get_items_by_category($category) {
    $sql = get_db(current_db);
    $result = $sql->query("SELECT * FROM " . Models . " WHERE category='$category'");
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
        $result = $db->query("INSERT INTO " . Models . " (name, color, price_retail, category, size_min, size_max) VALUES "
                . "('$shoesItem->name', '$shoesItem->color', '$shoesItem->price', '$shoesItem->category', '$shoesItem->min_size', '$shoesItem->max_size')");
        $shoesItem->ID = $db->insert_id;
        return $result ? true : false;
    }
    return false;
}

function get_item_price_retail($id) {
    $sql = get_db(current_db);
    $query = "SELECT price_retail FROM " . Models . " WHERE id='$id'";
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
    $query = "SELECT * FROM " . Models . " WHERE name = '$name' AND hide=0";
    if($result = $sql->query($query)) {
        $pos = 0;
        $items;
        while ($actor = $result->fetch_assoc()) {
            $items[$pos] = construct_item_from_array($actor);
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
    $query = "SELECT * FROM " . Models . " WHERE concat(name, color, price_retail, display_name, brand, gender) LIKE '%$str%'";
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
    $query = "SELECT * FROM " . Models . " WHERE hide=0";
    if($result = $sql->query($query)) {
        $pos = 0;
        $items = NULL;
        while ($actor = $result->fetch_assoc()) {
            $items[$pos] = construct_item_from_array($actor);
            $pos++;
        }
        return $items;
    }
}


/* ShoesEntry block */

function has_entry($cart_id, $item_id) {
    $sql = get_db(current_db);
    $result = $sql->query("SELECT item_id FROM " . Entries . " WHERE parent_id='$cart_id' AND parent_table='_Carts_' AND item_id='$item_id'");
    return $result->num_rows;
}

function get_entry_from_cart($cart_id, $item_id) {
    $sql = get_db(current_db);
    $result = $sql->query("SELECT * FROM " . Entries . " WHERE parent_id='$cart_id' AND parent_table='_Carts_' AND item_id='$item_id'");
    return $result ? $result->fetch_assoc() : null;
}

function update_entry_in_cart($cart_id, $item_id, $count, $size_data) {
    $sql = get_db(current_db);
    $sql->query("UPDATE " . Entries . " SET count='$count', size_data='$size_data' WHERE item_id='$item_id' AND parent_id='$cart_id' AND parent_table='_Carts_'");
    return $sql->affected_rows !== 0;
}

function add_entry_for_cart($cart_id, $item_id, $count, $size_data) {
    $sql = get_db(current_db);
    $sql->query("INSERT INTO " . Entries . " (item_id,parent_id,parent_table,count,size_data) VALUES ('$item_id','$cart_id','_Carts_','$count','$size_data')");
    return $sql->affected_rows !== 0;
}

function remove_entry_from_cart($cart_id, $item_id) {
    $sql = get_db(current_db);
    $sql->query("DELETE FROM " . Entries . " WHERE item_id='$item_id' AND parent_id='$cart_id' AND parent_table='_Carts_'");
    return $sql->affected_rows !== 0;
}

function get_entries($cart_id) {
    $sql = get_db(current_db);
    $result = $sql->query("SELECT item_id, count, size_data FROM " . Entries . " WHERE parent_id='$cart_id' AND parent_table='_Carts_'");
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

function sizes_sum(array $sizes) {
    $sum = 0;
    foreach ($sizes as $value) {
        $sum += $value;
    }
    return $sum;
}