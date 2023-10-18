<?php 
namespace Xirosoft\Formit\Admin\views\Settings;
use Xirosoft\Formit\Query;

class SettingConfig{
    private $query; 
    private $wpdb;
    private $table_name;
    private $data_format;
    private $where;
    private $existing_data;

    function __construct(){

        add_action('wp_ajax_form_settings_data', [$this, 'form_settings_data']);
        add_action('wp_ajax_nopriv_form_settings_data', [$this, 'form_settings_data']); // For non-logged-in users

        global $wpdb;
        $this->wpdb = $wpdb;
       
        $this->table_name = $wpdb->prefix . 'formit_settings'; // Replace 'ms_form_data' with your custom table name
        $this->query = new Query();
        $this->existing_data =  $this->wpdb->get_row("SELECT * FROM {$this->table_name}");
        $this->where = array('id' => $this->existing_data->id);
        $this->data_format = array(
            '%s', // 'post_id' is a string
            '%s', // 'form_title' is a string
            '%s', // 'form_json' is a string
            '%s', // 'form_html' is an integer

        );
     
    }

    function form_settings_data(){
        $formData = $_POST['formData'];
        parse_str($formData, $formFields);
        
        // Define data to insert or update
        $data_to_insert = array(
            'form_settings' => $formFields,
            'addon_setting' => 'coming soon',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
        );
        $defaults = array(
            'form_settings' => '',
            'addon_setting' => '',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
        );

       
        // Check if data exists in the database
        if (empty($this->existing_data)) {
            // Data does not exist, insert it
            $data_to_insert = array(
                'form_settings' => $formFields,
                'addon_setting' => 'coming soon',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            );
           
            // Insert the data
             $this->query->insert_data($this->table_name, $data_to_insert, $defaults);

        } else {
            // Data exists, update it
            $updated_data = array(
                'form_settings' => json_encode($formFields),
                'updated_at' => current_time('mysql'),
            );
            // Define the WHERE clause to identify the existing record (you might need to adjust this based on your table structure)
            $where = array('id' => $this->existing_data->id);
            // wp_send_json($updated_data);
            // Update the data
            $this->query->update_data( $this->table_name, $updated_data, $where);
            wp_send_json(array('success' => 'Settings have been updated!'));
        }
           
    }

