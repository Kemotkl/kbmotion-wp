<?php
defined( 'ABSPATH' ) || exit;

/**
 * Forms: contact, trial (free consultation / trial training), trainer booking.
 * Anti-spam: honeypot + signed time-trap + IP rate limit + optional Cloudflare Turnstile.
 * GDPR: required consent checkbox with link to the privacy policy, optional marketing consent.
 */

function kbm_form_types() {
	return array(
		'contact' => array( 'title' => 'Wiadomość z formularza kontaktowego', 'button' => 'Wyślij wiadomość' ),
		'trial'   => array( 'title' => 'Zapis na bezpłatną konsultację online', 'button' => 'Zapisz mnie na konsultację online' ),
		'trainer' => array( 'title' => 'Rezerwacja trenera personalnego', 'button' => 'Zarezerwuj trening' ),
	);
}

function kbm_trainer_price( $name ) {
	$t = get_page_by_title( $name, OBJECT, 'kbm_trainer' );
	return $t ? (int) get_post_meta( $t->ID, '_kbm_price', true ) : 0;
}

function kbm_trainer_names() {
	return wp_list_pluck( get_posts( array( 'post_type' => 'kbm_trainer', 'numberposts' => 50, 'orderby' => 'menu_order', 'order' => 'ASC' ) ), 'post_title' );
}

function kbm_form_token() {
	$ts = time();
	return $ts . '.' . substr( hash_hmac( 'sha256', (string) $ts, wp_salt( 'nonce' ) ), 0, 16 );
}

