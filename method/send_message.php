<?php

if(filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'GET') {
    $name = filter_input(INPUT_GET, 'name');
    $email = filter_input(INPUT_GET, 'email');
    $phone = filter_input(INPUT_GET, 'phone');
    $message = filter_input(INPUT_GET, 'message');
    $mess[] = 'Имя: ' . $name;
    $mess[] = 'E-mail: ' . $email;
    $mess[] = 'Phone: ' . $phone;
    $mess[] = 'Сообщение: ' . $message;
    
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/txt; charset=utf-8';
    // Дополнительные заголовки
    $headers[] = 'From: ShoeZzilla <admin@shoezzilla.zzz.com.ua>';
    echo mail("maximusfk@gmail.com", "Сообщение обратной связи", implode("\r\n", $mess), implode("\r\n", $headers));
}

