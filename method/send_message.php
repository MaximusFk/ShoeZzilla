<?php
ini_set("include_path", '/home/aorynqco/php:' . ini_get("include_path") );
require_once 'Mail.php';

if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'GET') {
    $name = filter_input(INPUT_GET, 'name');
    $email = filter_input(INPUT_GET, 'email');
    $phone = filter_input(INPUT_GET, 'phone');
    $message = filter_input(INPUT_GET, 'message');
    $mess[] = 'Имя: ' . $name;
    $mess[] = 'E-mail: ' . $email;
    $mess[] = 'Phone: ' . $phone;
    $mess[] = 'Сообщение: ' . $message;
    $body = implode("\r\n", $mess);
    
    $headers['From'] = 'callback@shoezzilla.com.ua';
    $headers['To'] = 'maximusfk@gmail.com';
    $headers['Subject'] = 'Сообщение обратной связи';
    $headers['Content-Type'] = 'text/txt; charset=utf-8';
    
    $params['host'] = 'mail.shoezzilla.com.ua';
    $params['port'] = 26;
    $params['auth'] = true;
    $params['username'] = 'callback@shoezzilla.com.ua';
    $params['password'] = 'maxime51mk';
    $params['timeout'] = 300;
    
    $mail = Mail::factory('smtp', $params);
    $mail->send('maximusfk@gmail.com', $headers, $body);
    echo true;
}
