<?php
defined( 'ABSPATH' ) || exit;

function kbm_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || class_exists( 'The_SEO_Framework\Load' );
}

function kbm_meta_description() {
	if ( is_front_page() ) {
		$d = get_post_meta( (int) get_option( 'page_on_front' ), '_kbm_desc', true );
		return $d ?: get_bloginfo( 'description' );
	}
	if ( is_singular() ) {
		$id = get_queried_object_id();
		$d  = get_post_meta( $id, '_kbm_desc', true );
		if ( ! $d ) {
			$post = get_post( $id );
			$d    = has_excerpt( $id ) ? get_the_excerpt( $id ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 28, '…' );
		}
		return $d;
	}
	if ( ( function_exists( 'is_product_category' ) && is_product_category() ) || is_category() || is_tag() ) {
		return wp_strip_all_tags( term_description() );
	}
	return get_bloginfo( 'description' );
}

add_action( 'wp_head', function () {
	if ( kbm_has_seo_plugin() ) {
		return;
	}
	$desc  = trim( preg_replace( '/\s+/', ' ', (string) kbm_meta_description() ) );
	$title = wp_get_document_title();
	$url   = is_singular() ? get_permalink() : home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
	$img   = '';
	if ( is_singular() && has_post_thumbnail() ) {
		$img = get_the_post_thumbnail_url( null, 'large' );
	}
	if ( ! $img ) {
		$img = (string) get_option( 'kbm_og_image' );
	}
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( mb_substr( $desc, 0, 160 ) ) . '">' . "\n";
	}
	echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
	echo '<meta property="og:locale" content="pl_PL">' . "\n";
	echo '<meta property="og:site_name" content="KB Motion">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta property="og:description" content="' . esc_attr( mb_substr( $desc, 0, 200 ) ) . '">' . "\n";
	}
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	if ( $img ) {
		echo '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	if ( is_singular() ) {
		echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '">' . "\n";
	}
}, 2 );

/* WP prints its own canonical for singular – avoid duplicates. */
add_action( 'init', function () {
	if ( ! kbm_has_seo_plugin() ) {
		remove_action( 'wp_head', 'rel_canonical' );
	}
} );

/* Custom document titles. */
add_filter( 'pre_get_document_title', function ( $t ) {
	if ( kbm_has_seo_plugin() || ! is_singular() ) {
		return $t;
	}
	$custom = get_post_meta( get_queried_object_id(), '_kbm_title', true );
	return $custom ? $custom : $t;
} );
add_filter( 'document_title_separator', function () {
	return '|';
} );

