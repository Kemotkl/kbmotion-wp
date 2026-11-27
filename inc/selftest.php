<?php
/**
 * Self-test: the server crawls its own pages (loopback), checks every internal link, button and image,
 * and runs a cart test through the WooCommerce Store API. Results: uploads/kbm-selftest.txt.
 * Runs in the background after a page request when the option kbm_selftest_want is newer than kbm_selftest_done.
 */
defined( 'ABSPATH' ) || exit;

define( 'KBM_SELFTEST_REV', 9 );

function kbm_st_get( $url, $args = array() ) {
	$args = wp_parse_args( $args, array( 'timeout' => 20, 'redirection' => 0, 'sslverify' => false, 'user-agent' => 'KBM-Selftest' ) );
	$r    = wp_remote_request( $url, $args );
	if ( is_wp_error( $r ) ) {
		return array( 0, '', array(), $r->get_error_message() );
	}
	return array( (int) wp_remote_retrieve_response_code( $r ), (string) wp_remote_retrieve_body( $r ), wp_remote_retrieve_headers( $r ), '' );
}

function kbm_st_norm( $href, $base ) {
	$href = html_entity_decode( trim( $href ) );
	if ( '' === $href || '#' === $href[0] || preg_match( '#^(mailto|tel|javascript|data):#i', $href ) ) {
		return '';
	}
	$home = home_url( '/' );
	if ( 0 === strpos( $href, '//' ) ) {
		$href = 'https:' . $href;
	} elseif ( '/' === $href[0] ) {
		$href = untrailingslashit( $home ) . $href;
	} elseif ( ! preg_match( '#^https?://#i', $href ) ) {
		$href = trailingslashit( dirname( $base ) ) . $href;
	}
	$href = preg_replace( '/#.*$/', '', $href );
	return ( 0 === strpos( $href, untrailingslashit( $home ) ) ) ? $href : '';
}

