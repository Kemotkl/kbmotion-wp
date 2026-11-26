<?php
defined( 'ABSPATH' ) || exit;

/**
 * Cookie banner (GDPR / Prawo telekomunikacyjne) with Google Consent Mode v2 in "basic" mode:
 * GA4 and Meta Pixel are loaded only after the visitor accepts the matching category.
 */

add_action( 'wp_head', function () {
	if ( ! kbm_opt( 'ga4_id' ) && ! kbm_opt( 'pixel_id' ) ) {
		return;
	}
	?>
	<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('consent','default',{ad_storage:'denied',ad_user_data:'denied',ad_personalization:'denied',analytics_storage:'denied',functionality_storage:'granted',security_storage:'granted',wait_for_update:500});</script>
	<?php
}, 1 );

add_action( 'wp_footer', function () {
	?>
	<div class="kbm-consent" id="kbm-consent" role="dialog" aria-modal="false" aria-labelledby="kbm-consent-title" hidden>
		<div class="kbm-consent__box">
			<p class="kbm-consent__title" id="kbm-consent-title">Szanujemy Twoją prywatność</p>
			<p>Używamy niezbędnych plików cookies, aby strona i sklep działały. Za Twoją zgodą użyjemy też cookies analitycznych (Google Analytics) i marketingowych (Meta Pixel), żeby ulepszać stronę i docierać z ofertą do osób, które mogą jej szukać. <a href="<?php echo esc_url( kbm_url( 'polityka-cookies' ) ); ?>">Polityka cookies</a></p>
			<div class="kbm-consent__prefs" hidden>
				<label class="kbm-check"><input type="checkbox" checked disabled> <span><strong>Niezbędne</strong> – koszyk, logowanie, bezpieczeństwo.</span></label>
				<label class="kbm-check"><input type="checkbox" data-consent="analytics"> <span><strong>Analityczne</strong> – anonimowe statystyki odwiedzin.</span></label>
				<label class="kbm-check"><input type="checkbox" data-consent="marketing"> <span><strong>Marketingowe</strong> – dopasowanie reklam.</span></label>
			</div>
			<div class="kbm-consent__btns">
				<button type="button" class="kbm-btn kbm-btn--ghost" data-consent-action="settings">Ustawienia</button>
				<button type="button" class="kbm-btn kbm-btn--ghost" data-consent-action="reject">Tylko niezbędne</button>
				<button type="button" class="kbm-btn kbm-btn--gold" data-consent-action="accept">Akceptuję wszystkie</button>
			</div>
		</div>
	</div>
	<?php
}, 20 );

/* Footer link that reopens the banner: [kbm_cookie_settings] */
add_shortcode( 'kbm_cookie_settings', function () {
	return '<a href="#" data-consent-open>Ustawienia cookies</a>';
} );
