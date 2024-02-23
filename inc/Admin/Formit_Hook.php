<?php
namespace Xirosoft\Formit\Admin;
use Xirosoft\Formit\Formit_Query;

if ( ! class_exists( 'Formit_Hook' ) ) {
    class Formit_Hook{
            
        function __construct(){
            
            /**
            * Remove Permalink Under the Title hook with below mention method
            * @formit_remove_permalink_under_title 
            */
            add_filter('get_sample_permalink_html', [ $this, 'formit_remove_permalink_under_title'], 10, 4);
            /**
            * Remove Permalink quick edit button from view list with below mention method
            * @formit_remove_quick_edit_view_actions 
            */
            add_filter('post_row_actions', [$this,  'formit_remove_quick_edit_view_actions'], 10, 2);
            /**
            * Added Short code Column with with below mention method.
            * @formit_add_shortcode_column 
            */
            add_filter('manage_formit_posts_columns', [$this, 'formit_add_shortcode_column']);
            /**
            * Display hortcode column in the View list page with with below mention method.
            * @formit_display_shortcode_column 
            */
            add_action('manage_formit_posts_custom_column', [$this, 'formit_display_shortcode_column'], 10, 2);
            
            /**
            * Added Short code Column with with below mention method.
            * @formit_add_mail_Count_column 
            */
            add_filter('manage_formit_posts_columns', [$this, 'formit_add_mail_Count_column']);
            
            /**
            * Added Short code Column with with below mention method.
            * @formit_add_author_column 
            */
            add_filter('manage_formit_posts_columns', [$this, 'formit_add_author_column']);

            /**
            * Display shortcode column in the View list page with with below mention method.
            * @formit_display_mail_count_column 
            */
            add_action('manage_formit_posts_custom_column', [$this, 'formit_display_mail_count_column'], 10, 2);
            
            /**
            * Display shortcode column in the View list page with with below mention method.
            * @formit_display_author_column 
            */
            add_action('manage_formit_posts_custom_column', [$this, 'formit_display_author_column'], 10, 2);

            /**
            *  screeen option remove
            * @formit_display_author_column 
            */
            add_filter("manage_formit_posts_columns", [$this, "formit_remove_screen_options_for_custom_post_type"]);

            /**
            * Remove button remove hook.
            * @formit_display_author_column 
            */
            // add_action('admin_head', [$this, 'remove_preview_button_for_formit_builder']);

            add_action('admin_enqueue_scripts', [$this, 'formit_disable_autosave_interval_for_custom_post_type']);
            
            /**
            * Change the title placeholder name
            * callback @formit_custom_post_title_placeholder
            */
            add_filter('enter_title_here', [$this, 'formit_custom_post_title_placeholder'], 10, 2);

            /**
            * Publish metabox remove 
            * @formit_remove_publish_meta_box_from_custom_post_type
            */
            add_action('add_meta_boxes', [$this, 'formit_remove_publish_meta_box_from_custom_post_type']);
        
            /**
            * Sidebar Metabox remove
            * @formit_remove_sidebar_from_custom_post_type
            */
            add_action('add_meta_boxes', [$this, 'formit_remove_sidebar_from_custom_post_type']);

            /**
            * Screen tab option remove function
            * callback @formit_remove_screen_options_tab
            */
            add_filter('screen_options_show_screen', [$this, 'formit_remove_screen_options_tab'], 10, 2);

            /**
            * Button added on PLugin page
            * callback @formit_add_settings_link;
            */
            $plugin = FORMIT_PLUGIN_BASE;
            add_filter("plugin_action_links_$plugin", [$this, 'formit_add_settings_link']);

            /**
            * All Notice remove from FOrmit post type
            */
            add_action('current_screen', [$this, 'formit_disable_notices_for_custom_post_type']);


            // add_action( 'add_meta_boxes', [$this, 'formit_remove_wp_seo_meta_box'],100 );


        }

        function formit_remove_wp_seo_meta_box() {
            remove_meta_box('slugdiv', 'formit', 'normal');
            remove_meta_box('postcustom', 'formit', 'normal');
            remove_meta_box('wpseo_meta', 'formit', 'normal');
        }
    

        /**
        * formit_disable_notices_for_custom_post_type function
        *
        * @return void
        */
        function formit_disable_notices_for_custom_post_type() {
            $screen = get_current_screen();
            // Check if we are on the admin side and the current screen is for your custom post type.
            if (is_admin() && $screen && $screen->post_type === 'formit') {
                remove_all_actions('admin_notices');
            }
        }

        
        /**
        * formit_add_settings_link function
        *
        * @param [type] $links
        * @return array
        */
        function formit_add_settings_link($links) {
            $settings_link = '<a href="' . admin_url('edit.php?post_type=formit&page=settings') . '">Settings</a>';
            $docs_link = '<a href="' . admin_url('edit.php?post_type=formit&page=docs') . '">Docs</a>';
            array_push($links, $docs_link);
            array_push($links, $settings_link);
            return $links;
        } 
        
        /**
        * Screen topion tab function
        *
        * @param [type] $display
        * @param [type] $screen
        * @return void
        */
        function formit_remove_screen_options_tab($display, $screen) {
            // Replace 'your_custom_post_type' with the name of your custom post type.
            if ($screen->id == 'formit') {
                return false;
            }
            return $display;
        }
        
        /**
        * Sidebar remove function
        * @formit_remove_sidebar_from_custom_post_type
        * @return void
        */
        function formit_remove_sidebar_from_custom_post_type() {
            // Replace 'your_custom_post_type' with the name of your custom post type.
            remove_meta_box('dashboard_primary', 'formit', 'side');
        }
        
        /**
        * Remove publish function
        * callback @formit_remove_publish_meta_box_from_custom_post_type
        * @return void
        */
        function formit_remove_publish_meta_box_from_custom_post_type() {
            // Replace 'your_custom_post_type' with the name of your custom post type.
            remove_meta_box('submitdiv', 'formit', 'side');
        }

        /*  This Filter hook apply for permalink remove
            Developer need to read
        */
        function formit_remove_permalink_under_title($return, $id, $new_title, $new_slug) {
            // Check if it's a custom post type where you want to hide the permalink
            if (get_post_type($id) === 'formit') {
                $return = '';
            }
            return $return;
        }

        /*  This Filter hook apply for remove view button and quick edit
            Developer need to read
        */
        function formit_remove_quick_edit_view_actions($actions, $post) {
            if ($post->post_type === 'formit') {
                // Remove the "Quick Edit" and "View" actions
                unset($actions['inline hide-if-no-js']);
                unset($actions['view']);
            }
            return $actions;
        }
            
        /*  Add a custom column to the post list screen
            Developer need to read
        */
        function formit_add_shortcode_column($columns) {
            $columns['shortcode'] = __( 'Shortcodes', 'formit' );
            return $columns;
        }
        
        /*  Add a custom column to the post list screen for Mail count
            Developer need to read
        */
        function formit_add_mail_Count_column($columns) {
            $columns['mailcount'] = __( 'Mail Count', 'formit' );
            return $columns;
        }
        
        /*  Add a custom column to the post list screen
            Developer need to read
        */
        function formit_add_author_column($columns) {
            $columns['author'] = __( 'Author', 'formit' );
            return $columns;
        }

        /* 
        This action hook use for shortcode showing in the column
        */
        function formit_display_shortcode_column($column, $post_id) {
            if ($column === 'shortcode') {
                // Get the generated shortcode from post meta
                $shortcode = get_post_meta($post_id, '_formit_builder_shortcode', true);
                // Display the shortcode
                echo '<span class="copy_shortcode" title="Click to copy">'.esc_html($shortcode).'</span>';
            }
        }

        /* 
        This action hook use for Mail Count showing in the column
        */
        function formit_display_mail_count_column($column, $post_id) {
            global $wpdb;

            // Replace 'your_table_name' with the actual table name if it's not the default 'wp_posts' table
            $table_name = $wpdb->prefix . 'formit_forms';
            $query = "SELECT ID FROM %1s WHERE post_id = %d";
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            $form_id_query = $wpdb->prepare($query, $table_name, $post_id);

            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            $form_id = $wpdb->get_var($form_id_query);

            if ($column === 'mailcount') {
                // Get the generated shortcode from post meta
                $total = new Formit_Query();
                $totalCount = $total->formit_count_form_entries($form_id);
                // Display the mailcount
                echo esc_html($totalCount);
            }
        }

        /*
        This action hook use for Mail Count showing in the column
        */
        function formit_display_author_column($column, $post_id) {
            if ($column === 'author') {
                // Replace 'your_post_id' with the actual post ID you want to look up.
                $postid = $post_id;
                // Get the author's display name for the given post ID
                $author_name = get_the_author_meta('display_name', get_post_field('post_author', $postid));
            }
        }
        
        /**
        * Screen Option function
        *
        * @param [type] $columns
        * @return void
        */
        function formit_remove_screen_options_for_custom_post_type($columns) {
            global $current_screen;
            // Check if we are on the desired custom post type screen.
            if ('formit' === $current_screen->post_type) {
                // Remove the screen option.
                add_filter('screen_options_show_screen', '__return_false');
            }
            return $columns;
        }

    /**
        * Disable auto save function
        *
        * @return void
        */
        function formit_disable_autosave_interval_for_custom_post_type() {
            global $typenow;
            // Replace 'your_custom_post_type' with the name of your custom post type.
            if ($typenow == 'formit') {
                wp_deregister_script('autosave');
            }
        }
        
        /**
        * Title placeholder change function
        *
        * @param [String] $translated_text
        * @param [String] $text
        * @param [String] $domain
        * @return void
        */
        function formit_custom_post_title_placeholder($title_placeholder, $post) {
            // Replace 'your_custom_post_type' with the name of your custom post type.
            if ($post->post_type == 'formit') {
                $title_placeholder = 'Enter Form Name';
            }
            return $title_placeholder;
        }
    }
}