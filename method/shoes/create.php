<?php
require_once '../../scripts/items/item_db.php';
require_once '../../scripts/database/method_accessor.php';

$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'GET' ? INPUT_GET : INPUT_POST;
$access_code = getallheaders()['AccessCode'];

if(!equals_with_global_access($access_code)) {
    echo '{"error":"access_denied"}';
    return;
}

$data['name'] = filter_has_var($method, 'name') ? filter_input($method, 'name', FILTER_SANITIZE_STRIPPED) : 'no_name';
$data['display_name'] = filter_has_var($method, 'display_name') ? filter_input($method, 'display_name', FILTER_SANITIZE_STRIPPED) : 'none';
$data['brand'] = filter_has_var($method, 'brand') ? filter_input($method, 'brand', FILTER_SANITIZE_STRIPPED) : 'none';
$data['image_path'] = filter_has_var($method, 'image_path') ? filter_input($method, 'image_path', FILTER_SANITIZE_STRIPPED) : null;
$data['image_head'] = filter_has_var($method, 'image_head') ? filter_input($method, 'image_head', FILTER_SANITIZE_STRIPPED) : null;
$data['price_retail'] = filter_has_var($method, 'price_retail') ? filter_input($method, 'price_retail', FILTER_VALIDATE_INT) : 0;
$data['price_whole'] = filter_has_var($method, 'price_whole') ? filter_input($method, 'price_whole', FILTER_VALIDATE_INT) : 0;
$data['size_min'] = filter_has_var($method, 'size_min') ? filter_input($method, 'size_min', FILTER_VALIDATE_INT) : 0;
$data['size_max'] = filter_has_var($method, 'size_max') ? filter_input($method, 'size_max', FILTER_VALIDATE_INT) : 0;
$data['category'] = filter_has_var($method, 'category') ? strtoupper(filter_input($method, 'category', FILTER_SANITIZE_STRIPPED)) : 'CROSS';
$data['season'] = filter_has_var($method, 'season') ? strtoupper(filter_input($method, 'season', FILTER_SANITIZE_STRIPPED)) : 'SPAU';
$data['color'] = filter_has_var($method, 'color') ? strtoupper(filter_input($method, 'color', FILTER_SANITIZE_STRIPPED)) : 'NONE';
$data['gender'] = filter_has_var($method, 'gender') ? strtoupper(filter_input($method, 'gender', FILTER_SANITIZE_STRIPPED)) : 'MALE';

$data['id'] = insert_item($data);

echo json_encode($data);