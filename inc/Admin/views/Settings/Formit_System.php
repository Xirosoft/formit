<?php 
namespace Xirosoft\Formit\Admin\views\Settings;

if ( ! class_exists( 'Formit_System' ) ) {
    class Formit_System{
        function __construct(){
            $this->formit_syestem_tab();
        }    
        function formit_syestem_tab(){
            ?>
            <div class="tab-title">
                <h1><?php esc_html_e('System Information', 'formit'); ?></h1>
                <legend> <?php esc_html_e('Your System Informations', 'formit'); ?> </legend>
            </div>
            <table class="form-table system-info-table">
                <?php foreach($this->formit_get_system_info() as $key => $info): ?>
                    <tr>
                        <th scope="row">
                            <?php 
                                $outputString = str_replace('_', ' ', $key);
                                printf(
                                        esc_html__( '%s', 'formit' ),
                                        esc_html(ucfirst($outputString))
                                    );
                            
                            ?>
                        </th>
                        <td>
                            <?php 
                                 $allowed_tags = array(
                                    'div' => array(
                                        'class' => true,
                                        'id' => true,
                                    ),
                                    'span' => array(),
                                
                                );
                                if(is_array($info)) {
                                    $sub_items='';
                                    foreach($info as $sub_item) {
                                        if($key === 'active_plugins') {
                                            [$sub_item] = explode('/', $sub_item, 2);
                                        }
                                        $pluginName = str_replace('-', ' ', $sub_item);

                                        $sub_items .='<span>'.ucfirst($pluginName).'</span>';
                                    }
                                    printf(
                                        esc_html__( '%s', 'formit' ),
                                        wp_kses($sub_items, $allowed_tags)
                                    );
                                } else {
                                    printf(
                                        esc_html__( '%s', 'formit' ),
                                        esc_html($info)
                                    );
                                }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php
        }
    
        /**
        * System info Return function
        *
        * @return array
        */          
        function formit_get_system_info() {
            $system_info = array();

            // Server information
            $system_info['server_software'] = sanitize_text_field($_SERVER['SERVER_SOFTWARE']);
            $system_info['php_version'] = phpversion();

            // WordPress information
            $system_info['wordpress_version'] = get_bloginfo('version');
            $system_info['theme'] = wp_get_theme()->get('Name');
            $system_info['active_plugins'] = get_option('active_plugins');

            // Server environment variables
            $system_info['mysql_version'] = sanitize_text_field($GLOBALS['wpdb']->db_version());
            $system_info['php_memory_limit'] = ini_get('memory_limit');
            $system_info['php_max_execution_time'] = ini_get('max_execution_time');

            return $system_info;
        }

    }
}