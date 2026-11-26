<?php
defined( 'ABSPATH' ) || exit;

/* =========================================================
 * FAQ data (also used by the AI assistant and FAQPage schema)
 * ======================================================= */
function kbm_faq_data() {
	return array(
		'karnety'  => array(
			array( 'Jaki karnet wybrać na start?', 'Jeśli chcesz trenować samodzielnie, wybierz karnet Siłownia: 399 zł miesięcznie bez zobowiązań albo taniej w umowie na 6 miesięcy (379 zł/mies.) lub 12 miesięcy (369 zł/mies.). Jeśli trenujesz z trenerem personalnym, wystarczy karnet Siłownia z trenerem za 219 zł miesięcznie.' ),
			array( 'Jak wygląda wejście do studia po zakupie?', 'Po opłaceniu zamówienia aktywujemy Twój karnet w systemie klubowym (zwykle tego samego dnia) i wysyłamy zaproszenie do aplikacji KB Motion. Do studia wchodzisz na kod QR z aplikacji, w godzinach 06:00–23:00.' ),
			array( 'Czy mogę przyjść tylko raz?', 'Tak. Wejście jednorazowe na siłownię kosztuje 49 zł, a na zajęcia grupowe 45 zł. Kupisz je online w kilka sekund.' ),
			array( 'Czy karnet można przekazać innej osobie?', 'Nie. Karnety są imienne i przypisane do Twojego konta w aplikacji.' ),
			array( 'Jakie formy płatności akceptujecie?', 'Płacisz online kartą, BLIK-iem lub szybkim przelewem. Potwierdzenie i paragon dostajesz na e-mail.' ),
			array( 'Czy mogę odstąpić od umowy?', 'Kupując online jako konsument, możesz odstąpić od umowy w ciągu 14 dni. Jeśli na Twoje wyraźne żądanie zaczniesz korzystać z usługi wcześniej, zwracamy kwotę proporcjonalną do niewykorzystanej części. Szczegóły znajdziesz w regulaminie sklepu.' ),
		),
		'treningi' => array(
			array( 'Jak zacząć współpracę z trenerem?', 'Najlepiej od treningu wdrożeniowego z konsultacją (ok. 60 minut, 99 zł). Poznamy Twoje cele, sprawdzimy technikę i ułożymy plan. Możesz też zapisać się na bezpłatną konsultację.' ),
			array( 'Ile kosztuje trening personalny?', 'Pierwszy trening wdrożeniowy z konsultacją kosztuje 99 zł. Kolejne treningi zależą od trenera: 200 zł (Piotr, Tomasz, Patryk, Kasia) lub 150 zł (Gustave, Aleksander, Paweł). Pakiet 10 treningów z trenerem klubowym kosztuje 1499 zł.' ),
			array( 'Czy do treningów personalnych potrzebuję karnetu?', 'Tak, do treningów i pakietów potrzebny jest aktywny karnet na wejście do studia. Najkorzystniejszy jest karnet Siłownia z trenerem (219 zł/mies.) albo Siłownia 4 wejścia w miesiącu (149 zł).' ),
			array( 'Czy mogę wybrać konkretnego trenera?', 'Tak. Przy pojedynczym treningu wybierasz trenera przy zakupie albo w formularzu rezerwacji, gdzie przy każdym nazwisku widać cenę. W pakiecie 10 treningów trenera dobiera klub.' ),
			array( 'Jak odwołać trening?', 'Trening można odwołać lub przełożyć najpóźniej 24 godziny przed jego rozpoczęciem. Później trening może zostać uznany za zrealizowany.' ),
			array( 'Mam kontuzję. Czy mogę trenować?', 'W większości przypadków tak, a trening z trenerem jest wtedy najbezpieczniejszy. Przed pierwszym treningiem wypełnisz krótką ankietę zdrowotną PAR-Q, a trener dopasuje ćwiczenia do Twoich możliwości.' ),
		),
		'ogolne'   => array(
			array( 'Gdzie jest studio i w jakich godzinach działa?', 'KB Motion mieści się przy ul. Nyskiej 61A we Wrocławiu. Studio jest otwarte codziennie od 06:00 do 23:00.' ),
			array( 'Czym KB Motion różni się od zwykłej siłowni?', 'To kameralne studio treningu personalnego o powierzchni 250 m². Zamiast tłumu masz spokojną przestrzeń, strefę siłową i salę fitness oraz trenerów, którzy znają Cię z imienia.' ),
			array( 'Jak zapisać się na zajęcia grupowe?', 'Na zajęcia zapisujesz się w aplikacji KB Motion. Wybierasz karnet na 4 lub 8 wejść w miesiącu albo wejście jednorazowe.' ),
			array( 'Czy mogę kupić coś do picia na miejscu?', 'Tak. W bufecie są woda, kawa, napoje, odżywki KFD i ręczniki KB Motion. Możesz je też zamówić online i odebrać w studiu.' ),
		),
	);
}

