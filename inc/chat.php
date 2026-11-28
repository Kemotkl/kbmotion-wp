<?php
defined( 'ABSPATH' ) || exit;

/**
 * AI assistant. Calls the Claude Messages API (raw HTTP via wp_remote_post – no Composer in a WP plugin)
 * with a system prompt generated from live site data (WooCommerce prices, schedule, trainers, FAQ).
 * Without an API key it answers from the same knowledge with a deterministic keyword matcher.
 */

function kbm_knowledge() {
	$k   = array();
	$k[] = 'STUDIO: KB Motion – kameralne studio treningu personalnego, ' . kbm_opt( 'street' ) . ', ' . kbm_opt( 'postcode' ) . ' ' . kbm_opt( 'city' ) . '. Powierzchnia 250 m², strefa siłowa i sala fitness. Godziny: ' . kbm_opt( 'hours' ) . '. E-mail: ' . kbm_opt( 'email' ) . ( kbm_opt( 'phone' ) ? '. Telefon: ' . kbm_opt( 'phone' ) : '' ) . '. Wejście do studia na kod QR w aplikacji KB Motion (eFitness).';
	$k[] = 'ZAKUP: karnety, treningi i bufet kupuje się online na stronie (płatność kartą, BLIK, szybkim przelewem). Po zakupie karnetu obsługa aktywuje go w systemie klubowym i wysyła zaproszenie do aplikacji.';

	if ( function_exists( 'wc_get_products' ) ) {
		foreach ( array( 'karnety' => 'KARNETY', 'treningi-personalne' => 'TRENINGI PERSONALNE', 'bufet' => 'BUFET' ) as $cat => $label ) {
			$rows = array();
			foreach ( wc_get_products( array( 'status' => 'publish', 'limit' => 60, 'category' => array( $cat ) ) ) as $p ) {
				$desc = wp_strip_all_tags( $p->get_short_description() );
				if ( $p->is_type( 'variable' ) ) {
					$vs = array();
					foreach ( $p->get_available_variations( 'objects' ) as $v ) {
						$vs[] = implode( ' ', $v->get_variation_attributes() ) . ': ' . wc_format_decimal( $v->get_price(), 2 ) . ' zł ' . $v->get_meta( '_kbm_unit' );
					}
					$rows[] = '- ' . $p->get_name() . ' (' . implode( '; ', $vs ) . ')' . ( $desc ? ' – ' . $desc : '' );
				} else {
					$rows[] = '- ' . $p->get_name() . ': ' . wc_format_decimal( $p->get_price(), 2 ) . ' zł ' . $p->get_meta( '_kbm_unit' ) . ( $desc ? ' – ' . $desc : '' ) . ( $p->managing_stock() ? ' (stan: ' . (int) $p->get_stock_quantity() . ' szt.)' : '' );
				}
			}
			if ( $rows ) {
				$k[] = $label . ":\n" . implode( "\n", $rows );
			}
		}
	}

	$trainers = get_posts( array( 'post_type' => 'kbm_trainer', 'numberposts' => 50, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
	if ( $trainers ) {
		$rows = array();
		foreach ( $trainers as $t ) {
			$rows[] = '- ' . $t->post_title . ' – ' . get_post_meta( $t->ID, '_kbm_role', true ) . ( get_post_meta( $t->ID, '_kbm_tags', true ) ? ' (' . get_post_meta( $t->ID, '_kbm_tags', true ) . ')' : '' ) . ( $t->post_excerpt ? ': ' . $t->post_excerpt : '' );
		}
		$k[] = "TRENERZY:\n" . implode( "\n", $rows );
	}

	$sched = kbm_get_schedule();
	if ( $sched ) {
		$days = kbm_days();
		$rows = array();
		foreach ( $sched as $s ) {
			$rows[] = '- ' . $days[ $s['day'] ] . ' ' . $s['start'] . '–' . $s['end'] . ' ' . $s['name'] . ( $s['trainer'] ? ' (' . $s['trainer'] . ')' : '' );
		}
		$k[] = "GRAFIK ZAJĘĆ GRUPOWYCH:\n" . implode( "\n", $rows );
	} else {
		$k[] = 'GRAFIK ZAJĘĆ GRUPOWYCH: aktualny grafik z wolnymi miejscami jest w aplikacji KB Motion (eFitness). Nie podawaj godzin zajęć, odeślij do grafiku.';
	}

	$faq = array();
	foreach ( kbm_faq_data() as $set ) {
		foreach ( $set as $qa ) {
			$faq[] = 'P: ' . $qa[0] . "\nO: " . $qa[1];
		}
	}
	$k[] = "FAQ:\n" . implode( "\n", $faq );

	if ( kbm_opt( 'chat_extra' ) ) {
		$k[] = "DODATKOWE INFORMACJE OD STUDIA:\n" . kbm_opt( 'chat_extra' );
	}
	$k[] = 'LINKI: karnety ' . kbm_url( 'karnety' ) . ' | treningi personalne i rezerwacja trenera ' . kbm_url( 'treningi-personalne' ) . ' | bezpłatna konsultacja online ' . kbm_url( 'trening-probny' ) . ' | grafik ' . kbm_url( 'grafik-zajec' ) . ' | trenerzy ' . kbm_url( 'trenerzy' ) . ' | bufet ' . kbm_url( 'bufet' ) . ' | kontakt ' . kbm_url( 'kontakt' );
	return implode( "\n\n", $k );
}

function kbm_chat_system_prompt() {
	return "Jesteś asystentem na stronie KB Motion, kameralnego studia treningu personalnego we Wrocławiu. Rozmawiasz z osobami, które rozważają karnet lub trening personalny.\n\n"
		. "Zasady:\n"
		. "- Odpowiadaj po polsku (albo w języku rozmówcy), ciepło i konkretnie, w 2–5 krótkich zdaniach. Bez markdownu, nagłówków i tabel.\n"
		. "- Opieraj się wyłącznie na wiedzy poniżej. Jeśli czegoś nie wiesz (np. parking, prysznice, konkretne terminy trenera), powiedz to wprost i zaproponuj kontakt przez formularz lub e-mail.\n"
		. "- Pomóż wybrać najlepszą opcję i zaproponuj następny krok: zakup karnetu, bezpłatną konsultację online, konsultację z treningiem w studiu (99 zł) albo rezerwację trenera. Konsultacja online jest bezpłatna, konsultacja w studiu jest zawsze płatna 99 zł i obejmuje pierwszy trening. Podawaj ceny dokładnie tak, jak w cenniku.\n"
		. "- Nie udzielaj porad medycznych. Przy kontuzjach i chorobach zaproponuj konsultację z trenerem i ankietę PAR-Q.\n"
		. "- Nie zbieraj danych osobowych w czacie. Do zapisu odsyłaj do formularza.\n\n"
		. "WIEDZA O STUDIU:\n" . kbm_knowledge();
}

/** Suggested buttons based on the question and answer. */
function kbm_chat_actions( $text ) {
	$t   = mb_strtolower( $text );
	$map = array(
		array( '/karnet|cen|koszt|ile|płat|wejści|siłowni/u', 'Zobacz karnety', kbm_url( 'karnety' ) ),
		array( '/trener|trening personal|pakiet|indywidual|kontuzj|redukc|schud/u', 'Umów trening', kbm_url( 'treningi-personalne' ) . '#rezerwacja' ),
		array( '/próbn|konsultac|pierwsz|zacz/u', 'Bezpłatna konsultacja online', kbm_url( 'trening-probny' ) ),
		array( '/grafik|zajęci|grupow|hyrox/u', 'Grafik zajęć', kbm_url( 'grafik-zajec' ) ),
		array( '/bufet|kaw|wod|odżyw|białk|kreatyn|ręcznik/u', 'Bufet', kbm_url( 'bufet' ) ),
		array( '/adres|dojazd|gdzie|kontakt|telefon|mail|parking/u', 'Kontakt i dojazd', kbm_url( 'kontakt' ) ),
	);
	$out = array();
	foreach ( $map as $m ) {
		if ( preg_match( $m[0], $t ) ) {
			$out[] = array( 'label' => $m[1], 'url' => $m[2] );
		}
	}
	if ( ! $out ) {
		$out = array( array( 'label' => 'Zobacz karnety', 'url' => kbm_url( 'karnety' ) ), array( 'label' => 'Umów trening', 'url' => kbm_url( 'treningi-personalne' ) . '#rezerwacja' ) );
	}
	return array_slice( $out, 0, 3 );
}

/** Offline answer: best FAQ match by keyword overlap. */
function kbm_chat_offline( $question ) {
	$q     = mb_strtolower( $question );
	$words = array_filter( preg_split( '/[^\p{L}\p{N}]+/u', $q ), function ( $w ) {
		return mb_strlen( $w ) > 3;
	} );
	$best  = null;
	$score = 0;
	foreach ( kbm_faq_data() as $set ) {
		foreach ( $set as $qa ) {
			$hay = mb_strtolower( $qa[0] . ' ' . $qa[1] );
			$s   = 0;
			foreach ( $words as $w ) {
				$stem = mb_substr( $w, 0, max( 4, mb_strlen( $w ) - 2 ) );
				if ( false !== mb_strpos( $hay, $stem ) ) {
					$s += ( false !== mb_strpos( mb_strtolower( $qa[0] ), $stem ) ) ? 2 : 1;
				}
			}
			if ( $s > $score ) {
				$score = $s;
				$best  = $qa[1];
			}
		}
	}
	if ( $best && $score >= 2 ) {
		return $best;
	}
	return 'Chętnie pomogę! Mogę opowiedzieć o karnetach i cenach, treningach personalnych, trenerach, grafiku zajęć i bufecie. Jeśli wolisz porozmawiać z człowiekiem, napisz do nas na ' . kbm_opt( 'email' ) . ' albo zostaw wiadomość w formularzu kontaktowym.';
}

function kbm_chat_reply( array $messages ) {
	$key = kbm_opt( 'anthropic_key' );
	if ( ! $key ) {
		$last = end( $messages );
		return array( kbm_chat_offline( $last['content'] ), 'offline' );
	}
	$body = array(
		'model'         => kbm_opt( 'anthropic_model' ) ?: 'claude-opus-5-5',
		'max_tokens'    => 1024,
		'system'        => kbm_chat_system_prompt(),
		'messages'      => $messages,
		'output_config' => array( 'effort' => 'low' ),
		'fallbacks'     => 'default',
	);
	$resp = wp_remote_post( 'https://api.anthropic.com/v1/messages', array(
		'timeout' => 45,
		'headers' => array(
			'content-type'      => 'application/json',
			'x-api-key'         => $key,
			'anthropic-version' => '2026-10-21',
			'anthropic-beta'    => 'server-side-fallback-2026-07-01',
		),
		'body'    => wp_json_encode( $body ),
	) );
	if ( is_wp_error( $resp ) ) {
		$last = end( $messages );
		return array( kbm_chat_offline( $last['content'] ), 'error' );
	}
	$code = wp_remote_retrieve_response_code( $resp );
	$json = json_decode( wp_remote_retrieve_body( $resp ), true );
	if ( 200 !== $code || ! is_array( $json ) ) {
		error_log( 'KB Motion chat API error ' . $code . ': ' . wp_remote_retrieve_body( $resp ) );
		$last = end( $messages );
		return array( kbm_chat_offline( $last['content'] ), 'error' );
	}
	if ( 'refusal' === ( $json['stop_reason'] ?? '' ) ) {
		return array( 'Na to pytanie nie odpowiem w czacie. Napisz do nas na ' . kbm_opt( 'email' ) . ', a zespół KB Motion chętnie pomoże.', 'refusal' );
	}
	$text = '';
	foreach ( (array) ( $json['content'] ?? array() ) as $block ) {
		if ( 'text' === ( $block['type'] ?? '' ) ) {
			$text .= $block['text'];
		}
	}
	$text = trim( $text );
	if ( '' === $text ) {
		$last = end( $messages );
		return array( kbm_chat_offline( $last['content'] ), 'empty' );
	}
	return array( $text, 'ai' );
}

add_action( 'rest_api_init', function () {
	register_rest_route( 'kbm/v1', '/chat', array(
		'methods'             => 'POST',
		'permission_callback' => '__return_true',
		'callback'            => function ( WP_REST_Request $r ) {
			if ( ! kbm_opt( 'chat_enabled' ) ) {
				return new WP_REST_Response( array( 'error' => 'disabled' ), 403 );
			}
			$ip  = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' );
			$key = 'kbm_chat_' . md5( $ip );
			$cnt = (int) get_transient( $key );
			if ( $cnt >= 40 ) {
				return new WP_REST_Response( array( 'reply' => 'Dziękujemy za rozmowę! Limit wiadomości na teraz został wyczerpany. Napisz do nas na ' . kbm_opt( 'email' ) . '.', 'actions' => array() ), 429 );
			}
			set_transient( $key, $cnt + 1, HOUR_IN_SECONDS );

			$raw  = (array) $r->get_param( 'messages' );
			$msgs = array();
			foreach ( array_slice( $raw, -10 ) as $m ) {
				$role = ( $m['role'] ?? '' ) === 'assistant' ? 'assistant' : 'user';
				$c    = trim( mb_substr( sanitize_textarea_field( (string) ( $m['content'] ?? '' ) ), 0, 1000 ) );
				if ( '' === $c ) {
					continue;
				}
				// Merge consecutive same-role turns.
				if ( $msgs && end( $msgs )['role'] === $role ) {
					$msgs[ count( $msgs ) - 1 ]['content'] .= "\n" . $c;
				} else {
					$msgs[] = array( 'role' => $role, 'content' => $c );
				}
			}
			while ( $msgs && 'user' !== $msgs[0]['role'] ) {
				array_shift( $msgs );
			}
			if ( ! $msgs || 'user' !== end( $msgs )['role'] ) {
				return new WP_REST_Response( array( 'error' => 'empty' ), 400 );
			}
			list( $reply, $mode ) = kbm_chat_reply( $msgs );
			$last = end( $msgs );
			return new WP_REST_Response( array(
				'reply'   => $reply,
				'mode'    => $mode,
				'actions' => kbm_chat_actions( $last['content'] . ' ' . $reply ),
			), 200 );
		},
	) );
} );
