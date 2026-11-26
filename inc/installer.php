<?php
/**
 * One-click content installer (idempotent: running it again updates instead of duplicating).
 * Admin: KB Motion → Instalator. REST: POST kbm/v1/install {step}.
 */
defined( 'ABSPATH' ) || exit;

require_once KBM_DIR . 'inc/content-legal.php';
require_once KBM_DIR . 'inc/content-pages.php';
require_once KBM_DIR . 'inc/content-data.php';

function kbm_install_steps() {
	return array(
		'base'     => 'Ustawienia ogólne, język, linki',
		'media'    => 'Logo i zdjęcia do biblioteki mediów',
		'woo'      => 'WooCommerce: ustawienia, kategorie, produkty',
		'trainers' => 'Trenerzy',
		'pages'    => 'Strony i regulaminy',
		'posts'    => 'Wpisy na blogu',
		'finalize' => 'Strona główna, strony sklepu, odświeżenie linków',
	);
}

function kbm_install_step( $step ) {
	$fn = 'kbm_install_' . $step;
	if ( ! array_key_exists( $step, kbm_install_steps() ) || ! function_exists( $fn ) ) {
		return new WP_Error( 'kbm_step', 'Nieznany krok: ' . $step );
	}
	@set_time_limit( 300 );
	if ( function_exists( 'kses_remove_filters' ) ) {
		kses_remove_filters(); // trusted installer content (blocks, inline styles).
	}
	$log = call_user_func( $fn );
	update_option( 'kbm_install_done_' . $step, time(), false );
	do_action( 'kbm_after_step', $step );
	return $log;
}

/* ---------------- helpers ---------------- */

function kbm_media_map() {
	return (array) get_option( 'kbm_media_map', array() );
}

function kbm_media_url( $key, $size = 'full' ) {
	$map = kbm_media_map();
	if ( ! empty( $map[ $key ] ) ) {
		$u = wp_get_attachment_image_url( (int) $map[ $key ], $size );
		if ( $u ) {
			return $u;
		}
	}
	$src = kbm_media_sources();
	return isset( $src[ $key ] ) ? $src[ $key ][0] : '';
}

function kbm_find_post( $slug, $type ) {
	$q = get_posts( array( 'name' => $slug, 'post_type' => $type, 'post_status' => array( 'publish', 'draft', 'private', 'pending' ), 'numberposts' => 1, 'fields' => 'ids' ) );
	return $q ? (int) $q[0] : 0;
}

function kbm_replace_tokens( $content ) {
	$content = preg_replace_callback( '/\{\{img:([a-z0-9_]+)\}\}/', function ( $m ) {
		return esc_url( kbm_media_url( $m[1], $m[1] === 'hero' ? 'full' : 'large' ) );
	}, $content );
	$content = preg_replace_callback( '/\{\{prod:([a-z0-9\-]+)\}\}/', function ( $m ) {
		return (string) kbm_find_post( $m[1], 'product' );
	}, $content );
	return $content;
}

function kbm_term( $name, $slug, $tax, $parent = 0 ) {
	$t = get_term_by( 'slug', $slug, $tax );
	if ( $t ) {
		return (int) $t->term_id;
	}
	$r = wp_insert_term( $name, $tax, array( 'slug' => $slug, 'parent' => $parent ) );
	return is_wp_error( $r ) ? 0 : (int) $r['term_id'];
}

/* ---------------- steps ---------------- */

function kbm_install_base() {
	$log = array();
	update_option( 'blogname', 'KB Motion' );
	update_option( 'blogdescription', 'Studio treningu personalnego we Wrocławiu' );
	update_option( 'timezone_string', 'Europe/Warsaw' );
	update_option( 'date_format', 'j F Y' );
	update_option( 'time_format', 'H:i' );
	update_option( 'start_of_week', 1 );
	update_option( 'default_comment_status', 'closed' );
	update_option( 'default_ping_status', 'closed' );
	update_option( 'blog_public', 1 );
	update_option( 'posts_per_page', 9 );
	update_option( 'permalink_structure', '/%postname%/' );
	update_option( 'uploads_use_yearmonth_folders', 1 );
	$log[] = 'Nazwa, strefa czasowa, linki bezpośrednie /%postname%/.';

	require_once ABSPATH . 'wp-admin/includes/translation-install.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	if ( ! in_array( 'pl_PL', get_available_languages(), true ) && wp_can_install_language_pack() ) {
		$ok = wp_download_language_pack( 'pl_PL' );
		$log[] = $ok ? 'Pobrano język polski.' : 'Nie udało się pobrać języka polskiego (sprawdź Ustawienia → Ogólne).';
	}
	if ( in_array( 'pl_PL', get_available_languages(), true ) ) {
		update_option( 'WPLANG', 'pl_PL' );
		$log[] = 'Język witryny: polski.';
		// Plugin translations (WooCommerce etc.) arrive with the next translation update.
		if ( ! wp_next_scheduled( 'wp_update_plugins' ) ) {
			wp_schedule_single_event( time() + 30, 'wp_update_plugins' );
		}
	}
	return $log;
}

