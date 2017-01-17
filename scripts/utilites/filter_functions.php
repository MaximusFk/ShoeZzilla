<?php

function filter_fields (array $obj, $filter) {
    if(!$filter) {
        return $obj;
    }
    foreach ($filter as $f) {
        $r[$f] = $obj[$f];
    }
    return $r;
}

function filter_equals(array $obj, $equals) {
    if(!$equals) {
        return true;
    }
    foreach ($equals as $key => $value) {
        if(array_key_exists($key, $obj) && $obj[$key] === $value) {
            return true;
        } 
    }
    return false;
}

function filter(array $jarray, $filter, $equals = null) {
    foreach ($jarray as $value) {
        if(filter_equals($value, $equals)) {
            $result[] = filter_fields($value, $filter);
        }
    }
    return $result;
}

function find_where(array $object, $key, $value) {
    foreach ($object as $c) {
        if(array_key_exists($key, $c) && $c[$key] === $value) {
            return $c;
        }
    }
    return null;
}
