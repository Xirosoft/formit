<?php 

namespace Xirosoft\Formit\Widgets\Block;
use Xirosoft\Formit\Formit_Query;

if ( ! class_exists( 'Formit_GutenbergWidget' ) ) {
    class Formit_GutenbergWidget{

        function __construct(){
            add_action( 'init', [$this, 'formit_register_block'] );
        }

        function formit_register_block() {
            register_block_type( __DIR__ );

            $formit_data = array(
                'formList' => $this->formit_get_form_list(), // Assuming this function fetches your form list.
            );
            wp_localize_script('block.js', 'formitData', $formit_data);
        }


        function formit_get_form_list() {
            global $wpdb;
            $query = new Formit_Query;
            $table_name = $wpdb->prefix . 'formit_forms'; // Corrected the table name
            $all_form_id = $query->formit_get_all_form_id($table_name); // Assuming you have a method called 'formit_get_all_form_id'
        
            $catlist = array();

            if ($all_form_id) {
                foreach ($all_form_id as $form) {
                    $catlist[(int)$form->post_id] = $form->form_title;
                }
            } else {
                $catlist['0'] = esc_html__('No Pages Found!', 'formit');
            }
        
            return $catlist;
        }
    }
}