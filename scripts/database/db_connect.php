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

