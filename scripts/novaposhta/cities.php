<?php
include_once 'HTTP/Request2.php';
require_once __DIR__ . '/../twig.php';

function get_cities_json() {
    if (file_exists(__DIR__ . "/cities.json")) {
        $file = fopen(__DIR__ . "/cities.json", 'r');
        $json = fread($file, filesize(__DIR__ . "/cities.json"));
        fclose($file);
        return $json;
    }
    else {
        $json = get_cities_json_net();
        $file = fopen(__DIR__ . "/cities.json", "w");
        fwrite($file, $json);
        fflush($file);
        fclose($file);
        return $json;
    }
}

function get_cities_object() {
    return json_decode(get_cities_json(), true);
}

function get_cities_json_net() {
    $request = new Http_Request2('http://api.novaposhta.ua/v2.0/json/Address/getCities');
    $url = $request->getUrl();
    $headers = [
        'Content-Type' => 'application/json',
    ];
    $request->setHeader($headers);
    $parameters = [
            // Request parameters
    ];
    $url->setQueryVariables($parameters);
    $request->setMethod(HTTP_Request2::METHOD_POST);
    $body = [
        'apiKey' => NOVA_POSHTA_ACCESS,
        'modelName' => 'Address',
        'calledMethod' => 'getCities'
    ];
// Request body
    $request->setBody(json_encode($body));
    try {
        $response = $request->send();
        $json = $response->getBody();
        return $json;
    } catch (HttpException $ex) {
        return null;
    }
}


