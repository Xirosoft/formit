<?php 

namespace Xirosoft\Formit;

class Installer{

    /**
     * run function
     * This funtion run when plugin active
     * @return void
     */
    function run(){
        $this->add_version();
        $this->create_table();
        $this->user_db_create_table();
        $this->formit_config_table();
    }   
    
    /**
     * Manage version 
     *
     * @return void
     */
    public function add_version(){
        $installed = get_option('formit_installed');
		if(!$installed){
			update_option( 'formit_installed', time(), );	
		}
		update_option( 'formit_version', FORMIT_VERSION, );
    }

    /**
     * Create Database table when install plugin
     *
     * @return void
     */
    public function create_table(){
        global $wpdb;
        $formit_forms_name = $wpdb->prefix . 'formit_forms';
        if (!$this->is_table_exists($formit_forms_name)) {
            // Check if the table exists before creating it
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $formit_forms_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                post_id varchar(255) NOT NULL,
                form_title varchar(255) NOT NULL,
                form_json text NOT NULL,
                form_html text NOT NULL,
                user_id bigint(20) NOT NULL,
                user_full_name varchar(255) NOT NULL,
                form_status_messages text NOT NULL,
                form_configs text NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;";
    
            if (!function_exists('dbDelta')) {
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            }
            dbDelta($sql);
        } else {
            wp_die('Table Already exists!');
        }
    }

    /**
     * User DB table creation function
     * @user_db_create_table
     * @return void
     */
    public function user_db_create_table(){
        global $wpdb;
        $formit_submissions_table = $wpdb->prefix . 'formit_submissions';
        // Check if the table exists before creating it
        if (!$this->is_table_exists($formit_submissions_table)) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $formit_submissions_table (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                mail_body TEXT NOT NULL,
                form_id INT NOT NULL,
                form_title VARCHAR(255) NOT NULL,
                delivery_status VARCHAR(255) NOT NULL,
                is_auto_reply VARCHAR(255) NOT NULL,
                ip_address VARCHAR(255) NOT NULL,
                user_agent TEXT NOT NULL,
                refer_page VARCHAR(255) NOT NULL,
                user_location TEXT NOT NULL,
                gcaptcha TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;";

            if (!function_exists('dbDelta')) {
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            }
            dbDelta($sql);
        } else {
            wp_die('Table Already exists!');
        }
    }

    /**
     * DB table create for formit settings function
     *
     * @return void
     */
    public function formit_config_table(){
        global $wpdb;
        $formit_submissions_table = $wpdb->prefix . 'formit_settings';
    
        // Check if the table exists before creating it
        if (!$this->is_table_exists($formit_submissions_table)) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $formit_submissions_table (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                form_settings TEXT NOT NULL,
                addon_setting TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;";

            if (!function_exists('dbDelta')) {
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            }

            dbDelta($sql);
            $query = "SELECT * FROM %1s";
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            $existing_data = $wpdb->get_row( $wpdb->prepare($query, $formit_submissions_table));
             // Insert data into the table after creating it
            $form_settings_data = '{"form_option_checkbox":"on","form_option_date":"on","form_option_files":"on","form_option_header":"on","form_option_hidden":"on","form_option_number":"on","form_option_paragraph":"on","form_option_radio":"on","form_option_select":"on","form_option_text":"on","form_option_textarea":"on","form_attr_description":"on","form_attr_name":"on","form_attr_maxlength":"on","form_attr_style":"on","form_attr_class":"on","form_attr_label":"on","form_attr_placeholder":"on","form_attr_value":"on","form_attr_type":"on","form_attr_required":"on"}';

            if (!$existing_data) {
                // Insert data if no existing data
                $data = array(
                    'form_settings' => wp_json_encode($form_settings_data),
                    'addon_setting' => 'coming soon',
                );
    
                $wpdb->insert(
                    $formit_submissions_table,
                    $data
                );
            } else {
                // Update data if existing data is found
                $data = array(
                    'form_settings' => wp_json_encode($form_settings_data),
                    'addon_setting' => 'coming soon',
                );
    
                $wpdb->update(
                    $formit_submissions_table,
                    $data,
                    array('id' => $existing_data->id)
                );
            }
        } else {
            wp_die('Table Already exists!');
        }
    }

    /**
     * is_table_exists function just checking exists table.
     *
     * @param [string] $table_name
     * @return boolean
     */
    function is_table_exists($table_name) {
        global $wpdb;
        $get_table = $wpdb->prefix . $table_name;
        $query = "SHOW TABLES LIKE %1s";
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        return $wpdb->get_var($wpdb->prepare($query, $get_table)) == $get_table;
    }
}
