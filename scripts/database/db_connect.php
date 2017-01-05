<?php

define('Password', 'maxime51mk');
define('Login', 'maximusfk');
define('current_db', 'maximusfk');
define('Host', 'localhost');

define('Accounts', '_Accounts_');
define('Models', '_Shoes_');
define('Entries', '_ShoesEntries_');
define('Sessions', '_Sessions_');
define('Carts', '_Carts_');
define('Orders', '_Orders_');

function get_db($name = current_db) {
	return new mysqli(Host, Login, Password, $name);
}

function get_unique_values_list($table, $column) {
    $sql = get_db();
    $result = $sql->query("SELECT DISTINCT($column) FROM " . $table . " ORDER BY $column");
    $items = [];
    if($result && $result->num_rows !== 0) {
        while($assoc = $result->fetch_assoc()) {
            $items[] = $assoc[$column];
        }
    }
    return $items;
}

function get_max_value($table, $column) {
    $sql = get_db();
    $result = $sql->query("SELECT MAX($column) FROM " . $table);
    if($result && $result->num_rows !== 0) {
        return $result->fetch_assoc()["MAX($column)"];
    }
    else {
        return 0;
    }
}

function get_min_value($table, $column) {
    $sql = get_db();
    $result = $sql->query("SELECT MIN($column) FROM " . $table);
    if($result && $result->num_rows !== 0) {
        return $result->fetch_assoc()["MIN($column)"];
    }
    else {
        return 0;
    }
}

function get_enum_values($table, $column) {
    $sql = get_db();
    $result = $sql->query("SHOW COLUMNS FROM {$table} WHERE Field = '{$column}'");
    $type = $result ? $result->fetch_assoc()['Type'] : null;
    preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
    $enum = explode("','", $matches[1]);
    return $enum;
}
