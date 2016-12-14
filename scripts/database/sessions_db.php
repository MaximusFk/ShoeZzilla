<?php
require_once __DIR__ . '/../items/item_db.php';

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
        return $sql->insert_id;
    }
    
    function equals_session($session_id) {
        $sql = get_db(current_db);
        $result = $sql->query("SELECT * FROM _Sessions_ WHERE id='$session_id'");
        return $result->num_rows !== 0;
    }