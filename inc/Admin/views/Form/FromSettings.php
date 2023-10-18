<?php
namespace Xirosoft\Formit\Admin\views\Form;
use Xirosoft\Formit\Admin\FromBuilderHandle;
use Xirosoft\Formit\Query;

class FromSettings{

    /**
     *Construct function
     */
    function __construct(){
        $this->from_setting_dom();      
        $this->get_current_redirect_menu(); 
    }

    function get_current_redirect_menu(){
        global $wpdb; // Access the WordPress database functions
        $post = get_post(); // Assuming you have access to the current post
        $get_current_option = new Query;
        $table_name = $wpdb->prefix . 'formit_forms'; // Replace 'your_custom_table' with your actual table name
        
        $query = $get_current_option->get_single_row($table_name, $post->ID);
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
     */
    function from_setting_dom(){
        global $wpdb; // Access the WordPress database functions
        // Get the current post
        $post = get_post(); // Assuming you have access to the current post
        $mail_setting_view_data = new FromBuilderHandle;
        $mail_setting_array = $mail_setting_view_data->view_data($post->post_author);

        foreach ($mail_setting_array as $mail_setting_object) {
            if ($mail_setting_object->post_id == $post->ID) {
                // Found a match, store the data in $specificData
                $mail_setting_specificData = $mail_setting_object;
                break; // Exit the loop since we found what we needed
            }
        }
        // // // Check if we found the specific data
        if($post->post_status == 'publish'){
            
            if ($mail_setting_specificData !== null) {
                // You can access specific properties of the object like this:
                $form_configs = $mail_setting_specificData->form_configs;
                $config_form_json = $mail_setting_specificData->form_json;
                // ... and so on
                $msg = json_decode($form_configs);
                $config_form_json_filter = json_decode($config_form_json, true);

                // Remove the extra backslashes
                $cleanedJsonString = stripslashes($config_form_json_filter);

                // Convert the cleaned JSON string to a PHP array
                $jsonArray = json_decode($cleanedJsonString, true);
            } else {
                // Handle the case when the specific 'post_id' was not found
                echo "Post ID $post->ID not found.";
            }
        }

        
        ?>
        <div class="tab-title">
            <h1><?php esc_html_e('Form Settings', 'formit') ?></h1>
            <legend> <?php esc_html_e('You can edit the mail template here.', 'formit'); ?> </legend>
        </div>
         <fieldset>
             <table class="form-table mailsender-form">
                 <tbody>
                     <tr>
                         <th scope="row"><label for="ms_email"><?php  esc_html_e('To', 'formit'); ?></label></th>
                         <td><input type="text" id="ms_email" name="formit_mail_to" value="<?php 
                          if(empty($msg[0]->formit_mail_to)){
                                esc_attr_e('hello@xirosoft.com', 'formit'); 
                            }else{
                            esc_attr_e($msg[0]->formit_mail_to, 'formit'); 
                            }
                        ?>" placeholder="<?php esc_attr_e('Enter you\'re email', 'formit')?> "></td>
                     </tr>
                     <tr>
                         <th scope="row"><label for="mail_cc"><?php  esc_html_e('Mail cc', 'formit'); ?></label></th>
                         <td><input type="text" id="mail_cc" name="mail_cc" value="<?php
                         $mail_cc = isset($msg[0]->mail_cc) ? $msg[0]->mail_cc : '';
                         esc_attr_e($mail_cc);
                         
                         ?>" placeholder="<?php esc_attr_e('Enter you\'re email', 'formit')?> "></td>
                     </tr>
                     <tr>
                         <th scope="row"><label for="mail_bcc"><?php  esc_html_e('Mail Bcc', 'formit'); ?></label></th>
                         <td><input type="text" id="mail_bcc" name="mail_bcc" value="<?php 
                        $mail_bcc = isset($msg[0]->mail_bcc) ? $msg[0]->mail_bcc : '';
                        esc_attr_e($mail_bcc);
                         
                         ?>" placeholder="<?php esc_attr_e('Enter you\'re email', 'formit')?> "></td>
                     </tr>
                     <tr>
                         <th scope="row"><label for="mail_sender"><?php  esc_html_e('From', 'formit'); ?></label></th>
                         <td><input type="text" id="mail_sender" name="formit_sender_mail" value="<?php 
                          if(empty($msg[0]->formit_sender_mail)){
                                esc_attr_e('shahzobayer@gmail.com', 'formit'); 
                            }else{
                            _e($msg[0]->formit_sender_mail, 'formit'); 
                            }
                        ?>" placeholder="<?php esc_attr_e('Enter you\'re from email', 'formit')?> "></td>
                     </tr>
                     <tr>
                         <th scope="row"><label for="mail_subject"><?php  esc_html_e('Subject', 'formit'); ?></label></th>
                         <td><input type="text" id="mail_subject" name="msfrom_mail_subject" value="<?php 
                          if(empty($msg[0]->msfrom_mail_subject)){
                                esc_attr_e('Your Subject', 'formit'); 
                            }else{
                            _e($msg[0]->msfrom_mail_subject, 'formit'); 
                            }
                        ?>" placeholder="<?php esc_attr_e('Enter mail subject', 'formit')?> "></td>
                     </tr>
                     <tr>
                         <th scope="row"><label for="mail_additional_headers"><?php  esc_html_e('Additional headers', 'formit'); ?></label></th>
                         <td><input type="text" id="mail_additional_headers" name="formit_mail_additional_headers" value="<?php 
                          if(empty($msg[0]->formit_mail_additional_headers)){
                                esc_attr_e('Reply-To:', 'formit'); 
                            }else{
                                esc_attr_e($msg[0]->formit_mail_additional_headers, 'formit'); 
                            }
                        ?>" placeholder="<?php esc_attr_e('Write custom Reply-To:', 'formit')?> "></td>
                     </tr>
                     <!-- Dynamic fields list for Message area -->
                     <tr>
                         <th scope="row"><label for="mail_body"><?php  esc_html_e('Message body', 'formit'); ?></label>
                         <?php 

                        //  var_dump($jsonArray);
                            if($post->post_status == 'publish'){
                            if($jsonArray) { ?>
                            <ul class="drag_list_items">
                                <li><?php  esc_html_e('Select an From Field:', 'formit'); ?>
                                    <ul id="drag-list">
                                    <?php
                                    
                                    // Check if JSON decoding was successful
                                    if ($jsonArray === null) {
                                        echo "JSON parsing error!";
                                    } else {
                                        // Loop through the JSON array and create options for the select dropdown
                                        foreach ($jsonArray as $item) {
                                            if (isset($item['label'])) {
                                                echo '<li  draggable="true"  data-text="[' . esc_attr_e(strtolower($item['label']), 'formit') . ']">[' . esc_html_e(strtolower($item['label']), 'formit') . ']</li>';
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
                                $data =  `Hi [Name], \nI hope you're doing well. I wanted to [briefly state the purpose of your email]. [Include any necessary details or requests concisely.] [Optional: Add a closing sentence or call to action.]
                                \nBest regards
                                \n[Your Name]`;
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
                                <label for="msfrom_redirect"><?php  esc_html_e('Select an option:', 'formit'); ?></label>

                                <select id="msfrom_redirect" name="msfrom_redirect">
                                    <option value="popup" <?php if ($this->get_current_redirect_menu() === 'popup') echo 'selected'; ?>>
                                        <?php esc_html_e('Popup Message', 'formit'); ?>
                                    </option>
                                    <option value="external" <?php if ($this->get_current_redirect_menu() === 'external') echo 'selected'; ?>>
                                        <?php esc_html_e('Redirect to External URL', 'formit'); ?>
                                    </option>
                                    <option value="internal" <?php if ($this->get_current_redirect_menu() === 'internal') echo 'selected'; ?>>
                                        <?php esc_html_e('Redirect to Internal URL', 'formit'); ?>
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
