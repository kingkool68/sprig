<?php
/**
 * The main Sprig class
 *
 * @package Sprig
 */

/**
 * Sprig for minimal Twig templating in WordPress
 */
class Sprig {

	/**
	 * An instance of Twig
	 *
	 * @var [type]
	 */
	public static $twig;

	/**
	 * Get an instance of this class
	 */
	public static function get_instance() {
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
			$instance->setup_actions();
			$instance->setup_filters();
		}
		return $instance;
	}

	/**
	 * Empty constructor since we don't allow it
	 */
	public function __construct() {}

	/**
	 * Hook into WordPress via actions
	 */
	public function setup_actions() {
		add_action( 'init', array( $this, 'action_init' ) );
	}

	/**
	 * Setup Twig and allow for other customizations
	 */
	public function action_init() {
		$this->setup_twig();

		// Add custom filters to Twig
		// See https://twig.symfony.com/doc/1.x/advanced.html#filters
		$twig_filters = apply_filters( 'sprig/twig/filters', array() );
		foreach ( $twig_filters as $name => $filter_callback ) {
			if ( is_callable( $filter_callback ) ) {
				static::$twig->addFilter( new \Twig\TwigFilter( $name, $filter_callback ) );
			}
		}

		// Add custom functions to Twig
		// See https://twig.symfony.com/doc/1.x/advanced.html#functions
		$twig_functions = apply_filters( 'sprig/twig/functions', array() );
		foreach ( $twig_functions as $name => $function_callback ) {
			if ( is_callable( $function_callback ) ) {
				static::$twig->addFunction( new \Twig\TwigFunction( $name, $function_callback ) );
			}
		}

		// Modify Twig itself
		// See https://twig.symfony.com/doc/1.x/advanced.html#extending-twig
		static::$twig = apply_filters( 'sprig/twig', static::$twig );
	}

	/**
	 * Setup Twig and define locations to look for templates
	 */
	public function setup_twig() {
		$open_basedir = ini_get( 'open_basedir' );
		$paths        = array_merge( $this->get_template_locations(), array( $open_basedir ? ABSPATH : '/' ) );
		$root_path    = '/';
		if ( $open_basedir ) {
			$root_path = null;
		}
		$twig_loader = new \Twig\Loader\FilesystemLoader( $paths, $root_path );
		$twig_loader = apply_filters( 'sprig/twig_loader', $twig_loader );
		$twig        = new \Twig\Environment(
			$twig_loader,
			array(
				'debug'      => WP_DEBUG,
				'autoescape' => false,
			)
		);
		if ( WP_DEBUG ) {
			$twig->addExtension( new \Twig\Extension\DebugExtension() );
		}
		static::$twig = $twig;
	}

	/**
	 * Hook into WordPress via filters
	 */
	public function setup_filters() {
		add_filter( 'sprig/twig/filters', array( $this, 'filter_sprig_twig_default_filters' ) );
		add_filter( 'sprig/twig/functions', array( $this, 'filter_sprig_twig_default_functions' ) );
	}

	/**
	 * Setup default filters for use in .twig files
	 * These are mostly escaping functions
	 *
	 * @param  array $filters List of Twig filters.
	 * @return array           Modified list of Twig filters to make avaialble
	 */
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
		$filters['wptexturize']         = 'wptexturize';
		$filters['absint']              = 'absint';
		return $filters;
	}

	/**
	 * Setup default functions for use in .twig files
	 *
	 * @param  array $functions List of Twig functions.
	 * @return array           Modified list of Twig functions to make avaialble
	 */
	public function filter_sprig_twig_default_functions( $functions = array() ) {
		$functions['checked']    = 'checked';
		$functions['selected']   = 'selected';
		$functions['disabled']   = 'disabled';
		$functions['wp_head']    = 'wp_head';
		$functions['wp_footer']  = 'wp_footer';
		$functions['body_class'] = 'body_class';
		$functions['get_header'] = 'get_header';
		$functions['get_footer'] = 'get_footer';
		$functions['wp_title']   = 'wp_title';
		return $functions;
	}

	/**
	 * Get paths of directories Twig should look for template files
	 *
	 * @return array List of locations for Twig to look for templates
	 */
	public function get_template_locations() {
		$theme_locations = array();
		$theme_dirs      = apply_filters( 'sprig/theme_dirs', array( 'views', 'twig' ) );
		$roots           = array( get_stylesheet_directory(), get_template_directory() );
		$roots           = apply_filters( 'sprig/roots', $roots );
		$roots           = array_map( 'realpath', $roots );
		$roots           = array_unique( $roots );
		foreach ( $roots as $root ) {
			if ( ! is_dir( $root ) ) {
				continue;
			}
			$theme_locations[] = $root;
			$root              = trailingslashit( $root );
			foreach ( $theme_dirs as $dirname ) {
				$theme_location = realpath( $root . $dirname );
				if ( is_dir( $theme_location ) ) {
					$theme_locations[] = $theme_location;
				}
			}
		}
		return $theme_locations;
	}

	/**
	 * Given a list of file names find the first template
	 * that exists and can be used for rendering
	 *
	 * @param  array $filenames List of template file names.
	 * @return string|false      Name of first template found or false if no templates are found
	 */
	public static function choose_template( $filenames = array() ) {
		$loader = static::$twig->getLoader();
		foreach ( $filenames as $filename ) {
			if ( $loader->exists( $filename ) ) {
				return $filename;
			}
		}
		return false;
	}

	/**
	 * Get the full path of the chosen template file
	 *
	 * @param  array $filenames List of template file names.
	 * @return string            Path of the chosen template file
	 */
	public static function get_template_path( $filenames = array() ) {
		$template_file_name = static::choose_template( $filenames );
		if ( empty( $template_file_name ) ) {
			return '';
		}
		$loader = static::$twig->getLoader();
		return $loader->getSourceContext( $template_file_name )->getPath();
	}

	/**
	 * Render a template using the provided data
	 *
	 * @param  array $filenames List of template file names.
	 * @param  array $data      Data to use when rendering the template.
	 * @return string           Rendered template
	 */
	public static function render( $filenames = array(), $data = array() ) {
		if ( ! is_array( $filenames ) ) {
			$filenames = array( $filenames );
		}
		$template_file_name = static::choose_template( $filenames );
		return static::$twig->render( $template_file_name, $data );
	}

	/**
	 * Helper method to echo the rendered output
	 *
	 * @param  array $filenames List of template file names.
	 * @param  array $data      Data to use when rendering the template.
	 */
	public static function out( $filenames = array(), $data = array() ) {
		echo static::render( $filenames, $data );
	}

	/**
	 * Trigger an action and return the captured output buffer
	 *
	 * @param  string $action_name The name of the action to be executed.
	 * @param  mixed  $arg          Additional arguments which are passed on to the functions hooked to the action.
	 * @return string              Output of the triggered action
	 */
	public static function do_action( $action_name = '', $arg = '' ) {
		if ( empty( $action_name ) ) {
			return;
		}
		ob_start();
		call_user_func_array( 'do_action', func_get_args() );
		return ob_get_clean();
	}
}

// Kick things off.
Sprig::get_instance();
