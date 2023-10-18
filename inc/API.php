<?php 
namespace Xirosoft\Formit;

class API{
    
    function __construct(){
        add_action('rest_api_init', [$this, 'register_api']);
    }

    function register_api(){
        $formitform = new API\FormitForm();
        $formitform->register_routes();
        
    }
}
