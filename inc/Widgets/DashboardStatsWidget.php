<?php 

namespace Xirosoft\Formit\Widgets;
use Xirosoft\Formit\Admin\Route;
use Xirosoft\Formit\Query;

class DashboardStatsWidget{

    function __construct(){
        add_action( 'wp_dashboard_setup', [$this, 'formit_dashboard_add_widgets'] );
        add_action( 'admin_enqueue_scripts', [$this, 'formit_scripts' ]);
    }



/*
Dashboard Widget
*/
function formit_dashboard_add_widgets() {
	wp_add_dashboard_widget( 'formit_dashboard_widget_news', __( 'Formit Stats', 'formit' ), [$this, 'formit_dashboard_widget_stats_handler'], 'formit_dashboard_widget_news_config_handler' );
}

function formit_dashboard_widget_stats_handler() {
    $query = new Query;
    $route = new Route;
	?>
    <div class="dashboard_table">
        <div class="number__stats">
            <a href="<?php echo esc_url(home_url('/wp-admin/edit.php?post_type=formit')); ?>" class="number__stats__single">
                <img class="icon" src="<?php echo esc_url( FORMIT_ASSETS_URL. "img/icons/formit-forms.webp") ?>" alt="formit-stats"/> 
                <span class="value"><?php echo esc_html__($query->total_forms(), 'formit'); ?></span>
                <span class="label"><?php echo esc_html__('Total Forms', 'formit') ?></span>
            </a>
            <a href="<?php echo esc_url($route->page_url('submission')); ?>" class="number__stats__single">
                <img class="icon" src="<?php echo esc_url( FORMIT_ASSETS_URL. "img/icons/formit-chart.webp") ?>" alt="formit-stats"/> 
                <span class="value"><?php echo esc_html__($query->total_submitions(), 'formit'); ?></span>    
                <span class="label"><?php echo esc_html__('Total Entries', 'formit') ?></span>
            </a>
            <a href="<?php echo esc_url($route->page_url('submission')); ?>" class="number__stats__single">
                <img class="icon" src="<?php echo esc_url( FORMIT_ASSETS_URL. "img/icons/formit-calender.webp") ?>" alt="formit-stats"/> 
                <span class="value"><?php echo esc_html__($query->todays_submission_count(), 'formit'); ?></span>    
                <span class="label"><?php echo esc_html__('Today\'s Entries', 'formit') ?></span>
            </a>
        </div>

        <table class="last__five__stats">
            <h3 style="padding: 16px 16px 0px;font-weight: 600;"><?php echo esc_html__('Last 5 Submissions', 'formit') ?></h3>
            <tr class="Last__five__stats__row">
                <th><?php echo esc_html__('Form Name', 'formit') ?></th>
                <th><?php echo esc_html__('Email', 'formit') ?></th>
                <th><?php echo esc_html__('Country', 'formit') ?></th>
            </tr>

            <?php 
            $results = $query->last_submitions(5); 
            if (!empty($results)) {
                
                // Loop through the results and display them in the table
                foreach ($results as $row) {
                    $formbody = json_decode($row['mail_body'], true);
                    $user_location = json_decode($row['user_location'], true);
                    ?>
                    <tr class="Last__five__stats__row">
                        <td><?php echo esc_html__($row['form_title'], 'formit') ?></td>
                        <td>
                            <?php
                                $email = null; // Initialize the email variable
                                foreach ($formbody as $field) {
                                    if ((isset($field['type']) && ($field['type'] === 'text' || $field['type'] === 'email')) && isset($field['value']) && filter_var($field['value'], FILTER_VALIDATE_EMAIL)) {
                                        $email = $field['value'];
                                        break; // Stop searching once a valid email is found
                                    }
                                }

                                if ($email !== null) {
                                    echo esc_html__( $email, 'formit');
                                } else {
                                    ?>
                                    <span class='no_email'><?php echo esc_html__("Email not found!", 'formit'); ?></span>
                                    <?php 
                                }
                            ?>
                        </td>
                        <td><?php echo esc_html__($user_location['country'], 'formit') ?></td>
                    </tr>
                    <?php

                }
            } else { ?>
                <tr class='Last__five__stats__row'><td colspan="3" style="text-align:center"><?php echo esc_html__('No records found.', 'formit') ?></td></tr>
            <?php  } ?>            
    
        </table>
    </div>
    <div class="t-overview__footer">
        <ul>
            <li class="e-overview"><a href="<?php echo esc_url($route->create_url()); ?>" target="_blank"><span aria-hidden="true" class="dashicons dashicons-plus"></span><?php echo esc_html__('Create New Form', 'formit') ?>  <span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
            <li class="e-overview"><a href="<?php echo esc_url($route->page_url('docs')); ?>" target="_blank"><?php echo esc_html__('Docs', 'formit') ?> <span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
            <li class="e-overview"><a href="<?php echo esc_url($route->page_url('settings')); ?>" target="_blank"><?php echo esc_html__('Settings', 'formit') ?><span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
        </ul>
    </div>

	<?php
}



function formit_scripts( $hook ) {
	$screen = get_current_screen();
	if ( 'dashboard' === $screen->id ) {
		// wp_enqueue_script( 'formit_script', plugin_dir_url( __FILE__ ) . 'path/to/script.js', array( 'jquery' ), '1.0', true );
		wp_enqueue_style( 'formit_style',  FORMIT_ASSETS_URL . 'admin/css/formit_dashboard.css', array(), '1.0' );
	}
 
}

    
}
