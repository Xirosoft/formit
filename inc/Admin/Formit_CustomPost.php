<?php

namespace Xirosoft\Formit\Admin;


/**
 * Custom post Hander calass class
 */

 if ( ! class_exists( 'Formit_CustomPost' ) ) {
    class Formit_CustomPost{
        function __construct(){
            add_action('init', [$this, 'formit_builder_custom_post_type']);
        }

        /**
        * Custom post callback function
        *
        * @return void
        */
        function formit_builder_custom_post_type() {
            $formit_builder_icon = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI3NDkiIGhlaWdodD0iOTAxIiBmaWxsPSJub25lIj48cGF0aCBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9Ii42IiBkPSJNNjc5LjkyMiAyOTAuMDc4SDY5djYxMC45MjFsNjEwLjkyMi02MTAuOTIxWiIvPjxwYXRoIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iLjYiIGQ9Ik02OSAwdjkwMUw2NzkuOTIyIDBINjlaIi8+PC9zdmc+"; // Replace with the URL of your custom icon
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
                'menu_icon'          => $formit_builder_icon, // Replace with the URL of your custom icon
                'supports'           => array( 'title' ),
                'template' => array(
                    array('core/paragraph', array('placeholder' => 'Add your content here...')),
                    // Add more blocks as needed.
                ),
            );

            register_post_type( 'formit', $args );
        }

        
    }
}