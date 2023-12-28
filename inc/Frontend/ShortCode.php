<?php

namespace Xirosoft\Formit\Frontend;
/* 
* Shortcode Builder 
* @save_formit_builder_shortcode method
*/

class ShortCode
{
    function __construct(){
        /**
         * Register the shortcode form builder action hook wtih below method
         * @shortcode_formit_builder
         */
        add_shortcode('formit',[$this, 'shortcode_formit_builder']);
    }

    /* 
    * Shortcode Builder 
    * 
    */
    function shortcode_formit_builder($atts) {
        // Get the post ID from the shortcode attributes
        $post_id = isset($atts['id']) ? intval($atts['id']) : null;
        // Get Data with Post ID

        global $wpdb;
        $table_name = $wpdb->prefix . 'formit_forms'; // Replace 'your_custom_table' with your actual table name
        $query = "SELECT * FROM %1s WHERE post_id = %d";
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $result = $wpdb->get_row($wpdb->prepare($query,$table_name, $post_id));
    
        if ($post_id !== null &&  $result !==  null) {
            // Retrieve the content of the 'formit' post
            $post_content = get_post($post_id);
        
            // Check if the post exists and is of the 'formit' type
            if ($post_content && $post_content->post_type === 'formit') {
                // Return the content of the post
                // Display the post title, metadata, and content
                $output = '<div class="formit-form template-basic">';
                $output .= '<form method="POST" class="formit-content" id="formit-'. $post_id .'"><input type="hidden" name="formit_id"  id="formit_id" value="'. $result->id .'"><input type="hidden" name="post_id"  id="post_id" value="'. $post_id .'">
                <input type="hidden" name="formit_name"  name="formit_name" value="'.$result->form_title.'">' . stripslashes($result->form_html) . '</form>
                <div class="xiroform-circle">
                </div> <div class="xiroform-msg"></div>';
                $output .= '</div>';
                return $output;

            } else {
                // Post not found or not of the correct type
                return __('Invalid or missing Shortcode.', 'formit');
            }
        } else {
            // No post ID provided
            return __('Please provide a valid form Shortcode This shorcode remove/delete.', 'formit');
        }

    }
}
