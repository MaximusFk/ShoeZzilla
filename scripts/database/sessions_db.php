<?php
require_once __DIR__ . '/../items/item_db.php';

    function clear_old_sessions($sql = null) {
        if(!$sql) {
            $sql = get_db();
        }
        $tcarts = Carts;
        $tsessions = Sessions;
        $query_free_carts = "DELETE FROM $tcarts,$tsessions USING $tcarts,$tsessions WHERE "
            . "({$tcarts}.session_id={$tsessions}.id) AND (TO_DAYS(NOW())-TO_DAYS({$tsessions}.time)>7)";
        $query_free_sessions = "DELETE FROM $tsessions WHERE (TO_DAYS(NOW())-TO_DAYS({$tsessions}.time)>7)";
        if(!$sql->query($query_free_carts)) {
            printf("Error: %s\n", $sql->error);
        }
        if(!$sql->query($query_free_sessions)) {
            printf("Error: %s\n", $sql->error);
        }
    }

    function create_session($user_id = null, $ip = null) {
        $sql = get_db(current_db);
        $date = date("Y-m-d H:i:s");
        $query = "INSERT INTO _Sessions_ (time";
        $values = ") VALUES ('$date'";
        if($user_id) {
            $query .= ",account_id";
            $values .= ",'$user_id'";
        }
        if($ip) {
            $query .= ",ip";
            $values .= ",'$ip'";
        }
        $query .= $values . ")";
        if(!$sql->query($query)) {
            printf("Error: %s\n", $sql->error);
            return null;
        }
        $newid = $sql->insert_id;
        clear_old_sessions($sql);
        return $newid;
    }
    
    function linked_to_account($session_id) {
        $sql = get_db(current_db);
        $result = $sql->query("SELECT account_id FROM _Sessions_ WHERE id='$session_id'");
        return $result->fetch_assoc()['account_id'] !== null;
    }


    function equals_session($session_id) {
        $sql = get_db(current_db);
        $result = $sql->query("SELECT * FROM _Sessions_ WHERE id='$session_id'");
        return $result->num_rows !== 0;
    }
    
    function get_account_id_by_session($session_id) {
        $sql = get_db(current_db);
        $res = $sql->query("SELECT account_id FROM " . Sessions . " WHERE id='$session_id'");
        return $res && $res->num_rows !== 0 ? $res->fetch_assoc()['account_id'] : NULL;
    }
    
    function set_account_id_for_session($session_id, $account_id) {
        $sql = get_db(current_db);
        if($account_id === null) {
            $sql->query("UPDATE " . Sessions . " SET account_id=NULL WHERE id='$session_id'");
        }
        else {
            $sql->query("UPDATE " . Sessions . " SET account_id='$account_id' WHERE id='$session_id'");
        }
        return $sql->affected_rows !== 0;
    }