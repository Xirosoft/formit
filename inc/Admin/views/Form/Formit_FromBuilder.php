<?php
namespace Xirosoft\Formit\Admin\views\Form;

if ( ! class_exists( 'Formit_FromBuilder' ) ) {
    class Formit_FromBuilder
    {
        function __construct(){
            echo '<div class="build-wrap"></div>';  
        }
    }
}