add_shortcode( 'kbm_faq', function ( $atts ) {
	$atts = shortcode_atts( array( 'set' => 'ogolne' ), $atts );
	$data = kbm_faq_data();
	$sets = array_map( 'trim', explode( ',', $atts['set'] ) );
	$html = '<div class="kbm-faq">';
	foreach ( $sets as $set ) {
		foreach ( $data[ $set ] ?? array() as $i => $qa ) {
			$html .= '<details class="kbm-faq__item"' . ( 0 === $i && $set === $sets[0] ? ' open' : '' ) . '><summary>' . esc_html( $qa[0] ) . '</summary><div class="kbm-faq__a"><p>' . esc_html( $qa[1] ) . '</p></div></details>';
		}
	}
	$GLOBALS['kbm_faq_used'] = array_merge( $GLOBALS['kbm_faq_used'] ?? array(), $sets );
	return $html . '</div>';
} );

/* =========================================================
 * Pricing cards from WooCommerce
 * ======================================================= */
add_shortcode( 'kbm_pricing', function ( $atts ) {
	$atts = shortcode_atts( array( 'cat' => 'karnety', 'limit' => 12, 'ids' => '' ), $atts );
	if ( ! function_exists( 'wc_get_products' ) ) {
		return current_user_can( 'manage_options' ) ? '<p class="kbm-note">Włącz WooCommerce i uruchom instalator KB Motion, aby wyświetlić cennik.</p>' : '';
	}
	$args = array( 'status' => 'publish', 'limit' => (int) $atts['limit'], 'orderby' => 'menu_order', 'order' => 'ASC', 'visibility' => 'visible' );
	if ( $atts['ids'] ) {
		$args['include'] = array_map( 'absint', explode( ',', $atts['ids'] ) );
		$args['orderby'] = 'include';
	} else {
		$args['category'] = array_map( 'trim', explode( ',', $atts['cat'] ) );
	}
	$products = wc_get_products( $args );
	if ( ! $products ) {
		return '';
	}
	$checkout = wc_get_checkout_url();
	ob_start();
	echo '<div class="kbm-pricing">';
	foreach ( $products as $p ) {
		$featured = $p->is_featured();
		$unit     = $p->get_meta( '_kbm_unit' );
		echo '<article class="kbm-price' . ( $featured ? ' is-featured' : '' ) . '">';
		if ( $featured ) {
			echo '<span class="kbm-price__badge">Najczęściej wybierany</span>';
		}
		echo '<h3 class="kbm-price__name">' . esc_html( $p->get_name() ) . '</h3>';
		echo '<form class="kbm-price__form" method="get" action="' . esc_url( $checkout ) . '">';
		if ( $p->is_type( 'variable' ) ) {
			$vars  = $p->get_available_variations( 'objects' );
			$first = true;
			echo '<fieldset class="kbm-price__vars"><legend class="screen-reader-text">Wybierz wariant</legend>';
			foreach ( $vars as $v ) {
				$attrs = $v->get_variation_attributes();
				$label = implode( ' · ', array_map( 'wc_clean', array_values( $attrs ) ) );
				$vunit = $v->get_meta( '_kbm_unit' );
				$json  = wp_json_encode( $attrs );
				printf(
					'<label class="kbm-var"><input type="radio" name="variation_id" value="%1$d" data-attrs="%2$s" %3$s><span class="kbm-var__label">%4$s</span><span class="kbm-var__price">%5$s%6$s</span></label>',
					(int) $v->get_id(),
					esc_attr( $json ),
					$first ? 'checked' : '',
					esc_html( $label ),
					wp_kses_post( wc_price( wc_get_price_to_display( $v ), array( 'decimals' => 0 ) ) ),
					$vunit ? '<small> ' . esc_html( $vunit ) . '</small>' : ''
				);
				if ( $first ) {
					foreach ( $attrs as $k => $val ) {
						echo '<input type="hidden" class="kbm-attr" name="' . esc_attr( $k ) . '" value="' . esc_attr( $val ) . '">';
					}
				}
				$first = false;
			}
			echo '</fieldset>';
			echo '<input type="hidden" name="add-to-cart" value="' . (int) $p->get_id() . '">';
		} else {
			echo '<p class="kbm-price__amount">' . wp_kses_post( wc_price( wc_get_price_to_display( $p ), array( 'decimals' => 0 ) ) ) . ( $unit ? '<small> ' . esc_html( $unit ) . '</small>' : '' ) . '</p>';
			echo '<input type="hidden" name="add-to-cart" value="' . (int) $p->get_id() . '">';
		}
		$desc = $p->get_short_description();
		if ( $desc ) {
			echo '<div class="kbm-price__desc">' . wp_kses_post( wpautop( $desc ) ) . '</div>';
		}
		echo '<button type="submit" class="kbm-btn kbm-btn--' . ( $featured ? 'gold' : 'dark' ) . ' kbm-price__cta">' . esc_html( $p->get_meta( '_kbm_cta' ) ?: 'Kup teraz' ) . '</button>';
		echo '</form></article>';
	}
	echo '</div>';
	return ob_get_clean();
} );