function kbm_install_media() {
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$map = kbm_media_map();
	$log = array();
	$t0  = time();
	foreach ( kbm_media_sources() as $key => $src ) {
		if ( ! empty( $map[ $key ] ) && get_post( (int) $map[ $key ] ) ) {
			continue;
		}
		if ( time() - $t0 > 200 ) {
			$log[] = 'Limit czasu – uruchom krok ponownie, aby dokończyć.';
			break;
		}
		$tmp = download_url( $src[0], 60 );
		if ( is_wp_error( $tmp ) ) {
			$log[] = 'Błąd pobierania ' . $key . ': ' . $tmp->get_error_message();
			continue;
		}
		$ext  = strtolower( pathinfo( wp_parse_url( $src[0], PHP_URL_PATH ), PATHINFO_EXTENSION ) );
		$name = 'kb-motion-' . str_replace( '_', '-', $key ) . '.' . ( $ext ?: 'jpg' );
		$id   = media_handle_sideload( array( 'name' => $name, 'tmp_name' => $tmp ), 0, $src[1] );
		if ( is_wp_error( $id ) ) {
			@unlink( $tmp );
			$log[] = 'Błąd zapisu ' . $key . ': ' . $id->get_error_message();
			continue;
		}
		update_post_meta( $id, '_wp_attachment_image_alt', $src[1] );
		$map[ $key ] = $id;
		update_option( 'kbm_media_map', $map, false );
		$log[] = 'Dodano: ' . $src[1];
	}
	if ( ! empty( $map['logo'] ) ) {
		set_theme_mod( 'custom_logo', (int) $map['logo'] );
		if ( ! get_option( 'site_icon' ) ) {
			update_option( 'site_icon', (int) $map['logo'] );
		}
	}
	if ( ! empty( $map['hero'] ) ) {
		update_option( 'kbm_og_image', wp_get_attachment_image_url( (int) $map['hero'], 'large' ) );
	}
	$log[] = 'Media w bibliotece: ' . count( $map ) . ' z ' . count( kbm_media_sources() ) . '.';
	return $log;
}

