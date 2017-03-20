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
    if(array_key_exists('price_whole', $array)) { $item->price2 = $array['price_whole']; }
    if(array_key_exists('size_data', $array)) { $item->size_data = json_decode($array['size_data'], true); }
    if(array_key_exists('url', $array)) { $item->url = $array['url']; }
    if(array_key_exists('season', $array)) { $item->season = $array['season']; }
    return $item;
}

function construct_item_raw(array $array) {
    $array['size_data'] = json_decode($array['size_data'], true);
    $array['info'] = json_decode($array['info'], true);
    return $array;
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

function get_item_by_id_raw($id) {
    $sql = get_db(current_db);
    $query = "SELECT * FROM " . Models . " WHERE id='$id'";
    if ($result = $sql->query($query)) {
        if ($result->num_rows !== 0) {
            $actor = $result->fetch_assoc();
            $actor['size_data'] = json_decode($actor['size_data'], true);
            $actor['info'] = json_decode($actor['info'], true);
            return $actor;
        }
    }
}

function update_item($id, array $data) {
    $sql = get_db(current_db);
    $query = "UPDATE " . Models . " SET ";
    $comma = "";
    foreach ($data as $key => $value) {
        if(is_array($value)) {
            $value = json_encode($value);
        }
        $query .= "{$comma}{$key}='{$value}'";
        $comma = ",";
    }
    $query = $query . " WHERE id='{$id}'";
    $sql->query($query);
    return $sql->affected_rows !== 0;
}

function insert_item(array $data) {
    $sql = get_db(current_db);
    $query = "INSERT INTO " . Models . " (";
    $values = "(";
    $comma = "";
    foreach ($data as $key => $value) {
        $query .= $comma . $key;
        $values .= "$comma'$value'";
        $comma = ",";
    }
    $query = $query . ") VALUES " . $values . ")";
    $sql->query($query);
    return $sql->insert_id;
}

function delete_item($id) {
    $sql = get_db();
    if(!$sql->query('DELETE FROM ' . Models . ' WHERE id=' . $id)) {
        printf("Error: %s\n", $sql->error);
    }
    return $sql->affected_rows !== 0;
}

function get_items_by_category($category) {
    $sql = get_db(current_db);
    $result = $sql->query("SELECT * FROM " . Models . " WHERE category='$category' AND hide=0");
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

function get_items_by_name_raw($name) {
    $sql = get_db(current_db);
    $query = "SELECT * FROM " . Models . " WHERE name='$name' AND hide=0";
    $result = $sql->query($query);
    if ($result) {
        while ($actor = $result->fetch_assoc()) {
            $actor['size_data'] = json_decode($actor['size_data'], true);
            $actor['info'] = json_decode($actor['info'], true);
            $items[] = $actor;
        }
        return $items;
    }
}

function get_items_by_tag_raw($tag) {
    $sql = get_db();
    $tag = $sql->escape_string($tag);
    $result = $sql->query("SELECT * FROM " . Models . " WHERE info REGEXP " . 
            '\'.*\\"tag\\":\\"' . $tag . '\\"\'');
    return $result !== null ? $result->fetch_assoc() : null;
}

function get_items_where($where, $order = "", $page = 1, $page_size = 20, $like = "") {
    $sql = get_db(current_db);
    if($page >= 1 || $page_size > 0) {
        $limit = "LIMIT " . ($page === 1 ? $page_size : ($page_size * ($page - 1) . ", " . $page_size));
    }
    if($order) {
        $order = "ORDER BY " . $order;
    }
    if($like) {
        $like = " concat(name, color, price_retail, display_name, brand, gender) LIKE " . "'%$like%'";
    }
    $query = "SELECT * FROM " . Models . " WHERE $where hide=0 $order $like " . $limit;
    $result = $sql->query($query);
    if($result) {
        while ($actor = $result->fetch_assoc()) {
            $items[] = construct_item_raw($actor);
        }
        return $items;
    }
}

function get_page_count_where($where, $page_size = 20) {
    $sql = get_db(current_db);
    $query = "SELECT COUNT(*) FROM " . Models . " WHERE $where hide=0";
    $result = $sql->query($query);
    $count = $result ? $result->fetch_assoc()["COUNT(*)"] : 0;
    $pages = ceil($count / $page_size);
    return $pages;
}

function get_brands() {
    $sql = get_db();
    $result = $sql->query("SELECT DISTINCT(brand) FROM " . Models . " ORDER BY brand");
    $items = [];
    if($result && $result->num_rows !== 0) {
        while($assoc = $result->fetch_assoc()) {
            $items[] = $assoc['brand'];
        }
    }
    return $items;
}

function get_categories() {
    $sql = get_db();
    $result = $sql->query("SELECT DISTINCT(category) FROM " . Models . " ORDER BY category");
    $items = [];
    if($result && $result->num_rows !== 0) {
        while($assoc = $result->fetch_assoc()) {
            $items[] = $assoc['category'];
        }
    }
    return $items;
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

function get_raw_items() {
    $sql = get_db();
    $query = "SELECT * FROM " . Models;
    $result = $sql->query($query);
    if($result) {
        while($item = $result->fetch_assoc()) {
            $items[] = $item;
        }
        return $items;
    }
    return null;
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
    return $result->num_rows !== 0;
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

function get_entries_array($parent_id, $parent_table) {
    $sql = get_db(current_db);
    $result = $sql->query("SELECT item_id, count, size_data FROM " . Entries . " WHERE parent_id='$parent_id' AND parent_table='$parent_table'");
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

function remove_entries($parent_id, $parent_table) {
    $sql = get_db();
    $sql->query("DELETE FROM " . Entries . " WHERE parent_id='$parent_id' AND parent_table='$parent_table'");
    return $sql->affected_rows !== 0;
}

function set_new_parent($parent_id, $parent_table, $old_parent_id, $old_parent_table) {
    $sql = get_db();
    $sql->query("UPDATE " . Entries . " SET parent_id='$parent_id', parent_table='$parent_table' WHERE parent_id='$old_parent_id' AND parent_table='$old_parent_table'");
    return $sql->affected_rows !== 0;
}

function sizes_sum(array $sizes) {
    $sum = 0;
    foreach ($sizes as $value) {
        $sum += $value;
    }
    return $sum;
}