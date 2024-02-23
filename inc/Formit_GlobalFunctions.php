<?php
namespace Xirosoft\Formit;
use Xirosoft\Formit\Admin\Popup\Formit_HeaderProfileInfo;
use Xirosoft\Formit\Admin\Formit_Route;
use is_plugin_active;

/**
 * This class using for manage all Admin raleted class 
 * @GlobalFunctions
 */

if ( ! class_exists( 'Formit_GlobalFunctions' ) ) {
    class Formit_GlobalFunctions {

        /**
        * Construct function for Gloabal
        */
        function __construct(){
            new Formit_Formhandle(); 
            new Formit_Query(); 

            /**
            * Custom Header action 
            * callback @formit_custom_dashboard_header
            */
            add_action('admin_head', [$this,'formit_custom_dashboard_header']);

            /**
            * Elementor addon will be active if Elementor active
            */
            if (did_action( 'elementor/loaded' ) ) {
                add_action('elementor/widgets/widgets_registered', [$this, 'formit_register_widgets']);	
            }
        }

    

        /**
        * MSFROM Custom Header function
        *
        * @return void
        */
        function formit_custom_dashboard_header() {
            global $post_type;
            $route = new Formit_Route;

            // Get the admin page title
            $page_title = get_admin_page_title();

            // Check if the current screen is for the 'formit' custom post type
            if ($post_type === 'formit' || strpos($route->formit_current_url(), $route->formit_page_slug('submission')) !== false  || strpos($route->formit_current_url(), $route->formit_page_slug('settings')) !== false   || strpos($route->formit_current_url(), $route->formit_page_slug('docs')) !== false ) {
    
            ?>
                <div id="wpheader">
                    <div class="wpheader__title">
                        <a class="wpheader__logo" href="<?php echo esc_url( FORMIT_URL ) ?>">
                            <img src="<?php echo esc_url( FORMIT_ASSETS_URL.'/img/logo-icon.svg') ?>" alt="formit-logo" />  
                        </a>
                        <div class="wpheader__name">
                            <small class="wpheader_title_version"><?php echo esc_html( 'FORMIT-v1.0.0' ) ?> <span class="version-beta"><?php echo esc_html__('Beta', 'formit'); ?></span></small>  
                            <h2>
                                <?php 
                                    // Check if it's the "Add New" page for the "formit" post type
                                    if (isset($_GET['post_type']) && sanitize_text_field($_GET['post_type']) === 'formit' && strpos(sanitize_url($_SERVER['REQUEST_URI']), 'post-new.php') !== false) {
                                        echo esc_html__('Create New Form', 'formit');
                                    } else {
                                        printf(
                                            esc_html__( '%s', 'formit' ),
                                            esc_html($page_title)
                                        );
                                    }
                                ?>
                            </h2>
                        </div>
                    </div>
                    <div class="wpheader__meta">
                        <a href="<?php echo esc_url($route->formit_create_url()); ?>" class="wpheader__meta__icon wpheader__meta__add_new" title="<?php echo esc_attr__( 'Create New Form', 'formit' ) ?>"><i class="formbuilder-icon-add"></i></a>
                        <a href="<?php echo esc_url($route->formit_page_url('docs')); ?>" class="wpheader__meta__icon wpheader__meta__docs" title="<?php echo esc_attr__( 'Documentation', 'formit' ) ?>"><i class="formbuilder-icon-docs"></i></a>
                        <a href="<?php echo esc_url($route->formit_page_url('settings')); ?>" class="wpheader__meta__icon wpheader__meta__addon" title="<?php echo esc_attr__( 'Settings', 'formit' ) ?>"><i class="formbuilder-icon-settings"></i></a>
                        <button class="wpheader__meta__icon wpheader__meta__info" title="<?php echo esc_attr__( 'Info', 'formit' ) ?>" data-popup="#info-popup"><i class="formbuilder-icon-info"></i></button>
                    </div>
                </div>
                <?php
                $header_profile = new Formit_HeaderProfileInfo;
                $header_profile->formit_profile_info_popup();
            }
        }


        public function formit_register_widgets($widgets_manager) {
            // Its is now safe to include Widgets files
            $widgets_manager->register_widget_type( new Widgets\Formit_ElementorWidget() );
        }

    }
}