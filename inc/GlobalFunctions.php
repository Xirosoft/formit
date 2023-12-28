<?php
namespace Xirosoft\Formit;
use Xirosoft\Formit\Admin\Popup\HeaderProfileInfo;
use Xirosoft\Formit\Admin\Route;
use is_plugin_active;

/**
 * This class using for manage all Admin raleted class 
 * @GlobalFunctions
 */
class GlobalFunctions {

    /**
     * Construct function for Gloabal
     */
    function __construct(){
        new Formhandle(); 
        new Query(); 

        /**
         * Custom Header action 
         * callback @formit_custom_dashboard_header
         */
        add_action('admin_head', [$this,'formit_custom_dashboard_header']);

        /**
         * Elementor addon will be active if Elementor active
         */
		if (did_action( 'elementor/loaded' ) ) {
            add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);	
		}
    }

   

    /**
     * MSFROM Custom Header function
     *
     * @return void
     */
    function formit_custom_dashboard_header() {
        global $post_type;
        $route = new Route;

        // Get the admin page title
        $page_title = get_admin_page_title();

        // Check if the current screen is for the 'formit' custom post type
        if ($post_type === 'formit' || strpos($route->current_url(), $route->page_slug('submission')) !== false  || strpos($route->current_url(), $route->page_slug('settings')) !== false   || strpos($route->current_url(), $route->page_slug('docs')) !== false ) {
 
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
                                if (isset($_GET['post_type']) && $_GET['post_type'] === 'formit' && strpos($_SERVER['REQUEST_URI'], 'post-new.php') !== false) {
                                    echo esc_html__('Create New Form', 'formit');
                                } else {
                                    echo esc_html__($page_title, 'formit'); 
                                }
                            ?>
                        </h2>
                    </div>
                </div>
                <div class="wpheader__meta">
                    <a href="<?php echo esc_url($route->create_url()); ?>" class="wpheader__meta__icon wpheader__meta__add_new" title="<?php echo esc_attr__( 'Create New Form', 'formit' ) ?>"><i class="formbuilder-icon-add"></i></a>
                    <a href="<?php echo esc_url($route->page_url('docs')); ?>" class="wpheader__meta__icon wpheader__meta__docs" title="<?php echo esc_attr__( 'Documentation', 'formit' ) ?>"><i class="formbuilder-icon-docs"></i></a>
                    <a href="<?php echo esc_url($route->page_url('settings')); ?>" class="wpheader__meta__icon wpheader__meta__addon" title="<?php echo esc_attr__( 'Settings', 'formit' ) ?>"><i class="formbuilder-icon-settings"></i></a>
                    <button class="wpheader__meta__icon wpheader__meta__info" title="<?php echo esc_attr__( 'Info', 'formit' ) ?>" data-popup="#info-popup"><i class="formbuilder-icon-info"></i></button>
                </div>
            </div>
            <?php
            $header_profile = new HeaderProfileInfo;
            $header_profile->profile_info_popup();
        }
    }


    public function register_widgets($widgets_manager) {
		// Its is now safe to include Widgets files
		$widgets_manager->register_widget_type( new Widgets\ElementorWidget() );
	}

   
    
   

}

