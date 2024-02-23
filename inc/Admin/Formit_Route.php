<?php
namespace Xirosoft\Formit\Admin;
use Xirosoft\Formit\Query;

if ( ! class_exists( 'Formit_Route' ) ) {
    final class Formit_Route{
        public function __construct(){
            // add_action('admin_init', array($this, 'add_rewrite_rules'));
        }
    
        function formit_page_slug($url_slug){
            $page_slug = '/wp-admin/edit.php?post_type=formit&page='.$url_slug;
            return $page_slug;
        }
        public function formit_page_url($url_slug){
            $page_url =  home_url('/wp-admin/edit.php?post_type=formit&page='.$url_slug);
            return $page_url;
        }
        public function formit_current_url(){
            $current_url = home_url(add_query_arg(array(), sanitize_text_field($_SERVER['REQUEST_URI'])));
            return $current_url;
        }
        public function formit_create_url(){
            $new_from_slug =  home_url('/wp-admin/post-new.php?post_type=formit');
            return $new_from_slug;
        }
        public function formit_settings_url(){
            $settings = $this->formit_page_url('settings');
            return $settings;
        }
        public function formit_entreies_url(){
            $docs = $this->formit_page_url('docs');
            return $docs;
        }
        public function formit_docs_url(){
            $submission = $this->formit_page_url('submission');
            return $submission;
        }

        public function formit_add_rewrite_rules() {
            // add_rewrite_rule('^wp-admin/formit/forms/?$', 'index.php?post_type=formit', 'top');
            // add_rewrite_rule('^wp-admin/formit/create/?$', 'wp-admin/post-new.php?post_type=formit', 'top');
            // add_rewrite_rule('^wp-admin/formit/settings/?$', 'index.php?post_type=formit&page=formit-settings', 'top');
            // add_rewrite_rule('^wp-admin/formit/submission/?$', 'index.php?post_type=formit&page=formit-submissions', 'top');
            // add_rewrite_rule('^wp-admin/formit/docs/?$', 'index.php?post_type=formit&page=formit-docs', 'top');
        }       
    }
}