function kbm_install_woo() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return array( 'WooCommerce nie jest aktywny – pomijam.' );
	}
	$log = array();
	$opts = array(
		'woocommerce_default_country'             => 'PL:DS',
		'woocommerce_store_address'               => kbm_opt( 'street' ),
		'woocommerce_store_city'                  => kbm_opt( 'city' ),
		'woocommerce_store_postcode'              => kbm_opt( 'postcode' ),
		'woocommerce_currency'                    => 'PLN',
		'woocommerce_currency_pos'                => 'right_space',
		'woocommerce_price_thousand_sep'          => ' ',
		'woocommerce_price_decimal_sep'           => ',',
		'woocommerce_price_num_decimals'          => 2,
		'woocommerce_allowed_countries'           => 'specific',
		'woocommerce_specific_allowed_countries'  => array( 'PL' ),
		'woocommerce_ship_to_countries'           => 'disabled',
		'woocommerce_calc_taxes'                  => 'no',
		'woocommerce_prices_include_tax'          => 'yes',
		'woocommerce_enable_guest_checkout'       => 'yes',
		'woocommerce_enable_checkout_login_reminder' => 'yes',
		'woocommerce_enable_signup_and_login_from_checkout' => 'yes',
		'woocommerce_enable_myaccount_registration' => 'yes',
		'woocommerce_registration_generate_password' => 'yes',
		'woocommerce_enable_reviews'              => 'no',
		'woocommerce_manage_stock'                => 'yes',
		'woocommerce_notify_low_stock_amount'     => 3,
		'woocommerce_hold_stock_minutes'          => 30,
		'woocommerce_cart_redirect_after_add'     => 'no',
		'woocommerce_enable_ajax_add_to_cart'     => 'yes',
		'woocommerce_coming_soon'                 => 'no',
		'woocommerce_store_pages_only'            => 'no',
		'woocommerce_email_from_name'             => 'KB Motion',
		'woocommerce_email_from_address'          => kbm_opt( 'email' ),
		'woocommerce_email_footer_text'           => 'KB Motion · ' . kbm_opt( 'street' ) . ', ' . kbm_opt( 'postcode' ) . ' ' . kbm_opt( 'city' ),
		'woocommerce_email_base_color'            => '#c8a15a',
		'woocommerce_email_background_color'      => '#f4f1ea',
		'woocommerce_allow_tracking'              => 'no',
		'woocommerce_show_marketplace_suggestions' => 'no',
		'woocommerce_analytics_enabled'           => 'yes',
		'woocommerce_feature_order_attribution_enabled' => 'yes',
	);
	foreach ( $opts as $k => $v ) {
		update_option( $k, $v );
	}
	$log[] = 'Sklep: PLN, Polska, zakup bez rejestracji, zarządzanie stanem magazynu.';

	// Categories.
	$cats = array(
		'karnety'             => array( 'Karnety', 0, 'Karnety na siłownię i zajęcia grupowe KB Motion Wrocław.' ),
		'dla-trenerow'        => array( 'Dla trenerów', 0, 'Karnet dla trenerów prowadzących treningi w studiu.' ),
		'treningi-personalne' => array( 'Treningi personalne', 0, 'Treningi personalne i pakiety w KB Motion.' ),
		'bufet'               => array( 'Bufet', 0, 'Napoje, kawa, suplementy i akcesoria. Odbiór w studiu.' ),
	);
	$ids = array();
	foreach ( $cats as $slug => $c ) {
		$ids[ $slug ] = kbm_term( $c[0], $slug, 'product_cat' );
		wp_update_term( $ids[ $slug ], 'product_cat', array( 'description' => $c[2] ) );
	}
	foreach ( array( 'napoje' => 'Napoje', 'kawa' => 'Kawa', 'suplementy' => 'Suplementy', 'akcesoria' => 'Akcesoria' ) as $slug => $name ) {
		$ids[ $slug ] = kbm_term( $name, $slug, 'product_cat', $ids['bufet'] );
	}
	// Featured image for bufet subcategories (first product image).
	$cat_img = array( 'napoje' => 'p_woda05', 'kawa' => 'p_kawa_c', 'suplementy' => 'p_wpc', 'akcesoria' => 'p_towel_black', 'bufet' => 'p_spritz' );
	$map     = kbm_media_map();
	foreach ( $cat_img as $slug => $k ) {
		if ( ! empty( $map[ $k ] ) && ! empty( $ids[ $slug ] ) ) {
			update_term_meta( $ids[ $slug ], 'thumbnail_id', (int) $map[ $k ] );
		}
	}
	// Default "Uncategorized" → rename to Inne so it does not show in English.
	$unc = (int) get_option( 'default_product_cat' );
	if ( $unc && get_term( $unc ) ) {
		wp_update_term( $unc, 'product_cat', array( 'name' => 'Inne', 'slug' => 'inne' ) );
	}

	$n = 0;
	foreach ( array() as $old ) { // retired products
		$oid = kbm_find_post( $old, 'product' );
		if ( $oid && 'draft' !== get_post_status( $oid ) ) {
			wp_update_post( array( 'ID' => $oid, 'post_status' => 'draft' ) );
		}
	}
	foreach ( kbm_products_data() as $d ) {
		if ( isset( $d['vars'] ) && 'TRAINERS' === $d['vars'] ) {
			$d['vars'] = array();
			foreach ( kbm_trainers_data() as $tr ) {
				if ( ! empty( $tr[5] ) ) {
					$d['vars'][] = array( $tr[0], $tr[5], '/ trening' );
				}
			}
		}
		kbm_install_product( $d, $ids, $map );
		$n++;
	}
	$log[] = 'Produkty utworzone/zaktualizowane: ' . $n . '.';
	wc_delete_product_transients();
	return $log;
}