add_shortcode( 'kbm_form', function ( $atts ) {
	$atts  = shortcode_atts( array( 'type' => 'contact' ), $atts );
	$type  = array_key_exists( $atts['type'], kbm_form_types() ) ? $atts['type'] : 'contact';
	$def   = kbm_form_types()[ $type ];
	$uid   = 'kbmf-' . $type . '-' . wp_rand( 100, 999 );
	$pre_t = isset( $_GET['trener'] ) ? sanitize_text_field( wp_unslash( $_GET['trener'] ) ) : '';
	$sent  = isset( $_GET['kbm_sent'] ) && $_GET['kbm_sent'] === $type;
	$f     = function ( $name, $label, $input ) use ( $uid ) {
		return '<div class="kbm-field kbm-field--' . esc_attr( $name ) . '"><label for="' . esc_attr( $uid . '-' . $name ) . '">' . $label . '</label>' . $input . '</div>';
	};
	$id    = function ( $n ) use ( $uid ) {
		return esc_attr( $uid . '-' . $n );
	};

	ob_start();
	?>
	<form class="kbm-form" id="<?php echo esc_attr( $uid ); ?>" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate data-kbm-form>
		<input type="hidden" name="action" value="kbm_form">
		<input type="hidden" name="kbm_type" value="<?php echo esc_attr( $type ); ?>">
		<input type="hidden" name="kbm_token" value="<?php echo esc_attr( kbm_form_token() ); ?>">
		<input type="hidden" name="kbm_page" value="<?php echo esc_url( get_permalink() ); ?>">
		<div class="kbm-hp" aria-hidden="true"><label>Strona www <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
		<div class="kbm-form__grid">
			<?php
			echo $f( 'name', 'Imię i nazwisko <span aria-hidden="true">*</span>', '<input id="' . $id( 'name' ) . '" name="name" type="text" autocomplete="name" required maxlength="120">' );
			echo $f( 'email', 'E-mail <span aria-hidden="true">*</span>', '<input id="' . $id( 'email' ) . '" name="email" type="email" autocomplete="email" required maxlength="160">' );
			echo $f( 'phone', 'Telefon' . ( 'contact' === $type ? '' : ' <span aria-hidden="true">*</span>' ), '<input id="' . $id( 'phone' ) . '" name="phone" type="tel" autocomplete="tel" inputmode="tel" maxlength="30"' . ( 'contact' === $type ? '' : ' required' ) . '>' );

			if ( 'contact' === $type || 'trainer' === $type ) {
				$opts = '<option value="">' . ( 'trainer' === $type ? 'Dobierzcie trenera za mnie' : 'Dowolny / nie wiem' ) . '</option>';
				foreach ( kbm_trainer_names() as $n ) {
					$pr    = 'trainer' === $type ? kbm_trainer_price( $n ) : 0;
					$opts .= '<option value="' . esc_attr( $n ) . '"' . selected( $pre_t, $n, false ) . '>' . esc_html( $n . ( $pr ? ' – ' . $pr . ' zł / trening' : '' ) ) . '</option>';
				}
				echo $f( 'trainer', 'contact' === $type ? 'Z którym trenerem chcesz się skontaktować?' : 'Preferowany trener', '<select id="' . $id( 'trainer' ) . '" name="trainer">' . $opts . '</select>' );
			}
			if ( 'trainer' === $type ) {
				echo $f( 'package', 'Co Cię interesuje?', '<select id="' . $id( 'package' ) . '" name="package"><option>Konsultacja + pierwszy trening w studiu (99 zł)</option><option>Trening personalny (cena zależna od trenera)</option><option>Pakiet 10 treningów z trenerem klubowym (1500 zł)</option><option>Jeszcze nie wiem, doradźcie</option></select>' );
				echo $f( 'when', 'Kiedy możesz trenować?', '<select id="' . $id( 'when' ) . '" name="when"><option>Rano (6–10)</option><option>W ciągu dnia (10–16)</option><option>Po pracy (16–20)</option><option>Wieczorem (20–23)</option><option>Elastycznie</option></select>' );
			}
			if ( 'trial' === $type ) {
				echo $f( 'goal', 'Twój główny cel', '<select id="' . $id( 'goal' ) . '" name="goal"><option>Redukcja tkanki tłuszczowej</option><option>Budowa siły i sylwetki</option><option>Zdrowy kręgosłup i mobilność</option><option>Powrót do formy po przerwie</option><option>Przygotowanie motoryczne</option><option>Inny</option></select>' );
				echo $f( 'when', 'Preferowana pora', '<select id="' . $id( 'when' ) . '" name="when"><option>Rano (6–10)</option><option>W ciągu dnia (10–16)</option><option>Po pracy (16–20)</option><option>Wieczorem (20–23)</option><option>Elastycznie</option></select>' );
			}
			$msg_label = 'contact' === $type ? 'Wiadomość <span aria-hidden="true">*</span>' : 'Dodatkowe informacje (kontuzje, oczekiwania)';
			echo $f( 'message', $msg_label, '<textarea id="' . $id( 'message' ) . '" name="message" rows="5" maxlength="3000"' . ( 'contact' === $type ? ' required' : '' ) . '></textarea>' );
			?>
		</div>
		<div class="kbm-consents">
			<label class="kbm-check"><input type="checkbox" name="consent_rodo" value="1" required> <span>Wyrażam zgodę na przetwarzanie moich danych osobowych przez <?php echo esc_html( kbm_opt( 'company' ) ); ?> w celu obsługi zgłoszenia i kontaktu zwrotnego. Zapoznałem/-am się z <a href="<?php echo esc_url( kbm_url( 'polityka-prywatnosci' ) ); ?>" target="_blank">polityką prywatności</a>. <span aria-hidden="true">*</span></span></label>
			<label class="kbm-check"><input type="checkbox" name="consent_marketing" value="1"> <span>Chcę otrzymywać informacje o promocjach i nowościach KB Motion (e-mail/SMS). Zgodę mogę wycofać w każdej chwili.</span></label>
		</div>
		<?php if ( kbm_opt( 'turnstile_site' ) ) : ?>
			<div class="cf-turnstile" data-sitekey="<?php echo esc_attr( kbm_opt( 'turnstile_site' ) ); ?>" data-theme="dark"></div>
			<?php wp_enqueue_script( 'cf-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js', array(), null, array( 'strategy' => 'defer' ) ); ?>
		<?php endif; ?>
		<button class="kbm-btn kbm-btn--gold kbm-form__submit" type="submit"><?php echo esc_html( $def['button'] ); ?></button>
		<p class="kbm-form__status" role="status" aria-live="polite"><?php echo $sent ? 'Dziękujemy! Wiadomość dotarła. Odezwiemy się najszybciej, jak to możliwe.' : ''; ?></p>
	</form>
	<?php
	return ob_get_clean();
} );

