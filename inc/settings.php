<?php
defined( 'ABSPATH' ) || exit;

/** Default settings – real business data of KB Motion. */
function kbm_defaults() {
	return array(
		'company'          => 'Tomasz Kluzek KB Motion',
		'nip'              => '8943242672',
		'company_address'  => 'ul. Józefa Piłsudskiego 91 lok. 1, 50-019 Wrocław',
		'street'           => 'ul. Nyska 61A',
		'postcode'         => '50-505',
		'city'             => 'Wrocław',
		'lat'              => '',
		'lng'              => '',
		'email'            => 'kontakt@kbmotion.pl',
		'phone'            => '',
		'hours'            => 'Pon–Nd 06:00–23:00',
		'opens'            => '06:00',
		'closes'           => '23:00',
		'form_to'          => 'kontakt@kbmotion.pl',
		'staff_email'      => 'kontakt@kbmotion.pl',
		'efitness_url'     => 'https://kbmotion-wroclaw.cms.efitness.com.pl',
		'instagram'        => '',
		'facebook'         => '',
		'ga4_id'           => '',
		'pixel_id'         => '',
		'turnstile_site'   => '',
		'turnstile_secret' => '',
		'chat_enabled'     => 1,
		'anthropic_key'    => '',
		'anthropic_model'  => 'claude-opus-5-5',
		'chat_extra'       => '',
	);
}

function kbm_opt( $key ) {
	$opts = wp_parse_args( (array) get_option( 'kbm_settings', array() ), kbm_defaults() );
	return $opts[ $key ] ?? '';
}

/** URL of a page by slug (falls back to home + slug). */
function kbm_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

function kbm_phone_link() {
	$p = kbm_opt( 'phone' );
	return $p ? 'tel:' . preg_replace( '/[^0-9+]/', '', $p ) : '';
}

add_action( 'admin_menu', function () {
	add_menu_page( 'KB Motion', 'KB Motion', 'manage_options', 'kbm', 'kbm_settings_page', 'dashicons-heart', 3 );
	add_submenu_page( 'kbm', 'Ustawienia', 'Ustawienia', 'manage_options', 'kbm', 'kbm_settings_page' );
} );

add_action( 'admin_init', function () {
	register_setting( 'kbm', 'kbm_settings', array(
		'type'              => 'array',
		'sanitize_callback' => function ( $in ) {
			$out = array();
			foreach ( kbm_defaults() as $k => $v ) {
				if ( 'chat_enabled' === $k ) {
					$out[ $k ] = empty( $in[ $k ] ) ? 0 : 1;
				} elseif ( 'chat_extra' === $k ) {
					$out[ $k ] = sanitize_textarea_field( $in[ $k ] ?? '' );
				} elseif ( in_array( $k, array( 'efitness_url', 'instagram', 'facebook' ), true ) ) {
					$out[ $k ] = esc_url_raw( $in[ $k ] ?? '' );
				} elseif ( in_array( $k, array( 'form_to', 'staff_email', 'email' ), true ) ) {
					$out[ $k ] = sanitize_email( $in[ $k ] ?? '' );
				} elseif ( 'anthropic_key' === $k || 'turnstile_secret' === $k ) {
					// Keep the stored secret when the field is left empty.
					$val       = trim( (string) ( $in[ $k ] ?? '' ) );
					$out[ $k ] = '' === $val ? kbm_opt( $k ) : sanitize_text_field( $val );
				} else {
					$out[ $k ] = sanitize_text_field( $in[ $k ] ?? '' );
				}
			}
			return $out;
		},
	) );
} );

function kbm_settings_page() {
	$fields = array(
		'Dane firmy'        => array(
			'company' => 'Nazwa firmy', 'nip' => 'NIP', 'company_address' => 'Adres siedziby',
			'street' => 'Ulica studia', 'postcode' => 'Kod pocztowy', 'city' => 'Miasto',
			'lat' => 'Szerokość geogr.', 'lng' => 'Długość geogr.',
			'email' => 'E-mail kontaktowy', 'phone' => 'Telefon (np. +48 500 000 000)',
			'hours' => 'Godziny otwarcia (tekst)', 'opens' => 'Otwarcie (HH:MM)', 'closes' => 'Zamknięcie (HH:MM)',
			'instagram' => 'Instagram URL', 'facebook' => 'Facebook URL',
		),
		'Formularze i sklep' => array(
			'form_to' => 'Formularze wysyłane na', 'staff_email' => 'Aktywacja karnetów (e-mail obsługi)',
			'efitness_url' => 'Adres eFitness', 'turnstile_site' => 'Cloudflare Turnstile – site key (opcjonalnie)',
			'turnstile_secret' => 'Cloudflare Turnstile – secret (opcjonalnie)',
		),
		'Analityka'         => array( 'ga4_id' => 'Google Analytics 4 – ID (G-XXXX)', 'pixel_id' => 'Meta Pixel – ID' ),
		'AI asystent'       => array(
			'chat_enabled' => 'Włącz czat', 'anthropic_key' => 'Klucz API Anthropic',
			'anthropic_model' => 'Model', 'chat_extra' => 'Dodatkowa wiedza dla asystenta (promocje, zasady)',
		),
	);
	?>
	<div class="wrap">
		<h1>KB Motion – ustawienia</h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'kbm' ); ?>
			<?php foreach ( $fields as $section => $items ) : ?>
				<h2><?php echo esc_html( $section ); ?></h2>
				<table class="form-table" role="presentation">
				<?php foreach ( $items as $key => $label ) :
					$val  = kbm_opt( $key );
					$name = 'kbm_settings[' . $key . ']'; ?>
					<tr><th scope="row"><label for="kbm-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th><td>
					<?php if ( 'chat_enabled' === $key ) : ?>
						<input type="checkbox" id="kbm-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( $val, 1 ); ?>>
					<?php elseif ( 'chat_extra' === $key ) : ?>
						<textarea id="kbm-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="5" class="large-text"><?php echo esc_textarea( $val ); ?></textarea>
					<?php elseif ( in_array( $key, array( 'anthropic_key', 'turnstile_secret' ), true ) ) : ?>
						<input type="password" id="kbm-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $name ); ?>" value="" class="regular-text" autocomplete="new-password" placeholder="<?php echo $val ? esc_attr( '•••••••• zapisany' ) : ''; ?>">
						<p class="description">Pozostaw puste, aby zachować zapisaną wartość.</p>
					<?php else : ?>
						<input type="text" id="kbm-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $val ); ?>" class="regular-text">
					<?php endif; ?>
					</td></tr>
				<?php endforeach; ?>
				</table>
			<?php endforeach; ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
