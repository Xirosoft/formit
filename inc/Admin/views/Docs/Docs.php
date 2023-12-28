<?php 
// namespace Xirosoft\Formit\Admin;
namespace Xirosoft\Formit\Admin\views\Docs;

 class Docs{
    function __construct(){
        add_action('admin_menu', [$this, 'add_settings_submenu_page']);
    }  
    /*  This method use for from settings. Add a submenu page for settings under the "Add New" menu
        Developer need to read
    */
    function add_settings_submenu_page() {
        add_submenu_page(
            'edit.php?post_type=formit', // Parent menu slug
            __('Docs', 'formit'), // Page title
            __('Docs', 'formit'), // Menu title
            'manage_options', // Capability required to access
            'docs', // Menu slug
            [$this, 'render_formit_builder_docs_page'] // Callback function
        );
    }


    /*  Callback function to render the settings page
        Developer need to read
    */
    function render_formit_builder_docs_page() {
      
        ?>
        <div class="wrap">
            <div class="container">
                <div class="doc__wrapper">
                    <div class="doc__sidebar">
                        <button class="doc-table-of-contact-toggoler" type="button"><i class="fi-select"></i></button>
                        <nav class="doc__nav">
                            <h5 class="doc__nav__title"><?php echo esc_html__( 'Navigation', 'formit' ) ?></h5>
                            <ul>
                                <li><a href="#introduction" class="active" data-target="introduction"><?php echo esc_html__( 'Introduction', 'formit' ) ?></a></li>
                                <li><a href="#how-to-install" data-target="how-to-install"><?php echo esc_html__( 'How to Install', 'formit' ) ?></a></li>
                                <li><a href="#create-a-new-form" data-target="create-a-new-form"><?php echo esc_html__( 'Create a New Form', 'formit' ) ?></a></li>
                                <li><a href="#widgets" data-target="widgets"><?php echo esc_html__( 'Widgets', 'formit' ) ?></a>
                                    <ul>
                                        <li><a href="#shortcode" data-target="shortcode"><?php esc_attr_e( 'Shortcode', 'formit' ) ?></a></li>
                                        <li><a href="#elementor-widget" data-target="elementor-widget"><?php echo esc_html__( 'Elementor Widgets', 'formit' ); ?></a></li>
                                        <li><a href="#gutenburg-widget" data-target="gutenburg-widget"><?php echo esc_html__( 'Gutenburg Widgets', 'formit' ); ?></a></li>
                                        <li><a href="#dashboard-widget" data-target="dashboard-widget"><?php echo esc_html__( 'Dashboard Widgets', 'formit' ); ?></a></li>
                                    </ul>
                                </li>
                                <li><a href="#form-builder-configs" data-target="form-builder-configs"><?php echo esc_html__( 'Form Builder Configs', 'formit' ); ?></a></li>
                                <li><a href="#form-settings" data-target="form-settings"><?php echo esc_html__( 'Form Settings', 'formit' ); ?></a></li>
                                <li><a href="#form-submissions" data-target="form-submissions"><?php echo esc_html__( 'Form Submissions', 'formit' ); ?></a>
                                    <ul>
                                        <li><a href="#form-submissions-form-data" data-target="form-submissions-form-data"><?php echo esc_html__( 'Form Data', 'formit' ); ?></a></li>
                                        <li><a href="#form-submissions-form-analytics" data-target="form-submissions-form-analytics"><?php echo esc_html__( 'Analytics', 'formit' ); ?></a></li>
                                        <li><a href="#form-submissions-form-geo" data-target="form-submissions-form-geo"><?php echo esc_html__( 'Geolocation', 'formit' ); ?></a></li>
                                    </ul>
                                </li>
                                <li><a href="#intregation" data-target="intregation"><?php echo esc_html__( 'Integration', 'formit' ); ?></a></li>
                                <li><a href="#import-export" data-target="import-export"><?php echo esc_html__( 'Import/export', 'formit' ); ?></a></li>
                            </ul>
                        </nav>
                    </div>
                    <div class="doc__main">
                        <article id="introduction" data-type="section">
                            <h2 class="doc__title"><?php echo esc_html__('Introduction', 'formit'); ?></h2>
                            <div class="doc__content">
                                <p><?php echo esc_html__('FormIt Builder is a cool and easy-to-use tool that you can add to your WordPress website. It helps you make special forms without any hassle. With FormIt Builder, you get more than 15 different kinds of fields and over 50 unique forms to choose from. This means you have lots of choices to create the perfect form for your website! 🎨 It\'s like having a magic wand for making forms. 🚀', 'formit'); ?> </p>
                            </div>
                        </article>

                        <article id="how-to-install" data-type="section">
                            <h2 class="doc__title"><?php echo esc_html__('How to Install', 'formit'); ?></h2>
                            <div class="doc__content">
                                <p><?php echo esc_html__('If you want to add FormIt Builder to your website, just follow these steps:', 'formit'); ?></p>
                                <ul class="steps">
                                    <li><?php echo esc_html__('Open your WordPress dashboard. It\'s like the control center for your website.', 'formit'); ?></li>
                                    <li><?php echo esc_html__('Look for the', 'formit'); ?> <kbd><?php echo esc_html__('Plugins > Add New', 'formit'); ?></kbd>.</li>
                                    <li><?php echo esc_html__('In the search bar, type', 'formit'); ?> <kbd><?php echo esc_html__('FormIt Builder', 'formit'); ?></kbd> <?php echo esc_html__( 'and hit Enter.', 'formit' ) ?></li>
                                    <li><?php echo esc_html__('When you see FormIt Builder, click on', 'formit'); ?> <kbd><?php echo esc_html__('Install Now', 'formit'); ?></kbd> <?php echo esc_html__('and then', 'formit'); ?> <kbd><?php echo esc_html__('Activate', 'formit'); ?></kbd>.</li>
                                </ul>
                            </div>
                        </article>


                        <article id="create-a-new-form" data-type="section">
                            <h2 class="doc__title"><?php echo esc_html__('Create a New Form', 'formit'); ?></h2>
                            <div class="doc__content">
                                <p><?php echo esc_html__('Once FormIt Builder is activated, follow these steps to make a new form:', 'formit'); ?></p>

                                <ul class="steps">
                                    <li><?php echo esc_html__('Go to FormIt Builder in your WordPress dashboard.', 'formit'); ?></li>
                                    <li><?php echo esc_html__('Find', 'formit'); ?> <kbd><?php echo esc_html__('FormIt Builder', 'formit'); ?></kbd> <?php esc_attr_e( 'in your WordPress dashboard. It\'s where all the form-making magic happens!', 'formit' ) ?></li>
                                    <li><?php echo esc_html__('Click', 'formit'); ?> <kbd><?php echo esc_html__('Add New Form', 'formit'); ?></kbd></li>
                                    <li><?php echo esc_html__('Set up your form the way you want by changing the settings and adding the fields you need.', 'formit'); ?></li>
                                    <li><?php echo esc_html__('Save your form and put it on the page or post where you want it to be.', 'formit'); ?></li>
                                </ul>
                                <img class="doc__image" src="<?php echo esc_url( FORMIT_ASSETS_URL. "img/docs/formit-screenshot.webp" ) ?>" alt="<?php esc_attr_e('screenshot', 'formit'); ?>" />
                            </div>
                        </article>

                        <article id="widgets" data-type="section">
                            <h2 class="doc__title"><?php echo esc_html__('Widgets', 'formit'); ?></h2>
                            <p style="font-weight:600"><?php echo esc_html__('Using FormIt Builder Widgets to Display Your Form', 'formit'); ?></p>
                            <p><?php echo esc_html__( 'FormIt Builder provides different widgets that make it easy to display your forms on your website using various page builders and platforms. In this guide, we\'ll show you how to use the available widgets: Shortcode, Elementor Widget, Gutenberg Widget, and Dashboard Widget.', 'formit' ) ?></p>
                            
                            <div class="doc__content">
                                <div data-type="section">
                                    <h4 id="shortcode"><?php echo esc_html__('1. Shortcode', 'formit'); ?></h4>
                                    <p><?php echo esc_html__('The Shortcode widget is a simple and versatile way to display your FormIt Builder form on any page or post.', 'formit' ) ?>
                                    <br>
                                    <br>
                                    <strong><?php echo esc_html__('To use the Shortcode widget:', 'formit' ) ?></strong>
                                    <br>
                                    <?php echo esc_html__('In the editor of the page or post where you want to display the form, add the following shortcode:', 'formit' ) ?></p>
                                    <kbd><?php esc_attr_e('[formit id="1" title="Formit Form"]', 'formit'); ?></kbd>
                                    <br>
                                    <i><?php echo esc_html__('Replace id="1" with the ID of your specific form and customize the title attribute if desired.', 'formit'); ?></i>
                                    <br>
                                    <br>
                                </div>

                                <div data-type="section">
                                    <h4 id="elementor-widget"><?php echo esc_html__('2. Elementor Widget', 'formit'); ?></h4>
                                    <p><?php echo esc_html__('Elementor is a popular page builder that allows for easy customization and design of web pages. To use the Elementor widget with Formit Builder:', 'formit'); ?></p>
                                    <ul class="steps">
                                        <li><?php echo esc_html__('Open the page with Elementor for editing.', 'formit'); ?></li>
                                        <li><?php echo esc_html__('Look for the FormIt Builder widget in the Elementor sidebar.', 'formit'); ?></li>
                                        <li><?php echo esc_html__('Drag and drop the widget onto your desired section of the page.', 'formit'); ?></li>
                                        <li><?php echo esc_html__('Configure the widget by selecting the form you want to display from the dropdown.', 'formit'); ?></li>
                                    </ul>
                                </div>

                                <div data-type="section">
                                    <h4 id="gutenburg-widget"><?php echo esc_html__('3. Gutenburg Widget', 'formit'); ?></h4>
                                    <p><?php echo esc_html__('Gutenberg is the default WordPress editor, providing a block-based interface for creating content. To use the Gutenberg widget with FormIt Builder:', 'formit'); ?></p>
                                    <ul class="steps">
                                        <li><?php echo esc_html__('Edit the page or post with Gutenberg.', 'formit'); ?></li>
                                        <li><?php echo esc_html__('Add a new block and search for "FormIt Builder" or find it under the widgets section.', 'formit'); ?></li>
                                        <li><?php echo esc_html__('Select the form you want to display from the block options.', 'formit'); ?></li>
                                    </ul>
                                </div>
                                
                                <div data-type="section">
                                    <h4 id="dashboard-widget"><?php echo esc_html__('4. Dashboard Widget', 'formit'); ?></h4>
                                    <img class="doc__image" style="max-width: 400px" src="<?php echo esc_url(FORMIT_ASSETS_URL. "img/docs/dashboard-widget.webp" )?>" alt="<?php esc_attr_e('screenshot', 'formit'); ?>" />
                                    <p><?php echo esc_html__('The Dashboard widget allows you to display your form on the WordPress dashboard.', 'formit'); ?> <br> <?php echo esc_html__('To add the FormIt Builder widget to the WordPress dashboard:', 'formit'); ?></p>
                                    <ul class="steps">
                                        <li><?php echo esc_html__('Navigate to the WordPress dashboard.', 'formit'); ?></li>
                                        <li><?php echo esc_html__('Look for the FormIt Builder section in the dashboard menu.', 'formit'); ?></li>
                                        <li><?php echo esc_html__('Configure the widget settings and select the form you want to display.', 'formit'); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </article>


                        <article id="form-builder-configs" data-type="section">
                            <h2 class="doc__title"><?php echo esc_html__('Form Builder Configs', 'formit'); ?></h2>
                            <div class="doc__content">
                                <img class="doc__image" src="<?php echo esc_url( esc_attr( FORMIT_ASSETS_URL. "img/docs/form-builder-configs.webp" ) ); ?>" alt="<?php esc_attr_e('screenshot', 'formit'); ?>" />
                            </div>
                        </article>

                        <article id="form-settings" data-type="section">
                            <h2 class="doc__title"><?php echo esc_html__('Form Settings', 'formit'); ?></h2>
                            <div class="doc__content">
                                <img class="doc__image" src="<?php echo esc_url( esc_attr( FORMIT_ASSETS_URL. "img/docs/form-settings.webp" ) ); ?>" alt="<?php esc_attr_e('screenshot', 'formit'); ?>" />
                            </div>
                        </article>


                        <article id="form-submissions" data-type="section">
                            <h2 class="doc__title"><?php echo esc_html__('Form Submissions', 'formit'); ?></h2>
                            <div class="doc__content">
                                <div data-type="section">
                                    <img class="doc__image" src="<?php echo esc_url( esc_attr( FORMIT_ASSETS_URL. "img/docs/form-submissions.webp" ) ); ?>" alt="<?php esc_attr_e('screenshot', 'formit'); ?>" />
                                    <h4 id="form-submissions-form-data"><?php echo esc_html__('Form Data', 'formit'); ?></h4>
                                    <img class="doc__image" src="<?php echo esc_url( esc_attr( FORMIT_ASSETS_URL. "img/docs/form-submissions-open.webp" ) ); ?>" alt="<?php esc_attr_e('screenshot', 'formit'); ?>" />
                                </div>

                                <div data-type="section">
                                    <h4 id="form-submissions-form-analytics"><?php echo esc_html__('Form Analytics', 'formit'); ?></h4>
                                    <img class="doc__image" src="<?php echo esc_url( esc_attr( FORMIT_ASSETS_URL. "img/docs/form-submissions-open-analytics.webp" ) ); ?>" alt="<?php esc_attr_e('screenshot', 'formit'); ?>" />
                                </div>

                                <div data-type="section">
                                    <h4 id="form-submissions-form-geo"><?php echo esc_html__('Form Geo Location', 'formit'); ?></h4>
                                    <img class="doc__image" src="<?php echo esc_url( esc_attr( FORMIT_ASSETS_URL. "img/docs/form-submissions-open-geo.webp" ) ); ?>" alt="<?php esc_attr_e('screenshot', 'formit'); ?>" />
                                </div>
                            </div>
                        </article>


                        <article id="integration" data-type="section">
                            <h2 class="doc__title"><?php echo esc_html__('Integration', 'formit'); ?></h2>
                            <div class="doc__content">
                                <p><?php echo esc_html__('As of this version, we do not currently provide any integration options.', 'formit'); ?></p>
                            </div>
                        </article>


                        <article id="import-export" data-type="section">
                            <h2 class="doc__title"><?php echo esc_html__('Import/Export', 'formit'); ?></h2>
                            <div class="doc__content">
                                <p><?php echo esc_html__('As of this version, we do not currently provide any Import/Export options.', 'formit'); ?></p>
                            </div>
                        </article>
                    </div>
                </div>
            </div>            
        </div>
        <?php
    }

}
