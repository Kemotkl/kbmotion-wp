<?php
/**
 * KB Motion theme.
 */
defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', function () {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support( 'custom-logo', array( 'height' => 160, 'width' => 320, 'flex-width' => true, 'flex-height' => true ) );
	add_theme_support( 'responsive-embeds' );
	add_editor_style( 'style.css' );
} );

add_action( 'wp_enqueue_scripts', function () {
	$v = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'kbmotion', get_stylesheet_uri(), array(), $v );
	wp_enqueue_script( 'kbmotion', get_theme_file_uri( 'assets/theme.js' ), array(), $v, array( 'strategy' => 'defer', 'in_footer' => true ) );
} );

/* Preload the two Latin font files (Polish glyphs load on demand from the latin-ext files). */
add_action( 'wp_head', function () {
	foreach ( array( 'oswald-latin-wght-normal.woff2', 'manrope-latin-wght-normal.woff2' ) as $f ) {
		echo '<link rel="preload" href="' . esc_url( get_theme_file_uri( 'assets/fonts/' . $f ) ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
	}
	echo '<meta name="theme-color" content="#0b0b0c">' . "\n";
}, 1 );

/* Sticky mobile CTA bar – "Kup karnet" and "Umów trening" always one tap away. */
add_action( 'wp_footer', function () {
	if ( function_exists( 'is_checkout' ) && ( is_checkout() || is_cart() ) ) {
		return;
	}
	echo '<nav class="kbm-mbar" aria-label="Szybkie akcje"><a class="kbm-mbar__btn" href="' . esc_url( home_url( '/treningi-personalne/#rezerwacja' ) ) . '">Umów trening</a><a class="kbm-mbar__btn kbm-mbar__btn--gold" href="' . esc_url( home_url( '/karnety/' ) ) . '">Kup karnet</a></nav>';
	echo '<script>(function(){var b=document.querySelector(".kbm-mbar");if(!b)return;function u(){b.classList.toggle("is-hidden",window.scrollY<Math.min(320,window.innerHeight*.45));}u();window.addEventListener("scroll",u,{passive:true});})();</script>';
}, 30 );

add_filter( 'body_class', function ( $c ) {
	if ( function_exists( 'is_checkout' ) && ( is_checkout() || is_cart() ) ) {
		$c[] = 'kbm-no-mbar';
	}
	return $c;
} );

/* Smaller default images for WooCommerce grids. */
add_filter( 'woocommerce_get_image_size_thumbnail', function () {
	return array( 'width' => 480, 'height' => 600, 'crop' => 1 );
} );

/* Polish quotes look nicer without curly-quote conversion of shortcodes. */