function kbm_selftest_run() {
	@set_time_limit( 280 );
	$t0    = time();
	$home  = home_url( '/' );
	$queue = array( $home );
	foreach ( get_pages( array( 'post_status' => 'publish' ) ) as $p ) {
		$queue[] = get_permalink( $p );
	}
	foreach ( get_posts( array( 'post_type' => 'product', 'post_status' => 'publish', 'numberposts' => -1 ) ) as $p ) {
		$queue[] = get_permalink( $p );
	}
	// old kbmotion.pl addresses must redirect
	$redir = array();
	foreach ( kbm_redirect_map() as $from => $to ) {
		$redir[] = home_url( '/' . $from . '/' );
	}
	$redir[] = home_url( '/blog/' );
	$seen   = array();
	$pages  = array();
	$assets = array();
	$issues = array();
	$from   = array();
	$anchors = array();
	$bodies  = array();
	while ( $queue && time() - $t0 < 200 && count( $seen ) < 260 ) {
		$url = array_shift( $queue );
		$key = preg_replace( '/\?.*$/', '', $url );
		if ( isset( $seen[ $url ] ) || ( false === strpos( $url, '?' ) && isset( $seen[ $key ] ) ) ) {
			continue;
		}
		$seen[ $url ] = 1;
		if ( preg_match( '#/(wp-admin|wp-login|wp-json|feed|xmlrpc)#', $url ) || preg_match( '/[?&](add-to-cart|remove_item|undo_item|_wpnonce)=/', $url ) ) {
			continue;
		}
		list( $code, $body, $h, $err ) = kbm_st_get( $url );
		$loc = is_object( $h ) || is_array( $h ) ? ( $h['location'] ?? '' ) : '';
		$pages[ $url ] = $code . ( $loc ? ' → ' . $loc : '' ) . ( $err ? ' ERR ' . $err : '' );
		if ( $code >= 400 || 0 === $code ) {
			$issues[] = 'HTTP ' . $code . ' ' . $url . ' (link z: ' . implode( ', ', array_slice( $from[ $url ] ?? array(), 0, 3 ) ) . ')';
			continue;
		}
		if ( $loc ) {
			$n = kbm_st_norm( $loc, $url );
			if ( $n && ! isset( $seen[ $n ] ) ) {
				$queue[] = $n;
				$from[ $n ][] = $url;
			}
			continue;
		}
		$ct = is_object( $h ) || is_array( $h ) ? (string) ( $h['content-type'] ?? '' ) : '';
		if ( $ct && false === stripos( $ct, 'text/html' ) ) {
			continue; // image or file link
		}
		foreach ( array( 'Fatal error', 'Warning:</b>', 'Notice:</b>', 'Deprecated:</b>', '[kbm_', '{{img', '{{prod', 'Parse error' ) as $bad ) {
			if ( false !== strpos( $body, $bad ) ) {
				$issues[] = 'Tekst "' . $bad . '" na ' . $url;
			}
		}
		if ( preg_match_all( '/<a\s(?:[^>]*\s)?href=["\']([^"\']+)["\']/i', $body, $m ) ) {
			foreach ( $m[1] as $href ) {
				$n = kbm_st_norm( $href, $url );
				if ( $n && ! isset( $seen[ $n ] ) ) {
					$queue[] = $n;
					$from[ $n ][] = $url;
				}
				if ( preg_match( '/^#([\w\-]+)$/', trim( $href ), $a ) && ! preg_match( '/id=["\']' . preg_quote( $a[1], '/' ) . '["\']/', $body ) ) {
					$issues[] = 'Kotwica #' . $a[1] . ' nie istnieje na ' . $url;
				}
				if ( preg_match( '#^(?:' . preg_quote( untrailingslashit( $home ), '#' ) . ')?(/[^"\'\#?]*)\#([\w\-]+)$#', trim( $href ), $a2 ) ) {
					$target = untrailingslashit( $home ) . $a2[1];
					$anchors[ $target ][ $a2[2] ] = $url;
				}
			}
		}
		if ( preg_match_all( '/<(?:img|source)\s(?:[^>]*\s)?(?:src|srcset)=["\']([^"\'\s,]+)/i', $body, $m ) ) {
			foreach ( $m[1] as $src ) {
				$n = kbm_st_norm( $src, $url );
				if ( $n ) {
					$assets[ $n ] = $url;
				}
			}
		}
		if ( preg_match_all( '/<(?:link|script)\s[^>]*(?:href|src)=["\']([^"\']+\.(?:css|js)[^"\']*)["\']/i', $body, $m ) ) {
			foreach ( $m[1] as $src ) {
				$n = kbm_st_norm( $src, $url );
				if ( $n ) {
					$assets[ $n ] = $url;
				}
			}
		}
		if ( false === stripos( $body, '<title>' ) || false === stripos( $body, 'name="viewport"' ) ) {
			$issues[] = 'Brak <title> albo viewport na ' . $url;
		}
		if ( preg_match_all( '/<img\s(?![^>]*\balt=)[^>]*>/i', $body, $m ) ) {
			$issues[] = count( $m[0] ) . ' obraz(y) bez alt na ' . $url;
		}
		$pages[ $url ] .= ' ' . strlen( $body ) . 'B';
		$bodies[ $url ] = $body;
	}
	// anchors to other pages
	foreach ( $anchors as $target => $ids ) {
		$b = $bodies[ trailingslashit( $target ) ] ?? ( $bodies[ $target ] ?? null );
		if ( null === $b ) {
			continue;
		}
		foreach ( $ids as $id => $src ) {
			if ( ! preg_match( '/id=["\']' . preg_quote( $id, '/' ) . '["\']/', $b ) ) {
				$issues[] = 'Kotwica ' . $target . '#' . $id . ' (link z ' . $src . ') nie istnieje';
			}
		}
	}
	$checked_assets = 0;
	foreach ( $assets as $a => $src ) {
		if ( time() - $t0 > 240 ) {
			$issues[] = 'Limit czasu: nie sprawdzono wszystkich obrazów.';
			break;
		}
		list( $code ) = kbm_st_get( $a, array( 'method' => 'HEAD', 'redirection' => 3 ) );
		$checked_assets++;
		if ( $code >= 400 || 0 === $code ) {
			$issues[] = 'Plik ' . $code . ' ' . $a . ' (na ' . $src . ')';
		}
	}
	foreach ( $redir as $r ) {
		list( $code, , $h ) = kbm_st_get( $r );
		$loc = $h['location'] ?? '';
		if ( 301 !== $code ) {
			$issues[] = 'Przekierowanie nie działa: ' . $r . ' → HTTP ' . $code;
		} else {
			list( $c2 ) = kbm_st_get( $loc, array( 'redirection' => 3 ) );
			if ( 200 !== $c2 ) {
				$issues[] = 'Przekierowanie ' . $r . ' → ' . $loc . ' daje HTTP ' . $c2;
			}
		}
	}
	$cart = array_merge( kbm_selftest_cart(), array( '', '== ZAKUP PRZEZ PRZYCISK KUP TERAZ ==' ), kbm_selftest_checkout() );
	$rest = array();
	foreach ( array( 'kbm/v1/form', 'kbm/v1/chat' ) as $route ) {
		list( $code, $b ) = kbm_st_get( rest_url( $route ), array( 'method' => 'POST', 'body' => array() ) );
		$rest[] = $route . ': HTTP ' . $code . ' ' . substr( wp_strip_all_tags( $b ), 0, 160 );
	}
	$out  = 'SELFTEST ' . gmdate( 'c' ) . ' wersja ' . KBM_VERSION . ' czas ' . ( time() - $t0 ) . "s\n";
	$out .= 'Strony: ' . count( $pages ) . ', pliki/obrazy: ' . $checked_assets . '/' . count( $assets ) . "\n\n== PROBLEMY (" . count( $issues ) . ") ==\n" . implode( "\n", array_unique( $issues ) ) . "\n\n== KOSZYK ==\n" . implode( "\n", $cart ) . "\n\n== REST ==\n" . implode( "\n", $rest ) . "\n\n== STRONY ==\n";
	foreach ( $pages as $u => $s ) {
		$out .= str_replace( untrailingslashit( $home ), '', $u ) . '  ' . $s . "\n";
	}
	file_put_contents( trailingslashit( wp_upload_dir()['basedir'] ) . 'kbm-selftest.txt', $out );
	kbm_selftest_snapshot( $bodies, $assets );
}