/** Validates and sends a submission. Returns array( ok, message ). */
function kbm_handle_form( array $in ) {
	$types = kbm_form_types();
	$type  = isset( $in['kbm_type'] ) && isset( $types[ $in['kbm_type'] ] ) ? $in['kbm_type'] : '';
	if ( ! $type ) {
		return array( false, 'Nieprawidłowy formularz.' );
	}
	// Honeypot.
	if ( ! empty( $in['website'] ) ) {
		return array( true, 'Dziękujemy!' ); // Silently accept bots.
	}
	// Signed time-trap: at least 3 s and at most 24 h after render.
	$tok   = explode( '.', (string) ( $in['kbm_token'] ?? '' ) );
	$valid = 2 === count( $tok ) && hash_equals( substr( hash_hmac( 'sha256', $tok[0], wp_salt( 'nonce' ) ), 0, 16 ), $tok[1] );
	$age   = $valid ? time() - (int) $tok[0] : -1;
	if ( ! $valid || $age < 3 || $age > DAY_IN_SECONDS ) {
		return array( false, 'Formularz wygasł lub został wysłany zbyt szybko. Odśwież stronę i spróbuj ponownie.' );
	}
	// Rate limit: 5 submissions / 10 min per IP.
	$ip  = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' );
	$key = 'kbm_rl_' . md5( $ip );
	$cnt = (int) get_transient( $key );
	if ( $cnt >= 5 ) {
		return array( false, 'Wysłano zbyt wiele wiadomości. Spróbuj ponownie za kilka minut.' );
	}
	// Turnstile.
	if ( kbm_opt( 'turnstile_secret' ) ) {
		$resp = wp_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', array(
			'timeout' => 10,
			'body'    => array( 'secret' => kbm_opt( 'turnstile_secret' ), 'response' => $in['cf-turnstile-response'] ?? '', 'remoteip' => $ip ),
		) );
		$ok   = ! is_wp_error( $resp ) && ! empty( json_decode( wp_remote_retrieve_body( $resp ), true )['success'] );
		if ( ! $ok ) {
			return array( false, 'Weryfikacja antyspamowa nie powiodła się. Spróbuj ponownie.' );
		}
	}

	$d = array(
		'name'    => sanitize_text_field( $in['name'] ?? '' ),
		'email'   => sanitize_email( $in['email'] ?? '' ),
		'phone'   => sanitize_text_field( $in['phone'] ?? '' ),
		'trainer' => sanitize_text_field( $in['trainer'] ?? '' ),
		'package' => sanitize_text_field( $in['package'] ?? '' ),
		'goal'    => sanitize_text_field( $in['goal'] ?? '' ),
		'when'    => sanitize_text_field( $in['when'] ?? '' ),
		'message' => sanitize_textarea_field( $in['message'] ?? '' ),
		'page'    => esc_url_raw( $in['kbm_page'] ?? '' ),
	);
	if ( mb_strlen( $d['name'] ) < 2 ) {
		return array( false, 'Podaj imię i nazwisko.' );
	}
	if ( ! is_email( $d['email'] ) ) {
		return array( false, 'Podaj poprawny adres e-mail.' );
	}
	if ( 'contact' !== $type && ! preg_match( '/^[0-9 +()-]{9,}$/', $d['phone'] ) ) {
		return array( false, 'Podaj numer telefonu, abyśmy mogli potwierdzić termin.' );
	}
	if ( 'contact' === $type && mb_strlen( $d['message'] ) < 5 ) {
		return array( false, 'Napisz, w czym możemy pomóc.' );
	}
	if ( empty( $in['consent_rodo'] ) ) {
		return array( false, 'Zaznacz zgodę na przetwarzanie danych, abyśmy mogli odpowiedzieć.' );
	}
	// Link spam filter.
	if ( preg_match_all( '#https?://#i', $d['message'] ) > 2 ) {
		return array( false, 'Wiadomość zawiera zbyt wiele linków.' );
	}

	set_transient( $key, $cnt + 1, 10 * MINUTE_IN_SECONDS );

	$labels = array( 'name' => 'Imię i nazwisko', 'email' => 'E-mail', 'phone' => 'Telefon', 'trainer' => 'Trener', 'package' => 'Oferta', 'goal' => 'Cel', 'when' => 'Pora', 'message' => 'Wiadomość', 'page' => 'Strona' );
	$lines  = array();
	foreach ( $labels as $k => $l ) {
		if ( '' !== $d[ $k ] ) {
			$lines[] = $l . ': ' . $d[ $k ];
		}
	}
	$lines[] = 'Zgoda RODO: tak (' . wp_date( 'Y-m-d H:i' ) . ')';
	$lines[] = 'Zgoda marketingowa: ' . ( empty( $in['consent_marketing'] ) ? 'nie' : 'tak' );
	$body    = implode( "\n", $lines );
	$title   = $types[ $type ]['title'];

	$lead = wp_insert_post( array(
		'post_type'    => 'kbm_lead',
		'post_status'  => 'private',
		'post_title'   => $title . ' – ' . $d['name'],
		'post_content' => $body,
	) );
	if ( $lead ) {
		update_post_meta( $lead, '_kbm_kind', $title );
		update_post_meta( $lead, '_kbm_email', $d['email'] );
	}

	$to      = kbm_opt( 'form_to' ) ?: get_option( 'admin_email' );
	$headers = array( 'Reply-To: ' . $d['name'] . ' <' . $d['email'] . '>' );
	$sent    = wp_mail( $to, '[KB Motion] ' . $title . ' – ' . $d['name'], $body, $headers );

	// Confirmation for the client.
	$confirm = "Cześć " . strtok( $d['name'], ' ' ) . ",\n\ndziękujemy za wiadomość do KB Motion. Odezwiemy się najszybciej, jak to możliwe (zwykle w ciągu jednego dnia roboczego).\n\nTwoje zgłoszenie:\n" . $body . "\n\nKB Motion\n" . kbm_opt( 'street' ) . ', ' . kbm_opt( 'city' ) . "\n" . kbm_opt( 'email' ) . ( kbm_opt( 'phone' ) ? ' · ' . kbm_opt( 'phone' ) : '' ) . "\n" . home_url( '/' );
	wp_mail( $d['email'], 'Dziękujemy za kontakt z KB Motion', $confirm, array( 'Reply-To: KB Motion <' . kbm_opt( 'email' ) . '>' ) );

	do_action( 'kbm_form_submitted', $type, $d );

	if ( ! $sent ) {
		return array( true, 'Zgłoszenie zapisaliśmy, ale wysyłka e-maila się opóźnia. Skontaktujemy się z Tobą.' );
	}
	return array( true, 'trial' === $type || 'trainer' === $type ? 'Dziękujemy! Zadzwonimy lub napiszemy, aby potwierdzić termin.' : 'Dziękujemy! Wiadomość dotarła. Odezwiemy się najszybciej, jak to możliwe.' );
}

