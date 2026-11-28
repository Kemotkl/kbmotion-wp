<?php
/**
 * Page content for the installer. {{img:key}} tokens are replaced with media library URLs.
 * Returns: slug => array( title, content, template, meta description, SEO title ).
 */
defined( 'ABSPATH' ) || exit;

function kbm_cta_html( $buy = 'Kup karnet', $book = 'Umów trening' ) {
	return '<div class="kbm-btn-row"><a class="kbm-btn kbm-btn--gold" href="/karnety/">' . $buy . '</a><a class="kbm-btn kbm-btn--ghost" href="/treningi-personalne/#rezerwacja">' . $book . '</a></div>';
}

function kbm_section( $inner, $class = '', $align = 'wide' ) {
	return '<!-- wp:group {"align":"full","className":"kbm-section ' . $class . '","layout":{"type":"constrained"}} -->' . "\n"
		. '<div class="wp-block-group alignfull kbm-section ' . $class . '">' . "\n"
		. '<!-- wp:group {"align":"' . $align . '","layout":{"type":"default"}} -->' . "\n<div class=\"wp-block-group align" . $align . '">' . $inner . "</div>\n<!-- /wp:group -->\n"
		. "</div>\n<!-- /wp:group -->\n\n";
}

function kbm_html( $html ) {
	return "\n<!-- wp:html -->\n" . $html . "\n<!-- /wp:html -->\n";
}

function kbm_sc( $sc ) {
	return "\n<!-- wp:shortcode -->\n" . $sc . "\n<!-- /wp:shortcode -->\n";
}

function kbm_head( $eyebrow, $title, $lead = '', $center = false, $tag = 'h2' ) {
	return kbm_html( '<div class="kbm-section-head' . ( $center ? ' is-center' : '' ) . ' kbm-reveal"><p class="kbm-eyebrow">' . $eyebrow . '</p><' . $tag . '>' . $title . '</' . $tag . '>' . ( $lead ? '<p class="kbm-lead">' . $lead . '</p>' : '' ) . '</div>' );
}

function kbm_consult_compare_html() {
	return kbm_html( '<div class="kbm-consult" role="group" aria-label="Jak zacząć: konsultacja online lub trening w studiu">'
		. '<div class="kbm-consult__card"><p class="kbm-eyebrow">Online</p><h3>Konsultacja online</h3><p class="kbm-consult__price">0 zł</p><p>Rozmowa z trenerem o Twoim celu, zdrowiu i dotychczasowych treningach. Doradzimy, od czego zacząć i jaki karnet lub pakiet wybrać.</p><a class="kbm-btn kbm-btn--ghost" href="/trening-probny/#zapis">Zapisz się bezpłatnie</a></div>'
		. '<div class="kbm-consult__card is-gold"><p class="kbm-eyebrow">W studiu, ul. Nyska 61A</p><h3>Konsultacja + trening</h3><p class="kbm-consult__price">99 zł</p><p>Ok. 60 minut z wybranym trenerem na miejscu: konsultacja, pierwszy trening wdrożeniowy i ocena techniki.</p><a class="kbm-btn kbm-btn--gold" href="/treningi-personalne/#pakiety">Kup za 99 zł</a></div>'
		. '</div>' );
}

function kbm_hero( $img, $eyebrow, $title, $lead, $buttons, $meta = '' ) {
	return '<!-- wp:cover {"url":"' . $img . '","dimRatio":60,"minHeight":88,"minHeightUnit":"vh","contentPosition":"bottom left","align":"full","className":"kbm-hero","layout":{"type":"constrained"}} -->' . "\n"
		. '<div class="wp-block-cover alignfull has-custom-content-position is-position-bottom-left kbm-hero" style="min-height:88vh"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-60 has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="' . $img . '" data-object-fit="cover" fetchpriority="high"/><div class="wp-block-cover__inner-container">'
		. '<!-- wp:group {"align":"wide","layout":{"type":"default"}} --><div class="wp-block-group alignwide">'
		. kbm_html( '<p class="kbm-eyebrow">' . $eyebrow . '</p><h1>' . $title . '</h1><p class="kbm-lead">' . $lead . '</p>' . $buttons . ( $meta ? '<div class="kbm-hero__meta">' . $meta . '</div>' : '' ) )
		. '</div><!-- /wp:group -->'
		. "</div></div>\n<!-- /wp:cover -->\n\n";
}

