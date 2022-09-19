<?php

function isValidField($field){

    $field = strtolower($field);

    $valid = true;

    if(str_contains($field, 'select') || str_contains($field, 'delete') ||
    str_contains($field, 'drop') || str_contains($field, 'alter') ||
    str_contains($field, 'update') || str_contains($field, 'create') ||
    str_contains($field, 'where') || str_contains($field, '"') ||
    str_contains($field, '%') || str_contains($field, '!'))
    {
        echo("yes");
        $valid = false;
    }

    // if(strpos($field, 'select') !== false || strpos($field, 'delete') !== false||
    // strpos($field, 'drop') !== false|| strpos($field, 'alter') !== false||
    // strpos($field, 'update') !== false|| strpos($field, 'create') !== false||
    // strpos($field, 'where') !== false|| strpos($field, '"') !== false ||
    // strpos($field, '%') !== false || strpos($field, '!') !== false)
    // {
    //     $valid = false;
    // }

    return $valid;

}

function prepare( $field ){

    $field = str_replace("'", "\'", $field); // '
    $field = str_replace("`", "\`", $field); // `
    $field = str_replace('"', '\"', $field); // "

    return $field;

}