/* REST endpoint used by JS. */
add_action( 'rest_api_init', function () {
	register_rest_route( 'kbm/v1', '/form', array(
		'methods'             => 'POST',
		'permission_callback' => '__return_true',
		'callback'            => function ( WP_REST_Request $r ) {
			list( $ok, $msg ) = kbm_handle_form( $r->get_params() );
			return new WP_REST_Response( array( 'ok' => $ok, 'message' => $msg ), $ok ? 200 : 422 );
		},
	) );
} );

/* No-JS fallback. */
function kbm_admin_post_form() {
	list( $ok, $msg ) = kbm_handle_form( wp_unslash( $_POST ) );
	$back = wp_get_referer() ?: home_url( '/' );
	if ( $ok ) {
		wp_safe_redirect( add_query_arg( 'kbm_sent', sanitize_key( $_POST['kbm_type'] ?? '' ), $back ) );
	} else {
		wp_die( esc_html( $msg ), 'KB Motion', array( 'response' => 422, 'back_link' => true ) );
	}
	exit;
}
add_action( 'admin_post_nopriv_kbm_form', 'kbm_admin_post_form' );
add_action( 'admin_post_kbm_form', 'kbm_admin_post_form' );

/* Use a proper From header. */
add_filter( 'wp_mail_from_name', function ( $n ) {
	return 'WordPress' === $n ? 'KB Motion' : $n;
} );
