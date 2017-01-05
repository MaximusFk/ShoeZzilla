<?php
require_once '../scripts/twig.php';
require_once '../scripts/database/accounts_db.php';
require_once '../scripts/database/orders_db.php';
$session_id = filter_has_var(INPUT_COOKIE, 'session_id') ? filter_input(INPUT_COOKIE, 'session_id') : null;
if($session_id && linked_to_account($session_id)) {
    $loader = create_file_loader(['templates/profile']);
    $twig = create_twig($loader);
    $id = get_account_id_by_session($session_id);
    $account = get_account_info($id);
    $orders = get_orders_for_user($id);
    echo $twig->render('profile_view.twig', ['account' => $account, 'orders' => $orders, 'admin' => is_admin($id)]);
}
else {
    header("Location: /profile.php?type=login");
}

