<?php
namespace Xirosoft\Formit\Admin;
use Xirosoft\Formit\Admin\views\Settings\SettingConfig;

class AdminEnqueue{
    function __construct(){
        add_action('admin_enqueue_scripts', [$this, 'enqueue_dashboard_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'BuilderFormaData']);
        add_action('admin_enqueue_scripts', [$this, 'formit_ajax_localie']);
    }
    
    function enqueue_dashboard_scripts($hook) {

        $current_screen = get_current_screen();
        // Check if you're on the appropriate admin page(s) where you want to include your script      
        if ($current_screen && $current_screen->post_type == 'formit') {
            wp_enqueue_script( 'jquery-ui-widget' , 'jquery');
            wp_enqueue_script( 'jquery-ui-mouse' ,  'jquery');
            wp_enqueue_script( 'jquery-ui-accordion' ,  'jquery');
            wp_enqueue_script( 'jquery-ui-autocomplete' ,  'jquery');
            wp_enqueue_script( 'jquery-ui-slider' ,  'jquery');
            wp_enqueue_editor();
            wp_enqueue_script('formit-form-builder', FORMIT_ASSETS_URL . 'admin/js/form-builder.min.js', array('jquery'), '1.0', true);
            wp_enqueue_script('form-render', FORMIT_ASSETS_URL . 'admin/js/form-render.min.js', array('jquery'), time(), true);
            wp_enqueue_script('formit-admin-scripts', FORMIT_ASSETS_URL . 'admin/js/formit-admin-scripts.js', array('jquery'), time(), true);
            wp_enqueue_style('formit-admin-style', FORMIT_ASSETS_URL . 'admin/css/formit-admin-style.css?', array(), time(), 'all' );
        }
        
    }

    /**
     * JS peramiter localize function
     *
     * @param [type] $json_localize
     * @return void
     */
    function BuilderFormaData($json_localize){
        $settingconfig = new SettingConfig();
        /**
         * Object data send send script
         * Retrieve the API token and pass it to the script
         * @formit-admin-scripts
         */

        wp_localize_script('formit-admin-scripts', 'formit_scripts_localize', array(
            'GetBuilderJson' => json_decode($json_localize),
            'Form_settings_data' => $settingconfig->form_settings_config_data(),
        ));
        
    }

    /**
     * Ajax Data localize function
     * Retrieve the API token and pass it to the script 
     * @return void
     */
    function formit_ajax_localie(){
        wp_localize_script('formit-admin-scripts', 'formit_ajax_localize', array(
            'site_url'  => site_url(),
            'plugin_url'=> FORMIT_URL,
            'ajax_url'  => admin_url('admin-ajax.php'),
            'nonce'     => wp_create_nonce('formit-nonce')
        ));
        
    }
}



