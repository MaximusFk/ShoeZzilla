<?php
include_once 'HTTP/Request2.php';
require_once __DIR__ . '/../twig.php';

function get_warehouses_json($cityref) {
    $filename = $cityref ? $cityref . ".json" : "warehouses.json";
    $fpath = __DIR__ . "/" . $filename;
    if (file_exists($fpath)) {
        $file = fopen($fpath, 'r');
        $json = fread($file, filesize($fpath));
        fclose($file);
        return $json;
    }
    else {
        $json = get_warehouses_json_net($cityref);
        $file = fopen($fpath, "w");
        fwrite($file, $json);
        fflush($file);
        fclose($file);
        return $json;
    }
}

function get_warehouses_object($cityref) {
    return json_decode(get_warehouses_json($cityref), true);
}

function get_warehouses_json_net($cityRef = null) {
    $request = new Http_Request2('http://api.novaposhta.ua/v2.0/json/Address/getWarehouses');
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
        'modelName' => 'AddressGeneral',
        'calledMethod' => 'getWarehouses'
    ];
    if($cityRef) {
        $body['methodProperties'] = [
            'CityRef' => $cityRef
        ];
    }
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

