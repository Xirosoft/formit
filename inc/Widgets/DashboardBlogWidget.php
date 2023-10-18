<?php

namespace Xirosoft\Formit\Widgets;
use Xirosoft\Formit\Query;

class DashboardBlogWidget{

    function __construct(){
        // Add action to initialize the dashboard widget and load scripts
        add_action( 'wp_dashboard_setup', [$this, 'formit_news_dashboard_add_widgets'] );
        add_action( 'admin_enqueue_scripts', [$this, 'formit_news_scripts'] );
    }

    /*
    Dashboard Widget Setup
    */
    function formit_news_dashboard_add_widgets() {
        // Add the Formit News dashboard widget
        wp_add_dashboard_widget( 'formit_news_dashboard_widget_news', __( 'Formit News', 'formit' ), [$this, 'formit_news_dashboard_widget_news_handler'], 'formit_news_dashboard_widget_news_config_handler' );
    }

    // Callback function to display the Formit News dashboard widget
    function formit_news_dashboard_widget_news_handler() {
        // Get widget configuration options
        $options = wp_parse_args( get_option( 'formit_news_dashboard_widget_news' ), $this->formit_news_dashboard_widget_news_config_defaults() );

        // Define RSS feeds
        $feeds = array(
            array(
                'url'          => 'https://www.themeies.com/blog/feed/',
                'items'        => $options['items'],
                'show_summary' => 0,
                'show_author'  => 0,
                'show_date'    => 1,
            ),
        );

        // Display the widget content
        wp_dashboard_primary_output( 'formit_news_dashboard_widget_news', $feeds );

        ?>
        <br>
        <hr>
        <div class="t-overview__footer">
            <ul>
                <li class="e-overview"><a href="<?php echo esc_url('https://themeies.com/blog/') ?>" target="_blank"> <?php esc_html_e( 'See More Blog', 'formit' ) ?> <span class="screen-reader-text"><?php esc_html_e( '(opens in a new window)', 'formit' ) ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
                <li class="e-overview"><a href="<?php echo esc_url('https://themeies.com/contact-us/' ) ?>" target="_blank"><?php esc_html_e( 'Help', 'formit' ) ?>  <span class="screen-reader-text"><?php esc_html_e( '(opens in a new window)', 'formit' ) ?></span><span aria-hidden="true" class a="dashicons dashicons-external"></span></a></li>
                <li class="e-overview"><a href="<?php echo esc_url('https://themeies.com/item/borax') ?>" target="_blank"><?php esc_html_e( 'Premium Borax', 'formit' ) ?> <span class="screen-reader-text"><?php esc_html_e( '(opens in a new window)', 'formit' ) ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
            </ul>
        </div>
        <?php
    }

    // Default configuration for the widget
    function formit_news_dashboard_widget_news_config_defaults() {
        return array(
            'items' => 5,
        );
    }

    // Callback function for handling widget configuration
    function formit_news_dashboard_widget_news_config_handler() {
        $options = wp_parse_args( get_option( 'formit_news_dashboard_widget_news' ), $this->formit_news_dashboard_widget_news_config_defaults() );

        if ( isset( $_POST['submit'] ) ) {
            if ( isset( $_POST['rss_items'] ) && intval( $_POST['rss_items'] ) > 0 ) {
                $options['items'] = intval( $_POST['rss_items'] );
            }

            // Update the widget configuration options
            update_option( 'formit_news_dashboard_widget_news', $options );
        }

        ?>
        <p>
            <label><?php _e( 'Number of RSS articles:', 'dw' ); ?>
                <input type="text" name="rss_items" value="<?php echo esc_attr( $options['items'] ); ?>" />
            </label>
        </p>
        <?php
    }

    // Enqueue styles for the widget
    function formit_news_scripts( $hook ) {
        $screen = get_current_screen();
        if ( 'dashboard' === $screen->id ) {
            wp_enqueue_style( 'formit_news_style', plugin_dir_url( __FILE__ ) . '../css/ele-custom.css', array(), '1.0' );
        }
    }
}