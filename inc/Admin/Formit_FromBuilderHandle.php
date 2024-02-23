<?php

namespace Xirosoft\Formit\Admin;
if ( ! class_exists( 'Formit_FromBuilderHandle' ) ) {
    class Formit_FromBuilderHandle
    {
        private $wpdb;
        private $table_name;
        private $data_format;
        // Constructor to initialize the $wpdb object
        public function __construct(){
            global $wpdb;
            $this->wpdb = $wpdb;
            // add_action('init', [$this, 'process_form_submission']);
            $this->table_name = $wpdb->prefix . 'formit_forms'; // Replace 'ms_form_data' with your custom table name
            $this->data_format = array(
                '%d', // 'post_id' is a string
                '%s', // 'form_title' is a string
                '%s', // 'form_json' is a string
                '%s', // 'form_html' is an integer
                '%d', // 'user_id' is a string
                '%s', // 'user_full_name' is a string
                '%s', // 'form_status_messages' is a string
                '%s', // 'form_configs' is a string (datetime)
                '%s', // 'updated_at' is a string (datetime)
                '%s', // 'updated_at' is a string (datetime)
            );
            add_action('wp_ajax_formit_process_form_message_submission', [$this, 'formit_process_form_message_submission']);
            // add_action('wp_ajax_nopriv_formit_process_form_message_submission', [$this, 'formit_process_form_message_submission']); // For non-logged-in users
        }

        /**
        * Data Insert function
        *
        * @param [json] $data
        * @return void
        */
        public function formit_insert_data($data) {
            // global $wpdb;
            $defaults = array(
                'post_id' => '',
                'form_title' => '',
                'form_json' => '',
                'form_html' => '',
                'user_id' => 0,
                'user_full_name' => '',
                'form_status_messages' => '',
                'form_configs' => '',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            );
            // Merge provided data with defaults
            $data = wp_parse_args($data, $defaults);
            // Ensure data format matches the expected format
            $this->wpdb->insert($this->table_name, $data, $this->data_format);
        }

        /**
        * Data Update Method function
        *
        * @param [json] $data
        * @param [intger] $where
        * @return void
        */
        public function formit_update_data($data, $where) {
            // global $wpdb;
            $this->wpdb->update($this->table_name, $data, $where, $this->data_format);
        }

        /**
        * View Data function
        *
        * @param [Integer] $where
        * @return void
        */
        public function formit_view_data($where) {
            global $wpdb;

            $query = "SELECT * FROM %1s WHERE user_id = %d";
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            return  $wpdb->get_results($wpdb->prepare($query, $this->table_name, $where), ARRAY_A);
        }

        /**
        * Delete function
        *
        * @param [Integer] $where
        * @return void
        */
        public function formit_delete_data($where) {
            $this->table_name =  $this->wpdb->prefix . 'formit_forms';
            $this->wpdb->delete($this->table_name, $where);
        }

        /**
        * Hook into the WordPress action for processing form submissions
        *
        * @return void
        */
        function formit_process_form_message_submission() {
            // Initialize an array to store errors
            $errors = array();
        
            // Define field names and corresponding error messages
            $fieldErrors = array(
                'message_successfully' => 'Successfully field is required.',
                'message_send_failed' => 'Message failed to send field is required.',
                'validation_errors' => 'Validation errors occurred field is required.',
                'submission_spam' => 'Spam field is required.',
                'accept_terms' => 'Accept field is required.',
                'fill_required_field' => 'Sender must fill in field is required.',
                'input_too_long' => 'Length long field is required.',
                'input_too_short' => 'Length short field is required.',
                'upload_failed' => 'Uploading field is required.',
                'file_type_invalid' => 'File type field is required.',
                'file_too_large' => 'File large field is required.',
                'upload_failed_php_error' => 'PHP error field is required.',
                'invalid_date_format' => 'Date format field is required.',
                'date_too_early' => 'Date too late field is required.',
                'date_too_late' => 'Date too late field is required.',
                'invalid_number_format' => 'Invalid Number field is required.',
                'number_too_small' => 'Too small field is required.',
                'number_too_large' => 'Number too large field is required.',
                'incorrect_quiz_answer' => 'Quiz answer field is required.',
                'invalid_email' => 'Invalid Email field is required.',
                'invalid_url' => 'Invalid URL field is required.',
                'invalid_telephone' => 'Invalid Phone field is required.',
                'formit_mail_to' => 'Mail receiver field is required.',
                'formit_sender_mail' => 'Mail Sender field is required.',
                'msfrom_mail_subject' => 'Mail Subject field is required.',
                'formit_mail_additional_headers' => 'Header field is required.',
                'formit_mail_body' => 'Mail Body field is required.',
                'msfrom_redirect' => 'Redirect field is required.',
            );
        

            // Verify nonce before processing form data
            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ) , 'formit-nonce' ) ) return;
            $allowed_tags = array(
                'div' => array(
                    'class' => true,
                    'id' => true,
                ),
                'span' => array(),
                'label' => array(
                    'for' => true,
                ),
                'strong' => array(),
                'p' => array(),
                'h1' => array(),
                'h2' => array(),
                'h3' => array(),
                'h4' => array(),
                'h5' => array(),
                'h6' => array(),
                'select' => array(
                    'type' => true,
                    'name' => true,
                    'id'   => true,
                    'class' => true,
                ),
                'textarea' => array(
                    'type' => true,
                    'name' => true,
                    'id'   => true,
                    'class' => true,
                ),
                'form' => array(
                    'action' => true,
                    'method' => true,
                ),
                'input' => array(
                    'type' => true,
                    'name' => true,
                    'id'   => true,
                    'class' => true,
                ),
                'button' => array(
                    'type' => true,
                    'id'   => true,
                    'class' => true,
                ),
            );
            $htmlData       = isset($_POST['htmlData']) ? wp_kses($_POST['htmlData'], $allowed_tags) : '';
            $fromTemplate   = isset($_POST['fromTemplate']) ? wp_json_encode(sanitize_textarea_field($_POST['fromTemplate'])) : '';
            $jsonData       = isset($_POST['jsonData']) ? wp_json_encode(sanitize_textarea_field($_POST['jsonData'])) : '';
            $raw_data       = isset($_POST['formData']) ? wp_json_encode(sanitize_textarea_field($_POST['formData'])) : '';
            $decoded_data   = urldecode($raw_data);

            // Sanitize and validate the decoded data
            $clean_data = sanitize_text_field($decoded_data);
            parse_str($clean_data, $formFields);
            
            if (!empty($errors)) {
                // If there are errors, return them as a JSON response
                wp_send_json(array('errors' => $errors));
            } else {
                // Form data is valid, continue processing
                $postId = $formFields['post_ID'];
                $post_title = $formFields['post_title'];
        
                // Get the post object
                $post = get_post($postId);
        
                if ($post && $post->post_type === 'formit') {
                    $updated_post = get_post($postId);
                    $author_id = $updated_post->post_author;
                    $author_name = get_the_author_meta('display_name', $author_id);
                }
        
                $msfrom_popup_message       = sanitize_text_field($formFields['msfrom_popup_message']);
                $msfrom_external_url        = esc_url_raw($formFields['msfrom_external_url']);
                $msfrom__internal_page      = esc_url_raw($formFields['msfrom__internal_page']);
                $formit_mail_to             = sanitize_email($formFields['formit_mail_to']);
                $mail_cc                    = sanitize_email($formFields['mail_cc']);
                $mail_bcc                   = sanitize_email($formFields['mail_bcc']);
                $formit_sender_mail         = sanitize_email($formFields['formit_sender_mail']);
                $msfrom_mail_subject        = sanitize_text_field($formFields['msfrom_mail_subject']);
                $formit_mail_headers        = sanitize_text_field($formFields['formit_mail_additional_headers']);
                $formit_mail_body           = wp_kses_post($formFields['formit_mail_body']);
                $msfrom_redirect            = esc_url_raw($formFields['msfrom_redirect']);
            
                
                if(empty($msfrom_popup_message)){
                    $msfrom_popup_message = "Thanks for Submit";
                }
                if(empty($msfrom_external_url)){
                    $msfrom_external_url = home_url();
                }
                if(empty($msfrom__internal_page)){
                    $msfrom__internal_page = "https://xirosoft.com/";
                }


                if($msfrom_redirect == 'popup'){
                    $msfrom_redirect_data =  $msfrom_popup_message;
                }else if($msfrom_redirect == 'external'){
                    $msfrom_redirect_data = $msfrom_external_url;  
                }else if($msfrom_redirect == 'internal'){
                    $msfrom_redirect_data =  $msfrom__internal_page;
                }else{
                    $msfrom_redirect_data = 'Not Assign';
                }

                
                /**
                * Mail fields validation
                */
                $mail_config = [
                "formit_mail_to" => $formit_mail_to,
                "formit_sender_mail" => $formit_sender_mail,
                "msfrom_mail_subject" => $msfrom_mail_subject,
                "mail_cc" => $mail_cc,
                "mail_bcc" => $mail_bcc,
                "formit_mail_additional_headers" => $formit_mail_headers,
                "formit_mail_body" => $fromTemplate,
                "msfrom_redirect" => [
                    "options" => $msfrom_redirect,
                    "msfrom_submission_data" => $msfrom_redirect_data,
                    ]
                ];
                
                // Append the new data to the existing array
                $mail_config_array[] = $mail_config;
                
                // Encode the updated array back to JSON
                // Separate the form field data into two JSON objects
                $status_messages_data = wp_json_encode(array_slice($formFields, 24, 22)); // First 22 fields
                
                $updated_mail_config = wp_json_encode($mail_config_array, JSON_PRETTY_PRINT);
        
                // Define data to insert or update
                $data_to_insert = array(
                    'post_id' => $postId,
                    'form_title' => $post_title,
                    'form_json' => $jsonData,
                    'form_html' => $htmlData, // Include this if needed
                    'user_id' => $author_id,
                    'user_full_name' => $author_name,
                    'form_status_messages' => $status_messages_data,
                    'form_configs' => $updated_mail_config,
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql'),
                );
        
                global $wpdb;
                $this->table_name = $wpdb->prefix . 'formit_forms';
                $query = "SELECT * FROM %1s WHERE post_id = %d";
                // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                $existing_data = $wpdb->get_row($wpdb->prepare($query,$this->table_name, $postId));
        
                $table_name = $wpdb->prefix . 'posts';
                $wp_title = $formFields['post_title'];

                $meta_key = '_formit_builder_shortcode'; // Replace with your custom meta key
                $shortcode_title = '[formit id="' . esc_attr($postId) . '"  title="' . esc_attr($wp_title) . '"]';


                if ($existing_data) {
                    // Data exists, perform an update
                    $where = array(
                        'post_id' => $postId,
                    );
        
                    $this->formit_update_data($data_to_insert, $where);
                    $sql_meta = $wpdb->prepare(
                        "UPDATE {$wpdb->prefix}postmeta 
                        SET meta_value = %s 
                        WHERE post_id = %d AND meta_key = %s",
                        $shortcode_title,
                        $postId,
                        $meta_key
                    );
                    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                    $wpdb->query($sql_meta);
                    $sql = "UPDATE $table_name SET post_status = 'publish', post_title = %s WHERE ID = %d";
                    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                    $wpdb->query($wpdb->prepare($sql, $wp_title, $postId));
                    wp_send_json(array('success' => $htmlData ));
                    // wp_send_json(array('success' => 'Form Updated'));
                } else {
                    $this->formit_insert_data($data_to_insert);
                    $sql_meta = $wpdb->prepare(
                        "UPDATE {$wpdb->prefix}postmeta 
                        SET meta_value = %s 
                        WHERE post_id = %d AND meta_key = %s",
                        $shortcode_title,
                        $postId,
                        $meta_key
                    );
                    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                    $wpdb->query($sql_meta);

                    $sql = "UPDATE $table_name SET post_status = 'publish', post_title = %s WHERE ID = %d";
                    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                    $wpdb->query($wpdb->prepare($sql, $wp_title, $postId));
                    wp_send_json(array('success' => $htmlData ));
                    // wp_send_json(array('success' => 'From Created', 'home_url' => stripslashes(home_url())));
                }
            }
        }

        public function formit_form_settings($post_id){
            if($post_id){
                global $wpdb;
                $this->table_name = $wpdb->prefix . 'formit_forms';
                $query = "SELECT * FROM %1s WHERE post_id = %d";
                // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                $existing_form_configs = $wpdb->get_row($wpdb->prepare($query,$this->table_name, $post_id));
                return $existing_form_configs->form_configs;
            }
        }
    }
}