function kbm_install_product( array $d, array $cat_ids, array $map ) {
	$id       = kbm_find_post( $d['slug'], 'product' );
	$variable = ( $d['type'] === 'variable' );
	if ( $id ) {
		$p = wc_get_product( $id );
		if ( $variable && ! $p->is_type( 'variable' ) ) {
			wp_set_object_terms( $id, 'variable', 'product_type' );
			$p = new WC_Product_Variable( $id );
		} elseif ( ! $variable && ! $p->is_type( 'simple' ) ) {
			wp_set_object_terms( $id, 'simple', 'product_type' );
			$p = new WC_Product_Simple( $id );
		}
	} else {
		$p = $variable ? new WC_Product_Variable() : new WC_Product_Simple();
	}
	$p->set_name( $d['name'] );
	$p->set_slug( $d['slug'] );
	$p->set_status( 'publish' );
	$p->set_catalog_visibility( 'visible' );
	$p->set_description( $d['desc'] ?? '' );
	$p->set_short_description( $d['short'] ?? '' );
	$p->set_menu_order( (int) ( $d['order'] ?? 0 ) );
	$p->set_featured( ! empty( $d['featured'] ) );
	$p->set_virtual( true ); // services + bufet pickup in the studio: no shipping address needed.
	$p->set_tax_status( 'none' );
	$p->set_reviews_allowed( false );
	$p->set_sold_individually( ! empty( $d['sold_ind'] ) );
	$cats = array( $cat_ids[ $d['cat'] ] ?? 0 );
	if ( in_array( $d['cat'], array( 'napoje', 'kawa', 'suplementy', 'akcesoria' ), true ) ) {
		$cats[] = $cat_ids['bufet'] ?? 0; // parent too, so has_term( 'bufet' ) works.
	}
	$p->set_category_ids( array_values( array_filter( $cats ) ) );
	if ( ! empty( $d['img'] ) && ! empty( $map[ $d['img'] ] ) ) {
		$p->set_image_id( (int) $map[ $d['img'] ] );
	}
	if ( isset( $d['stock'] ) ) {
		$p->set_manage_stock( true );
		if ( ! $id ) { // never overwrite real stock on re-run.
			$p->set_stock_quantity( (int) $d['stock'] );
		}
		$p->set_backorders( 'no' );
	} else {
		$p->set_manage_stock( false );
		$p->set_stock_status( 'instock' );
	}
	if ( ! $variable ) {
		foreach ( ( $id ? (array) get_children( array( 'post_parent' => $id, 'post_type' => 'product_variation', 'fields' => 'ids' ) ) : array() ) as $vid ) {
			wp_delete_post( (int) $vid, true ); // leftovers after a variable → simple change.
		}
		$p->set_attributes( array() );
		$p->set_default_attributes( array() );
		$p->set_regular_price( (string) $d['price'] );
		$p->set_sale_price( '' );
	} else {
		$attr = new WC_Product_Attribute();
		$attr->set_id( 0 );
		$attr->set_name( $d['attr'] );
		$attr->set_options( wp_list_pluck( $d['vars'], 0 ) );
		$attr->set_position( 0 );
		$attr->set_visible( true );
		$attr->set_variation( true );
		$p->set_attributes( array( $attr ) );
		$p->set_default_attributes( array( sanitize_title( $d['attr'] ) => $d['vars'][0][0] ) );
	}
	$pid = $p->save();
	update_post_meta( $pid, '_kbm_unit', $d['unit'] ?? '' );
	update_post_meta( $pid, '_kbm_cta', $d['cta'] ?? '' );
	update_post_meta( $pid, '_kbm_deposit', empty( $d['deposit'] ) ? '' : '0.50' );

	if ( $variable ) {
		$key      = sanitize_title( $d['attr'] );
		$existing = array();
		foreach ( $p->get_children() as $cid ) {
			$v = wc_get_product( $cid );
			if ( $v ) {
				$existing[ $v->get_attribute( $key ) ?: (string) $v->get_meta( 'attribute_' . $key ) ] = $v;
			}
		}
		$keep = array();
		foreach ( $d['vars'] as $i => $row ) {
			$v = $existing[ $row[0] ] ?? new WC_Product_Variation();
			$v->set_parent_id( $pid );
			$v->set_attributes( array( $key => $row[0] ) );
			$v->set_regular_price( (string) $row[1] );
			$v->set_virtual( true );
			$v->set_tax_status( 'none' );
			$v->set_menu_order( $i );
			$v->set_status( 'publish' );
			$v->set_stock_status( 'instock' );
			$vid = $v->save();
			update_post_meta( $vid, '_kbm_unit', $row[2] );
			$keep[] = $vid;
		}
		foreach ( $p->get_children() as $cid ) {
			if ( ! in_array( $cid, $keep, true ) ) {
				wp_delete_post( $cid, true );
			}
		}
		WC_Product_Variable::sync( $pid );
	}
	return $pid;
}

function kbm_install_trainers() {
	$log = array();
	foreach ( kbm_trainers_data() as $t ) {
		list( $name, $role, $tags, $owner, $order ) = $t;
		$price = (int) ( $t[5] ?? 0 );
		$slug  = sanitize_title( $name );
		$id    = kbm_find_post( $slug, 'kbm_trainer' );
		$first = strtok( $name, ' ' );
		$args  = array(
			'post_type'   => 'kbm_trainer',
			'post_title'  => $name,
			'post_name'   => $slug,
			'post_status' => 'publish',
			'menu_order'  => $order,
			'post_excerpt' => $role,
		);
		if ( ! $id ) {
			$args['post_content'] = '<!-- wp:paragraph --><p>' . esc_html( $first ) . ' ' . ( $owner ? 'współtworzy KB Motion i prowadzi treningi personalne w naszym studiu przy ul. Nyskiej 61A.' : ( false !== strpos( $role, 'grupowe' ) ? 'prowadzi zajęcia grupowe w KB Motion.' : 'prowadzi treningi personalne w KB Motion.' ) ) . '</p><!-- /wp:paragraph -->';
		} else {
			$args['ID'] = $id;
		}
		$id = wp_insert_post( $args );
		update_post_meta( $id, '_kbm_role', $role );
		update_post_meta( $id, '_kbm_tags', $tags );
		update_post_meta( $id, '_kbm_price', $price ?: '' );
		if ( $owner ) {
			update_post_meta( $id, '_kbm_is_owner', '1' );
		} else {
			delete_post_meta( $id, '_kbm_is_owner' );
		}
		$log[] = $name;
	}
	return array( 'Trenerzy: ' . implode( ', ', $log ) . '.' );
}

