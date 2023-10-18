<?php
// Enqueue assets
namespace Xirosoft\Formit\Frontend;
class FrontendEnqueue{
    function __construct(){
        /**
         * This Enquee method for script and style 
         * @load_ms_form_assets
         */
        add_action('wp_enqueue_scripts', [$this, 'load_ms_form_assets']);

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
    function load_ms_form_assets() {
        /**
         * Enquee All Scripts
         */
        wp_enqueue_script('jquery');
        wp_enqueue_script('formit-fontend-script', FORMIT_ASSETS_URL . 'frontend/js/formit-fontend-script.js', array('jquery'), time(), true);
        wp_localize_script('formit-fontend-script', 'formit_ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
        
        /**
         * Enquee All Styles
         */
        wp_enqueue_style('formit-frontend-style', FORMIT_ASSETS_URL . 'frontend/css/formit-frontend-style.css', array(), time(), 'all' );
    }

}