    /**
     * Settings Dom function
     * $form_settings_config_data
     * @return void
     */
    function Setting_Tab(){
        // Default SettingsArray
        $pre_defined_settings = array(
            'form_option_checkbox-group' => true,
            'form_option_date' => false,
            'form_option_files' => false,
            'form_option_header' => false,
            'form_option_hidden' => false,
            'form_option_number' => false,
            'form_option_paragraph' => false,
            'form_option_radio-group' => false,
            'form_option_select' => false,
            'form_option_text' => false,
            'form_option_textarea' => false,
            'form_attr_description' => false,
            'form_attr_name' => false,
            'form_attr_options' => true,
            'form_attr_maxlength' => false,
            'form_attr_style' => false,
            'form_attr_class' => false,
            'form_attr_label' => false,
            'form_attr_placeholder' => false,
            'form_attr_value' => false,
            'form_attr_subtype' => false,
            'form_attr_required' => false
        );
        // user's modified settingsArray
        $user_defined_settings = $this->form_settings_config_data();

        

        // Update the SettingsArray
        $SettingsArray = $this->update_settings_data($pre_defined_settings, $user_defined_settings);
        // return;

        $form_elements_array = array(
            array(
                'name' => 'form_option_checkbox-group',
                'label' => 'Checkbox Group 🗂️ ',
                'desc' => 'The "Checkbox Group" in Form Builder lets users select multiple options by clicking checkboxes for each item.',
                'value' => $SettingsArray['form_option_checkbox-group'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_option_date',
                'label' => 'Date 📅',
                'desc' => 'The "Date" field in Form Builder allows users to input or select a specific date within the form.',
                'value' => $SettingsArray['form_option_date'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_option_files',
                'label' => 'Files 📂 ',
                'desc' => 'The "File" field in Form Builder allows users to upload and submit files or documents as part of the form submission.',
                'value' => $SettingsArray['form_option_files'] ? false : false,
                'disabled' => true
            ),
            array(
                'name' => 'form_option_header',
                'label' => 'Header 📌 ',
                'desc' => 'The "Header" element in Form Builder is used to create section headings or titles to organize and structure the form',
                'value' => $SettingsArray['form_option_header'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_option_hidden',
                'label' => 'Hidden Input 🕵️‍♂️ ',
                'desc' => 'The "Hidden Input" in Form Builder lets you include data that\'s not visible to users but is submitted with the form.',
                'value' => $SettingsArray['form_option_hidden'] ? false : false,
                'disabled' => true
            ),
            array(
                'name' => 'form_option_number',
                'label' => 'Number 🔢 ',
                'desc' => 'The "Number" field in Form Builder enables users to input numerical values, making it ideal for collecting numeric data.',
                'value' => $SettingsArray['form_option_number'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_option_paragraph',
                'label' => 'Paragraph 📝 ',
                'desc' => 'The "Paragraph" field in Form Builder allows users to input longer text or comments, promoting detailed responses.',
                'value' => $SettingsArray['form_option_paragraph'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_option_radio-group',
                'label' => 'Radio Group 📻 ',
                'desc' => 'The "Radio Group" in Form Builder lets users select a single option from a list of choices by clicking radio buttons.',
                'value' => $SettingsArray['form_option_radio-group'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_option_select',
                'label' => 'Select 🔽 ',
                'desc' => 'The "Select" field in Form Builder allows users to pick one option from a dropdown menu, simplifying selection from a list.',
                'value' => $SettingsArray['form_option_select'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_option_text',
                'label' => 'Text Field ✍️ ',
                'desc' => 'The "Text Field" in Form Builder permits users to enter text or information, making it versatile for various data inputs.',
                'value' => $SettingsArray['form_option_text'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_option_textarea',
                'label' => 'Textarea 🖋️ ',
                'desc' => 'The "Textarea" field in Form Builder provides a larger text input area for users to enter longer comments or responses.',
                'value' => $SettingsArray['form_option_textarea'] ? true : false,
                'disabled' => false
            )
        );

        $form_attributes_array = array(
            array(
                'name' => 'form_attr_description',
                'label' => 'Description 📝 ',
                'desc' => 'The "Description" field in Form Builder allows you to provide additional information or instructions to users within the form.',
                'value' => $SettingsArray['form_attr_description'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_attr_name',
                'label' => 'Name 📛 ',
                'desc' => 'The "Name" attribute in Form Builder is used to specify the unique identifier for form elements, ensuring data integrity.',
                'value' => $SettingsArray['form_attr_name'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_attr_maxlength',
                'label' => 'Max-length 📏',
                'desc' => 'The "Max-length" attribute in Form Builder sets the maximum character limit for user input, helping enforce data constraints.',
                'value' => $SettingsArray['form_attr_maxlength'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_attr_style',
                'label' => 'Style ✨ ',
                'desc' => 'The "Style" option in Form Builder allows you to customize the visual appearance of form elements, enhancing aesthetics.',
                'value' => $SettingsArray['form_attr_style'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_attr_options',
                'label' => 'Options 📇 ',
                'desc' => 'The option within <select> in Form Builder helps to create dropdowns.',
                'value' => $SettingsArray['form_attr_options'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_attr_class',
                'label' => 'Class 🧩 ',
                'desc' => 'The "Class" attribute in Form Builder enables you to assign CSS classes for styling and layout customization.',
                'value' => $SettingsArray['form_attr_class'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_attr_label',
                'label' => 'Label 🏷️ ',
                'desc' => 'The "Label" element in Form Builder provides descriptive text or instructions for form fields, enhancing user understanding.',
                'value' => $SettingsArray['form_attr_label'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_attr_placeholder',
                'label' => 'Placeholder 📝 ',
                'desc' => 'The "Placeholder" attribute in Form Builder provides hints or examples within input fields, guiding users on expected content.',
                'value' => $SettingsArray['form_attr_placeholder'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_attr_value',
                'label' => 'Value 💼 ',
                'desc' => 'The "Value" attribute in Form Builder pre-fills a default or initial content within form fields, saving users time.',
                'value' => $SettingsArray['form_attr_value'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_attr_subtype',
                'label' => 'Input Type 📥 ',
                'desc' => 'The "Input Type" attribute in Form Builder specifies the type of data accepted in a field, such as text, email, or number.',
                'value' => $SettingsArray['form_attr_subtype'] ? true : false,
                'disabled' => false
            ),
            array(
                'name' => 'form_attr_required',
                'label' => 'Required 🔒 ',
                'desc' => 'The "Required" attribute in Form Builder mandates that users must complete the field, ensuring vital data is provided.',
                'value' => $SettingsArray['form_attr_required'] ? true : false,
                'disabled' => false
            )
        );
      
        ?>
        <form method="POST" id="form_settings">
            <!-- Form Elements Switcher -->
            <div class="tab-title">
                <h1><?php esc_html_e('Form Elements', 'formit') ?></h1>
                <legend> <?php esc_html_e('Which features do you want to enable or disable?', 'formit'); ?> </legend>
            </div>
            <table class="form-table form-element-table">
                <?php foreach($form_elements_array as $field): ?>
                <tr>
                    <td class="option-key-col">
                        <h3 class="option-title"><?php esc_html_e($field['label'], 'formit') ?></h3>
                        <p class="option-desc"><?php echo esc_html__($field['desc'], 'formit'); ?></p>
                    </td>
                    <td class="option-value-col">
                        <input type="checkbox" clas="option-switch" name="<?php echo $field['name'] ?>" id="<?php echo $field['name'] ?>" <?php  echo $field['value'] ? 'checked':'' ?> <?php  echo $field['disabled'] ? 'disabled': '' ?>>
                        <label for="<?php echo $field['name'] ?>"></label>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

            <!-- spacher -->
            <div style="height: clamp(50px, 3vw, 75px)"></div>

            <!-- Form Field Attribute Switcher -->
            <div class="tab-title">
                <h1><?php esc_html_e('Form Attributes', 'formit') ?></h1>
                <legend> <?php esc_html_e('Which attributes do you want to enable or disable in our form?', 'formit'); ?> </legend>
            </div>
            <table class="form-table form-element-table">
                <?php foreach($form_attributes_array as $field): ?>
                <tr>
                    <td class="option-key-col">
                        <h3 class="option-title"><?php esc_html_e($field['label'], 'formit') ?></h3>
                        <p class="option-desc"><?php echo esc_html__($field['desc'], 'formit'); ?></p>
                    </td>
                    <td class="option-value-col">
                        <input type="checkbox" clas="option-switch" name="<?php echo $field['name'] ?>" id="<?php echo $field['name'] ?>" <?php echo $field['value'] ? 'checked':'' ?> <?php  echo $field['disabled'] ? 'disabled': '' ?>>
                        <label for="<?php echo esc_html__($field['name'], 'formit') ?>"></label>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <div id="publishing-actions">
                <span class="spinner"></span>
                <input type="submit" name="formit_settings_submit" class="button-primary" id="formit_settings_submit" value="<?php echo esc_attr_e('Save Changes', 'formit') ?>" />
            </div>
        </form>
        <?php
    }

    /**
     * Get Setting Config darta function
     *
     * @return array
     */
    function form_settings_config_data(){
        if($this->where['id'] != null){
        }
        // $rows = $this->query->view_data($this->table_name, $this->where['id']);
        $rows = $this->query->view_data($this->table_name, array('id' => $this->where['id']));

        $data = $rows[0]->form_settings;
        $SettingsArray = json_decode($data, true);
        if(gettype($SettingsArray) == 'string') {
            $SettingsArray = json_decode($SettingsArray, true);
        }
        return $SettingsArray;           
    }

    // Function to update settings based on object elements
    function update_settings_data($pre, $new) {
        if(count($new) > 0) {
            foreach ($pre as $key => $value) {
                if (array_key_exists($key, $new) && $new[$key] === "on") {
                    $pre[$key] = true;
                }
            }
        }
        return $pre;
    }
    
}
