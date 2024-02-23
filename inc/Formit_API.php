<?php 
namespace Xirosoft\Formit;

 if ( ! class_exists( 'Formit_API' ) ) {
    class Formit_API{
        function __construct(){
            add_action('rest_api_init', [$this, 'formit_register_api']);
        }

        function formit_register_api(){
            $formitform = new API\Formit_FormitForm();
            $formitform->formit_register_routes();
            
        }
    }
}