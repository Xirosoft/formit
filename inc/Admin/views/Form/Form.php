<?php
namespace Xirosoft\Formit\Admin\views\Form;
use Xirosoft\Formit\Query;

class Form{
    function __construct(){   
        add_action('wp_ajax_get_wp_pages', array($this, 'get_wp_pages'));
        add_action('wp_ajax_nopriv_get_wp_pages', array($this, 'get_wp_pages'));

        add_action('wp_ajax_from_after_submission', array($this, 'from_after_submission'));
        add_action('wp_ajax_nopriv_from_after_submission', array($this, 'from_after_submission'));
    }

    /**
     * Get Page list function
     *
     * @return void
     */
    function get_wp_pages() {
        $pages = get_pages(); // Fetch all WordPress pages
        $formatted_pages = array();

        foreach ($pages as $page) {
            $formatted_pages[] = array(
                'ID' => $page->ID,
                'post_title' => $page->post_title,  
                'page_link' => get_permalink($page->ID),
            );
        }

        wp_send_json(array('pages' => $formatted_pages));
        wp_die();
    }

    function from_after_submission(){
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ) , 'formit-nonce' ) ) return;
        $from_id = sanitize_text_field($_POST['from_id']);
        global $wpdb;
        $getfromSettings = new Query;
        $table_name = $wpdb->prefix . 'formit_forms'; // Replace 'your_custom_table' with your actual table name
    
        $query = $getfromSettings->get_single_row($table_name, $from_id);
        $get_data = json_decode($query['form_configs']);

        wp_send_json($get_data[0]->msfrom_redirect);
    }

    function form_builder_dom($post_id){
        // Get the generated shortcode from post meta
        $shortcode = get_post_meta($post_id, '_formit_builder_shortcode', true);
        ob_start();
        ?>
        <div class="wrap">
             <div class="form-data">
                <div class="form-data">
                    <div class="tab-header">
                        <button type="button" class="tab-button active-tab" data-tab="tab1"><?php echo esc_html__('Form Builder', 'formit'); ?></button>
                        <button type="button" class="tab-button" data-tab="tab2"><?php echo esc_html__('Settings', 'formit'); ?></button>
                        <button type="button" class="copy_shortcode" title="Click to copy"><?php echo esc_html($shortcode); ?></button>
                        <button type="button" class="copy_shortcode copy_shortcode_mbl" title="Click to copy"><?php echo esc_html__('Copy', 'formit'); ?></button>
                    </div>

                    <div id="tab1" class="tab tab-builder active">
                        <?php 
                           new FromBuilder();
                        ?>
                    </div>
                    <div id="tab2" class="tab">
                    <?php 
                           new FromSettings(); 
                        ?>
                    </div>
        
                </div>
            </div>
        </div>
    <?php 
    ob_end_flush();
    }
  

}
