<?php
/*
Plugin Name: Sprig
Description: Minimal Twig template engine setup.
Plugin URI: http://timber.upstatement.com
Author: Russell Heimlich
Version: 0.0.1
Author URI: https://russellheimlich.com
*/

// we look for Composer files first in the plugins dir.
// then in the wp-content dir (site install).
// and finally in the current themes directories.
if ( file_exists( $composer_autoload = __DIR__ . '/vendor/autoload.php' ) /* check in self */
	|| file_exists( $composer_autoload = WP_CONTENT_DIR . '/vendor/autoload.php' ) /* check in wp-content */
	|| file_exists( $composer_autoload = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' ) /* check in plugin directory */
	|| file_exists( $composer_autoload = get_stylesheet_directory() . '/vendor/autoload.php' ) /* check in child theme */
	|| file_exists( $composer_autoload = get_template_directory() . '/vendor/autoload.php' ) /* check in parent theme */
) {
	require_once $composer_autoload;
	require_once 'class-sprig.php';
}
