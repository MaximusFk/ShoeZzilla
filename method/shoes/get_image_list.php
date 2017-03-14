<?php
require_once '../../scripts/items/item_db.php';

$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'GET' ? INPUT_GET : INPUT_POST;
$access_code = getallheaders()['AccessCode'];

function endsWith($haystack)
{
    $length = strlen('.jpg');
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === '.jpg');
}

if(filter_has_var($method, 'item_id')) {
    $item_id = filter_input($method, 'item_id');
    $item = get_item_by_id_raw($item_id);
    $image_path = $item['image_path'];
    $files = scandir("../../shoes/photos/thumbnails/" . $image_path);
    $files = array_values(array_filter($files, 'endsWith'));
    $json['count'] = count($files);
    $json['items'] = $files;
    echo json_encode($json);
}
