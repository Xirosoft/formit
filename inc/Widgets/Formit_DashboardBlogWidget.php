<?php 

namespace Xirosoft\Formit\Widgets;
use Xirosoft\Formit\Query;

if ( ! class_exists( 'Formit_DashboardBlogWidget' ) ) {
	class Formit_DashboardBlogWidget{

		function __construct(){
			add_action( 'wp_dashboard_setup', [$this, 'formit_news_dashboard_add_widgets'] );
			add_action( 'admin_enqueue_scripts', [$this, 'formit_news_scripts' ]);
		}



		/*
		Dashboard Widget
		*/
		function formit_news_dashboard_add_widgets() {
			wp_add_dashboard_widget( 'formit_news_dashboard_widget_news', __( 'Formit News', 'formit' ), [$this, 'formit_news_dashboard_widget_news_handler'], 'formit_news_dashboard_widget_news_config_handler' );
		}

		function formit_news_dashboard_widget_news_handler() {
			$options = wp_parse_args( get_option( 'formit_news_dashboard_widget_news' ), $this->formit_news_dashboard_widget_news_config_defaults() );
			$feeds = array(
				array(
					'url'          => 'https://www.xirosoft.com/feed/',
					'items'        => $options['items'],
					'show_summary' => 0,
					'show_author'  => 0,
					'show_date'    => 1,
				),
			);

			wp_dashboard_primary_output( 'formit_news_dashboard_widget_news', $feeds );

			?>
			<br><hr>
			<div class="t-overview__footer">
				<ul>
					<li class="e-overview"><a href="https://themeies.com/blog/" target="_blank"><?php echo esc_html__(' See More Blog ', 'formit'); ?><span class="screen-reader-text">(opens in a new window)</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
					<li class="e-overview"><a href="https://themeies.com/contact-us/" target="_blank"><?php echo esc_html__('Help', 'formit'); ?>  <span class="screen-reader-text">(opens in a new window)</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
					<li class="e-overview"><a href="https://wpborax.com" target="_blank"><?php echo esc_html__('Premium Borax ', 'formit'); ?><span class="screen-reader-text">(opens in a new window)</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>
				</ul>
			</div>

			<?php
		}

		function formit_news_dashboard_widget_news_config_defaults() {
			return array(
				'items' => 5,
			);
		}

		function formit_news_dashboard_widget_news_config_handler() {
			$options = wp_parse_args( get_option( 'formit_news_dashboard_widget_news' ), $this->formit_news_dashboard_widget_news_config_defaults() );

			?>
			<form method="post" action="">
				<?php wp_nonce_field( 'formit_news_dashboard_widget_nonce', 'formit_news_dashboard_widget_nonce_field' ); ?>
				<p>
					<label>
						<?php echo esc_html__( 'Number of RSS articles:', 'formit' ); ?>
						<input type="text" name="rss_items" value="<?php echo esc_attr( $options['items'] ); ?>" />
					</label>
				</p>
				<p>
					<input type="submit" name="submit" value="<?php echo esc_attr__( 'Save', 'formit' ); ?>" />
				</p>
			</form>
			<?php

			if ( isset( $_POST['submit'] ) && isset( $_POST['formit_news_dashboard_widget_nonce_field'] ) && wp_verify_nonce(  sanitize_text_field(wp_unslash ($_POST['formit_news_dashboard_widget_nonce_field'])), 'formit_news_dashboard_widget_nonce' ) ) {
				if ( isset( $_POST['rss_items'] ) && absint( $_POST['rss_items'] ) > 0 ) {
					$options['items'] = absint( $_POST['rss_items'] );
				}

				update_option( 'formit_news_dashboard_widget_news', $options );
			}
		}


		function formit_news_scripts( $hook ) {
			$screen = get_current_screen();
			if ( 'dashboard' === $screen->id ) {
				// wp_enqueue_script( 'formit_news_script', plugin_dir_url( __FILE__ ) . 'path/to/script.js', array( 'jquery' ), '1.0', true );
				// wp_enqueue_style( 'formit_news_style', plugin_dir_url( __FILE__ ) . '../css/ele-custom.css', array(), '1.0' );
			}
		
		}
	}
}
