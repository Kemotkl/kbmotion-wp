<?php
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce tweaks for a gym:
 * - memberships & PT packages go straight to checkout (max 3 clicks),
 * - virtual-only checkout without address fields, optional invoice (NIP),
 * - membership start date, staff e-mail "activate in eFitness",
 * - bufet orders are picked up in the studio.
 */

if ( ! function_exists( 'kbm_cart_has_cat' ) ) {
	function kbm_cart_has_cat( $cat ) {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return false;
		}
		foreach ( WC()->cart->get_cart() as $item ) {
			$pid = $item['product_id'];
			if ( has_term( $cat, 'product_cat', $pid ) ) {
				return true;
			}
		}
		return false;
	}
}

function kbm_order_has_cat( $order, $cat ) {
	foreach ( $order->get_items() as $item ) {
		if ( has_term( $cat, 'product_cat', $item->get_product_id() ) ) {
			return true;
		}
	}
	return false;
}

/* 1. Direct checkout for memberships and PT. */
add_filter( 'woocommerce_add_to_cart_redirect', function ( $url, $product = null ) {
	$pid = $product instanceof WC_Product ? ( $product->get_parent_id() ?: $product->get_id() ) : absint( $_REQUEST['add-to-cart'] ?? 0 );
	if ( $pid && ( has_term( array( 'karnety', 'dla-trenerow', 'treningi-personalne' ), 'product_cat', $pid ) ) ) {
		return wc_get_checkout_url();
	}
	return $url;
}, 10, 2 );

/* Do not show "added to cart" notice on checkout after a direct buy. */
add_filter( 'wc_add_to_cart_message_html', function ( $msg ) {
	return is_checkout() ? '' : $msg;
} );

/* Single-product button label. */
add_filter( 'woocommerce_product_single_add_to_cart_text', function ( $t, $p ) {
	if ( has_term( array( 'karnety', 'dla-trenerow', 'treningi-personalne' ), 'product_cat', $p->get_id() ) ) {
		return 'Kup teraz';
	}
	return $t;
}, 10, 2 );

/* 2. Checkout fields. */
add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
	if ( ! WC()->cart || WC()->cart->needs_shipping() ) {
		return $fields;
	}
	foreach ( array( 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_postcode', 'billing_state' ) as $f ) {
		unset( $fields['billing'][ $f ] );
	}
	if ( isset( $fields['billing']['billing_country'] ) ) {
		$fields['billing']['billing_country']['class'][] = 'kbm-hidden';
	}
	$fields['billing']['billing_phone']['required'] = true;
	$fields['billing']['billing_phone']['priority'] = 25;
	$fields['billing']['billing_email']['priority'] = 22;

	$fields['billing']['billing_company']['label']    = 'Firma (do faktury)';
	$fields['billing']['billing_company']['priority'] = 90;
	$fields['billing']['billing_company']['required'] = false;
	$fields['billing']['billing_nip']                 = array(
		'label'    => 'NIP (jeśli chcesz fakturę)',
		'required' => false,
		'priority' => 91,
		'class'    => array( 'form-row-wide' ),
	);

	if ( kbm_cart_has_cat( array( 'karnety', 'dla-trenerow' ) ) ) {
		$fields['order']['kbm_start_date'] = array(
			'type'              => 'date',
			'label'             => 'Od kiedy chcesz zacząć karnet?',
			'required'          => true,
			'default'           => wp_date( 'Y-m-d' ),
			'custom_attributes' => array( 'min' => wp_date( 'Y-m-d' ), 'max' => wp_date( 'Y-m-d', time() + 60 * DAY_IN_SECONDS ) ),
			'priority'          => 1,
		);
	}
	$fields['order']['order_comments']['label']       = 'Uwagi do zamówienia';
	$fields['order']['order_comments']['placeholder'] = 'Np. preferowany trener, pytania';
	return $fields;
} );

add_action( 'woocommerce_after_checkout_validation', function ( $data, $errors ) {
	if ( ! empty( $_POST['billing_nip'] ) ) {
		$nip = preg_replace( '/[^0-9]/', '', wp_unslash( $_POST['billing_nip'] ) );
		if ( 10 !== strlen( $nip ) ) {
			$errors->add( 'validation', 'NIP powinien mieć 10 cyfr.' );
		}
	}
	if ( isset( $_POST['kbm_start_date'] ) ) {
		$d = sanitize_text_field( wp_unslash( $_POST['kbm_start_date'] ) );
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $d ) || $d < wp_date( 'Y-m-d' ) ) {
			$errors->add( 'validation', 'Wybierz poprawną datę rozpoczęcia karnetu (dziś lub później).' );
		}
	}
}, 10, 2 );

add_action( 'woocommerce_checkout_create_order', function ( $order ) {
	if ( ! empty( $_POST['billing_nip'] ) ) {
		$order->update_meta_data( '_billing_nip', sanitize_text_field( wp_unslash( $_POST['billing_nip'] ) ) );
	}
	if ( ! empty( $_POST['kbm_start_date'] ) ) {
		$order->update_meta_data( '_kbm_start_date', sanitize_text_field( wp_unslash( $_POST['kbm_start_date'] ) ) );
	}
} );

