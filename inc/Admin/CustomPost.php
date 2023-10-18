<?php

namespace Xirosoft\Formit\Admin;


/**
 * Custom post Hander calass class
 */
class CustomPost
{
    function __construct(){
        add_action('init', [$this, 'custom_formit_builder_post_type']);
    }

    /**
     * Custom post callback function
     *
     * @return void
     */
    function custom_formit_builder_post_type() {
        // $msfrom_builder_icon = FORMIT_ASSETS_URL . 'img/logo-icon.svg'; // Replace with the URL of your custom icon
        $msfrom_builder_icon = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI3NDkiIGhlaWdodD0iOTAxIiBmaWxsPSJub25lIj48cGF0aCBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9Ii42IiBkPSJNNjc5LjkyMiAyOTAuMDc4SDY5djYxMC45MjFsNjEwLjkyMi02MTAuOTIxWiIvPjxwYXRoIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iLjYiIGQ9Ik02OSAwdjkwMUw2NzkuOTIyIDBINjlaIi8+PC9zdmc+"; // Replace with the URL of your custom icon
        // $msfrom_builder_icon = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI3NDkiIGhlaWdodD0iOTAxIiBmaWxsPSJub25lIj48ZyBmaWxsPSIjOUI1M0Y2IiBjbGlwLXBhdGg9InVybCgjYSkiPjxwYXRoIGQ9Ik02OTggMEg1MnYyMzUuOTA4aDQ1My44MTVMNjk4IDBaTTU1Mi42NSAzNTYuODM1SDUyVjkwMmw1MDAuNjUtNTQ1LjE2NVoiLz48L2c+PGRlZnM+PGNsaXBQYXRoIGlkPSJhIj48cGF0aCBmaWxsPSIjZmZmIiBkPSJNMCAwaDc0OXY5MDFIMHoiLz48L2NsaXBQYXRoPjwvZGVmcz48L3N2Zz4="; // Replace with the URL of your custom icon
        $labels = array(
            'name'               => _x( 'Formit Forms', 'Post Type General Name', 'formit' ),
            'singular_name'      => _x( 'Formit Builder', 'Post Type Singular Name', 'formit' ),
            'menu_name'          => _x( 'Formit Builder', 'Admin Menu text', 'formit' ),
            'name_admin_bar'     => _x( 'Formit Builder', 'Add New on Toolbar', 'formit' ),
            'add_new'            => __( 'Create From', 'formit' ),
            'add_new_item'       => __( ' ', 'formit' ),
            'new_item'           => __( 'New Formit Builder', 'formit' ),
            'edit_item'          => __( 'Edit Formit Builder', 'formit' ),
            'view_item'          => __( 'View Formit Builder', 'formit' ),
            'all_items'          => __( 'All Forms', 'formit' ),
            'search_items'       => __( 'Search Formit Builders', 'formit' ),
            'parent_item_colon'  => __( 'Parent Formit Builders:', 'formit' ),
            'not_found'          => __( 'No Formit Builders found.', 'formit' ),
            'not_found_in_trash' => __( 'No Formit Builders found in Trash.', 'formit' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'formit' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'           => $msfrom_builder_icon, // Replace with the URL of your custom icon
            'supports'           => array( 'title' ),
            'template' => array(
                array('core/paragraph', array('placeholder' => 'Add your content here...')),
                // Add more blocks as needed.
            ),
        );

        register_post_type( 'formit', $args );
    }

    
}
