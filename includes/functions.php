<?php

/**
 * Helper function to return object of user metadata
 * @param $id
 * @return object
 */
function aact_user_meta_object($id){
    $meta = (object)array_map(function ($a) {
        return $a[0];
    }, get_user_meta($id));

    return $meta;
}


/**
 * Helper function to return object of post and custom post metadata
 * @param $id
 * @return object
 */
function aact_post_meta_object($id){
    $meta = (object)array_map(function ($a) {
        return $a[0];
    }, get_post_meta($id));

    return $meta;
}

/**
 * Check if a date is before or after earlybird date
 * @param $date
 * @return bool
 *
 */
function check_early_bird($date){
    $earlybird = true;
    if (strtotime(str_replace('/', '-', $date)) > time()) {
        $earlybird = false;
    }
    return $earlybird;
}