add_action( 'woocommerce_admin_order_data_after_billing_address', function ( $order ) {
	if ( $order->get_meta( '_billing_nip' ) ) {
		echo '<p><strong>NIP:</strong> ' . esc_html( $order->get_meta( '_billing_nip' ) ) . '</p>';
	}
	if ( $order->get_meta( '_kbm_start_date' ) ) {
		echo '<p><strong>Start karnetu:</strong> ' . esc_html( $order->get_meta( '_kbm_start_date' ) ) . '</p>';
	}
} );

add_filter( 'woocommerce_email_order_meta_fields', function ( $fields, $sent_to_admin, $order ) {
	if ( $order->get_meta( '_kbm_start_date' ) ) {
		$fields['kbm_start'] = array( 'label' => 'Start karnetu', 'value' => $order->get_meta( '_kbm_start_date' ) );
	}
	if ( $order->get_meta( '_billing_nip' ) ) {
		$fields['kbm_nip'] = array( 'label' => 'NIP', 'value' => $order->get_meta( '_billing_nip' ) );
	}
	return $fields;
}, 10, 3 );

/* Notices on checkout. */
add_action( 'woocommerce_review_order_before_payment', function () {
	if ( kbm_cart_has_cat( array( 'karnety', 'dla-trenerow' ) ) ) {
		echo '<div class="kbm-checkout-note"><strong>Co dalej?</strong> Po opłaceniu aktywujemy karnet w systemie klubowym od wybranej daty i wyślemy Ci zaproszenie do aplikacji KB Motion z kodem QR do wejścia.</div>';
	}
	if ( kbm_cart_has_cat( 'treningi-personalne' ) ) {
		echo '<div class="kbm-checkout-note"><strong>Termin treningu</strong> ustalimy telefonicznie lub mailowo w ciągu jednego dnia roboczego. Przy pakietach potrzebny jest aktywny karnet.</div>';
	}
	if ( kbm_cart_has_cat( 'bufet' ) ) {
		echo '<div class="kbm-checkout-note"><strong>Odbiór w studiu:</strong> produkty z bufetu odbierzesz w KB Motion, ' . esc_html( kbm_opt( 'street' ) ) . ', w godzinach otwarcia.</div>';
	}
} );

/* 3. Staff e-mail after payment. */
function kbm_staff_notify( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order || $order->get_meta( '_kbm_staff_notified' ) ) {
		return;
	}
	$has_m  = kbm_order_has_cat( $order, array( 'karnety', 'dla-trenerow' ) );
	$has_pt = kbm_order_has_cat( $order, 'treningi-personalne' );
	$has_b  = kbm_order_has_cat( $order, 'bufet' );
	if ( ! $has_m && ! $has_pt && ! $has_b ) {
		return;
	}
	$lines   = array();
	$lines[] = 'Zamówienie #' . $order->get_order_number() . ' (' . wc_price( $order->get_total() ) . ') zostało opłacone.';
	$lines[] = '';
	$lines[] = 'Klient: ' . $order->get_formatted_billing_full_name();
	$lines[] = 'E-mail: ' . $order->get_billing_email();
	$lines[] = 'Telefon: ' . $order->get_billing_phone();
	if ( $order->get_meta( '_kbm_start_date' ) ) {
		$lines[] = 'Start karnetu: ' . $order->get_meta( '_kbm_start_date' );
	}
	$lines[] = '';
	foreach ( $order->get_items() as $item ) {
		$lines[] = '• ' . $item->get_name() . ' × ' . $item->get_quantity();
	}
	$lines[] = '';
	if ( $has_m ) {
		$lines[] = 'DO ZROBIENIA: aktywuj karnet w eFitness od podanej daty i wyślij klientowi zaproszenie do aplikacji.';
	}
	if ( $has_pt ) {
		$lines[] = 'DO ZROBIENIA: skontaktuj się z klientem i ustal termin treningu.';
	}
	if ( $has_b ) {
		$lines[] = 'DO ZROBIENIA: przygotuj produkty z bufetu do odbioru w studiu.';
	}
	if ( $order->get_customer_note() ) {
		$lines[] = '';
		$lines[] = 'Uwagi klienta: ' . $order->get_customer_note();
	}
	$lines[] = '';
	$lines[] = 'Zamówienie: ' . $order->get_edit_order_url();
	$subject = $has_m ? '[KB Motion] Aktywuj karnet – zamówienie #' . $order->get_order_number() : '[KB Motion] Nowe zamówienie #' . $order->get_order_number();
	wp_mail( kbm_opt( 'staff_email' ) ?: get_option( 'admin_email' ), $subject, wp_strip_all_tags( html_entity_decode( implode( "\n", $lines ) ) ), array( 'Reply-To: ' . $order->get_billing_email() ) );
	$order->update_meta_data( '_kbm_staff_notified', 1 );
	$order->add_order_note( 'Wysłano powiadomienie do obsługi (' . ( $has_m ? 'aktywacja karnetu' : 'nowe zamówienie' ) . ').' );
	$order->save();
}
add_action( 'woocommerce_payment_complete', 'kbm_staff_notify' );
add_action( 'woocommerce_order_status_processing', 'kbm_staff_notify' );
add_action( 'woocommerce_order_status_completed', 'kbm_staff_notify' );

