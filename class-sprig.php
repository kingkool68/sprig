<?php

// namespace Sprig;
/**
 * [Sprig description]
 */
class Sprig {

	static $twig;

	/**
	 * Get an instance of this class
	 */
	static function get_instance() {
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
			$instance->setup_twig();
		}
		return $instance;
	}

	public function  __construct() {
		// $this->setup_twig();
	}

	public function setup_twig() {
		$open_basedir = ini_get( 'open_basedir' );
		$paths = array_merge( $this->get_template_locations(), array( $open_basedir ? ABSPATH : '/' ) );
		$paths = apply_filters( 'sprig/paths', $paths );
		$rootPath = '/';
		if ( $open_basedir ) {
			$rootPath = null;
		}
		$twig_loader = new \Twig_Loader_Filesystem( $paths, $rootPath );
		$twig_loader = apply_filters( 'sprig/twig_loader', $twig_loader );
		$twig = new \Twig_Environment( $twig_loader, array(
			'debug'      => WP_DEBUG,
			'autoescape' => false,
		) );
		if ( WP_DEBUG ) {
			$twig->addExtension( new \Twig_Extension_Debug() );
		}
		self::$twig = $twig;
	}

	public function get_template_locations() {
		$theme_locs = array();
		$roots = array( get_stylesheet_directory(), get_template_directory() );
		$roots = array_map( 'realpath', $roots );
		$roots = array_unique( $roots );
		foreach ( $roots as $root ) {
			if ( ! is_dir( $root ) ) {
				continue;
			}
			$theme_locs[] = $root;
			$root = trailingslashit( $root );
			foreach ( $theme_dirs as $dirname ) {
				$tloc = realpath( $root . $dirname );
				if ( is_dir( $tloc ) ) {
					$theme_locs[] = $tloc;
				}
			}
		}
		return $theme_locs;
	}

	public static function render( $filenames, $data = array() ) {
		if ( ! is_array( $filenames ) ) {
			$filenames = array( $filenames );
		}
		// @TODO Figure out the right template to use based on parent theme, child theme, filtering etc.
		$output = self::$twig->render( $filenames[0], $data );
		return $output;
	}

	public static function echo( $filenames, $data = array() ) {
		echo self::render( $filenames, $data );
	}
}

// Kick things off
Sprig::get_instance();
