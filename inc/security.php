<?php
defined( 'ABSPATH' ) || exit;

/* Basic hardening that does not interfere with WooCommerce or Hostinger tools. */
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'xmlrpc_methods', function () {
	return array();
} );
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );

add_action( 'send_headers', function () {
	if ( is_admin() ) {
		return;
	}
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
} );

/* Hide user enumeration via ?author=N and the public users endpoint. */
add_action( 'template_redirect', function () {
	if ( isset( $_GET['author'] ) && ! is_admin() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
} );
add_filter( 'rest_endpoints', function ( $e ) {
	if ( ! is_user_logged_in() ) {
		unset( $e['/wp/v2/users'], $e['/wp/v2/users/(?P<id>[\d]+)'] );
	}
	return $e;
} );

/* Login throttling: 6 failed attempts per IP in 15 minutes. */
add_filter( 'authenticate', function ( $user, $username ) {
	$ip = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' );
	if ( (int) get_transient( 'kbm_login_' . md5( $ip ) ) >= 6 ) {
		return new WP_Error( 'kbm_locked', 'Zbyt wiele nieudanych prób logowania. Spróbuj ponownie za 15 minut.' );
	}
	return $user;
}, 30, 2 );
add_action( 'wp_login_failed', function () {
	$ip  = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' );
	$key = 'kbm_login_' . md5( $ip );
	set_transient( $key, (int) get_transient( $key ) + 1, 15 * MINUTE_IN_SECONDS );
} );
add_filter( 'login_errors', function () {
	return 'Nieprawidłowe dane logowania.';
} );

/* Disable the file editor in wp-admin. */
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}
