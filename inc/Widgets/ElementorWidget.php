<?php
namespace Xirosoft\Formit\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Xirosoft\Formit\Query;

// Define a custom Elementor widget class
class ElementorWidget extends Widget_Base{
    
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
                'options' => $this->get_formit_formlist(), // Fetch options from the get_formit_formlist() method
            ]
        );

        $this->end_controls_section();
    }
        
    // Fetch a list of Formit contact forms from the database
    public function get_formit_formlist(){
        global $wpdb;
        $query = new Query;
        $table_name = $wpdb->prefix . 'formit_forms'; 
        $all_form_id = $query->get_all_form_id($table_name);
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
    public function get_all_wp_pages(){
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
}
