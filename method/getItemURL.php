<?php
require_once '../scripts/items/item_db.php';

$item_id = filter_input(INPUT_POST, 'item_id');

echo get_item_url($item_id);