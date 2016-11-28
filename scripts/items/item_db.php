<?php
require_once 'item.php';

function get_item_by_id($id) {
        $sql = new mysqli('127.0.0.1', 'root', 'maxime51mk', 'ShoesDB');
        $query = "SELECT name, color, price, img_code FROM models_test WHERE id = $id, hidden=0";
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
    $sql = new mysqli('127.0.0.1', 'root', 'maxime51mk', 'ShoesDB');
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
    $sql = new mysqli('127.0.0.1', 'root', 'maxime51mk', 'ShoesDB');
    $query = "SELECT * FROM models_test WHERE name = '$name', hidden=0";
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
    $sql = new mysqli('127.0.0.1', 'root', 'maxime51mk', 'ShoesDB');
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
    $sql = new mysqli('127.0.0.1', 'root', 'maxime51mk', 'ShoesDB');
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