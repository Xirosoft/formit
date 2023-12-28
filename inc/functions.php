<?php

function fromit_get_forms($args = array()) {
    global $wpdb;

    // Define the default options as an associative array
    $defaults = array(
        'number'     => 20,
        'offset'     => 0,
        'orderby'    => 'id',
        'order'      => 'ASC',
    );

    // Merge the provided arguments with the default options
    $args       = wp_parse_args($args, $defaults);
    $table_name = $wpdb->prefix . 'formit_forms';
    $query = "SELECT * FROM %1s ORDER BY %2s %3s LIMIT %d, %d";
    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
    $results = $wpdb->get_results($wpdb->prepare($query, $table_name, $args['orderby'], $args['order'], $args['offset'], $args['number']));

    // Check if there are results
    if ($results) {
        return $results;
    } else {
        // If no results were found, return an empty array or an error message, as needed
        return array();
        // Or you can return an error message, e.g., return 'No forms found.';
    }
}



/**
 * Get the count of total Forms
 *
 * @return int
 */
function formit_forms_count() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'formit_forms';
    $count = wp_cache_get( 'count', 'address' );
    $query = "SELECT count(id) FROM %1s";
    if ( false === $count ) {
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $count = (int) $wpdb->get_var($wpdb->prepare($query, $table_name) );

        wp_cache_set( 'count', $count, 'address' );
    }

    return $count;
}