function kbm_install_pages() {
	$log = array();
	$ids = (array) get_option( 'kbm_page_ids', array() );
	foreach ( kbm_pages_content() as $slug => $pg ) {
		list( $title, $content, $template, $desc, $seo ) = array_pad( $pg, 5, '' );
		$id   = kbm_find_post( $slug, 'page' );
		$args = array(
			'post_type'      => 'page',
			'post_title'     => $title,
			'post_name'      => $slug,
			'post_status'    => 'publish',
			'post_content'   => kbm_replace_tokens( $content ),
			'comment_status' => 'closed',
		);
		if ( $id ) {
			$args['ID'] = $id;
		}
		$id = wp_insert_post( wp_slash( $args ), true );
		if ( is_wp_error( $id ) ) {
			$log[] = 'Błąd strony ' . $slug . ': ' . $id->get_error_message();
			continue;
		}
		update_post_meta( $id, '_wp_page_template', $template ?: 'default' );
		update_post_meta( $id, '_kbm_desc', $desc );
		update_post_meta( $id, '_kbm_title', $seo );
		$ids[ $slug ] = $id;
	}
	update_option( 'kbm_page_ids', $ids, false );
	$log[] = 'Strony: ' . count( $ids ) . ' (' . implode( ', ', array_keys( $ids ) ) . ').';
	return $log;
}

function kbm_install_posts() {
	$log = array();
	$map = kbm_media_map();
	$unc = (int) get_option( 'default_category' );
	if ( $unc && ( $t = get_term( $unc, 'category' ) ) && in_array( $t->slug, array( 'uncategorized', 'bez-kategorii' ), true ) ) {
		wp_update_term( $unc, 'category', array( 'name' => 'Aktualności', 'slug' => 'aktualnosci' ) );
	}
	// Remove WP sample content.
	foreach ( array( array( 'hello-world', 'post' ), array( 'witaj-swiecie', 'post' ), array( 'sample-page', 'page' ), array( 'przykladowa-strona', 'page' ) ) as $s ) {
		$sid = kbm_find_post( $s[0], $s[1] );
		if ( $sid ) {
			wp_trash_post( $sid );
		}
	}
	$day = 0;
	foreach ( kbm_posts_data() as $d ) {
		$cat  = kbm_term( $d['cat'], sanitize_title( $d['cat'] ), 'category' );
		$id   = kbm_find_post( $d['slug'], 'post' );
		$args = array(
			'post_type'     => 'post',
			'post_title'    => $d['title'],
			'post_name'     => $d['slug'],
			'post_status'   => 'draft', // blog is switched off; posts kept as drafts.
			'post_content'  => kbm_replace_tokens( $d['body'] ),
			'post_excerpt'  => $d['desc'],
			'post_category' => array( $cat ),
			'comment_status' => 'closed',
		);
		if ( $id ) {
			$args['ID'] = $id;
		} else {
			$args['post_date'] = gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) - DAY_IN_SECONDS * ( 2 + 9 * $day ) );
		}
		$id = wp_insert_post( wp_slash( $args ) );
		if ( ! empty( $map[ $d['img'] ] ) ) {
			set_post_thumbnail( $id, (int) $map[ $d['img'] ] );
		}
		update_post_meta( $id, '_kbm_desc', $d['desc'] );
		$log[] = $d['title'];
		$day++;
	}
	return array( 'Wpisy: ' . count( $log ) . '.' );
}

