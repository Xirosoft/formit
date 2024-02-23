<?php
// Enqueue assets
namespace Xirosoft\Formit\Frontend;

if ( ! class_exists( 'Formit_FrontendEnqueue' ) ) {
    class Formit_FrontendEnqueue{
        function __construct(){
            /**
            * This Enquee method for script and style 
            * @formit_load_fornend_assets
            */
            add_action('wp_enqueue_scripts', [$this, 'formit_load_fornend_assets']);

            /**
            * This Enquee method for data send to API 
            * @enqueue_my_scripts
            */
            // add_action('wp_enqueue_scripts', [$this, 'enqueue_my_scripts']);
        }

        /**
        * All Front-end Script and style enquee method.
        *
        * @return void
        */
        function formit_load_fornend_assets() {
            /**
            * Enquee All Scripts
            */
            wp_enqueue_script('jquery');
            wp_enqueue_script('formit-fontend-script', FORMIT_ASSETS_URL . 'frontend/js/formit-fontend-script.js', array('jquery'), time(), true);
            wp_localize_script('formit-fontend-script', 'formit_ajax_object', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('formit-nonce')
                ));
            
            /**
            * Enquee All Styles
            */
            wp_enqueue_style('formit-frontend-style', FORMIT_ASSETS_URL . 'frontend/css/formit-frontend-style.css', array(), time(), 'all' );
        }

        function formit_check_bootstrap_usage() {
            // Check if Bootstrap is being used
            $is_bootstrap_used = false;

            // Get all enqueued scripts and styles
            global $wp_scripts, $wp_styles;
            $enqueued_scripts = $wp_scripts->queue;
            $enqueued_styles = $wp_styles->queue;

            // Combine both script and style handles for comprehensive search
            $enqueued_items = array_merge($enqueued_scripts, $enqueued_styles);

            // Loop through the enqueued items to check for Bootstrap
            foreach ($enqueued_items as $handle) {
                // Get the URL of the enqueued script or style
                $src = "";
                if (wp_scripts()->registered[$handle]) {
                    $src = wp_scripts()->registered[$handle]->src;
                } elseif (wp_styles()->registered[$handle]) {
                    $src = wp_styles()->registered[$handle]->src;
                }

                // Check if Bootstrap is in the handle or file path
                if (strpos($handle, 'bootstrap') !== false || strpos($src, 'bootstrap') !== false) {
                    $is_bootstrap_used = true;
                    break;
                }
            }


            // If Bootstrap is not used, enqueue your CSS grid file
            if (!$is_bootstrap_used) {
                wp_enqueue_style('formit-frontend-grid-style', FORMIT_ASSETS_URL . 'frontend/css/formit-frontend-grid-style.css', array(), time(), 'all');
            }

        }
    }
}