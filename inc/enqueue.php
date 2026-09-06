<?php
/**
 * Enqueue the theme stylesheet and script.
 *
 * @package astra_child_starter
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Read an asset file produced by `npm run build`.
 *
 * @param string $handle Path under build/, without the `.asset.php` suffix.
 * @return array{dependencies: array<non-empty-string>, version: string}|null Null when build/ is missing.
 */
function astra_child_starter_asset( $handle ) {
	$path = get_stylesheet_directory() . '/build/' . $handle . '.asset.php';

	if ( ! file_exists( $path ) ) {
		return null;
	}

	$asset = include $path;

	if ( ! is_array( $asset ) || ! isset( $asset['dependencies'], $asset['version'] ) ) {
		return null;
	}

	if ( ! is_string( $asset['version'] ) ) {
		return null;
	}

	$dependencies = array();

	foreach ( (array) $asset['dependencies'] as $dependency ) {
		if ( is_string( $dependency ) && '' !== $dependency ) {
			$dependencies[] = $dependency;
		}
	}

	return array(
		'dependencies' => $dependencies,
		'version'      => $asset['version'],
	);
}

/**
 * Enqueue the built stylesheet and script.
 *
 * @return void
 */
function astra_child_starter_enqueue_assets() {
	$css_asset = astra_child_starter_asset( 'css/main' );
	$js_asset  = astra_child_starter_asset( 'js/main' );

	if ( null === $css_asset || null === $js_asset ) {
		return;
	}

	wp_enqueue_style(
		'astra-child-starter-style',
		get_stylesheet_directory_uri() . '/build/css/main.css',
		array( 'astra-theme-css' ),
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

/**
 * Tell administrators when build/ is missing.
 *
 * @return void
 */
function astra_child_starter_build_notice() {
	if ( ! current_user_can( 'manage_options' ) || null !== astra_child_starter_asset( 'css/main' ) ) {
		return;
	}

	printf(
		'<div class="notice notice-error"><p>%s</p></div>',
		esc_html__( 'Ce thème enfant n\'a pas de dossier build/. Lancez `npm install` puis `npm run build`.', 'astra-child-starter-theme' )
	);
}
add_action( 'admin_notices', 'astra_child_starter_build_notice' );
