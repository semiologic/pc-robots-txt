<?php
/*
Plugin Name: PC Robots.txt
Plugin URI: http://petercoughlin.com/wp-plugins/
Description: Create and manage a virtual robots.txt file for your blog.
Version: 1.5 fork
Author: Peter Coughlin & Mike Koepke
Author URI: http://petercoughlin.com/
*/


function pc_robots_txt() {

	if ( strpos($_SERVER['REQUEST_URI'], '/robots.txt') !== false ) {
		$options = get_site_option( 'pc_robots_txt' );

		if ( is_array($options) ) {
			$options = $options['user_agents'];
			update_site_option( 'pc_robots_txt', $options );
		}

		// only block wp-admin see: https://core.trac.wordpress.org/ticket/28604
		if ( strpos ( $options, 'Allow: /wp-includes/' ) === false ) {
			$options = str_replace( "Allow: /wp-content/uploads",
				"Allow: /wp-includes/\n"
				. "Allow: /wp-admin/load-scripts.php?*\n"
				. "Allow: /wp-content/uploads/\n"
				. "Allow: /wp-content/cache/assets/\n"
				. "Allow: /wp-content/themes/*/*.css$\n"
				. "Allow: /wp-content/plugins/*/*.js$\n"
				. "Allow: /*.png$\n"
				. "Allow: /*.jpg$\n"
				. "Allow: /*.gif$\n"
				, $options );
			update_site_option( 'pc_robots_txt', $options );
		}

		// add semiologic folders in wp-content
		if ( strpos ( $options, 'Allow: /wp-content/semiologic/' ) === false ) {
			$options = str_replace( "Allow: /wp-content/plugins/*/*.js$",
				"Allow: /wp-content/plugins/*/*.js$\n"
				. "Allow: /wp-content/authors/\n"
				. "Allow: /wp-content/semiologic/\n"
				, $options );
			update_site_option( 'pc_robots_txt', $options );
		}
		if ( !$options)
			$options = pc_robots_txt_set_defaults();
		
		$pc_robots_txt = stripslashes($options);
		
		header( 'Content-Type: text/plain; charset=utf-8' );
		echo trim($pc_robots_txt) . "\n";
		
		do_action( 'do_robotstxt' );
	}# end if ( strpos($_SERVER['REQUEST_URI'], '/robots.txt') !== false ) {

}#pc_robots_txt()


function pc_robots_txt_set_defaults() {
	$options = "User-agent: *\n"
		. "Disallow: /wp-*\n"
		. "Allow: /wp-includes/\n"
		. "Allow: /wp-admin/load-scripts.php?*\n"
		. "Allow: /wp-content/uploads/\n"
		. "Allow: /wp-content/cache/assets/\n"
		. "Allow: /wp-content/themes/*/*.css$\n"
		. "Allow: /wp-content/plugins/*/*.js$\n"
		. "Allow: /wp-content/authors/\n"
		. "Allow: /wp-content/semiologic/\n"
		. "Allow: /*.png$\n"
		. "Allow: /*.jpg$\n"
		. "Allow: /*.gif$\n";

	
	update_site_option('pc_robots_txt', $options);

	return $options;

}#pc_robots_txt_set_defaults() {


function pc_robots_txt_init() {

	if ( get_option('blog_public') == '1' ) {
		remove_action('do_robots', 'do_robots');
		add_action('do_robots', 'pc_robots_txt', -1000);
	}

	if ( is_admin() )
		include_once dirname(__FILE__) . '/pc-robots-txt-admin.php';

}#pc_robots_txt_init() {

add_action('init', 'pc_robots_txt_init');