/* Snapshot for visual review: page HTML, list of assets, small copies of uploaded images (base64, one per line). */
function kbm_selftest_snapshot( $bodies, $assets ) {
	$dir = trailingslashit( wp_upload_dir()['basedir'] ) . 'kbm-snap/';
	wp_mkdir_p( $dir );
	$home = untrailingslashit( home_url() );
	$keep = array( '/', '/karnety/', '/treningi-personalne/', '/trenerzy/', '/grafik-zajec/', '/bufet/', '/o-nas/', '/kontakt/', '/galeria/', '/faq/', '/trening-probny/', '/sklep/', '/koszyk/', '/regulamin-studia/', '/produkt/silownia/', '/produkt/trening-personalny/', '/produkt/woda-naleczowianka-niegazowana-0-5-l/', '/moje-konto/' );
	$idx  = array();
	foreach ( $keep as $i => $path ) {
		$b = $bodies[ $home . $path ] ?? null;
		if ( null === $b ) {
			continue;
		}
		$f = 'p' . $i . '.html';
		file_put_contents( $dir . $f, $b );
		$idx[] = $f . ' ' . $path;
	}
	file_put_contents( $dir . 'index.txt', implode( "\n", $idx ) );
	file_put_contents( $dir . 'assets.txt', implode( "\n", array_keys( $assets ) ) );
	$up   = wp_upload_dir();
	$out  = '';
	$done = array();
	foreach ( array_keys( $assets ) as $a ) {
		$a = preg_replace( '/\?.*$/', '', $a );
		if ( 0 !== strpos( $a, $up['baseurl'] ) || isset( $done[ $a ] ) || ! preg_match( '/\.(jpe?g|png|webp|gif)$/i', $a ) ) {
			continue;
		}
		$done[ $a ] = 1;
		$file = $up['basedir'] . substr( $a, strlen( $up['baseurl'] ) );
		if ( ! file_exists( $file ) ) {
			continue;
		}
		$ed = wp_get_image_editor( $file );
		if ( is_wp_error( $ed ) ) {
			continue;
		}
		$sz = $ed->get_size();
		if ( $sz['width'] > 700 ) {
			$ed->resize( 700, null, false );
		}
		$ed->set_quality( 55 );
		$tmp = wp_tempnam( 'kbm-snap' ) . '.jpg';
		$r   = $ed->save( $tmp, 'image/jpeg' );
		if ( is_wp_error( $r ) ) {
			continue;
		}
		$out .= substr( $a, strlen( $up['baseurl'] ) ) . ' ' . base64_encode( (string) file_get_contents( $r['path'] ) ) . "\n";
		@unlink( $r['path'] );
	}
	file_put_contents( $dir . 'img.txt', $out );
}

