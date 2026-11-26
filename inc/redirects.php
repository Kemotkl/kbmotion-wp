<?php
defined( 'ABSPATH' ) || exit;

/* 301 redirects from the old kbmotion.pl (Hostinger Builder) URLs, so Google rankings carry over. */
function kbm_redirect_map() {
	return array(
		'about'                                         => 'o-nas',
		'regulamin'                                     => 'regulamin-studia',
		'regulamin1'                                    => 'regulamin-aplikacji',
		'en'                                            => '',
		'blog'                                          => '',
		'kaucja-050zl'                                  => 'bufet',
		'woda-naleczowianka-niegazowana-05-l'           => 'produkt/woda-naleczowianka-niegazowana-0-5-l',
		'woda-mineralna-naleczowianka-niegazowana-15-l' => 'produkt/woda-naleczowianka-niegazowana-1-5-l',
		'dobry-material-wrclw-spritz-bezalkoholowy'     => 'produkt/dobry-material-wrclw-spritz-bezalkoholowy',
		'kfd-vitamin-d3-4000-iu-200-kaps'               => 'produkt/kfd-vitamin-d3-4000-iu-200-kaps',
		'kfd-vitamin-c-zinc-120-kapsulek'               => 'produkt/kfd-vitamin-c-zinc-120-kaps',
		'kfd-premium-x-gnr-1000-g'                      => 'produkt/kfd-premium-x-gnr-1000-g',
		'kfd-premium-eaa-375-g'                         => 'produkt/kfd-premium-eaa-375-g',
		'kfd-premium-creatine-500-g'                    => 'produkt/kfd-premium-creatine-500-g',
		'recznik-szary-50x100-mikrofibra'               => 'produkt/recznik-kb-motion-szary-50x100',
		'recznik-czarny-50x100-mikrofibra'              => 'produkt/recznik-kb-motion-czarny-50x100',
		'kawa-mala-'                                    => 'produkt/kawa-mala',
		'kawa-duza-biala'                               => 'produkt/kawa-duza-biala',
		'kawa'                                          => 'produkt/kawa-duza-czarna',
		'kfd-premium-wpc-82-bialko'                     => 'produkt/kfd-premium-wpc-82-900-g',
	);
}

add_action( 'template_redirect', function () {
	if ( ! is_404() ) {
		return;
	}
	$path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );
	$map  = kbm_redirect_map();
	if ( isset( $map[ $path ] ) ) {
		wp_safe_redirect( home_url( '/' . ( $map[ $path ] ? $map[ $path ] . '/' : '' ) ), 301 );
		exit;
	}
	if ( 0 === strpos( $path, 'product/' ) ) { // product base changed to /produkt/
		wp_safe_redirect( home_url( '/produkt/' . substr( $path, 8 ) . '/' ), 301 );
		exit;
	}
	// Former blog posts (now drafts) → home page.
	if ( $path && false === strpos( $path, '/' ) && get_posts( array( 'name' => $path, 'post_type' => 'post', 'post_status' => 'draft', 'numberposts' => 1, 'fields' => 'ids' ) ) ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
} );
