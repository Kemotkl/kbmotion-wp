<?php
/**
 * Plugin Name:       KB Motion Core
 * Description:       Funkcje strony KB Motion: trenerzy, grafik, opinie, formularze z RODO i antyspamem, AI asystent, SEO (schema ExerciseGym), baner cookies z Consent Mode, integracja WooCommerce (karnety, treningi, bufet) oraz instalator treści.
 * Version:           1.0.5
 * Requires at least: 6.5
 * Requires PHP:      8.0
 * Author:            KB Motion
 * Text Domain:       kbmotion-core
 * License:           GPL-2.0-or-later
 */

defined( 'ABSPATH' ) || exit;

define( 'KBM_VERSION', '1.0.5' );
define( 'KBM_FILE', __FILE__ );
define( 'KBM_DIR', plugin_dir_path( __FILE__ ) );
define( 'KBM_URL', plugin_dir_url( __FILE__ ) );

require_once KBM_DIR . 'inc/settings.php';
require_once KBM_DIR . 'inc/cpt.php';
require_once KBM_DIR . 'inc/shortcodes.php';
require_once KBM_DIR . 'inc/forms.php';
require_once KBM_DIR . 'inc/chat.php';
require_once KBM_DIR . 'inc/seo.php';
require_once KBM_DIR . 'inc/consent.php';
require_once KBM_DIR . 'inc/woo.php';
require_once KBM_DIR . 'inc/security.php';
require_once KBM_DIR . 'inc/redirects.php';
require_once KBM_DIR . 'inc/installer.php';

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'kbm-core', KBM_URL . 'assets/kbm.css', array(), KBM_VERSION );
	wp_enqueue_script( 'kbm-core', KBM_URL . 'assets/kbm.js', array(), KBM_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );
	wp_localize_script( 'kbm-core', 'KBM', array(
		'rest'     => esc_url_raw( rest_url( 'kbm/v1/' ) ),
		'chat'     => kbm_opt( 'chat_enabled' ) ? 1 : 0,
		'chatName' => 'Asystent KB Motion',
		'buyUrl'   => kbm_url( 'karnety' ),
		'bookUrl'  => kbm_url( 'treningi-personalne' ) . '#rezerwacja',
		'privacy'  => kbm_url( 'polityka-prywatnosci' ),
		'cookies'  => kbm_url( 'polityka-cookies' ),
		'ga4'      => kbm_opt( 'ga4_id' ),
		'pixel'    => kbm_opt( 'pixel_id' ),
	) );
} );

register_activation_hook( __FILE__, function () {
	kbm_register_cpts();
	flush_rewrite_rules();
	do_action( 'kbm_activated' );
} );