/* =========================================================
 * Trainers grid
 * ======================================================= */
add_shortcode( 'kbm_trainers', function ( $atts ) {
	$atts = shortcode_atts( array( 'owners' => '', 'limit' => -1 ), $atts );
	$args = array( 'post_type' => 'kbm_trainer', 'posts_per_page' => (int) $atts['limit'], 'orderby' => 'menu_order', 'order' => 'ASC' );
	if ( '1' === $atts['owners'] ) {
		$args['meta_query'] = array( array( 'key' => '_kbm_is_owner', 'value' => '1' ) );
	} elseif ( '0' === $atts['owners'] ) {
		$args['meta_query'] = array( 'relation' => 'OR', array( 'key' => '_kbm_is_owner', 'compare' => 'NOT EXISTS' ), array( 'key' => '_kbm_is_owner', 'value' => '1', 'compare' => '!=' ) );
	}
	$q = new WP_Query( $args );
	if ( ! $q->have_posts() ) {
		return '';
	}
	$book = kbm_url( 'treningi-personalne' );
	ob_start();
	echo '<div class="kbm-trainers">';
	while ( $q->have_posts() ) {
		$q->the_post();
		$id    = get_the_ID();
		$name  = get_the_title();
		$first = strtok( $name, ' ' );
		$role  = get_post_meta( $id, '_kbm_role', true );
		$tags  = array_filter( array_map( 'trim', explode( ',', (string) get_post_meta( $id, '_kbm_tags', true ) ) ) );
		echo '<article class="kbm-trainer">';
		echo '<div class="kbm-trainer__img">';
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'large', array( 'loading' => 'lazy', 'alt' => esc_attr( $name . ' – trener KB Motion Wrocław' ) ) );
		} else {
			echo '<span class="kbm-trainer__initials" aria-hidden="true">' . esc_html( mb_substr( $first, 0, 1 ) . mb_substr( (string) strrchr( $name, ' ' ), 1, 1 ) ) . '</span>';
		}
		echo '</div><div class="kbm-trainer__body">';
		if ( $role ) {
			echo '<p class="kbm-eyebrow">' . esc_html( $role ) . '</p>';
		}
		echo '<h3 class="kbm-trainer__name">' . esc_html( $name ) . '</h3>';
		if ( $tags ) {
			echo '<ul class="kbm-tags">';
			foreach ( $tags as $t ) {
				echo '<li>' . esc_html( $t ) . '</li>';
			}
			echo '</ul>';
		}
		$price = (int) get_post_meta( $id, '_kbm_price', true );
		if ( $price ) {
			echo '<p class="kbm-trainer__price">' . esc_html( $price ) . ' zł / trening</p>';
		}
		$ex = get_the_excerpt();
		if ( $ex && $ex !== $role ) {
			echo '<p class="kbm-trainer__bio">' . esc_html( $ex ) . '</p>';
		}
		echo '<a class="kbm-link-arrow" href="' . esc_url( add_query_arg( 'trener', rawurlencode( $name ), $book ) . '#rezerwacja' ) . '">Umów trening z ' . esc_html( $first ) . '</a>';
		echo '</div></article>';
	}
	wp_reset_postdata();
	echo '</div>';
	return ob_get_clean();
} );