function kbm_install_finalize() {
	$log = array();
	$front = kbm_find_post( 'strona-glowna', 'page' );
	$blog  = kbm_find_post( 'blog', 'page' );
	if ( $front ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front );
	}
	if ( $blog && 'draft' !== get_post_status( $blog ) ) {
		wp_update_post( array( 'ID' => $blog, 'post_status' => 'draft' ) ); // blog removed from the site.
	}
	update_option( 'page_for_posts', 0 );
	foreach ( get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids' ) ) as $pid ) {
		wp_update_post( array( 'ID' => $pid, 'post_status' => 'draft' ) );
	}
	$priv = kbm_find_post( 'polityka-prywatnosci', 'page' );
	if ( $priv ) {
		update_option( 'wp_page_for_privacy_policy', $priv );
	}
	$log[] = 'Strona główna ustawiona, blog wyłączony.';

	if ( class_exists( 'WooCommerce' ) ) {
		// Make sure Woo pages exist, then give them Polish slugs + classic shortcodes
		// (custom checkout fields rely on the classic checkout).
		if ( class_exists( 'WC_Install' ) ) {
			WC_Install::create_pages();
		}
		$pages = array(
			'shop'      => array( 'Sklep', 'sklep', '' ),
			'cart'      => array( 'Koszyk', 'koszyk', '<!-- wp:shortcode -->[woocommerce_cart]<!-- /wp:shortcode -->' ),
			'checkout'  => array( 'Zamówienie', 'zamowienie', '<!-- wp:shortcode -->[woocommerce_checkout]<!-- /wp:shortcode -->' ),
			'myaccount' => array( 'Moje konto', 'moje-konto', '<!-- wp:shortcode -->[woocommerce_my_account]<!-- /wp:shortcode -->' ),
		);
		foreach ( $pages as $key => $pg ) {
			$pid = (int) get_option( 'woocommerce_' . $key . '_page_id' );
			if ( ! $pid || ! get_post( $pid ) ) {
				continue;
			}
			$args = array( 'ID' => $pid, 'post_title' => $pg[0], 'post_name' => $pg[1], 'post_status' => 'publish' );
			if ( $pg[2] ) {
				$args['post_content'] = $pg[2];
			}
			wp_update_post( wp_slash( $args ) );
			update_post_meta( $pid, '_kbm_title', $pg[0] . ' | KB Motion' );
		}
		$terms = kbm_find_post( 'regulamin-sklepu', 'page' );
		if ( $terms ) {
			update_option( 'woocommerce_terms_page_id', $terms );
		}
		if ( $priv ) {
			update_option( 'woocommerce_privacy_policy_page_id', $priv ); // legacy key, harmless.
		}
		update_option( 'woocommerce_checkout_privacy_policy_text', 'Twoje dane osobowe zostaną wykorzystane do realizacji zamówienia, obsługi karnetu i w innych celach opisanych w naszej [privacy_policy].' );
		update_option( 'woocommerce_registration_privacy_policy_text', 'Twoje dane osobowe zostaną wykorzystane do obsługi konta i w innych celach opisanych w naszej [privacy_policy].' );
		update_option( 'woocommerce_checkout_terms_and_conditions_checkbox_text', 'Przeczytałem/am i akceptuję [terms] *' );
		$log[] = 'Strony sklepu: /sklep/, /koszyk/, /zamowienie/, /moje-konto/; regulamin podpięty do zamówienia.';
	}
	flush_rewrite_rules( false );
	update_option( 'kbm_installed', KBM_VERSION );
	$log[] = 'Gotowe. Wersja ' . KBM_VERSION . '.';
	return $log;
}

/* ---------------- REST + admin UI ---------------- */

add_action( 'rest_api_init', function () {
	register_rest_route( 'kbm/v1', '/install', array(
		'methods'             => 'POST',
		'permission_callback' => function () {
			return current_user_can( 'manage_options' );
		},
		'args'                => array( 'step' => array( 'required' => true, 'type' => 'string' ) ),
		'callback'            => function ( WP_REST_Request $r ) {
			$out = kbm_install_step( sanitize_key( $r['step'] ) );
			if ( is_wp_error( $out ) ) {
				return $out;
			}
			return array( 'step' => $r['step'], 'log' => $out );
		},
	) );
} );

add_action( 'admin_menu', function () {
	add_submenu_page( 'kbm', 'Instalator treści', 'Instalator', 'manage_options', 'kbm-install', 'kbm_installer_page' );
}, 20 );

