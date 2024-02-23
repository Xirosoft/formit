<?php 
namespace Xirosoft\Formit\Admin;

if ( ! class_exists( 'Formit_AddMetaBox' ) ) {
    class Formit_AddMetaBox{

        /**
        * AddMetaBox construct function
        */
        function __construct(){
            
            /**
            * Save shortcode builder action hook wtih below method
            * @formit_save_builder_shortcode
            */
            add_action('save_post',[$this, 'formit_save_builder_shortcode']);

            /**
            * Form Builder Metaboxes Action action hook wtih below method
            * @formit_add_custom_field_to_builder
            */
            add_action('add_meta_boxes',[$this, 'formit_add_custom_field_to_builder']);
        
            /**
            * This Working for publish hook
            * and callback @formit_builder_post_publish_hook
            */       
            add_action('save_post', [$this, 'formit_builder_post_publish_hook']);

            /**
            * Custom message hook
            * callback @formit_custom_post_published_message
            */
            add_filter('post_updated_messages', [$this, 'formit_custom_post_published_message']);
            
            /**
            * Delete from hook
            * @formit_custom_post_deleted_callback callback
            */
            add_action('wp_trash_post', [$this, 'formit_custom_post_deleted_callback']);

        }
        

        /**
        * Callback function to Save databese shortcode
        * Developer need to read below method
        * @return void
        */
        function formit_save_builder_shortcode($post_id) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'formit_forms';
            $query = "SELECT * FROM %1s WHERE post_id = %d";
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            $result = $wpdb->get_row($wpdb->prepare($query, $table_name, $post_id));
            
            if ($result !== null) {
                $shortcode_title = $result->form_title;
            } else {
                $shortcode_title = get_the_title($post_id);
            }

            if ($post_id && get_post_type($post_id) === 'formit') {
                $shortcode = '[formit id="' . esc_attr($post_id) . '"  title="' . esc_attr($shortcode_title) . '"]';
                update_post_meta($post_id, '_formit_builder_shortcode', $shortcode);
            }
        }


        /**
        * Add a custom field under the title
        * Developer need to read
        * @return void
        */
        function formit_add_custom_field_to_builder() {
            add_meta_box(
                'formit_builder_custom_field',
                __( 'Formit Form Builder', 'formit' ),
                [$this, 'formit_builder_custom_field'],
                'formit',
                'normal',
                'default'
            );
        }

        /**
        * This method just for form builder add on meta 
        * Developer need to read
        * @param [type] $post
        * @return void
        */
        function formit_builder_custom_field($post) {
            $post            = get_post($post->ID); // Get post with ID
            $post_status     = get_post_status($post->ID); // Get 
        ?>
            <div id="publishing-action">
                <span class="spinner"></span>
                <?php
                    if ($post_status !== 'publish') {
                        echo '<input name="original_publish" type="hidden" id="original_publish" value="' . esc_attr__('Create From', 'formit') . '">';
                        echo '<input type="submit" name="publish" id="publish" class="button button-primary button-large" value="' . esc_attr__('Create From', 'formit') . '"> ';
                    } else {
                        echo '<input name="original_publish" type="hidden" id="original_publish" value="' . esc_attr__('Update From', 'formit') . '">';
                        echo '<input type="submit" name="publish" id="publish" class="button button-primary button-large" value="' . esc_attr__('Update From', 'formit') . '"> ';
                    }
                ?>			
            </div>       
            <?php
            // Add your custom HTML here
            $formit_form_builder_dom = new views\Form\Formit_Form();
            $formit_form_builder_dom->formit_form_builder_dom($post->ID);
                
            /**
            * For Json Localize
            */
            $postIdTransfer = new Formit_AdminEnqueue(); 
            global $wpdb;
            $table_name = $wpdb->prefix . 'formit_forms'; 
            $query = "SELECT * FROM %1s WHERE post_id = %d";
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            $result = $wpdb->get_row($wpdb->prepare($query, $table_name, $post->ID));
        
            if ($post_status !== 'publish') {
                $postIdTransfer->formit_ajax_localie();
            }else{
                if($result){
                    $postIdTransfer->formit_builder_form_data($result->form_json);
                }
            }
            // Add a nonce field for security
            wp_nonce_field('formit_builder_nonce', 'formit_builder_nonce_field');
        }
        
        /**
        * Undocumented function
        *
        * @param [type] $post_id
        * @return void
        */
        function formit_save_from_dom_field($post_id) {
            // Check if the request is an autosave or a nonce is not set
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
            if ( !isset($_POST['formit_builder_nonce_field']) || !wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['formit_builder_nonce_field'] ) ) , 'formit_builder_nonce' ) ) return;
            if ($post_id && isset($_POST['formit_from_dom']) && isset($_POST['formit_from_json']) && current_user_can('manage_options')) {
                // $formit_from_dom_value = wp_json_encode($_POST['formit_from_dom']); // Encode JSON data
                // $formit_from_json_value = wp_json_encode($_POST['formit_from_json']); // Encode JSON data
                $formit_from_dom_value = wp_json_encode(sanitize_text_field($_POST['formit_from_dom']));
                $formit_from_json_value = wp_json_encode(sanitize_text_field($_POST['formit_from_json']));
                update_post_meta($post_id, 'formit_from_dom', $formit_from_dom_value);
                update_post_meta($post_id, 'formit_from_json', $formit_from_json_value);
            }

        }

        /**
        * Undocumented function
        *
        * @param [type] $post_id
        * 
        */
        function formit_builder_post_publish_hook($post_id) {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
            // Check if this is the custom post type 'formit'

            $post            = get_post($post_id); // Get post with ID
            $author_name     = get_the_author_meta('display_name', $post->post_author); //Get Full name with author ID
            $post_status     = get_post_status($post_id); // Get Post status
            $insert_instance = new Formit_FromBuilderHandle(); // Instance of FormHandler class
        
            if ($post_status !== 'publish') return;
            
            if ('formit' == get_post_type($post_id)) {
                $status_messages_data = $insert_instance->formit_process_form_message_submission(); 
                $formit_settings = $insert_instance->formit_form_settings($post_id); 
                // Add nonce field to the form
                wp_nonce_field( 'formit_nonce_action', 'formit_nonce_field' );

                // Verify nonce before processing form data
                if ( ! isset( $_POST['formit_nonce_field'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['formit_nonce_field'] ) ) , 'formit_nonce_action' ) ){
                    // Process form data here
                    $formit_from_json_value = wp_json_encode(sanitize_textarea_field($_POST['formit_from_json'])); // Encode JSON data
                    $formit_from_dom_value  = wp_json_encode(sanitize_textarea_field($_POST['formit_from_dom'] ));
                    // Add further processing logic if needed
                } else {
                    // Nonce verification failed, handle the error or display a message
                    // For example:
                    echo 'Nonce verification failed. Please try again.';
                }

                /**
                * Send to Insert Method
                */
                $data_to_insert = array(
                    'post_id'               => $post_id,
                    'form_title'            => $post->post_title,
                    'form_json'             => $formit_from_json_value,
                    'form_html'             => $formit_from_dom_value,
                    'user_id'               => $post->post_author,
                    'user_full_name'        => $author_name,
                    'form_status_messages'  => $status_messages_data,
                    'form_configs'          => $formit_settings,
                    'created_at'            => current_time('mysql'),
                    'updated_at'            => current_time('mysql'),
                );

                global $wpdb;
                $table_name = $wpdb->prefix . 'formit_forms'; // Replace 'ms_form_data' with your custom table name

                $update_query = "SELECT * FROM %1s WHERE post_id = %d";
                // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                $existing_data = $wpdb->get_row($wpdb->prepare($update_query, $table_name, $post_id));
            

                // Debugging: Print the SQL query and existing data to help identify issues
                if ($existing_data) {
                    // Data exists, perform an update
                    $where = array(
                        'post_id' => $post_id,
                    );
                    $insert_instance->formit_update_data($data_to_insert, $where);
                    return esc_html_e('Data updated successfully.');
                } else {
                    // Data doesn't exist, perform an insert
                    $inserted_row_id = $insert_instance->formit_insert_data($data_to_insert);
                    return esc_html_e('Data inserted successfully');
                }
            }     

        }


        /**
        * Form Delete function
        *
        * @param [type] $post_id
        * @return void
        */
        function formit_custom_post_deleted_callback($post_id) {
            $insert_instance = new Formit_FromBuilderHandle(); // Instance of FormHandler class
            // Check if the post being deleted is of your custom post type
            if (get_post_type($post_id) === 'formit') {
                $where = array(
                    'post_id' => $post_id,
                );
                $insert_instance->formit_delete_data($where);
                // Perform actions or execute your callback code here
                // For example, you can log the deletion or send an email notification.
            }
        }
    
        /**
        * Custom Message box methoc function
        *
        * @param [type] $messages
        * @return void
        */
        function formit_custom_post_published_message($messages) {
            global $post, $post_ID;
            $post_type = get_post_type($post_ID);
            $messages[$post_type] = array(
                0 => '', // Unused. Messages start at index 1.
                1 => 'MS Form updated', // The default message for a single post.
                2 => 'Custom post updated. View post', // The message shown after updating a post.
                3 => 'Custom post deleted.', // The message shown after deleting a post.
                4 => 'Custom post published.', // The message shown after a post is published.
                5 => '', // Unused.
                6 => 'MS Form created', // The message shown after submitting a post for review.
                7 => 'Custom post scheduled for: <strong>%1$s</strong>. View post', // The message shown when a post is scheduled.
                8 => 'Custom post draft updated. View post', // The message shown after updating a draft.
            );
            return $messages;
        }
    }
}