/* =========================================================
 * Reviews
 * ======================================================= */
add_shortcode( 'kbm_reviews', function () {
	$q = new WP_Query( array( 'post_type' => 'kbm_review', 'posts_per_page' => 9, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
	if ( ! $q->have_posts() ) {
		return '';
	}
	ob_start();
	echo '<div class="kbm-reviews">';
	while ( $q->have_posts() ) {
		$q->the_post();
		$stars  = max( 1, min( 5, (int) ( get_post_meta( get_the_ID(), '_kbm_stars', true ) ?: 5 ) ) );
		$source = get_post_meta( get_the_ID(), '_kbm_source', true );
		echo '<figure class="kbm-review"><div class="kbm-review__stars" aria-label="Ocena ' . (int) $stars . ' na 5">' . str_repeat( '★', $stars ) . '</div>';
		echo '<blockquote>' . wp_kses_post( wpautop( get_the_content() ) ) . '</blockquote>';
		echo '<figcaption><strong>' . esc_html( get_the_title() ) . '</strong>' . ( $source ? ' · ' . esc_html( $source ) : '' ) . '</figcaption></figure>';
	}
	wp_reset_postdata();
	echo '</div>';
	return ob_get_clean();
} );

/* Wraps a whole section and hides it when there are no reviews. */
add_shortcode( 'kbm_if_reviews', function ( $atts, $content = '' ) {
	$has = (bool) get_posts( array( 'post_type' => 'kbm_review', 'numberposts' => 1, 'fields' => 'ids' ) );
	return $has ? do_shortcode( $content ) : '';
} );

/* =========================================================
 * Class schedule
 * ======================================================= */
function kbm_get_schedule() {
	$posts = get_posts( array( 'post_type' => 'kbm_class', 'numberposts' => 200, 'post_status' => 'publish' ) );
	$out   = array();
	foreach ( $posts as $p ) {
		$out[] = array(
			'name'    => $p->post_title,
			'day'     => (int) get_post_meta( $p->ID, '_kbm_day', true ),
			'start'   => (string) get_post_meta( $p->ID, '_kbm_start', true ),
			'end'     => (string) get_post_meta( $p->ID, '_kbm_end', true ),
			'trainer' => (string) get_post_meta( $p->ID, '_kbm_trainer', true ),
			'level'   => (string) get_post_meta( $p->ID, '_kbm_level', true ),
		);
	}
	usort( $out, function ( $a, $b ) {
		return array( $a['day'], $a['start'] ) <=> array( $b['day'], $b['start'] );
	} );
	return $out;
}

add_shortcode( 'kbm_schedule', function () {
	$items = kbm_get_schedule();
	$app   = trailingslashit( kbm_opt( 'efitness_url' ) ) . 'kalendarz-zajec';
	if ( ! $items ) {
		return '<div class="kbm-schedule-empty"><p>Aktualny grafik zajęć grupowych z wolnymi miejscami sprawdzisz i zarezerwujesz w aplikacji KB Motion.</p><p><a class="kbm-btn kbm-btn--gold" href="' . esc_url( $app ) . '" target="_blank" rel="noopener">Zobacz grafik i zapisz się</a></p></div>';
	}
	$days = kbm_days();
	$by   = array();
	foreach ( $items as $it ) {
		$by[ $it['day'] ][] = $it;
	}
	ob_start();
	echo '<div class="kbm-schedule" role="list">';
	foreach ( $days as $n => $label ) {
		echo '<section class="kbm-schedule__day" role="listitem"><h3>' . esc_html( $label ) . '</h3>';
		if ( empty( $by[ $n ] ) ) {
			echo '<p class="kbm-schedule__none">Trening własny i treningi personalne</p>';
		} else {
			foreach ( $by[ $n ] as $it ) {
				echo '<div class="kbm-class"><span class="kbm-class__time">' . esc_html( $it['start'] . ( $it['end'] ? '–' . $it['end'] : '' ) ) . '</span><strong class="kbm-class__name">' . esc_html( $it['name'] ) . '</strong>';
				if ( $it['trainer'] || $it['level'] ) {
					echo '<span class="kbm-class__meta">' . esc_html( trim( $it['trainer'] . ( $it['trainer'] && $it['level'] ? ' · ' : '' ) . $it['level'] ) ) . '</span>';
				}
				echo '</div>';
			}
		}
		echo '</section>';
	}
	echo '</div><p class="kbm-schedule__cta"><a class="kbm-btn kbm-btn--gold" href="' . esc_url( $app ) . '" target="_blank" rel="noopener">Zarezerwuj miejsce w aplikacji</a></p>';
	return ob_get_clean();
} );

/* =========================================================
 * Contact data + privacy-friendly map
 * ======================================================= */
add_shortcode( 'kbm_contact', function () {
	$phone = kbm_opt( 'phone' );
	$html  = '<ul class="kbm-contact">';
	$html .= '<li><span class="kbm-contact__k">Adres</span><span>' . esc_html( kbm_opt( 'street' ) . ', ' . kbm_opt( 'postcode' ) . ' ' . kbm_opt( 'city' ) ) . '</span></li>';
	if ( $phone ) {
		$html .= '<li><span class="kbm-contact__k">Telefon</span><a href="' . esc_attr( kbm_phone_link() ) . '">' . esc_html( $phone ) . '</a></li>';
	}
	$html .= '<li><span class="kbm-contact__k">E-mail</span><a href="mailto:' . esc_attr( kbm_opt( 'email' ) ) . '">' . esc_html( kbm_opt( 'email' ) ) . '</a></li>';
	$html .= '<li><span class="kbm-contact__k">Godziny</span><span>' . esc_html( kbm_opt( 'hours' ) ) . '</span></li>';
	$html .= '</ul>';
	return $html;
} );

add_shortcode( 'kbm_map', function () {
	$q   = rawurlencode( 'KB Motion, ' . kbm_opt( 'street' ) . ', ' . kbm_opt( 'postcode' ) . ' ' . kbm_opt( 'city' ) );
	$src = 'https://www.google.com/maps?q=' . $q . '&output=embed';
	$nav = 'https://www.google.com/maps/dir/?api=1&destination=' . $q;
	return '<div class="kbm-map" data-src="' . esc_url( $src ) . '"><div class="kbm-map__inner"><p><strong>' . esc_html( kbm_opt( 'street' ) . ', ' . kbm_opt( 'city' ) ) . '</strong></p><p>Mapa Google ładuje się dopiero po kliknięciu, aby nie zapisywać cookies bez Twojej zgody.</p><p class="kbm-map__btns"><button type="button" class="kbm-btn kbm-btn--gold kbm-map__load">Pokaż mapę</button> <a class="kbm-btn kbm-btn--ghost" href="' . esc_url( $nav ) . '" target="_blank" rel="noopener">Wyznacz trasę</a></p></div></div>';
} );

/* Current year for the footer. */
add_shortcode( 'kbm_year', function () {
	return esc_html( wp_date( 'Y' ) );
} );

add_shortcode( 'kbm_company', function () {
	return esc_html( kbm_opt( 'company' ) . ' · NIP ' . kbm_opt( 'nip' ) . ' · ' . kbm_opt( 'company_address' ) );
} );

add_shortcode( 'kbm_opt', function ( $atts ) {
	$atts = shortcode_atts( array( 'key' => '' ), $atts );
	return in_array( $atts['key'], array( 'email', 'phone', 'street', 'city', 'postcode', 'hours', 'company', 'nip', 'company_address' ), true ) ? esc_html( kbm_opt( $atts['key'] ) ) : '';
} );

/* Social icons (rendered only when URLs are set). */
add_shortcode( 'kbm_social', function () {
	$out = '';
	foreach ( array( 'instagram' => 'Instagram', 'facebook' => 'Facebook' ) as $k => $label ) {
		$u = kbm_opt( $k );
		if ( $u ) {
			$out .= '<a href="' . esc_url( $u ) . '" target="_blank" rel="noopener">' . esc_html( $label ) . '</a>';
		}
	}
	return $out ? '<p class="kbm-social">' . $out . '</p>' : '';
} );

/* Shortcodes inside block-theme template parts. */
add_filter( 'render_block_core/shortcode', function ( $content ) {
	return do_shortcode( $content );
} );
