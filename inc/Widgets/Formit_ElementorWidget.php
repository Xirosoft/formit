<?php
namespace Xirosoft\Formit\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Xirosoft\Formit\Formit_Query;

if ( ! class_exists( 'Formit_ElementorWidget' ) ) {
    // Define a custom Elementor widget class
    class Formit_ElementorWidget extends Widget_Base{
        
        // Define the widget's name
        public function get_name() {
            return 'formit_builder';
        }

        // Define the widget's title
        public function get_title() {
            return esc_html__( 'Fromit Builder', 'formit' );
        }

        // Define the widget's icon (used in Elementor)
        public function get_icon() {
            return 'eicon-form-horizontal';
        }

        // Define the widget's category (used in Elementor)
        public function get_categories() {
            return [ 'basic' ];
        }

        // Define keywords associated with the widget (used in Elementor)
        public function get_keywords() {
            return [ 'formit', 'form' ];
        }

        // Register controls for the widget in Elementor
        protected function _register_controls() {
            $this->start_controls_section(
                'section_content',
                [
                    'label' => esc_html__( 'Formit Contact Form', 'formit' ),
                ]
            );

            // Add a control for selecting a Formit contact form
            $this->add_control(
                'formit_id',
                [
                    'label' => esc_html__( 'Select Contact Form', 'formit' ),
                    'description' => esc_html__('Formit Contact Form - plugin must be installed and there must be create contact forms made with the contact form 7 as you want','formit'),
                    'type' => Controls_Manager::SELECT2,
                    'multiple' => false,
                    'label_block' => 1,
                    'options' => $this->formit_get_form_list(), // Fetch options from the formit_get_form_list() method
                ]
            );

            $this->end_controls_section();
        }
            
        // Fetch a list of Formit contact forms from the database
        public function formit_get_form_list(){
            global $wpdb;
            $query = new Formit_Query;
            $table_name = $wpdb->prefix . 'formit_forms'; 
            $all_form_id = $query->formit_get_all_form_id($table_name);
            $catlist=[];
            if($all_form_id){
                foreach ($all_form_id as $from ) {
                (int)$catlist[$from->post_id] = $from->form_title;
                }
            }
            else{
                (int)$catlist['0'] = esc_html__('No Pages Found!', 'formit');
            }
            return $catlist;
        }

        // Fetch a list of all WordPress pages
        public function formit_get_all_wp_pages(){
            $args = array('post_type' => 'page', 'posts_per_page' => -1);
            $catlist=[];
            if( $categories = get_posts($args)){
                foreach ( $categories as $category ) {
                (int)$catlist[$category->ID] = $category->post_title;
                }
            }
            else{
                (int)$catlist['0'] = esc_html__('No Pages Found!', 'formit');
            }
            return $catlist;
        }
        
        // Render the widget's content
        protected function render() {
            // call load widget script
            $this->load_widget_script();
            static $data=0;
            $settings = $this->get_settings();
            if(!empty($settings['formit_id'])){
                // Render the Formit contact form using a shortcode
                echo'<div class="elementor-shortcode formit-'.$data.'">';
                echo do_shortcode('[formit id="'.$settings['formit_id'].'" title="Dev Hunt"]');    
                echo '</div>';  
            }
            $data++;
        }

        public function load_widget_script(){
            if( \Elementor\Plugin::$instance->editor->is_edit_mode() === true  ) {
                ?>
                <script>
                    ( function( $ ){
                        function formitDomMerge() {
                            console.log('yyy');
                            var mergedContent = {};

                            // Iterate through elements with a specific class or attribute
                            $(".rendered-form .row").each(function() {
                                var id = $(this).attr("id");
                                var content = $(this).html();

                                // Store or merge the content based on ID
                                if (mergedContent[id]) {
                                    mergedContent[id] += content;
                                } else {
                                    mergedContent[id] = content;
                                }
                            });

                            // Clear existing elements
                            $(".rendered-form").empty();

                            // Rebuild the DOM with merged content
                            for (var id in mergedContent) {
                                $("<div>")
                                    .attr("id", id)
                                    .addClass("row")
                                    .html(mergedContent[id])
                                    .appendTo(".rendered-form");
                            }
                        }

                        formitDomMerge()

                    })(jQuery);
                </script>
                <?php 
            }
        }
    }
}