<?php
require_once '../scripts/twig.php';
require_once '../scripts/database/orders_db.php';
require_once '../scripts/items/item_db.php';
require_once '../scripts/novaposhta/cities.php';
require_once '../scripts/novaposhta/warehouses.php';
require_once '../scripts/utilites/filter_functions.php';

$loader = create_file_loader(["info_pages"]);
$twig = create_twig($loader);

function get_address_from_delivery_data($delivery_data) {
    if($delivery_data['type'] === 'novaposhta'){
        $city = find_where(get_cities_object()['data'], 'Ref', $delivery_data['cityUUID']);
        $office = find_where(get_warehouses_object(NULL)['data'], 'Ref', $delivery_data['officeUUID']);
        return $city['DescriptionRu'] . ", " . $office['DescriptionRu'] . "[{$delivery_data['type']}]";
    }
}

if(!is_admin(get_account_id_by_session(filter_input(INPUT_COOKIE, 'session_id')))) {
    echo $twig->render('error_page_twig', ['text' => 'Ошибка доступа!']);
}

if(filter_has_var(INPUT_GET, 'order_id') && filter_has_var(INPUT_GET, 'data')) {
    $order_id = filter_input(INPUT_GET, 'order_id');
    $data = json_decode(filter_input(INPUT_GET, 'data'), true);
    echo update_order($order_id, $data) ? "access" : "denied";
}
else if(filter_has_var(INPUT_GET, 'order_id') && filter_has_var(INPUT_GET, 'remove')){
    $order_id = filter_input(INPUT_GET, 'order_id');
    if(remove_entries($order_id, Orders)) {
        echo remove_order($order_id) ? "done" : "error";
    }
}
else if(filter_has_var(INPUT_GET, 'order_id')) {
    $order_id = filter_input(INPUT_GET, 'order_id');
    $order = get_order_by_id($order_id);
    $order['address'] = get_address_from_delivery_data(json_decode($order['delivery_data'], true));
    $status_list = get_enum_values(Orders, 'status');
    $entries = get_entries_array($order_id, Orders);
    foreach ($entries as &$key) {
        $key['size_data'] = json_decode($key['size_data'], true);
        ksort($key['size_data']);
        $key['node'] = get_item_by_id($key['item_id']);
    }

    echo $twig->render('edit_order.twig', ['order' => $order, 'status_list' => $status_list, 'items' => $entries]);
}
else {
    $orders = get_orders();
    usort($orders, function ($a, $b) {
        return strcmp($b['date'], $a['date']);
    });
    
    echo $twig->render('edit_order.twig', ['order_list' => $orders]);
}