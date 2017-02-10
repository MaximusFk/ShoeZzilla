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
    $where = "";
    if (filter_has_var(INPUT_GET, 'category')) {
        $category = filter_input(INPUT_GET, 'category');
        if ($category !== 'ALL') {
            $where .= "category='" . "$category' AND";
        }
    }
    if (filter_has_var(INPUT_GET, 'season')) {
        $season = filter_input(INPUT_GET, 'season');
        if ($season !== 'all') {
            $where .= " season='$season' AND";
        }
    }
    $filter['price_max'] = get_max_value(Models, 'price_retail');
    $filter['price_min'] = get_min_value(Models, 'price_retail');
    if (filter_has_var(INPUT_GET, 'price_min') && filter_has_var(INPUT_GET, 'price_max')) {
        $price_min = filter_input(INPUT_GET, 'price_min');
        $price_max = filter_input(INPUT_GET, 'price_max');
        if (!$price_min) {
            $price_min = $filter['price_min'];
        }
        if (!$price_max) {
            $price_max = $filter['price_max'];
        }
        $where .= " price_retail>='$price_min' AND price_retail<='$price_max' AND";
    }
    if (filter_has_var(INPUT_GET, 'brands')) {
        $args = implode('|', $current['brands']);
        $where .= ' brand REGEXP ' . "'$args' AND";
    }

    $sort_arg = filter_input(INPUT_GET, 'sort');
    $sort_type = filter_input(INPUT_GET, 'sort_t');
    $page = filter_has_var(INPUT_GET, 'page') ? filter_input(INPUT_GET, 'page') : 0;
    $page_size = filter_has_var(INPUT_GET, 'page_size') ? filter_input(INPUT_GET, 'page_size') : 20;
    $items['items'] = get_items_where($where, $sort_arg ? "$sort_arg $sort_type" : "", $page, $page_size);
    $items['count'] = count($items['items']);
    echo json_encode($items);
}