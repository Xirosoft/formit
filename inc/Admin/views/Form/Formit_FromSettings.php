<?php
namespace Xirosoft\Formit\Admin\views\Form;
use Xirosoft\Formit\Admin\Formit_FromBuilderHandle;
use Xirosoft\Formit\Formit_Query;
if ( ! class_exists( 'Formit_FromSettings' ) ) {
    class Formit_FromSettings{

        /**
        *Construct function
        */
        function __construct(){
            $this->formit_from_setting_dom();      
            $this->formit_get_current_redirect_menu(); 
        }

        /**
        * Redirect Menu Function
        *
        * @return void
        */
        function formit_get_current_redirect_menu(){
            global $wpdb; // Access the WordPress database functions
            $post               = get_post(); // Assuming you have access to the current post
            $get_current_option = new Formit_Query;
            $table_name         = $wpdb->prefix . 'formit_forms'; 
            $query              = $get_current_option->formit_get_single_row($table_name, $post->ID);
            
            if(!$query == null){
                $get_data = json_decode($query['form_configs']);
                $redirct = $get_data[0]->msfrom_redirect->options;
            }else{
                $redirct = 'popup';
            }
            return $redirct;
        }

        /**
        * Return Dom function
        *
        * @return void
        * @param [Integer]
        */
        function formit_from_setting_dom(){
            global $wpdb; // Access the WordPress database functions
            // Get the current post
            $post = get_post(); // Assuming you have access to the current post
            $mail_setting_formit_view_data = new Formit_FromBuilderHandle;
            $mail_setting_array = $mail_setting_formit_view_data->formit_view_data($post->post_author);
            $mail_setting_specificData = null; // Initialize with a default value


            if(is_array($mail_setting_array)){
                foreach ($mail_setting_array as $mail_setting_object) {
                    if ($mail_setting_object['post_id'] == $post->ID) {
                        $mail_setting_specificData = $mail_setting_object;
                        break;
                    }
                }
            }
            // Check if we found the specific data
            if($post->post_status == 'publish'){
                if ($mail_setting_specificData !== null) {
                    // You can access specific properties of the object like this:
                    $form_configs = $mail_setting_specificData['form_configs'];
                    $config_form_json = $mail_setting_specificData['form_json'];
                    
                    $msg = json_decode($form_configs);
                    $config_form_json_filter = json_decode($config_form_json, true);

                    // Remove the extra backslashes
                    $cleanedJsonString = stripslashes($config_form_json_filter);
                    

                    // Convert the cleaned JSON string to a PHP array
                    $jsonArray = json_decode($cleanedJsonString, true);
                } else {
                    // Handle the case when the specific 'post_id' was not found
                    echo esc_html("Post ID " . esc_html($post->ID) . " not found.");
                }
            }

            
            ?>
            <div class="tab-title">
                <h1><?php echo esc_html__('Form Settings', 'formit') ?></h1>
                <legend> <?php echo esc_html__('You can edit the mail template here.', 'formit'); ?> </legend>
            </div>
            <fieldset>
                <table class="form-table mailsender-form">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="ms_email"><?php  echo esc_html__('To', 'formit'); ?></label></th>
                            <td>
                                <input type="text" id="ms_email" name="formit_mail_to" value="<?php 
                                    if(empty($msg[0]->formit_mail_to)){
                                        echo esc_attr(sanitize_email(get_option('admin_email'))); 
                                    }else{
                                        echo esc_attr(sanitize_email($msg[0]->formit_mail_to));
                                    }
                                ?>" placeholder="<?php esc_attr_e('Enter you\'re email', 'formit')?> ">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mail_cc"><?php  echo esc_html__('Mail cc', 'formit'); ?></label></th>
                            <td>
                                <input type="text" id="mail_cc" name="mail_cc" value="<?php
                                    $mail_cc = isset($msg[0]->mail_cc) ? $msg[0]->mail_cc : '';
                                    echo esc_attr(sanitize_email($mail_cc));
                                ?>" placeholder="<?php esc_attr_e('Enter you\'re email', 'formit')?> ">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mail_bcc"><?php  echo esc_html__('Mail Bcc', 'formit'); ?></label></th>
                            <td>
                                <input type="text" id="mail_bcc" name="mail_bcc" value="<?php 
                                    $mail_bcc = isset($msg[0]->mail_bcc) ? $msg[0]->mail_bcc : '';
                                    echo esc_attr(sanitize_email($mail_bcc));
                                ?>" placeholder="<?php esc_attr_e('Enter you\'re email', 'formit')?> ">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mail_sender"><?php  echo esc_html__('From', 'formit'); ?></label></th>
                            <td>
                                <input type="text" id="mail_sender" name="formit_sender_mail" value="<?php 
                                if(empty($msg[0]->formit_sender_mail)){
                                        echo esc_attr(sanitize_email(get_option('admin_email'))); 
                                    }else{
                                        echo esc_attr(sanitize_email($msg[0]->formit_sender_mail));
                                    }
                                ?>" placeholder="<?php esc_attr_e('Enter you\'re from email', 'formit')?> ">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mail_subject"><?php  echo esc_html__('Subject', 'formit'); ?></label></th>
                            <td>
                                <input type="text" id="mail_subject" name="msfrom_mail_subject" value="<?php 
                                    if(empty($msg[0]->msfrom_mail_subject)){
                                    echo esc_attr__('Your Subject', 'formit'); 
                                    }else{
                                        echo esc_attr(sanitize_text_field($msg[0]->msfrom_mail_subject));
                                    }
                                ?>" placeholder="<?php esc_attr_e('Enter mail subject', 'formit')?> ">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="mail_additional_headers"><?php  echo esc_html__('Additional headers', 'formit'); ?></label></th>
                            <td>
                                <input type="text" id="mail_additional_headers" name="formit_mail_additional_headers" value="<?php 
                                    if(empty($msg[0]->formit_mail_additional_headers)){
                                        esc_html_e('Reply-To:', 'formit'); 
                                    }else{
                                        echo esc_attr(sanitize_text_field($msg[0]->formit_mail_additional_headers));
                                    }
                                ?>" placeholder="<?php esc_attr_e('Write custom Reply-To:', 'formit')?> ">
                            </td>
                        </tr>
                        <!-- Dynamic fields list for Message area -->
                        <tr>
                            <th scope="row"><label for="mail_body"><?php  echo esc_html__('Message body', 'formit'); ?></label>
                            <?php 

                                if($post->post_status == 'publish'){
                                if($jsonArray) { ?>
                                <ul class="drag_list_items">
                                    <li><?php  echo esc_html__('Select an From Field:', 'formit'); ?>
                                        <ul id="drag-list">
                                        <?php
                                        
                                        // Check if JSON decoding was successful
                                        if ($jsonArray === null) {
                                            echo esc_html__('JSON parsing error!', 'formit');
                                        } else {
                                            // Loop through the JSON array and create options for the select dropdown
                                            foreach ($jsonArray as $item) {
                                                if (isset($item['label'])) {
                                                    echo '<li draggable="true" data-text="' . esc_attr('[' . trim(strtolower($item['label'])) . ']') . '">' . esc_html('[' . trim(strtolower($item['label'])) . ']') . '</li>';
                                                }
                                            }
                                        }
                                        ?>
                                            
                                        </ul>
                                    </li>
                                </ul>
                            <?php } } ?>

                            </th>
                            <?php 
                                if(empty($msg[0]->formit_mail_body)){
                                    $data =  "Hi [Name], 
                                    \nI hope you're doing well. I wanted to [briefly state the purpose of your email]. [Include any necessary details or requests concisely.] [Optional: Add a closing sentence or call to action.]
                                    \n\nBest regards
                                    \n[Your Name]";
                                }else{
                                    $data = $msg[0]->formit_mail_body; 
                                }
                            ?>
                            <td>
                                <textarea name="formit_mail_body" id="mail_body" cols="30" rows="10"><?php echo esc_textarea($data); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="msfrom_redirect"></label></th>
                            <td>
                            <div class="custom-dropdown">
                                <div class="form-wrap">
                                    <label for="msfrom_redirect"><?php  echo esc_html__('Select an option:', 'formit'); ?></label>

                                    <select id="msfrom_redirect" name="msfrom_redirect">
                                        <option value="popup" <?php if ($this->formit_get_current_redirect_menu() === 'popup') echo 'selected'; ?>>
                                            <?php echo esc_html__('Popup Message', 'formit'); ?>
                                        </option>
                                        <option value="external" <?php if ($this->formit_get_current_redirect_menu() === 'external') echo 'selected'; ?>>
                                            <?php echo esc_html__('Redirect to External URL', 'formit'); ?>
                                        </option>
                                        <option value="internal" <?php if ($this->formit_get_current_redirect_menu() === 'internal') echo 'selected'; ?>>
                                            <?php echo esc_html__('Redirect to Internal URL', 'formit'); ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="dynamic-fields">
                                    <!-- Fields will be appended here based on user selection -->
                                </div>
                            </div>
                            </td>
                            
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        <?php
        ob_end_flush();
        }

    }
}
