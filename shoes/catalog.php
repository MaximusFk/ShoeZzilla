<?php
require_once '../scripts/items/cart_db.php';
require_once '../scripts/twig.php';

function cmp_name($a, $b) {
    return strcasecmp($a->display_name, $b->display_name);;
}

function cmp_price($a, $b) {
    return $a->price !== $b->price ? ($a->price < $b->price ? -1 : 1) : 0;
}

if($_SERVER['REQUEST_METHOD'] === 'GET') {
    $loader = create_file_loader(['info_pages', 'shoes_cards']);
    $twig = create_twig($loader);
    if(filter_has_var(INPUT_GET, 'item_id')) {
        $item_id = filter_input(INPUT_GET, 'item_id');
        $item = get_item_by_id_raw($item_id);
        $session_id = filter_input(INPUT_COOKIE, 'session_id');
        $has = has_entry(get_cart_id($session_id), $item_id);
        $additional = get_items_by_name_raw($item['name']);
        if($item) {
            echo $twig->render('shoes_show.twig', [
                "item" => $item, 
                "has_in_cart" => $has,
                "additional" => $additional,
                "admin" => is_admin(get_account_id_by_session($session_id))]);
        }
        else {
            echo $twig->render('error_page.twig', array("text" => "Страница не найдена!"));
        }
    }
    else {
        $where = "";
        if (filter_has_var(INPUT_GET, 'category')) {
            $category = filter_input(INPUT_GET, 'category');
            $current['category'] = $category = strtoupper($category);
            if($category !== 'ALL') {
                $where .= "category='" . "$category' AND";
            }
        }
        if(filter_has_var(INPUT_GET, 'season')) {
            $season = filter_input(INPUT_GET, 'season');
            $current['season'] = $season;
            if($season !== 'all') {
                $where .= " season='$season' AND";
            }
        }
        $filter['price_max'] = get_max_value(Models, 'price_retail');
        $filter['price_min'] = get_min_value(Models, 'price_retail');
        if(filter_has_var(INPUT_GET, 'price_min') && filter_has_var(INPUT_GET, 'price_max')) {
            $price_min = filter_input(INPUT_GET, 'price_min');
            $price_max = filter_input(INPUT_GET, 'price_max');
            $current['price_min'] = $price_min;
            $current['price_max'] = $price_max;
            if(!$price_min) {
                $price_min = $filter['price_min'];
            }
            if(!$price_max) {
                $price_max = $filter['price_max'];
            }
            $where .= " price_retail>='$price_min' AND price_retail<='$price_max' AND";
        }
        $current['sort'] = $sort_arg = filter_input(INPUT_GET, 'sort');
        $current['sort_t'] = $sort_type = filter_input(INPUT_GET, 'sort_t');
        $current['page'] = $page = filter_has_var(INPUT_GET, 'page') ? filter_input(INPUT_GET, 'page') : 1;
        $items = get_items_where($where, $sort_arg ? "$sort_arg $sort_type" : "", $page);
        $filter['brands'] = get_brands();
        $filter['categories'] = get_unique_values_list(Models, 'category');
        $filter['seasons'] = get_unique_values_list(Models, 'season');
        $filter['page_count'] = get_page_count_where($where);
        echo $twig->render('show_shoes_list_box.twig', ["items" => $items, 'filter' => $filter, 'current' => $current]);
    }
}
else {
    die('Ошибка!');
}
