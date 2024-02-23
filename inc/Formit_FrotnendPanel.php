<?php
namespace Xirosoft\Formit;

/**
 * This class using for manage all Admin raleted class 
 * @FrotnendPanel
 */
if ( ! class_exists( 'Formit_FrotnendPanel' ) ) {
    class Formit_FrotnendPanel {
        function __construct(){

            /**
            * Handles frontend asset enqueueing.
            * Frontend All endquee class
            */        
            new Frontend\Formit_FrontendEnqueue();  

            /**
            * Handles DOM manipulation for views.
            * Dom Mange class
            */
            new Frontend\views\Formit_DomHandle();
        }
    }
}