function kbm_installer_page() {
	$steps = kbm_install_steps();
	?>
	<div class="wrap">
		<h1>KB Motion – instalator treści</h1>
		<p>Tworzy (lub aktualizuje, bez duplikatów) strony, regulaminy, trenerów, wpisy blogowe, produkty WooCommerce i ustawienia sklepu. Bezpieczny do ponownego uruchomienia: stany magazynowe produktów nie są nadpisywane, ale treść stron wraca do wersji z instalatora.</p>
		<ol id="kbm-steps">
			<?php foreach ( $steps as $k => $label ) : ?>
				<li data-step="<?php echo esc_attr( $k ); ?>"><strong><?php echo esc_html( $label ); ?></strong>
					<?php echo get_option( 'kbm_install_done_' . $k ) ? ' <em>(wykonano ' . esc_html( wp_date( 'Y-m-d H:i', (int) get_option( 'kbm_install_done_' . $k ) ) ) . ')</em>' : ''; ?>
					<button type="button" class="button button-small kbm-one" data-step="<?php echo esc_attr( $k ); ?>">Uruchom ten krok</button>
					<div class="kbm-log" style="color:#555;margin:4px 0 10px"></div></li>
			<?php endforeach; ?>
		</ol>
		<p><button type="button" class="button button-primary button-hero" id="kbm-run-all">Zainstaluj wszystko</button></p>
	</div>
	<script>
	(function(){
		var api = <?php echo wp_json_encode( esc_url_raw( rest_url( 'kbm/v1/install' ) ) ); ?>, nonce = <?php echo wp_json_encode( wp_create_nonce( 'wp_rest' ) ); ?>;
		function run(step){
			var li = document.querySelector('li[data-step="'+step+'"]'), log = li.querySelector('.kbm-log');
			log.textContent = 'Trwa…';
			return fetch(api,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','X-WP-Nonce':nonce},body:JSON.stringify({step:step})})
				.then(function(r){return r.json();})
				.then(function(j){ log.innerHTML = j.log ? j.log.map(function(l){return '✓ '+String(l).replace(/</g,'&lt;');}).join('<br>') : ('Błąd: '+(j.message||'?')); return j; })
				.catch(function(e){ log.textContent = 'Błąd: '+e; });
		}
		document.querySelectorAll('.kbm-one').forEach(function(b){ b.addEventListener('click',function(){ run(b.dataset.step); }); });
		document.getElementById('kbm-run-all').addEventListener('click', async function(){
			this.disabled = true;
			for (const li of document.querySelectorAll('#kbm-steps li')) { await run(li.dataset.step); }
			this.disabled = false; this.textContent = 'Gotowe – uruchom ponownie';
		});
	})();
	</script>
	<?php
}

/* ---------------- Auto-install (no wp-admin needed) ----------------
 * On activation the steps run immediately when activated from WP-CLI / hosting panel;
 * otherwise one pending step runs after each page request finishes (fastcgi_finish_request).
 */
function kbm_auto_pending() {
	$done = (array) get_option( 'kbm_auto_done', array() );
	return array_values( array_diff( array_keys( kbm_install_steps() ), $done ) );
}

function kbm_auto_run_step( $step ) {
	$out  = kbm_install_step( $step );
	$done = (array) get_option( 'kbm_auto_done', array() );
	$done[] = $step;
	update_option( 'kbm_auto_done', array_values( array_unique( $done ) ), false );
	$log = (array) get_option( 'kbm_auto_log', array() );
	$log[ $step ] = is_wp_error( $out ) ? array( 'Błąd: ' . $out->get_error_message() ) : $out;
	update_option( 'kbm_auto_log', $log, false );
	if ( ! kbm_auto_pending() ) {
		delete_option( 'kbm_auto_install' );
	}
}

add_action( 'kbm_activated', function () {
	if ( get_option( 'kbm_installed' ) ) {
		return;
	}
	update_option( 'kbm_auto_install', 1, false );
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		foreach ( kbm_auto_pending() as $step ) {
			kbm_auto_run_step( $step );
		}
	}
} );

add_action( 'init', function () {
	if ( ! get_option( 'kbm_auto_install' ) || wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || get_transient( 'kbm_auto_lock' ) ) {
		return;
	}
	add_action( 'shutdown', function () {
		if ( get_transient( 'kbm_auto_lock' ) ) {
			return;
		}
		set_transient( 'kbm_auto_lock', 1, 5 * MINUTE_IN_SECONDS );
		if ( function_exists( 'fastcgi_finish_request' ) ) {
			fastcgi_finish_request();
		} elseif ( function_exists( 'litespeed_finish_request' ) ) {
			litespeed_finish_request();
		}
		ignore_user_abort( true );
		$pending = kbm_auto_pending();
		if ( $pending ) {
			kbm_auto_run_step( $pending[0] );
		}
		delete_transient( 'kbm_auto_lock' );
	}, 999 );
}, 99 );

