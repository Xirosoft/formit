<?php 
namespace Xirosoft\Formit\Admin\views\Settings;

 class Settings{
    function __construct(){
        add_action('admin_menu', [$this, 'add_settings_submenu_page']);
        new SettingConfig();
    }  
    /*  This method use for from settings. Add a submenu page for settings under the "Add New" menu
        Developer need to read
    */
    function add_settings_submenu_page() {
        add_submenu_page(
            'edit.php?post_type=formit', // Parent menu slug
            __('Form Settings', 'formit'), // Page title
            __('Form Settings', 'formit'), // Menu title
            'manage_options', // Capability required to access
            'settings', // Menu slug
            [$this, 'render_formit_builder_settings_page'] // Callback function
        );
    }


    /*  Callback function to render the settings page
        Developer need to read
    */
    function render_formit_builder_settings_page() {
        ?>
        <div class="wrap">
            <div class="form-data">
                <!-- Add your form settings HTML here -->
                <?php if (!current_user_can('manage_options')) { var_dump('helloss'); return; } ?>
                <div class="form-data">
                    <button type="button" class="tab-button active-tab" data-tab="tab1"><?php echo esc_html__('Settings', 'formit'); ?></button>
                    <button type="button" class="tab-button" data-tab="tab2"><?php echo esc_html__('System', 'formit'); ?></button>
                    <div id="tab1" class="tab active">
                        <?php 
                          $settingConfig = new SettingConfig();
                          $settingConfig->Setting_Tab(); // Call the Setting_Tab methoda();
                        ?>
                    </div>
                    <div id="tab2" class="tab">
                        <?php new System(); ?>
                    </div>
        
                </div>                
            </div>
        </div>
        <?php
    }

}
