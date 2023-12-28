<?php
namespace Xirosoft\Formit;
class FormBuilder {
    // Constructor
    public function __construct() {
        // Register custom routes
        add_action('init', array($this, 'register_routes'));

        // Register admin menus and submenus
        add_action('admin_menu', array($this, 'register_menus'));
    }

    // Register custom routes
    public function register_routes() {
        add_rewrite_rule('^wp-admin/formit/builder/?$', 'index.php?formit=builder', 'top');
        add_rewrite_rule('^wp-admin/formit/create/?$', 'index.php?formit=create', 'top');
        add_rewrite_rule('^wp-admin/formit/settings/?$', 'index.php?formit=settings', 'top');
        add_rewrite_rule('^wp-admin/formit/entries/?$', 'index.php?formit=entries', 'top');
        add_rewrite_rule('^wp-admin/formit/docs/?$', 'index.php?formit=docs', 'top');
        add_rewrite_rule('^wp-admin/formit/pro/?$', 'index.php?formit=pro', 'top');
        // Add more custom routes as needed

        flush_rewrite_rules();
    }

    // Register admin menus and submenus
    public function register_menus() {
        add_menu_page('Formit Builder', 'Formit Builder', 'manage_options', 'formit', array($this, 'builder_page'));
        add_submenu_page('formit', 'Create Form', 'Create Form', 'manage_options', 'formit_create', array($this, 'create_page'));
        add_submenu_page('formit', 'Form Settings', 'Form Settings', 'manage_options', 'formit_settings', array($this, 'settings_page'));
        add_submenu_page('formit', 'Entries', 'Entries', 'manage_options', 'formit_entries', array($this, 'entries_page'));
        add_submenu_page('formit', 'Docs', 'Docs', 'manage_options', 'formit_docs', array($this, 'docs_page'));
        add_submenu_page('formit', 'Premium', 'Premium', 'manage_options', 'formit_premium', array($this, 'premium_page'));
        // Add more menus and submenus as needed
    }

    // Callback function for Formit Builder page
    public function builder_page() {
        // Your code for the Formit Builder page goes here
        echo '<h1>Formit Builder Page</h1>';
    }

    // Callback function for Create Form page
    public function create_page() {
        // Your code for the Create Form page goes here
        echo '<h1>Create Form Page</h1>';
    }

    // Callback function for Form Settings page
    public function settings_page() {
        // Your code for the Form Settings page goes here
        echo '<h1>Form Settings Page</h1>';
    }

    // Callback function for Entries page
    public function entries_page() {
        // Your code for the Entries page goes here
        echo '<h1>Entries Page</h1>';
    }

    // Callback function for Docs page
    public function docs_page() {
        // Your code for the Docs page goes here
        echo '<h1>Docs Page</h1>';
    }

    // Callback function for Premium page
    public function premium_page() {
        // Your code for the Premium page goes here
        echo '<h1>Premium Page</h1>';
    }
}
