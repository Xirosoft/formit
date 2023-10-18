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
    // SQL query to select rows from the custom table with pagination and order
    $sql = $wpdb->prepare(
        "SELECT * FROM {$table_name}
        ORDER BY {$args['orderby']} {$args['order']}
        LIMIT %d, %d",
        $args['offset'], $args['number']
    );

    // Use $wpdb->get_results to execute the query and retrieve the data
    $results = $wpdb->get_results($sql);

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

    if ( false === $count ) {
        $count = (int) $wpdb->get_var( "SELECT count(id) FROM {$table_name}" );

        wp_cache_set( 'count', $count, 'address' );
    }

    return $count;
}