/* Extra line in the customer "processing" e-mail. */
add_action( 'woocommerce_email_before_order_table', function ( $order, $sent_to_admin ) {
	if ( $sent_to_admin ) {
		return;
	}
	if ( kbm_order_has_cat( $order, array( 'karnety', 'dla-trenerow' ) ) ) {
		echo '<p>Dziękujemy za zakup karnetu w KB Motion! Aktywujemy go od wybranej daty (zwykle tego samego dnia) i wyślemy Ci zaproszenie do aplikacji z kodem QR do wejścia.</p>';
	}
	if ( kbm_order_has_cat( $order, 'treningi-personalne' ) ) {
		echo '<p>Odezwiemy się w ciągu jednego dnia roboczego, aby ustalić termin treningu.</p>';
	}
	if ( kbm_order_has_cat( $order, 'bufet' ) ) {
		echo '<p>Produkty z bufetu odbierzesz w studiu przy ' . esc_html( kbm_opt( 'street' ) ) . '.</p>';
	}
}, 5, 2 );

/* Ajax mini cart count for the header button. */
add_filter( 'woocommerce_add_to_cart_fragments', function ( $f ) {
	$f['span.kbm-cart-count'] = '<span class="kbm-cart-count">' . (int) WC()->cart->get_cart_contents_count() . '</span>';
	return $f;
} );

add_shortcode( 'kbm_cart_link', function () {
	if ( ! function_exists( 'wc_get_cart_url' ) ) {
		return '';
	}
	$n = WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
	return '<a class="kbm-cart" href="' . esc_url( wc_get_cart_url() ) . '" aria-label="Koszyk"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 7h12l-1 13H7L6 7Z"/><path d="M9 7a3 3 0 0 1 6 0"/></svg><span class="kbm-cart-count">' . $n . '</span></a>';
} );

/* Bufet: product grid columns and "related" off for services. */
add_filter( 'woocommerce_related_products', function ( $related, $product_id ) {
	return has_term( array( 'karnety', 'dla-trenerow', 'treningi-personalne' ), 'product_cat', $product_id ) ? array() : $related;
}, 10, 2 );

/* Bottle deposit (kaucja) 0,50 zł per bottle for products flagged by the installer (_kbm_deposit). */
add_action( 'woocommerce_cart_calculate_fees', function ( $cart ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return;
	}
	$sum = 0.0;
	$qty = 0;
	foreach ( $cart->get_cart() as $item ) {
		$dep = (float) get_post_meta( $item['product_id'], '_kbm_deposit', true );
		if ( $dep > 0 ) {
			$sum += $dep * (int) $item['quantity'];
			$qty += (int) $item['quantity'];
		}
	}
	if ( $sum > 0 ) {
		$cart->add_fee( sprintf( 'Kaucja za butelki (%d × 0,50 zł)', $qty ), round( $sum, 2 ), false );
	}
} );

add_action( 'woocommerce_single_product_summary', function () {
	global $product;
	if ( $product && (float) get_post_meta( $product->get_id(), '_kbm_deposit', true ) > 0 ) {
		echo '<p class="kbm-deposit-note">+ 0,50 zł kaucji za butelkę (zwrotna przy oddaniu butelki w studiu).</p>';
	}
}, 11 );

/* Single product: show the short description with its list (the excerpt block strips HTML). */
add_filter( 'render_block_core/post-excerpt', function ( $html ) {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return $html;
	}
	$p = get_post();
	if ( ! $p || '' === trim( (string) $p->post_excerpt ) ) {
		return $html;
	}
	return '<div class="wp-block-post-excerpt kbm-short woocommerce-product-details__short-description">' . wp_kses_post( $p->post_excerpt ) . '</div>';
}, 20 );

/* Strings missing in the Polish WooCommerce translation. */
add_filter( 'ngettext_woocommerce', function ( $t, $single, $plural, $n ) {
	if ( 'Category:' === $single ) {
		return 1 === (int) $n ? 'Kategoria:' : 'Kategorie:';
	}
	if ( 'Tag:' === $single ) {
		return 1 === (int) $n ? 'Tag:' : 'Tagi:';
	}
	return $t;
}, 10, 4 );
add_filter( 'gettext_woocommerce', function ( $t, $orig ) {
	$map = array( 'Category:' => 'Kategoria:', 'Categories:' => 'Kategorie:' );
	return $map[ $orig ] ?? $t;
}, 10, 2 );
add_filter( 'render_block_core/post-terms', function ( $html ) {
	return str_replace( array( '__prefix">Category: <', '__prefix">Categories: <', '__prefix">Tags: <', '__prefix">Tag: <' ), array( '__prefix">Kategoria: <', '__prefix">Kategorie: <', '__prefix">Tagi: <', '__prefix">Tag: <' ), $html );
} );
