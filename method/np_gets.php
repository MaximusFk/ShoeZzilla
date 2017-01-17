<?php
require_once __DIR__ . "/../scripts/novaposhta/cities.php";
require_once __DIR__ . "/../scripts/novaposhta/warehouses.php";
require_once __DIR__ . "/../scripts/utilites/filter_functions.php";

// REST Get method input(method = cities | warehouses, [filter = {json field}]) >>> output json

if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'GET') {
    $get_type = filter_input(INPUT_GET, 'method');
    $field_filter = $_GET['filter'];
    $field_equals = $_GET['equals'];
    switch ($get_type) {
        case 'cities':
            $json = get_cities_json();
            $cities = json_decode($json, true)['data'];
            echo json_encode(filter($cities, $field_filter, $field_equals));
            break;
        case 'warehouses':
            $cityRef = filter_has_var(INPUT_GET, 'cityRef') ? filter_input(INPUT_GET, 'cityRef') : null;
            $json = get_warehouses_json($cityRef);
            $warehouses = json_decode($json, true)['data'];
            echo json_encode(filter($warehouses, $field_filter, $field_equals));
            break;
        case 'find_city':
            $ref = filter_input(INPUT_GET, 'ref');
            $cities = get_cities_object()['data'];
            echo json_encode(find_where($cities, 'Ref', $ref));
            break;
        default:
            echo 'Method not found';
            break;
    }
}