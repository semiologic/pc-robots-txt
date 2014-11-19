<?php
#
# This is the admin page for the PC Robots.txt plugin
#

function pc_robots_txt_admin_menu() {
	
	if ( function_exists('add_options_page') ) {
		if ( function_exists('is_super_admin') && !is_super_admin() )
			return;
		#add_options_page(page_title, menu_title, access_level/capability, file, [function]);
		add_options_page('Robots.txt', 'Robots.txt', 'manage_options', str_replace("\\", "/", __FILE__), 'pc_robots_txt_options');
	}

}#pc_robots_txt_admin_menu() {


function pc_robots_txt_options() {
	if ( function_exists('is_super_admin') && !is_super_admin() )
		return;

	if ( isset($_POST['update']) ){

		check_admin_referer('pc_robots_txt_options');

		$options = esc_attr( $_POST['user_agents'] );

		update_site_option('pc_robots_txt', $options);

		echo "<div id=\"message\" class=\"updated fade\"><p>Settings saved.</p></div>";
	}
	elseif ( isset($_POST['reset']) ) {

		check_admin_referer('pc_robots_txt_options');

		$options = pc_robots_txt_set_defaults();

		echo "<div id=\"message\" class=\"updated fade\"><p>Settings reset to defaults.</p></div>";
	}
	else {
		$options = get_site_option('pc_robots_txt');
		if ( $options ) {
			// only block wp-admin see: https://core.trac.wordpress.org/ticket/28604
			if ( strpos ( $options, 'Allow: /wp-includes/' ) === false ) {
				$options = str_replace( "Allow: /wp-content/uploads",
					"Allow: /wp-includes/\n"
					. "Allow: /wp-admin/load-scripts.php?*\n"
					. "Allow: /wp-content/uploads/\n"
					. "Allow: /wp-content/cache/assets/\n"
					. "Allow: /wp-content/themes/*/*.css\n"
					. "Allow: /wp-content/themes/*/*.js\n"
					. "Allow: /wp-content/plugins/*/*.css\n"
					. "Allow: /wp-content/plugins/*/*.js\n"
					. "Allow: /wp-content/authors/\n"
					. "Allow: /wp-content/semiologic/\n"
					. "Allow: /*.png$\n"
					. "Allow: /*.jpg$\n"
					. "Allow: /*.gif$\n"
					, $options );
				update_site_option( 'pc_robots_txt', $options );
			}

			// add semiologic folders in wp-content
			if ( strpos ( $options, 'Allow: /wp-content/semiologic/' ) === false ) {
				$options = str_replace( "Allow: /*.png$",
					"Allow: /wp-content/authors/\n"
					. "Allow: /wp-content/semiologic/"
					. "Allow: /*.png$\n"
					, $options );

				update_site_option( 'pc_robots_txt', $options );
			}

			// update the rules to handle versioned WP files
			if ( strpos ( $options, "Allow: /wp-content/themes/*/*.css$" ) !== false ) {
				$options = str_replace( "Allow: /wp-content/themes/*/*.css$",
					"Allow: /wp-content/themes/*/*.css\n"
					. "Allow: /wp-content/themes/*/*.js"
					, $options );

				$options = str_replace( "Allow: /wp-content/plugins/*/*.js$",
					"Allow: /wp-content/plugins/*/*.css\n"
					. "Allow: /wp-content/plugins/*/*.js"
					, $options );

				update_site_option( 'pc_robots_txt', $options );
			}
		}
		else {
			$options = pc_robots_txt_set_defaults();
		}
	}

	echo '<div class="wrap">'
		. '<h2>PC Robots.txt Options</h2>'
		. '<form method="post">';
	if ( function_exists('wp_nonce_field') ) wp_nonce_field('pc_robots_txt_options');
	echo '<h3>User Agents &amp; rules for this blog</h3>'
		. '<p>As a general rule, it\'s best to put the most specific rules at the top of the list and work down towards more generic rules. Put conditions that apply to all robots at the bottom of the list.</p><p>You can <a href="'; echo get_bloginfo('url') . '/robots.txt' . '" target="_blank">preview your robots.txt file</a> or visit <a href="http://www.robotstxt.org/" target="_blank" title="robotstxt.org">robotstxt.org</a> to find out more about the robots.txt standard.</p>'
		. '<p><table class="form-table">'
		. '<tr valign="top">'
			. '<td><textarea name="user_agents" id="user_agents" style="width:99%;height:200px;">' . esc_textarea( $options ) . '</textarea></td>'
		. '</tr>'
		. '</table></p>'
		. '<p class="submit"><input type="submit" name="update" value="Save Changes &raquo;" /> &nbsp;  &nbsp; <input type="submit" name="reset" value="Restore Defaults &raquo;" /></p>'
		. '</form>'
		. '</div>';

}#pc_robots_txt_options() {

add_action('admin_menu', 'pc_robots_txt_admin_menu');
