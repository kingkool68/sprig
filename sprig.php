<?php
/*
Plugin Name: Sprig
Description: Minimal Twig template engine setup for WordPress.
Plugin URI: https://github.com/kingkool68/sprig
GitHub Plugin URI: kingkool68/sprig
Author: Russell Heimlich
Version: 2.0.0
Author URI: https://russellheimlich.com
*/

// We look for Composer files first in the plugins dir.
// Then in the wp-content dir (site install).
// And finally in the current themes directories.
if ( file_exists( $composer_autoload = __DIR__ . '/vendor/autoload.php' ) /* check in self */
	|| file_exists( $composer_autoload = WP_CONTENT_DIR . '/vendor/autoload.php' ) /* check in wp-content */
	|| file_exists( $composer_autoload = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' ) /* check in plugin directory */
	|| file_exists( $composer_autoload = get_stylesheet_directory() . '/vendor/autoload.php' ) /* check in child theme */
	|| file_exists( $composer_autoload = get_template_directory() . '/vendor/autoload.php' ) /* check in parent theme */
) {
	require_once $composer_autoload;
	require_once 'class-sprig.php';
}
