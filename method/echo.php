<?php
$request['method'] = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
$request['variables'] = $_REQUEST;
$request['host'] = filter_input(INPUT_SERVER, 'HTTP_HOST');

echo json_encode($request);

