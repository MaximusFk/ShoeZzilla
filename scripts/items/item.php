<?php

class ShoesItem {
    public $name;
    public $display_name;
    public $color = 0;
    public $secondaryColor;
    public $category;
    public $ID;
    public $price = 0.0;
    public $price2;
    public $img_code;
    public $img_main;
    public $min_size;
    public $max_size;
    public $url;
    public $size_data;
    public $infoPairs = array("none" => "none");
}

class ShoesEntry {
    public $item_id;
    public $count;
    public $size_data;
}