function kbm_pages_content() {
	$p = array();

	/* ---------------- HOME ---------------- */
	$home  = kbm_hero(
		'{{img:hero}}',
		'Wrocław · ul. Nyska 61A',
		'Twoje <em>kameralne</em> studio treningu personalnego',
		'250 m² bez tłumów, profesjonalni trenerzy i wejście przez aplikację codziennie od 6:00 do 23:00.',
		kbm_cta_html( 'Kup karnet', 'Umów trening' ),
		'<span>Bez tłumów i kolejek</span><span>Treningi personalne od 150 zł</span><span>Otwarte codziennie 6–23</span>'
	);
	$home .= kbm_html( '<div class="kbm-strip alignfull" aria-hidden="true"><div class="kbm-strip__track"><span>Trening personalny</span><span>Redukcja tkanki tłuszczowej</span><span>Przygotowanie motoryczne</span><span>Siła i sylwetka</span><span>Zdrowy kręgosłup</span><span>Zajęcia grupowe</span><span>Trening personalny</span><span>Redukcja tkanki tłuszczowej</span><span>Przygotowanie motoryczne</span><span>Siła i sylwetka</span><span>Zdrowy kręgosłup</span><span>Zajęcia grupowe</span></div></div>' );
	$home .= kbm_section(
		kbm_head( 'Dlaczego KB Motion', 'Przestrzeń zaprojektowana<br>dla Twojego komfortu', 'Powstaliśmy z potrzeby stworzenia dyskretnego miejsca dla wymagających mieszkańców Wrocławia. Zamiast przepełnionych sal: spokój, porządek i trener, który zna Twoje cele.' )
		. kbm_html( '<div class="kbm-features">'
			. '<div class="kbm-feature kbm-reveal"><span class="kbm-feature__num">01</span><h3>Profesjonalna kadra</h3><p>Trenerzy personalni, którzy uczą świadomego ruchu, pilnują techniki i układają plan pod Ciebie.</p></div>'
			. '<div class="kbm-feature kbm-reveal"><span class="kbm-feature__num">02</span><h3>Strefa siłowa i sala fitness</h3><p>Sprzęt najwyższej klasy w kameralnej przestrzeni. Zawsze znajdziesz wolne stanowisko.</p></div>'
			. '<div class="kbm-feature kbm-reveal"><span class="kbm-feature__num">03</span><h3>Wejście przez aplikację</h3><p>Kod QR zamiast karty, zapisy na zajęcia i karnety w telefonie. Wchodzisz, kiedy chcesz, od 6:00 do 23:00.</p></div>'
			. '<div class="kbm-feature kbm-reveal"><span class="kbm-feature__num">04</span><h3>Prywatność i spokój</h3><p>Elegancja zamiast zgiełku. Skupiasz się na treningu, nie na tłumie dookoła.</p></div>'
			. '</div>' )
	);
	$home .= kbm_section(
		kbm_html( '<div class="kbm-split"><div class="kbm-reveal"><p class="kbm-eyebrow">Studio bez kompromisów</p><h2>Inne podejście<br>do treningu</h2><p class="kbm-lead">Każdy detal wnętrza zaprojektowaliśmy z myślą o estetyce i funkcjonalności. Tu trening jest czasem tylko dla Ciebie.</p><div class="kbm-stats" style="margin:28px 0"><div class="kbm-stat"><b>250 m²</b><span>kameralnej przestrzeni</span></div><div class="kbm-stat"><b>6–23</b><span>otwarte codziennie</span></div><div class="kbm-stat"><b>100%</b><span>komfortu i prywatności</span></div></div>' . kbm_cta_html( 'Zobacz karnety', 'Poznaj trenerów' ) . '</div><div class="kbm-reveal"><img src="{{img:studio2}}" alt="Strefa siłowa w studiu KB Motion we Wrocławiu" loading="lazy" width="900" height="1125"></div></div>' ),
		'is-dark'
	);
	// Fix the second CTA to point at trainers.
	$home = str_replace( '<a class="kbm-btn kbm-btn--ghost" href="/treningi-personalne/#rezerwacja">Poznaj trenerów</a>', '<a class="kbm-btn kbm-btn--ghost" href="/trenerzy/">Poznaj trenerów</a>', $home );
	$home .= kbm_section(
		kbm_head( 'Cennik', 'Wybierz karnet<br>w 30 sekund', 'Bez ukrytych opłat. Kupujesz online, płacisz BLIK-iem lub kartą, a my aktywujemy dostęp w aplikacji.', true )
		. kbm_sc( '[kbm_pricing cat="karnety" limit="3"]' )
		. kbm_html( '<p style="text-align:center;margin-top:28px"><a class="kbm-link-arrow" href="/karnety/">Pełny cennik i wejścia jednorazowe</a></p>' ),
		'is-sand'
	);
	$home .= kbm_section(
		kbm_html( '<div class="kbm-split is-reverse"><div class="kbm-reveal"><p class="kbm-eyebrow">Treningi personalne</p><h2>Efekty bez zgadywania</h2><p class="kbm-lead">Redukcja tkanki tłuszczowej, siła, mobilność albo powrót do formy. Zaczynamy od konsultacji i treningu wdrożeniowego, a potem trener prowadzi Cię krok po kroku.</p><ul class="kbm-checklist"><li>Analiza celu, techniki i możliwości</li><li>Plan treningowy dopasowany do Twojego tygodnia</li><li>Bezpieczeństwo także przy kontuzjach</li><li>Pakiet 10 treningów z trenerem klubowym: 1500 zł</li></ul><div class="kbm-btn-row"><a class="kbm-btn kbm-btn--gold" href="/treningi-personalne/">Zacznij od 99 zł</a><a class="kbm-btn kbm-btn--ghost" href="/trening-probny/">Konsultacja online 0 zł</a></div></div><div class="kbm-reveal"><img src="{{img:studio3}}" alt="Trening personalny w KB Motion Wrocław" loading="lazy" width="900" height="1125"></div></div>' )
	);
	$home .= kbm_section(
		kbm_head( 'Jak zacząć', 'Trzy kroki do pierwszego treningu', '', true )
		. kbm_html( '<div class="kbm-steps"><div class="kbm-step kbm-reveal"><h3>Wybierz</h3><p>Karnet na siłownię, zajęcia grupowe albo trening z trenerem. Nie wiesz, co wybrać? Zapytaj asystenta w czacie.</p></div><div class="kbm-step kbm-reveal"><h3>Zapłać online</h3><p>BLIK, karta lub szybki przelew. Zajmuje to mniej niż minutę.</p></div><div class="kbm-step kbm-reveal"><h3>Trenuj</h3><p>Aktywujemy karnet i wysyłamy zaproszenie do aplikacji. Wchodzisz na kod QR.</p></div></div>' ),
		'is-dark'
	);
	$home .= kbm_section(
		kbm_head( 'Zespół', 'Poznaj naszych trenerów', 'Każdy z nich to ekspert w swojej dziedzinie, z pasją do świadomego ruchu i indywidualnego podejścia.' )
		. kbm_sc( '[kbm_trainers owners="0" limit="4"]' )
		. kbm_html( '<p style="margin-top:28px"><a class="kbm-link-arrow" href="/trenerzy/">Cały zespół</a></p>' )
	);
	$home .= kbm_sc( '[kbm_if_reviews]' . kbm_section( kbm_head( 'Opinie', 'Co mówią klubowicze', '', true ) . '[kbm_reviews]', 'is-sand' ) . '[/kbm_if_reviews]' );
	$home .= kbm_section(
		kbm_head( 'Galeria', 'Zajrzyj do środka' )
		. kbm_html( '<div class="kbm-gallery"><figure><img src="{{img:studio1}}" alt="Wnętrze studia KB Motion" loading="lazy"></figure><figure><img src="{{img:studio4}}" alt="Sprzęt siłowy KB Motion" loading="lazy"></figure><figure><img src="{{img:studio5}}" alt="Sala fitness KB Motion" loading="lazy"></figure><figure><img src="{{img:studio6}}" alt="Strefa treningowa KB Motion" loading="lazy"></figure><figure><img src="{{img:studio7}}" alt="Detale wnętrza KB Motion" loading="lazy"></figure><figure><img src="{{img:studio8}}" alt="Studio treningu personalnego Wrocław" loading="lazy"></figure></div><p style="margin-top:24px"><a class="kbm-link-arrow" href="/galeria/">Więcej zdjęć</a></p>' )
	);
	$home .= kbm_section(
		kbm_head( 'FAQ', 'Najczęstsze pytania', '', true )
		. kbm_html( '<div style="max-width:820px;margin:0 auto">' ) . kbm_sc( '[kbm_faq set="ogolne,karnety"]' ) . kbm_html( '</div>' ),
		'is-sand'
	);
	$home .= kbm_section(
		kbm_html( '<div class="kbm-split"><div><p class="kbm-eyebrow">Kontakt i dojazd</p><h2>Wpadnij na Nyską 61A</h2>' ) . kbm_sc( '[kbm_contact]' ) . kbm_html( '<div class="kbm-btn-row" style="margin-top:24px"><a class="kbm-btn kbm-btn--gold" href="/kontakt/">Napisz do nas</a><a class="kbm-btn kbm-btn--ghost" href="#czat">Zapytaj asystenta</a></div></div><div>' ) . kbm_sc( '[kbm_map]' ) . kbm_html( '</div></div>' ),
		'is-dark'
	);
	$p['strona-glowna'] = array( 'Strona główna', $home, 'page-landing', 'KB Motion – kameralne studio treningu personalnego we Wrocławiu (ul. Nyska 61A). Treningi personalne, redukcja, siłownia i zajęcia grupowe. Kup karnet online.', 'Trening personalny Wrocław | Studio KB Motion – Nyska 61A' );

	/* ---------------- KARNETY ---------------- */
	$k  = kbm_section(
		kbm_head( 'Karnety i cennik', 'Wybierz karnet<br>i zacznij jeszcze dziś', 'Kupujesz online w trzech krokach. Po płatności aktywujemy karnet w aplikacji, a do studia wchodzisz na kod QR, codziennie od 6:00 do 23:00.', false, 'h1' )
		. kbm_sc( '[kbm_pricing cat="karnety"]' ),
		'is-sand'
	);
	$k .= kbm_section(
		kbm_html( '<div class="kbm-inline-cta" style="margin:0"><p class="kbm-eyebrow">Trenujesz z trenerem?</p><h2>Treningi personalne od 150 zł</h2><p>Konsultacja online jest bezpłatna. Konsultacja z pierwszym treningiem w studiu kosztuje 99 zł. Cena kolejnych treningów zależy od trenera (150 lub 200 zł), a pakiet 10 treningów z trenerem klubowym kosztuje 1500 zł. Do treningów potrzebny jest karnet, najlepiej Siłownia z trenerem.</p><div class="kbm-btn-row"><a class="kbm-btn kbm-btn--gold" href="/treningi-personalne/">Zobacz treningi personalne</a><a class="kbm-btn kbm-btn--ghost" href="/trening-probny/">Konsultacja online 0 zł</a></div></div>' )
	);
	$k .= kbm_section(
		kbm_head( 'Pytania o karnety', 'Wszystko, co warto wiedzieć' ) . kbm_sc( '[kbm_faq set="karnety"]' )
		. kbm_html( '<p style="margin-top:28px">Prowadzisz własnych podopiecznych? Sprawdź <a href="#dla-trenerow">karnet trenerski</a>.</p>' )
	);
	$k .= kbm_section(
		kbm_html( '<div id="dla-trenerow"></div>' ) . kbm_head( 'Dla trenerów', 'Prowadź treningi w KB Motion', 'Karnet trenerski pozwala prowadzić treningi personalne z własnymi podopiecznymi na terenie studia.' ) . kbm_sc( '[kbm_pricing cat="dla-trenerow"]' ),
		'is-dark'
	);
	$p['karnety'] = array( 'Karnety i cennik', $k, 'page-landing', 'Cennik karnetów KB Motion Wrocław: siłownia od 369 zł/mies., zajęcia grupowe, wejścia jednorazowe od 45 zł. Kup karnet online i płać BLIK-iem.', 'Karnety i cennik siłowni | KB Motion Wrocław' );

	/* ---------------- TRENINGI PERSONALNE ---------------- */
	$t  = kbm_hero(
		'{{img:studio3}}',
		'Treningi personalne Wrocław',
		'Trener, który<br><em>prowadzi Cię do celu</em>',
		'Redukcja tkanki tłuszczowej, budowa siły, przygotowanie motoryczne i zdrowy kręgosłup. Indywidualny plan, pełna uwaga trenera i kameralne warunki.',
		'<div class="kbm-btn-row"><a class="kbm-btn kbm-btn--gold" href="#pakiety">Zobacz ceny</a><a class="kbm-btn kbm-btn--ghost" href="#rezerwacja">Zarezerwuj trenera</a></div>',
		'<span>Pierwszy trening + konsultacja 99 zł</span><span>Treningi od 150 zł</span><span>Pakiet 10 treningów 1500 zł</span>'
	);
	$t .= kbm_section(
		kbm_head( 'Dla kogo', 'Z czym przychodzą do nas klienci' )
		. kbm_html( '<div class="kbm-features"><div class="kbm-feature kbm-reveal"><h3>Redukcja tkanki tłuszczowej</h3><p>Trening i nawyki, które da się utrzymać. Bez głodówek i cudownych diet.</p></div><div class="kbm-feature kbm-reveal"><h3>Siła i sylwetka</h3><p>Progresja oparta na technice. Wiesz, co robisz i dlaczego.</p></div><div class="kbm-feature kbm-reveal"><h3>Przygotowanie motoryczne</h3><p>Szybkość, moc i sprawność dla sportowców amatorów i zawodników.</p></div><div class="kbm-feature kbm-reveal"><h3>Zdrowy kręgosłup</h3><p>Mobilność, stabilizacja i bezpieczny powrót do ruchu po przerwie lub kontuzji.</p></div></div>' )
	);
	$t .= kbm_section(
		kbm_html( '<div id="pakiety"></div>' ) . kbm_head( 'Cennik', 'Wybierz, jak chcesz zacząć', 'Cena treningu zależy od trenera. Płacisz online, a termin ustalamy telefonicznie w ciągu jednego dnia roboczego.', true )
		. kbm_sc( '[kbm_pricing cat="treningi-personalne"]' ),
		'is-sand'
	);
	$t .= kbm_section(
		kbm_head( 'Jak pracujemy', 'Od pierwszej rozmowy do efektów', '', true )
		. kbm_html( '<div class="kbm-steps"><div class="kbm-step"><h3>Konsultacja</h3><p>Poznajemy Twój cel, historię treningową i zdrowie (ankieta PAR-Q). Online za darmo albo w studiu razem z pierwszym treningiem za 99 zł.</p></div><div class="kbm-step"><h3>Trening wdrożeniowy</h3><p>Sprawdzamy technikę i możliwości.</p></div><div class="kbm-step"><h3>Regularna praca</h3><p>Trener pilnuje progresji, techniki i motywacji. Ty widzisz efekty.</p></div></div>' ) . kbm_consult_compare_html(),
		'is-dark'
	);
	$t .= kbm_section(
		kbm_html( '<div class="kbm-split"><div><div id="rezerwacja"></div><p class="kbm-eyebrow">Rezerwacja trenera</p><h2>Zarezerwuj trening</h2><p class="kbm-lead">Wybierz trenera i porę, a my oddzwonimy z konkretnym terminem. Zwykle odpowiadamy w ciągu kilku godzin.</p><p>Wolisz od razu zapłacić? <a href="#pakiety">Kup trening online</a>.</p></div><div>' ) . kbm_sc( '[kbm_form type="trainer"]' ) . kbm_html( '</div></div>' )
	);
	$t .= kbm_section(
		kbm_head( 'Zespół', 'Nasi trenerzy' ) . kbm_sc( '[kbm_trainers owners="0"]' ),
		'is-sand'
	);
	$t .= kbm_section( kbm_head( 'FAQ', 'Pytania o treningi personalne' ) . kbm_sc( '[kbm_faq set="treningi"]' ) );
	$p['treningi-personalne'] = array( 'Treningi personalne', $t, 'page-landing', 'Trening personalny we Wrocławiu od 150 zł, pierwszy trening wdrożeniowy z konsultacją 99 zł. Redukcja, siła, przygotowanie motoryczne, zdrowy kręgosłup. Pakiet 10 treningów 1500 zł. Wybierz trenera online.', 'Trener personalny Wrocław – treningi od 150 zł | KB Motion' );

	/* ---------------- TRENERZY ---------------- */
	$tr  = kbm_section(
		kbm_head( 'Zespół KB Motion', 'Profesjonalna kadra.<br>Świadomy ruch.', 'Wierzymy, że świadomy ruch to klucz do trwałego zdrowia, komfortu i pełnej swobody w każdym aspekcie życia.', false, 'h1' )
		. kbm_html( '<h2 class="kbm-eyebrow" style="margin-top:12px">Właściciele</h2><figure class="kbm-owners-photo"><img src="{{img:studio8}}" alt="Piotr Brzóstowicz i Tomasz Kluzek, właściciele KB Motion Wrocław" loading="lazy" width="768" height="854"></figure>' ) . kbm_sc( '[kbm_trainers owners="1"]' ),
		'is-dark'
	);
	$tr .= kbm_section( kbm_head( 'Trenerzy', 'Nasi trenerzy', 'Każdy z naszych trenerów to ekspert w swojej dziedzinie, z pasją do świadomego ruchu i indywidualnego podejścia do podopiecznych.' ) . kbm_sc( '[kbm_trainers owners="0"]' ) );
	$tr .= kbm_section( kbm_html( '<div class="kbm-inline-cta" style="margin:0"><p class="kbm-eyebrow">Gotów na zmianę?</p><h2>Umów pierwszą konsultację</h2><p>Konsultacja online jest bezpłatna. Konsultacja z pierwszym treningiem w studiu kosztuje 99 zł. Wspólnie z trenerem stworzysz plan dopasowany do Twojego celu i kalendarza.</p><div class="kbm-btn-row"><a class="kbm-btn kbm-btn--gold" href="/trening-probny/">Konsultacja online 0 zł</a><a class="kbm-btn kbm-btn--ghost" href="/treningi-personalne/#rezerwacja">Zarezerwuj trenera</a></div></div>' ) );
	$p['trenerzy'] = array( 'Trenerzy', $tr, 'page-landing', 'Poznaj trenerów personalnych KB Motion we Wrocławiu. Treningi personalne, zajęcia grupowe, przygotowanie motoryczne. Umów konsultację.', 'Trenerzy personalni Wrocław | KB Motion' );

	/* ---------------- GRAFIK ---------------- */
	$g  = kbm_section(
		kbm_head( 'Zajęcia grupowe', 'Grafik zajęć', 'Małe grupy, uważny instruktor i miejsce, które rezerwujesz w aplikacji. Na zajęcia wchodzisz z karnetem na 4 lub 8 wejść albo z wejściem jednorazowym.', false, 'h1' )
		. kbm_sc( '[kbm_schedule]' ),
		'is-sand'
	);
	$g .= kbm_section( kbm_head( 'Karnety na zajęcia', 'Wybierz pakiet wejść' ) . kbm_sc( '[kbm_pricing cat="karnety" ids="{{prod:zajecia-grupowe}},{{prod:wejscie-jednorazowe}}"]' ) );
	$p['grafik-zajec'] = array( 'Grafik zajęć', $g, 'page-landing', 'Grafik zajęć grupowych w KB Motion Wrocław. Małe grupy, zapisy w aplikacji, karnety na 4 lub 8 wejść i wejścia jednorazowe.', 'Grafik zajęć grupowych | KB Motion Wrocław' );

	/* ---------------- BUFET ---------------- */
	$b  = kbm_section(
		kbm_head( 'Bufet KB Motion', 'Woda, kawa, odżywki', 'Zamów online i odbierz w studiu albo kup na miejscu. Odżywki i suplementy KFD, kawa, napoje i ręczniki KB Motion.', false, 'h1' )
		. kbm_html( '<ul class="kbm-bufet-cats"><li><a href="/kategoria-produktu/bufet/napoje/">Napoje</a></li><li><a href="/kategoria-produktu/bufet/kawa/">Kawa</a></li><li><a href="/kategoria-produktu/bufet/suplementy/">Odżywki i suplementy</a></li><li><a href="/kategoria-produktu/bufet/akcesoria/">Akcesoria</a></li></ul>' )
		. kbm_sc( '[products category="bufet" columns="4" limit="40" orderby="menu_order" order="ASC"]' ),
		'is-sand'
	);
	$p['bufet'] = array( 'Bufet', $b, 'page-landing', 'Bufet KB Motion: woda, kawa, odżywki KFD (białko, kreatyna, EAA, witaminy) i ręczniki. Zamów online, odbierz w studiu przy Nyskiej 61A.', 'Bufet i odżywki KFD | KB Motion Wrocław' );

	/* ---------------- O NAS ---------------- */
	$o  = kbm_hero(
		'{{img:studio1}}',
		'O nas',
		'Elegancja i spokój<br><em>zamiast zgiełku</em>',
		'KB Motion powstało z potrzeby stworzenia dyskretnego miejsca dla wymagających mieszkańców Wrocławia. Zamiast przepełnionych sal proponujemy kameralną atmosferę, w której skupiasz się wyłącznie na swoich celach.',
		kbm_cta_html( 'Zobacz karnety', 'Umów trening' )
	);
	$o .= kbm_section(
		kbm_html( '<div class="kbm-split"><div><p class="kbm-eyebrow">Nasze credo</p><h2>Świadomy ruch to klucz do zdrowia</h2><p class="kbm-lead">Wierzymy, że świadomy ruch to klucz do trwałego zdrowia, komfortu i pełnej swobody w każdym aspekcie życia.</p><p>Studio założyli trenerzy Piotr Brzóstowicz i Tomasz Kluzek. Każdy detal wnętrza zaprojektowaliśmy z myślą o estetyce i funkcjonalności: 250 m² przemyślanej przestrzeni, strefa siłowa, sala fitness i wejście przez aplikację mobilną.</p><ul class="kbm-checklist"><li>Kameralna przestrzeń bez tłumów</li><li>Profesjonalna opieka trenerska</li><li>Nowoczesny wstęp przez aplikację (kod QR)</li><li>Otwarte codziennie od 6:00 do 23:00</li></ul></div><div><img src="{{img:studio2}}" alt="Studio KB Motion – strefa siłowa" loading="lazy"></div></div>' )
	);
	$o .= kbm_section(
		kbm_html( '<div class="kbm-stats"><div class="kbm-stat"><b>250 m²</b><span>przestrzeni treningowej</span></div><div class="kbm-stat"><b>17 h</b><span>dziennie otwarte, 7 dni w tygodniu</span></div><div class="kbm-stat"><b>QR</b><span>wejście przez aplikację</span></div><div class="kbm-stat"><b>1:1</b><span>pełna uwaga trenera</span></div></div>' ),
		'is-dark'
	);
	$o .= kbm_section( kbm_head( 'Zespół', 'Ludzie KB Motion' ) . kbm_sc( '[kbm_trainers limit="8"]' ) );
	$p['o-nas'] = array( 'O nas', $o, 'page-landing', 'KB Motion to kameralne studio treningu personalnego we Wrocławiu: 250 m², strefa siłowa, sala fitness i wejście przez aplikację. Poznaj naszą historię.', 'O nas – studio treningu personalnego | KB Motion Wrocław' );

	/* ---------------- GALERIA ---------------- */
	$gal  = kbm_section(
		kbm_head( 'Galeria', 'Zobacz studio', '250 m² przemyślanej przestrzeni: strefa siłowa, sala fitness i detale, o które zadbaliśmy.', false, 'h1' )
		. kbm_html( '<div class="kbm-gallery"><figure><img src="{{img:hero}}" alt="Studio KB Motion" loading="lazy"></figure><figure><img src="{{img:studio1}}" alt="Wnętrze KB Motion" loading="lazy"></figure><figure><img src="{{img:studio2}}" alt="Strefa siłowa" loading="lazy"></figure><figure><img src="{{img:studio3}}" alt="Stanowisko treningowe" loading="lazy"></figure><figure><img src="{{img:studio4}}" alt="Sprzęt KB Motion" loading="lazy"></figure><figure><img src="{{img:studio5}}" alt="Sala fitness" loading="lazy"></figure><figure><img src="{{img:studio6}}" alt="Strefa treningowa" loading="lazy"></figure><figure><img src="{{img:studio7}}" alt="Detale wnętrza" loading="lazy"></figure><figure><img src="{{img:studio8}}" alt="KB Motion Wrocław" loading="lazy"></figure></div>' )
	);
	$gal .= kbm_section( kbm_html( '<div class="kbm-inline-cta" style="margin:0"><h2>Zobacz to na żywo</h2><p>Przyjdź na konsultację z pierwszym treningiem w studiu (99 zł) albo zacznij od bezpłatnej konsultacji online.</p>' . kbm_cta_html( 'Kup karnet', 'Umów trening' ) . '</div>' ) );
	$p['galeria'] = array( 'Galeria', $gal, 'page-landing', 'Zdjęcia studia treningu personalnego KB Motion we Wrocławiu: strefa siłowa, sala fitness, wnętrze.', 'Galeria studia | KB Motion Wrocław' );

	/* ---------------- KONTAKT ---------------- */
	$c  = kbm_section(
		kbm_html( '<div class="kbm-split" style="align-items:start"><div><p class="kbm-eyebrow">Kontakt i lokalizacja</p><h1>Dyskretna przestrzeń treningowa</h1><p class="kbm-lead">Kameralne studio we Wrocławiu łączące nowoczesny wstęp przez aplikację z dedykowaną opieką trenerską.</p>' ) . kbm_sc( '[kbm_contact]' ) . kbm_html( '<p style="margin-top:20px">Masz szybkie pytanie? <a href="#czat">Zapytaj asystenta</a> – odpowiada od razu.</p></div><div><h2 style="font-size:1.6rem">Zadaj pytanie zespołowi</h2>' ) . kbm_sc( '[kbm_form type="contact"]' ) . kbm_html( '</div></div>' ),
		'is-sand'
	);
	$c .= kbm_section( kbm_sc( '[kbm_map]' ), 'is-tight' );
	$p['kontakt'] = array( 'Kontakt', $c, 'page-landing', 'Kontakt z KB Motion: ul. Nyska 61A, 50-505 Wrocław, kontakt@kbmotion.pl. Otwarte codziennie 6:00–23:00. Formularz kontaktowy i mapa dojazdu.', 'Kontakt i dojazd | KB Motion Wrocław, Nyska 61A' );

	/* ---------------- TRENING PRÓBNY ---------------- */
	$tp  = kbm_section(
		kbm_html( '<div class="kbm-split" style="align-items:start"><div><p class="kbm-eyebrow">Bez zobowiązań</p><h1>Bezpłatna konsultacja online</h1><p class="kbm-lead">Porozmawiaj z trenerem online o swoim celu. Konsultacja online jest <strong>bezpłatna</strong>. Konsultacja w studiu razem z pierwszym treningiem kosztuje <strong>99 zł</strong>.</p><ul class="kbm-checklist"><li>Rozmowa o celu, zdrowiu i dotychczasowych treningach</li><li>Wskazówki, od czego zacząć</li><li>Rekomendacja karnetu lub pakietu treningów</li><li>Zero presji i zobowiązań</li></ul><p>Chcesz od razu potrenować w studiu? Wybierz <a href="/treningi-personalne/#pakiety">konsultację z treningiem wdrożeniowym za 99 zł</a>.</p></div><div id="zapis"><h2 style="font-size:1.6rem">Zapisz się na konsultację online</h2>' ) . kbm_sc( '[kbm_form type="trial"]' ) . kbm_html( '</div></div>' ) . kbm_html( '<h2 style="margin-top:56px">Online czy w studiu?</h2>' ) . kbm_consult_compare_html()
	);
	$p['trening-probny'] = array( 'Bezpłatna konsultacja online', $tp, 'page-landing', 'Bezpłatna konsultacja online z trenerem personalnym KB Motion Wrocław. Konsultacja z pierwszym treningiem w studiu: 99 zł.', 'Bezpłatna konsultacja online z trenerem | KB Motion Wrocław' );

	/* ---------------- FAQ ---------------- */
	$f  = kbm_section(
		kbm_head( 'FAQ', 'Najczęstsze pytania', 'Nie znalazłeś odpowiedzi? Zapytaj asystenta w czacie albo napisz do nas.', false, 'h1' )
		. kbm_html( '<h2 style="font-size:1.4rem;margin-top:32px">Studio</h2>' ) . kbm_sc( '[kbm_faq set="ogolne"]' )
		. kbm_html( '<h2 style="font-size:1.4rem;margin-top:40px">Karnety i płatności</h2>' ) . kbm_sc( '[kbm_faq set="karnety"]' )
		. kbm_html( '<h2 style="font-size:1.4rem;margin-top:40px">Treningi personalne</h2>' ) . kbm_sc( '[kbm_faq set="treningi"]' )
		. kbm_html( '<div class="kbm-inline-cta"><h2>Masz inne pytanie?</h2><p>Odpowiemy zwykle w ciągu jednego dnia roboczego.</p><div class="kbm-btn-row"><a class="kbm-btn kbm-btn--gold" href="/kontakt/">Napisz do nas</a><a class="kbm-btn kbm-btn--ghost" href="#czat">Zapytaj asystenta</a></div></div>' ),
		'',
		'wide'
	);
	$p['faq'] = array( 'FAQ', $f, 'page-landing', 'Najczęstsze pytania o KB Motion Wrocław: karnety, płatności, wejście przez aplikację, treningi personalne, godziny otwarcia.', 'FAQ – pytania o karnety i treningi | KB Motion' );


	/* ---------------- LEGAL ---------------- */
	foreach ( kbm_legal_pages() as $slug => $legal ) {
		$p[ $slug ] = $legal;
	}
	return $p;
}
