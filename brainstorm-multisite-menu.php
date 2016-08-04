<?php

/**
 * Plugin Name: Brainstorm - Multisite Menu
 * Description: Get navigation menu of any subsite and use on any other sibsite using shortcode. E.g. [BrainstormMMS blog_id="2" menu="Primary Menu"] Or Use [BrainstormMMS info="true"] to get all shortcode list.
 * Version: 1.0.0
 * Author: Brainstorm Force
 * Author URI: http://www.brainstormforce.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 *    Brainstorm Multisite Menu
 */
if ( ! class_exists( 'BrainstormMultisiteMenuShortcode' ) ) {

	class BrainstormMultisiteMenuShortcode {
		private static $instance;

		public static $_dir;
		public static $_url;

		public static function instance() {

			if ( ! self::$instance ) {
				self::$instance = new self;

				self::$_dir = plugin_dir_path( __FILE__ );
				self::$_url = plugin_dir_url( __FILE__ );

				self::$instance->hooks();
			}

			return self::$instance;
		}

		private function hooks() {

			//	Register shortcode
			add_shortcode( 'BrainstormMMS', array($this, 'register_shortcode' )  );
		}

		function register_shortcode( $atts ) {

			$atts = extract( shortcode_atts( array(
				'blog_id'       => 1,
				'menu'          => 'primary',
				'menu_class'    => '',
				'info'          => false,
				'wrapper_class' => '',
			), $atts ) );

		    $output = '';
			if( $info ) {
				$output .= $this->avaialble_multisite_menus();
			}
			
			$args = array(
				'container_class' => $menu_class,
				'menu'            => $menu,
				'echo'            => false
			);

		    switch_to_blog( $blog_id );
		    $output .= '<div class="'.$wrapper_class.'">';
		    $output .= 		wp_nav_menu( $args );
		    $output .= '</div>';
		    restore_current_blog();

		    return $output;
		}

		/**
		 *	Return Available multisite menu list
		*/
		function avaialble_multisite_menus() {

			ob_start();
			
			// Output dropdown menu of available sites...
			$blogs = wp_get_sites();
			foreach( $blogs as $blog ) {
				
				switch_to_blog( $blog['blog_id'] ); ?>

				<div class="site-info">
					<div class="site-name">
				 		Site: <?php echo site_url(); ?>
					</div><!-- .site-name -->

					<div class="site-shortocodes">
						<?php $locations = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
						if( count($locations) > 0 ) {
							foreach ($locations as $location_slug => $location ) { ?>
								[BrainstormMMS blog_id="<?php echo $blog['blog_id']; ?>" menu="<?php echo $location->name; ?>"]<br/>
							<?php } ?>
						<?php } else { ?>
							No Menu Found
						<?php } ?>
					</div><!-- .site-shortocodes -->
				</div><!-- .site-info -->
				<?php
				restore_current_blog();
			} ?>
			
			<style type="text/css" media="screen">
				.site-info {
				    border: 1px solid #eee;
				    margin-bottom: 20px;
				    font-size: 12px;
				    line-height: 1.5em;
				    font-family: sans-serif;
				    color: #828282;
				}
				.site-name {
				    padding: 7px 10px;
				    background: #ececec;
				    color: #717171;
				}
				.site-shortocodes {
				    padding: 7px 10px;
				}
			</style>
			<?php

			return ob_get_clean();

		}
	}

	BrainstormMultisiteMenuShortcode::instance();
}
