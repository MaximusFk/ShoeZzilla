<?php
require_once '../../scripts/items/item_db.php';

$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'GET' ? INPUT_GET : INPUT_POST;
$access_code = getallheaders()['AccessCode'];

if(filter_has_var($method, 'item_id')) {
    $item_id = filter_input($method, 'item_id');
    $item = get_item_by_id_raw($item_id);
    echo json_encode($item);
}
else {
    $items = get_raw_items();
    echo json_encode($items);
}