/* Cart test through the public Store API: water (deposit), karnet, trainer variation, totals. */
function kbm_selftest_cart() {
	$log  = array();
	$base = rest_url( 'wc/store/v1/' );
	list( $code, $b, $h ) = kbm_st_get( $base . 'cart' );
	$tok = $h['cart-token'] ?? '';
	$log[] = 'GET cart: HTTP ' . $code . ( $tok ? ' (token ok)' : ' (brak tokenu)' );
	if ( ! $tok ) {
		return $log;
	}
	$hdr  = array( 'Cart-Token' => $tok, 'Content-Type' => 'application/json' );
	$add  = function ( $id, $qty = 1, $variation = array() ) use ( $base, $hdr ) {
		$body = array( 'id' => $id, 'quantity' => $qty );
		if ( $variation ) {
			$body['variation'] = $variation;
		}
		return kbm_st_get( $base . 'cart/add-item', array( 'method' => 'POST', 'headers' => $hdr, 'body' => wp_json_encode( $body ) ) );
	};
	$tests = array(
		array( 'woda-naleczowianka-niegazowana-0-5-l', 2, array() ),
		array( 'kawa-duza-biala', 1, array() ),
		array( 'silownia-z-trenerem', 1, array() ),
		array( 'trening-wdrozeniowy-konsultacja', 1, array() ),
		array( 'pakiet-10-treningow-personalnych', 1, array() ),
	);
	foreach ( $tests as $t ) {
		$pid = kbm_find_post( $t[0], 'product' );
		list( $c, $b ) = $add( $pid, $t[1] );
		$j     = json_decode( $b, true );
		$log[] = 'Dodaj ' . $t[0] . ' x' . $t[1] . ': HTTP ' . $c . ( isset( $j['message'] ) ? ' ' . $j['message'] : '' );
	}
	// variable product: Trening personalny with a chosen trainer
	$pt = wc_get_product( kbm_find_post( 'trening-personalny', 'product' ) );
	if ( $pt && $pt->is_type( 'variable' ) ) {
		$vars = $pt->get_available_variations();
		$log[] = 'Trening personalny: ' . count( $vars ) . ' wariantów (trenerów): ' . implode( ', ', array_map( function ( $v ) {
			return implode( '', $v['attributes'] ) . ' ' . $v['display_price'] . ' zł';
		}, $vars ) );
		if ( $vars ) {
			$v0 = $vars[0];
			$va = array();
			foreach ( $v0['attributes'] as $k => $val ) {
				$va[] = array( 'attribute' => str_replace( 'attribute_', '', $k ), 'value' => $val );
			}
			list( $c, $b ) = $add( $pt->get_id(), 1, $va );
			$j     = json_decode( $b, true );
			$log[] = 'Dodaj trening personalny (' . implode( '', $v0['attributes'] ) . '): HTTP ' . $c . ( isset( $j['message'] ) ? ' ' . $j['message'] : '' );
		}
	}
	list( $c, $b ) = kbm_st_get( $base . 'cart', array( 'headers' => $hdr ) );
	$j = json_decode( $b, true );
	if ( is_array( $j ) ) {
		foreach ( (array) ( $j['items'] ?? array() ) as $it ) {
			$log[] = '  pozycja: ' . $it['name'] . ' x' . $it['quantity'] . ' = ' . ( $it['totals']['line_total'] / 100 ) . ' zł';
		}
		foreach ( (array) ( $j['fees'] ?? array() ) as $f ) {
			$log[] = '  opłata: ' . $f['name'] . ' = ' . ( $f['totals']['total'] / 100 ) . ' zł';
		}
		$log[] = '  RAZEM: ' . ( ( $j['totals']['total_price'] ?? 0 ) / 100 ) . ' zł, wysyłka potrzebna: ' . ( ! empty( $j['needs_shipping'] ) ? 'tak' : 'nie' ) . ', płatność potrzebna: ' . ( ! empty( $j['needs_payment'] ) ? 'tak' : 'nie' );
		$log[] = '  metody płatności: ' . implode( ', ', (array) ( $j['payment_methods'] ?? array() ) );
		foreach ( (array) ( $j['errors'] ?? array() ) as $e ) {
			$log[] = '  BŁĄD koszyka: ' . ( $e['message'] ?? '' );
		}
	}
	return $log;
}

add_action( 'init', function () {
	if ( (int) get_option( 'kbm_selftest_done' ) >= KBM_SELFTEST_REV || get_transient( 'kbm_selftest_lock' ) || is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}
	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && 'KBM-Selftest' === $_SERVER['HTTP_USER_AGENT'] ) {
		return;
	}
	if ( function_exists( 'kbm_auto_pending' ) && get_option( 'kbm_auto_install' ) && kbm_auto_pending() ) {
		return; // installer first
	}
	add_action( 'shutdown', function () {
		set_transient( 'kbm_selftest_lock', 1, 6 * MINUTE_IN_SECONDS );
		update_option( 'kbm_selftest_done', KBM_SELFTEST_REV, false );
		if ( function_exists( 'fastcgi_finish_request' ) ) {
			fastcgi_finish_request();
		} elseif ( function_exists( 'litespeed_finish_request' ) ) {
			litespeed_finish_request();
		}
		ignore_user_abort( true );
		kbm_selftest_run();
		delete_transient( 'kbm_selftest_lock' );
	}, 1000 );
}, 100 );

