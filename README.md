# Sprig
_Bare bones Twig templating support for WordPress_

Sprig brings the Twig templating engine to WordPress. Install the plugin and get started separating your HTML from PHP.

Other Twig plugins like [Timber](https://www.upstatement.com/timber/) try to bring lots of WordPress functionality into Twig. Sprig believes in separating PHP from HTML as much as possible. PHP is for gathering and massaging data and Sprig/Twig templates are for rendering HTML using the data passed to it. Keep things simple and only pass data to a template that the template needs to render.

## Installation

1. Clone this repo or download a zip
2. Add this to the plugin directory of your WordPress site in `/wp-content/plugins/`
3. Run `composer install` to download the [Twig](https://twig.symfony.com/) template engine dependency

## How to Use

Create a directroy in your theme called `views` or `twig` to hold your Twig template files. Render your templates using the following methods passing an array of data to be used by the template.

 - `Sprig::render()` will render a Twig template using an array of data and return it as a string
 - `Sprig::out()` will `echo` a rendered template
 - `Sprig::do_action()` will capture the output of a WordPress action and return a string

## Example

**example.php**

```php
<?php
$context = array(
  'title' => 'Sprig is awesome!',
  'url'   => 'https://github.com/kingkool68/sprig/',
);

// Render the template and return a string
$thing = Sprig::render( 'example.twig', $context );

// Echo out the rendered template
Sprig::out( 'example.twig', $context );
```

**example.twig**

```twig
<p>
	<a href="{{ url|esc_url }}">{{ title }}</a>
</p>
```

**Output**

```html
<p>
	<a href="https://github.com/kingkool68/sprig/">Sprig is awesome!</a>
</p>
```

## Demo Theme

I've put together a [simple demo theme](https://github.com/kingkool68/sprig-demo-theme) to show how Sprig can be used within a WordPress theme.

## Twig Filters

Twig filters allow a string to be manipulated in a Twig template. Most of the default filters in Sprig are WordPress' [escaping functions](https://developer.wordpress.org/themes/theme-security/data-sanitization-escaping/#escaping-securing-output) for securing output as late as possible.

 - [`esc_attr`](https://developer.wordpress.org/reference/functions/esc_attr/)
 - [`esc_html`](https://developer.wordpress.org/reference/functions/esc_html/)
 - [`esc_url`](https://developer.wordpress.org/reference/functions/esc_url/)
 - [`esc_js`](https://developer.wordpress.org/reference/functions/esc_js/)
 - [`esc_textarea`](https://developer.wordpress.org/reference/functions/esc_textarea/)
 - [`tag_escape`](https://developer.wordpress.org/reference/functions/tag_escape/)
 - [`sanitize_email`](https://developer.wordpress.org/reference/functions/sanitize_email/)
 - [`sanitize_html_class`](https://developer.wordpress.org/reference/functions/sanitize_html_class/)
 - [`antispambot`](https://developer.wordpress.org/reference/functions/antispambot/)
 - [`wptexturize`](https://developer.wordpress.org/reference/functions/wptexturize/)
 - [`absint`](https://developer.wordpress.org/reference/functions/absint/)

Additional Twig filters can be added via the WordPress filter `sprig/twig/filters` and adding a callable function with a key to the array. This filter should be called before the `init` action so Twig filters can be set up properly in time.

Example for adding the [`sanitize_title()`](https://developer.wordpress.org/reference/functions/sanitize_title/) function as a Twig filter:

```
function filter_sprig_twig_filters( $filters = array() ) {
	$filters['sanitize_title'] = 'sanitize_title';
	return $filters;
}
add_filter( 'sprig/twig/filters', 'filter_sprig_twig_filters' );
```

## Twig Functions

Twig functions let you call PHP functions from within Twig templates. Sprig enables a handful of WordPress functions used in the base Twig template and WordPress' [`checked`](https://developer.wordpress.org/reference/functions/checked/)/[`selected`](https://developer.wordpress.org/reference/functions/selected/)/[`disabled`](https://developer.wordpress.org/reference/functions/disabled/) form helpers.

 - [`checked()`](https://developer.wordpress.org/reference/functions/checked/)
 - [`selected()`](https://developer.wordpress.org/reference/functions/selected/)
 - [`disabled()`](https://developer.wordpress.org/reference/functions/disabled/)
 - [`wp_head()`](https://developer.wordpress.org/reference/functions/wp_head/)
 - [`wp_footer()`](https://developer.wordpress.org/reference/functions/wp_footer/)
 - [`body_class()`](https://developer.wordpress.org/reference/functions/body_class/)
 - [`get_header()`](https://developer.wordpress.org/reference/functions/get_header/)
 - [`get_footer()`](https://developer.wordpress.org/reference/functions/get_footer/)
 - [`wp_title()`](https://developer.wordpress.org/reference/functions/wp_title/)

Additional Twig functions can be added via the WordPress filter `sprig/twig/functions` and adding a callable function with a key to the array. This filter should be called before the `init` action so Twig functions can be set up properly in time.

Example for adding the [`wp_nonce_field()`](https://developer.wordpress.org/reference/functions/wp_nonce_field/) function as a Twig function:

```
function filter_sprig_twig_functions( $functions = array() ) {
	$functions['wp_nonce_field'] = 'wp_nonce_field';
	return $functions;
}
add_filter( 'sprig/twig/functions', 'filter_sprig_twig_functions' );
```

## Extending Sprig

Sprig offers various WordPress filters to customize its behavior.

 - `sprig/twig` for modifying Twig itself
 - `sprig/twig/filters` for adding or removing available Twig filters
 - `sprig/twig/functions` for adding or removing available Twig functions
 - `sprig/roots` for modifying which directories Twig should look for twig templates in
 - `sprig/theme_dirs` for modifying the name of directories to look for Twig templates in (Example: if you want to change the directory from `views` or `twig` to `templates`)
 - `sprig/twig_loader` for modifying the Twig loader environment (see <https://twig.symfony.com/doc/1.x/api.html>)

 ## PHP Compatibility

PHP compatibility is dependent on Twig's PHP prerequisites. Use the appropriate branch of Sprig to meet your minimum PHP requirements.

 - [Twig 3.x](https://twig.symfony.com/doc/3.x/intro.html) needs at least PHP 7.2.0 to run, use the Sprig `master` branch.
 - [Twig 2.x](https://twig.symfony.com/doc/2.x/intro.html) needs at least PHP 7.0.0 to run, use the Sprig `2.x` branch.
 - [Twig 1.x](https://twig.symfony.com/doc/1.x/intro.html) needs at least PHP 5.5.0 to run , use the Sprig `1.x` branch.