/* LocalBusiness / ExerciseGym schema. */
function kbm_gym_schema() {
	$s = array(
		'@context'                  => 'https://schema.org',
		'@type'                     => array( 'ExerciseGym', 'LocalBusiness' ),
		'@id'                       => home_url( '/#gym' ),
		'name'                      => 'KB Motion',
		'description'               => 'Kameralne studio treningu personalnego we Wrocławiu: treningi personalne, przygotowanie motoryczne, redukcja tkanki tłuszczowej, siłownia i zajęcia grupowe.',
		'url'                       => home_url( '/' ),
		'email'                     => kbm_opt( 'email' ),
		'priceRange'                => '45–1500 PLN',
		'currenciesAccepted'        => 'PLN',
		'paymentAccepted'           => 'Karta, BLIK, przelew',
		'address'                   => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => kbm_opt( 'street' ),
			'postalCode'      => kbm_opt( 'postcode' ),
			'addressLocality' => kbm_opt( 'city' ),
			'addressCountry'  => 'PL',
		),
		'openingHoursSpecification' => array(
			array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ),
				'opens'     => kbm_opt( 'opens' ),
				'closes'    => kbm_opt( 'closes' ),
			),
		),
		'floorSize'                 => array( '@type' => 'QuantitativeValue', 'value' => 250, 'unitCode' => 'MTK' ),
		'hasMap'                    => 'https://www.google.com/maps?q=' . rawurlencode( 'KB Motion ' . kbm_opt( 'street' ) . ' ' . kbm_opt( 'city' ) ),
	);
	if ( kbm_opt( 'phone' ) ) {
		$s['telephone'] = kbm_opt( 'phone' );
	}
	if ( kbm_opt( 'lat' ) && kbm_opt( 'lng' ) ) {
		$s['geo'] = array( '@type' => 'GeoCoordinates', 'latitude' => (float) kbm_opt( 'lat' ), 'longitude' => (float) kbm_opt( 'lng' ) );
	}
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$s['logo'] = wp_get_attachment_image_url( $logo_id, 'full' );
	}
	if ( get_option( 'kbm_og_image' ) ) {
		$s['image'] = get_option( 'kbm_og_image' );
	}
	$same = array_values( array_filter( array( kbm_opt( 'instagram' ), kbm_opt( 'facebook' ) ) ) );
	if ( $same ) {
		$s['sameAs'] = $same;
	}
	// Aggregate rating only from real reviews entered in the admin.
	$reviews = get_posts( array( 'post_type' => 'kbm_review', 'numberposts' => 50 ) );
	if ( count( $reviews ) >= 3 ) {
		$sum = 0;
		foreach ( $reviews as $r ) {
			$sum += (int) ( get_post_meta( $r->ID, '_kbm_stars', true ) ?: 5 );
		}
		$s['aggregateRating'] = array( '@type' => 'AggregateRating', 'ratingValue' => round( $sum / count( $reviews ), 1 ), 'reviewCount' => count( $reviews ), 'bestRating' => 5 );
	}
	return $s;
}

add_action( 'wp_footer', function () {
	$graph = array();
	if ( is_front_page() || is_page( array( 'kontakt', 'o-nas', 'karnety', 'treningi-personalne' ) ) ) {
		$graph[] = kbm_gym_schema();
	}
	if ( ! empty( $GLOBALS['kbm_faq_used'] ) ) {
		$data  = kbm_faq_data();
		$items = array();
		foreach ( array_unique( $GLOBALS['kbm_faq_used'] ) as $set ) {
			foreach ( $data[ $set ] ?? array() as $qa ) {
				$items[] = array( '@type' => 'Question', 'name' => $qa[0], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $qa[1] ) );
			}
		}
		if ( $items ) {
			$graph[] = array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items );
		}
	}
	if ( is_singular( 'kbm_trainer' ) ) {
		$graph[] = array( '@context' => 'https://schema.org', '@type' => 'Person', 'name' => get_the_title(), 'jobTitle' => get_post_meta( get_the_ID(), '_kbm_role', true ) ?: 'Trener personalny', 'worksFor' => array( '@id' => home_url( '/#gym' ) ) );
	}
	foreach ( $graph as $g ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $g, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}, 5 );

/* Sitemap: hide users and private types. */
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}, 10, 2 );
add_filter( 'wp_sitemaps_post_types', function ( $types ) {
	unset( $types['kbm_review'], $types['kbm_class'], $types['kbm_lead'] );
	return $types;
} );
add_filter( 'wp_sitemaps_posts_query_args', function ( $args, $post_type ) {
	if ( 'page' === $post_type && function_exists( 'wc_get_page_id' ) ) {
		$args['post__not_in'] = array_filter( array( wc_get_page_id( 'cart' ), wc_get_page_id( 'checkout' ), wc_get_page_id( 'myaccount' ) ) );
	}
	return $args;
}, 10, 2 );

/* robots.txt hint for the sitemap. */
add_filter( 'robots_txt', function ( $out, $public ) {
	if ( $public ) {
		$out .= "Disallow: /koszyk/\nDisallow: /zamowienie/\nDisallow: /moje-konto/\nDisallow: /*?add-to-cart=*\n";
	}
	return $out;
}, 10, 2 );
