<?php 
namespace Xirosoft\Formit\Admin\Popup;
use Xirosoft\Formit\Admin\Formit_Route;

if ( ! class_exists( 'Formit_HeaderProfileInfo' ) ) {
    class Formit_HeaderProfileInfo{
        public function __construct(){

        }
        public function formit_profile_info_popup(){
            $route = new Formit_Route;
            ?>
                <div class="info-popup mini" id="info-popup">
                    <a href="<?php echo esc_url(home_url('wp-admin/profile.php')) ?>" class="info-popup__header">
                        <div class="avatar">
                            <?php
                                $current_user   = wp_get_current_user();
                                $user_avatar    = get_avatar($current_user->user_email, 64);
                                echo $user_avatar;
                            ?>
                        </div>
                        <div class="user">
                            <h2 class="user__name">
                                <?php
                                    echo esc_html($current_user->display_name);
                                ?>
                            </h2>
                            <p class="user__email">
                                <?php
                                    echo esc_html($current_user->user_email);
                                ?>
                            </p>
                        </div>
                    </a>
                    <div class="info-popup__inner">
                        <a class="info-popup__link" href="<?php echo esc_url($route->formit_page_url('settings')); ?>">
                            <i class="fi-settings"></i>
                            <span><?php echo esc_html__( 'Settings', 'formit' ) ?></span>
                        </a>
                        <a class="info-popup__link" href="#">
                            <i class="dashicons dashicons-arrow-up-alt"></i>
                            <span><?php echo esc_html__( 'Upgrade', 'formit' ) ?></span>
                        </a>
                        <a class="info-popup__link" href="#">
                            <i class="fi-docs"></i>
                            <span><?php echo esc_html__( 'Change Log', 'formit' ) ?></span>
                        </a>
                        <a class="info-popup__link" href="#">
                            <i class="fi-link"></i>
                            <span><?php echo esc_html__( 'Other Plugins', 'formit' ) ?></span>
                        </a>

                        

                        <div class="grid-full-width">
                            <h2 class="title"><?php echo esc_html__('System Information', 'formit'); ?></h2>
                            <table class="form-table system-info-table info-popup__table">
                                <tbody>
                                    <tr>
                                        <th scope="row"><?php echo esc_html__('Server software', 'formit'); ?></th>
                                        <td><?php echo esc_html(sanitize_text_field($_SERVER['SERVER_SOFTWARE'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php echo esc_html__('PHP version', 'formit'); ?></th>
                                        <td><?php echo esc_html(phpversion()); ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php echo esc_html__('WordPress version', 'formit'); ?></th>
                                        <td><?php echo esc_html(get_bloginfo('version')); ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php echo esc_html__('Theme', 'formit'); ?></th>
                                        <td><?php echo esc_html(wp_get_theme()->Name); ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php echo esc_html__('MySQL version', 'formit'); ?></th>
                                        <td><?php
                                            global $wpdb;
                                            echo esc_html($wpdb->db_version()); ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php echo esc_html__('PHP memory limit', 'formit'); ?></th>
                                        <td><?php echo esc_html(ini_get('memory_limit')); ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php echo esc_html__('PHP max execution time', 'formit'); ?></th>
                                        <td><?php echo esc_html(ini_get('max_execution_time')); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    
                    <div class="info-popup__footer">
                        <p class="xirosoft-credit">
                            <a class="logout" href="<?php echo esc_url(wp_logout_url()); ?>">
                                <span class="dashicons dashicons-exit"></span>
                                <?php echo esc_html__('Logout', 'formit'); ?>
                            </a>

                            <?php esc_html_e('Powered by: ', 'formit') ?>
                            <a href="<?php echo esc_url('https://xirosoft.com'); ?>" target="_blank">
                                <img src="<?php echo esc_url( FORMIT_ASSETS_URL.'/img/xirosoft.webp') ?>" alt="<?php echo esc_attr__('xirosoft', 'formit') ?>" />
                            </a>
                        </p>
                    </div>
                </div>
            <?php
        }

    }
}
