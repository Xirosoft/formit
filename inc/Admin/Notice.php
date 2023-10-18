<?php
namespace Xirosoft\Formit\Admin;
use Xirosoft\Formit\Query;

class Notice {
    public function __construct() {
        add_action('admin_notices', array($this, 'display_notice'));
    }

    public function display_notice() {
        $route = new Route;
        $plugin_basename = plugin_basename(dirname(__FILE__) . '/formit.php');
        $dismissed_notice = 'formit_dismissed_notice';

        if (!get_user_meta(get_current_user_id(), $dismissed_notice)) {
            $message = __('Thank you for choice the Formit plugin! If you find it helpful, please consider leaving us a <a href="'. $route->create_url() .'">Create new form</a>.', 'formit');
            $dismiss_url = add_query_arg(array('dismiss' => $dismissed_notice), admin_url());

            echo '<div class="notice notice-info is-dismissible">';
            echo '<p>' . wp_kses_post($message) . '</p>';
            echo '<p><a href="' . esc_url($dismiss_url) . '">' . __('Dismiss', 'formit') . '</a></p>';
            echo '</div>';
        }
    }

    public function plugin_activation_redirect() {
        $route = new Route;
        // Redirect to the desired page or URL after plugin activation
        $redirect_url = $route->create_url(); // Replace with your desired URL

        // Redirect the user
        wp_redirect($redirect_url);
        exit;
    }

}

// Initialize the Formit_Admin_Notice class
// Dismiss admin notice when the "Dismiss" link is clicked
if (isset($_GET['dismiss']) && $_GET['dismiss'] === 'formit_dismissed_notice') {
    update_user_meta(get_current_user_id(), 'formit_dismissed_notice', true);
}
