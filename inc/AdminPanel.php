<?php

namespace Xirosoft\Formit;

/**
 * AdminPanel class used for managing 
 * all Admin-related functionality.
 */
class AdminPanel {
  function __construct(){
    /**
     * Instantiate the 'Notice' 
     * class from the 'Admin' namespace.
     */
    new Admin\Notice();

    /**
     * Instantiate the 'AdminEnqueue' class 
     * from the 'Admin' namespace.
     */
    new Admin\AdminEnqueue();

    /**
     * Manages custom post types.
     * Custom Post define class
     */
    new Admin\CustomPost(); 

     /**
     * Manages All Route and URL.
     * Route define class
     */
    new Admin\Route(); 

    /**
     * Adds custom meta boxes to admin screens.
     * Meta box Control classs
     */
    new Admin\AddMetaBox();  

    /**
     * Handles form-related views.
     * Vew Dom Control Class
     */
    new Admin\views\Form\Form();
    
    /**
     * Handles form builder operations.
     * Form Builder Class
     */
    new Admin\FromBuilderHandle();  
    
    /**
     * Manages hooks and actions.
     * All Hook Manage Class
     */
    new Admin\Hook();
    
    /**
     * Handles settings-related views.
     * Dom Settings view class
     */
    new Admin\views\Settings\Settings();

    /**
     * Manages form submissions.
     * Mange form Submission class
     */
    new Admin\FormSubmission();   
    
    /**
     * Handles Docs views.
     * Docs Page class
     */
    new Admin\views\Docs\Docs();

    /**
     * Handles Gutenbarg Widget.
     * Gutenbarg Widget class 
     */
    new Widgets\Block\GutenbergWidget(); 

     /**
     * Handles Dasboard Status Widget.
     * stats Widget class 
     */
    new Widgets\DashboardStatsWidget(); 

     /**
     * Handles Dasboard News Widget.
     * news Widget class 
     */
    new Widgets\DashboardBlogWidget(); 
  }


}
