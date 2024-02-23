<?php

namespace Xirosoft\Formit;

/**
 * AdminPanel class used for managing 
 * all Admin-related functionality.
 */

if ( ! class_exists( 'Formit_AdminPanel' ) ) {
    class Formit_AdminPanel {
        function __construct(){
        /**
        * Instantiate the 'Notice' 
        * class from the 'Admin' namespace.
        */
        new Admin\Formit_Notice();

        /**
        * Instantiate the 'AdminEnqueue' class 
        * from the 'Admin' namespace.
        */
        new Admin\Formit_AdminEnqueue();

        /**
        * Manages custom post types.
        * Custom Post define class
        */
        new Admin\Formit_CustomPost(); 

        /**
        * Manages All Route and URL.
        * Route define class
        */
        new Admin\Formit_Route(); 

        /**
        * Adds custom meta boxes to admin screens.
        * Meta box Control classs
        */
        new Admin\Formit_AddMetaBox();  

        /**
        * Handles form-related views.
        * Vew Dom Control Class
        */
        new Admin\views\Form\Formit_Form();
        
        /**
        * Handles form builder operations.
        * Form Builder Class
        */
        new Admin\Formit_FromBuilderHandle();  
        
        /**
        * Manages hooks and actions.
        * All Hook Manage Class
        */
        new Admin\Formit_Hook();
        
        /**
        * Handles settings-related views.
        * Dom Settings view class
        */
        new Admin\views\Settings\Formit_Settings();

        /**
        * Manages form submissions.
        * Mange form Submission class
        */
        new Admin\Formit_FormSubmission();   
        
        /**
        * Handles Docs views.
        * Docs Page class
        */
        new Admin\views\Docs\Formit_Docs();

        /**
        * Handles Gutenbarg Widget.
        * Gutenbarg Widget class 
        */
        new Widgets\Block\Formit_GutenbergWidget(); 

        /**
        * Handles Dasboard Status Widget.
        * stats Widget class 
        */
        new Widgets\Formit_DashboardStatsWidget(); 

        /**
        * Handles Dasboard News Widget.
        * news Widget class 
        */
        new Widgets\Formit_DashboardBlogWidget(); 
        }
    }
}