/* The real button path: GET checkout?add-to-cart=… with cookies, like a browser. */
function kbm_selftest_checkout() {
	$log   = array();
	$cases = array();
	$s     = wc_get_product( kbm_find_post( 'silownia', 'product' ) );
	if ( $s && $s->is_type( 'variable' ) ) {
		$vs = $s->get_available_variations( 'objects' );
		if ( $vs ) {
			$v = $vs[1] ?? $vs[0];
			$q = array( 'add-to-cart' => $s->get_id(), 'variation_id' => $v->get_id() );
			foreach ( $v->get_variation_attributes() as $k => $val ) {
				$q[ $k ] = $val;
			}
			$cases['Siłownia (wariant 2)'] = $q;
		}
	}
	$cases['Siłownia z trenerem'] = array( 'add-to-cart' => kbm_find_post( 'silownia-z-trenerem', 'product' ) );
	$cases['Woda 0,5 l (kaucja)'] = array( 'add-to-cart' => kbm_find_post( 'woda-naleczowianka-niegazowana-0-5-l', 'product' ) );
	foreach ( $cases as $label => $q ) {
		$url     = add_query_arg( $q, wc_get_checkout_url() );
		$cookies = array();
		$body    = '';
		$code    = 0;
		for ( $i = 0; $i < 4; $i++ ) {
			$r = wp_remote_get( $url, array( 'timeout' => 20, 'redirection' => 0, 'sslverify' => false, 'cookies' => array_values( $cookies ), 'user-agent' => 'KBM-Selftest' ) );
			if ( is_wp_error( $r ) ) {
				$log[] = $label . ': błąd ' . $r->get_error_message();
				continue 2;
			}
			foreach ( wp_remote_retrieve_cookies( $r ) as $c ) {
				$cookies[ $c->name ] = $c;
			}
			$code = (int) wp_remote_retrieve_response_code( $r );
			$loc  = wp_remote_retrieve_header( $r, 'location' );
			if ( $code >= 300 && $code < 400 && $loc ) {
				$url = $loc;
				continue;
			}
			$body = wp_remote_retrieve_body( $r );
			break;
		}
		$name  = wp_strip_all_tags( get_the_title( $q['add-to-cart'] ) );
		$has   = false !== strpos( $body, esc_html( $name ) ) || false !== strpos( $body, $name );
		$form  = false !== strpos( $body, 'woocommerce-checkout' );
		$pay   = preg_match_all( '/<li class="wc_payment_method/', $body );
		$nopay = false !== stripos( $body, 'brak dostępnych metod płatności' ) || false !== stripos( $body, 'no available payment methods' );
		$err   = preg_match_all( '/<ul class="woocommerce-error"[^>]*>(.*?)<\/ul>/s', $body, $m ) ? wp_strip_all_tags( implode( ' ', $m[1] ) ) : '';
		$fee   = false !== strpos( $body, 'Kaucja' );
		if ( 'Woda 0,5 l (kaucja)' === $label ) {
			$sd = trailingslashit( wp_upload_dir()['basedir'] ) . 'kbm-snap/';
			wp_mkdir_p( $sd );
			file_put_contents( $sd . 'p18.html', $body );
			$rc = wp_remote_get( wc_get_cart_url(), array( 'timeout' => 20, 'sslverify' => false, 'cookies' => array_values( $cookies ), 'user-agent' => 'KBM-Selftest' ) );
			if ( ! is_wp_error( $rc ) ) {
				file_put_contents( $sd . 'p19.html', wp_remote_retrieve_body( $rc ) );
			}
		}
		$log[] = $label . ': HTTP ' . $code . ' -> ' . str_replace( home_url(), '', $url ) . ' | produkt w zamówieniu: ' . ( $has ? 'tak' : 'NIE' ) . ' | formularz zamówienia: ' . ( $form ? 'tak' : 'NIE' ) . ' | metody płatności: ' . $pay . ( $nopay ? ' (komunikat: brak metod płatności)' : '' ) . ( $fee ? ' | kaucja: tak' : '' ) . ( $err ? ' | BŁĄD: ' . trim( preg_replace( '/\s+/', ' ', $err ) ) : '' );
	}
	return $log;
}
