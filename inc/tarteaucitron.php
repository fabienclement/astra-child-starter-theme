<?php
/**
 * Load Tarteaucitron, and declare the services it gates.
 *
 * @package astra_child_starter
 * @since 2.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Read the Google Analytics measurement ID, of the form G-XXXXXXXXXX.
 *
 * The theme ships no value. Define ASTRA_CHILD_STARTER_GTAG_ID in wp-config.php
 * to enable gtag: a starter carrying an example ID would risk sending a new
 * site's visits to someone else's property.
 *
 * @return string The measurement ID, empty when the constant is not defined.
 */
function astra_child_starter_gtag_id() {
	if ( ! defined( 'ASTRA_CHILD_STARTER_GTAG_ID' ) ) {
		return '';
	}

	$id = constant( 'ASTRA_CHILD_STARTER_GTAG_ID' );

	return is_string( $id ) ? $id : '';
}

/**
 * The version of the tarteaucitronjs package copied into build/tarteaucitron/.
 *
 * Bump it together with the dependency in package.json.
 */
const ASTRA_CHILD_STARTER_TARTEAUCITRON_VERSION = '1.34.0';

/**
 * List the Tarteaucitron services this site gates.
 *
 * Push a key into the array to enable a service. The three a brochure site
 * usually needs are `googlemapsembed`, `youtube` and `recaptcha`. Every other
 * key lives in the library catalogue, tarteaucitron.services.js.
 *
 * @return array<string> The service keys, in the order they are declared.
 */
function astra_child_starter_tarteaucitron_services() {
	$services = array();

	if ( '' !== astra_child_starter_gtag_id() ) {
		$services[] = 'gtag';
	}

	return $services;
}

/**
 * Build the inline script that starts Tarteaucitron and declares its services.
 *
 * The three parameters below already default to true in version 1.34.0. Writing
 * them down locks the French consent rules against a change of default: no
 * implied consent on scroll, and a deny button as reachable as the accept one.
 *
 * @return string The JavaScript to run once the library has loaded.
 */
function astra_child_starter_tarteaucitron_boot() {
	$lines = array(
		'tarteaucitron.init({',
		'	highPrivacy: true,',
		'	AcceptAllCta: true,',
		'	DenyAllCta: true',
		'});',
	);

	if ( '' !== astra_child_starter_gtag_id() ) {
		$lines[] = 'tarteaucitron.user.gtagUa = "' . esc_js( astra_child_starter_gtag_id() ) . '";';
	}

	foreach ( astra_child_starter_tarteaucitron_services() as $service ) {
		$lines[] = '(tarteaucitron.job = tarteaucitron.job || []).push("' . esc_js( $service ) . '");';
	}

	return implode( "\n", $lines );
}

/**
 * Enqueue Tarteaucitron in the head, without defer.
 *
 * The library reads its own folder from document.currentScript, then loads its
 * language file, its service catalogue and its stylesheet from there. Deferring
 * it would delay the consent panel past the scripts it is meant to gate.
 *
 * @return void
 */
function astra_child_starter_tarteaucitron_enqueue() {
	$file = '/build/tarteaucitron/tarteaucitron.min.js';

	if ( ! file_exists( get_stylesheet_directory() . $file ) ) {
		return;
	}

	wp_enqueue_script(
		'astra-child-starter-tarteaucitron',
		get_stylesheet_directory_uri() . $file,
		array(),
		ASTRA_CHILD_STARTER_TARTEAUCITRON_VERSION,
		false
	);

	wp_add_inline_script( 'astra-child-starter-tarteaucitron', astra_child_starter_tarteaucitron_boot(), 'after' );
}
add_action( 'wp_enqueue_scripts', 'astra_child_starter_tarteaucitron_enqueue' );
