<?php
/**
 * Plugin Name: Sprig
 * Description: Minimal Twig template engine setup for WordPress.
 * Plugin URI: https://github.com/kingkool68/sprig
 * GitHub Plugin URI: kingkool68/sprig
 * Author: Russell Heimlich
 * Version: 4.0.0
 * Author URI: https://russellheimlich.com
 *
 * @package Sprig
 */

// We look for Composer files first in the plugin dir.
// Then in the wp-content dir (site install).
// And finally in the current themes directories.
$sprig_autoload_paths = array(
	__DIR__ . '/vendor/autoload.php',              // Check in self.
	WP_CONTENT_DIR . '/vendor/autoload.php',       // Check in wp-content.
	get_stylesheet_directory() . '/vendor/autoload.php', // Check in child theme.
	get_template_directory() . '/vendor/autoload.php',   // Check in parent theme.
);
foreach ( $sprig_autoload_paths as $sprig_autoload_path ) {
	if ( file_exists( $sprig_autoload_path ) ) {
		require_once $sprig_autoload_path;
		require_once __DIR__ . '/class-sprig.php';
		break;
	}
}
