<?php
function map( $function, $array ){
    return array_map( $function, array_keys($array), array_values($array) );
}

function slice( array $array, $keys ){
    $args  = func_get_args();
    $input = array_shift($args);

    if( is_array( $args[0] ) ){
        $args = $args[0];
    }

    $args = array_flip($args);

    return array_intersect_key( $input, $args );
}
