<?php
require_once('HTML/BBCodeParser.php');

function decode_bb($string, array $filters = []) {
    $bb = new HTML_BBCodeParser();
    $bb->addFilters($filters);
    return $bb->qparse($string);
}

function decode_bb_a($string) {
    return decode_bb($string, ['Basic', 'Extended', 'Images', 'Links']);
}

