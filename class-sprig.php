<?php

class Sprig {

	static $twig;

	/**
	 * Get an instance of this class
	 */
	static function get_instance() {
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
			$instance->setup_actions();
			$instance->setup_filters();
		}
		return $instance;
	}

	public function  __construct() {}

	public function setup_actions() {
		add_action( 'init', [ $this, 'action_init' ] );
	}

	public function action_init() {
		$this->setup_twig();

		// Add custom filters to Twig
		// See https://twig.symfony.com/doc/2.x/advanced.html#filters
		$twig_filters = apply_filters( 'sprig/twig/filters', array() );
		foreach ( $twig_filters as $name => $filter_callback ) {
			if ( is_callable( $filter_callback ) ) {
				self::$twig->addFilter( new \Twig_SimpleFilter( $name, $filter_callback ) );
			}
		}

		// Add custom functions to Twig
		// See https://twig.symfony.com/doc/2.x/advanced.html#functions
		$twig_functions = apply_filters( 'sprig/twig/functions', array() );
		foreach ( $twig_functions as $name => $function_callback ) {
			if ( is_callable( $function_callback ) ) {
				self::$twig->addFunction( new \Twig_SimpleFunction( $name, $function_callback ) );
			}
		}

		// Modify Twig itself
		// See https://twig.symfony.com/doc/2.x/advanced.html#extending-twig
		self::$twig = apply_filters( 'sprig/twig', self::$twig );
	}

	public function setup_twig() {
		$open_basedir = ini_get( 'open_basedir' );
		$paths = array_merge( $this->get_template_locations(), array( $open_basedir ? ABSPATH : '/' ) );
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

	public function setup_filters() {
		add_filter( 'sprig/twig/filters', [ $this, 'filter_sprig_twig_default_filters' ] );
		add_filter( 'sprig/twig/functions', [ $this, 'filter_sprig_twig_default_functions' ] );
	}

	public function filter_sprig_twig_default_filters( $filters = array() ) {
		$filters['esc_attr']            = 'esc_attr';
		$filters['esc_html']            = 'esc_html';
		$filters['esc_url']             = 'esc_url';
		$filters['esc_js']              = 'esc_js';
		$filters['esc_textarea']        = 'esc_textarea';
		$filters['tag_escape']          = 'tag_escape';
		$filters['sanitize_email']      = 'sanitize_email';
		$filters['sanitize_html_class'] = 'sanitize_html_class';
		$filters['antispambot']         = 'antispambot';
		return $filters;
	}

	public function filter_sprig_twig_default_functions( $functions = array() ) {
		$functions['checked']    = 'checked';
		$functions['selected']   = 'selected';
		$functins['disabled']    = 'disabled';
		$functions['wp_head']    = 'wp_head';
		$functions['wp_footer']  = 'wp_footer';
		$functions['get_header'] = 'get_header';
		$functions['get_footer'] = 'get_footer';
		return $functions;
	}

	public function get_template_locations() {
		$theme_locations = array();
		$theme_dirs = apply_filters( 'sprig/theme_dirs', array( 'views', 'twig' ) );
		$roots = array( get_stylesheet_directory(), get_template_directory() );
		$roots = apply_filters( 'sprig/roots', $roots );
		$roots = array_map( 'realpath', $roots );
		$roots = array_unique( $roots );
		foreach ( $roots as $root ) {
			if ( ! is_dir( $root ) ) {
				continue;
			}
			$theme_locations[] = $root;
			$root = trailingslashit( $root );
			foreach ( $theme_dirs as $dirname ) {
				$theme_location = realpath( $root . $dirname );
				if ( is_dir( $theme_location ) ) {
					$theme_locations[] = $theme_location;
				}
			}
		}
		return $theme_locations;
	}

	public static function render( $filenames, $data = array() ) {
		if ( ! is_array( $filenames ) ) {
			$filenames = array( $filenames );
		}
		$output = self::$twig->render( $filenames[0], $data );
		return $output;
	}

	public static function echo( $filenames, $data = array() ) {
		echo self::render( $filenames, $data );
	}
}

// Kick things off
Sprig::get_instance();
