<?php
/**
 * Astra Child Starter Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package astra-child-starter-theme
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_STARTER_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function astra_child_starter_enqueue_assets() {
	$css_asset = include get_stylesheet_directory() . '/build/css/main.asset.php';
	$js_asset  = include get_stylesheet_directory() . '/build/js/main.asset.php';

	wp_enqueue_style( 'astra-child-starter-theme-css', get_stylesheet_directory_uri() . '/style.css', array( 'astra-theme-css' ), CHILD_THEME_ASTRA_STARTER_VERSION, 'all' );

	wp_enqueue_style(
		'astra-child-starter-style',
		get_stylesheet_directory_uri() . '/build/css/main.css',
		array(),
		$css_asset['version']
	);

	wp_enqueue_script(
		'astra-child-starter-script',
		get_stylesheet_directory_uri() . '/build/js/main.js',
		$js_asset['dependencies'],
		$js_asset['version'],
		true
	);
}
add_action( 'wp_enqueue_scripts', 'astra_child_starter_enqueue_assets' );