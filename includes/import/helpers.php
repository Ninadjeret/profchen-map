<?php

function import_gym_coor_lat( $value ) {
    $coor = explode(',', $value);
    return $coor[0]; 
}

function import_gym_coor_lng( $value ) {
    $coor = explode(',', $value);
    return $coor[1]; 
}