/* Status file for remote diagnostics (no personal data): wp-content/uploads/kbm-status.txt */
function kbm_write_status() {
	$up = wp_upload_dir( null, false );
	if ( empty( $up['basedir'] ) ) {
		return;
	}
	$counts = array();
	foreach ( array( 'page', 'post', 'product', 'kbm_trainer', 'attachment' ) as $t ) {
		$c = wp_count_posts( $t );
		$counts[ $t ] = $t === 'attachment' ? (int) ( $c->inherit ?? 0 ) : (int) ( $c->publish ?? 0 );
	}
	$data = array(
		'time'        => gmdate( 'c' ),
		'version'     => KBM_VERSION,
		'installed'   => get_option( 'kbm_installed' ),
		'auto_flag'   => get_option( 'kbm_auto_install' ),
		'auto_done'   => get_option( 'kbm_auto_done' ),
		'auto_log'    => get_option( 'kbm_auto_log' ),
		'counts'      => $counts,
		'front'       => get_option( 'show_on_front' ) . ':' . get_option( 'page_on_front' ),
		'theme'       => get_stylesheet(),
		'lang'        => get_option( 'WPLANG' ),
		'permalinks'  => get_option( 'permalink_structure' ),
		'woo'         => class_exists( 'WooCommerce' ) ? WC()->version : null,
		'checkout_id' => get_option( 'woocommerce_checkout_page_id' ),
		'gateways'    => class_exists( 'WooCommerce' ) && WC()->payment_gateways() ? array_keys( array_filter( WC()->payment_gateways()->payment_gateways(), function ( $g ) { return 'yes' === $g->enabled; } ) ) : array(),
		'php'         => PHP_VERSION,
		'wp'          => get_bloginfo( 'version' ),
	);
	@file_put_contents( trailingslashit( $up['basedir'] ) . 'kbm-status.txt', wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );
}
add_action( 'wp_loaded', function () {
	$up = wp_upload_dir( null, false );
	$f  = trailingslashit( $up['basedir'] ) . 'kbm-status.txt';
	if ( ! file_exists( $f ) || filemtime( $f ) < time() - 60 ) {
		kbm_write_status();
	}
} );
add_action( 'kbm_after_step', 'kbm_write_status' );

/* Small base64 previews of the photos, so the build team can check them without a browser. */
function kbm_write_thumbs( $step ) {
	if ( 'media' !== $step ) {
		return;
	}
	$map = kbm_media_map();
	$out = '';
	foreach ( array( 'studio8', 'studio9', 't_a', 't_b', 't_c', 't_d' ) as $k ) {
		if ( empty( $map[ $k ] ) ) {
			continue;
		}
		$file = get_attached_file( (int) $map[ $k ] );
		if ( ! $file || ! file_exists( $file ) ) {
			continue;
		}
		$ed = wp_get_image_editor( $file );
		if ( is_wp_error( $ed ) ) {
			continue;
		}
		$ed->resize( 260, 260, false );
		$ed->set_quality( 60 );
		$tmp = wp_tempnam( 'kbm-thumb' ) . '.jpg';
		$r   = $ed->save( $tmp, 'image/jpeg' );
		if ( is_wp_error( $r ) ) {
			continue;
		}
		$out .= $k . ' ' . base64_encode( (string) file_get_contents( $r['path'] ) ) . "\n";
		@unlink( $r['path'] );
	}
	$up = wp_upload_dir();
	file_put_contents( trailingslashit( $up['basedir'] ) . 'kbm-thumbs.txt', $out );
}
add_action( 'kbm_after_step', 'kbm_write_thumbs', 20 );

/* Upgrade routine: re-queue steps that need a full WordPress request (WooCommerce loaded). */
add_action( 'init', function () {
	$v = get_option( 'kbm_auto_version', '1.0.0' );
	if ( version_compare( $v, KBM_VERSION, '>=' ) || ! get_option( 'kbm_installed' ) ) {
		return;
	}
	$rerun = version_compare( $v, '1.0.1', '<' ) ? array( 'woo', 'pages', 'finalize' ) : array( 'media', 'trainers', 'woo', 'pages', 'posts', 'finalize' );
	$done  = array_values( array_diff( (array) get_option( 'kbm_auto_done', array() ), $rerun ) );
	update_option( 'kbm_auto_done', $done, false );
	update_option( 'kbm_auto_install', 1, false );
	update_option( 'kbm_auto_version', KBM_VERSION, false );
	kbm_dedupe_media();
}, 5 );

/* Remove duplicate media created by an interrupted first install (only our own kb-motion-* files not in the media map). */
function kbm_dedupe_media() {
	$keep = array_map( 'intval', array_values( kbm_media_map() ) );
	if ( count( $keep ) < 10 ) {
		return;
	}
	$q = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => 200, 'fields' => 'ids' ) );
	foreach ( $q as $id ) {
		$file = (string) get_post_meta( $id, '_wp_attached_file', true );
		if ( ! in_array( (int) $id, $keep, true ) && preg_match( '#(^|/)kb-motion-[a-z0-9-]+\.(jpe?g|png|webp)$#i', $file ) && ! (int) wp_get_post_parent_id( $id ) ) {
			wp_delete_attachment( $id, true );
		}
	}
}
