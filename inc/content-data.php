<?php
/**
 * Installer data: media, trainers, products, blog posts.
 */
defined( 'ABSPATH' ) || exit;

function kbm_media_sources() {
	$a = 'https://assets.zyrosite.com/xiOxc95W3ywYtK9n/';
	$s = 'https://cdn.zyrosite.com/cdn-ecommerce/store_01M2FSJSWSP7PS97MW4QT8V00V/assets/';
	return array(
		'logo'          => array( $a . 'fe78bfe3-56f3-43bb-bdde-7d90d5a46250-hyZsSB0QC2VvVEoB.png', 'Logo KB Motion' ),
		'hero'          => array( $a . 'img_9810-rZ3PoFIyVFuNMVzZ.JPG', 'Studio KB Motion Wrocław' ),
		'studio1'       => array( $a . 'img_9806-jmsLqJpxsfEoc2p6.JPG', 'Wnętrze studia KB Motion' ),
		'studio2'       => array( $a . 'img_9822-0e9IejUYyB2FeGk3.JPG', 'Strefa siłowa KB Motion' ),
		'studio3'       => array( $a . 'img_9819-YFMj32iF95bFiigH.JPG', 'Trening personalny KB Motion' ),
		'studio4'       => array( $a . 'img_9817-oImO1IgCe1jtljYE.JPG', 'Sprzęt treningowy KB Motion' ),
		'studio5'       => array( $a . 'img_9812-z5R916Pm0ejGNg9B.JPG', 'Sala fitness KB Motion' ),
		'studio6'       => array( $a . 'img_0307-fEMKzmRXs0pj6jCL.JPG', 'Strefa treningowa KB Motion' ),
		'studio7'       => array( $a . 'img_0330-7Q7T1V9V4NiIVrwp.JPG', 'Detale wnętrza KB Motion' ),
		'studio8'       => array( $a . 'fe8a0141-vMkEGFATKh53foi9.jpg', 'KB Motion Wrocław' ),
		'studio9'       => array( $a . 'jarz_2-eLl670y8yrLnpMov.jpg', 'KB Motion – studio' ),
		't_a'           => array( $a . '9ec89c4f-f068-47d3-91f9-2343426e8469-5sIAVa0MnmHXtmzi.JPG', 'Trener KB Motion Wrocław' ),
		't_b'           => array( $a . 'aa8ba88e-1705-4a8f-a531-59faa5ec1299-IcobbGwNwuDpDz2d.JPG', 'Trener KB Motion Wrocław' ),
		't_c'           => array( $a . 'img_0086-bn5VJIZbPJ333aii.JPG', 'Trener KB Motion Wrocław' ),
		't_d'           => array( $a . 'img_0035-jYxtm6Z3yWtA3Mtd.JPG', 'Trener KB Motion Wrocław' ),
		'p_woda05'      => array( $s . 'e3a8470f-cce5-404d-ae73-a8b999478cf7.png', 'Woda Nałęczowianka 0,5 l' ),
		'p_woda15'      => array( $s . 'a026c63d-9d9b-4b1d-a956-39220fc731af.png', 'Woda Nałęczowianka 1,5 l' ),
		'p_spritz'      => array( $s . '23ae0eca-bae6-4bef-8231-e377e8eca864.webp', 'WRCLW Spritz bezalkoholowy' ),
		'p_d3'          => array( $s . 'c8cf95c3-0b3b-45c1-8958-ef79ff8e4972.webp', 'KFD Vitamin D3' ),
		'p_czinc'       => array( $s . '02744dd9-0516-4d3e-9aa2-ac962940d965.webp', 'KFD Vitamin C + Zinc' ),
		'p_xgnr'        => array( $s . 'd001d592-4c55-4fae-ac5e-4db179a536dc.webp', 'KFD Premium X-GNR' ),
		'p_eaa'         => array( $s . '3aa4f4ef-0c0e-41d5-9896-9eb80ed4e7d6.webp', 'KFD Premium EAA' ),
		'p_creatine'    => array( $s . '5bd826c7-fff7-454b-a9d2-157d37249650.png', 'KFD Premium Creatine' ),
		'p_towel_grey'  => array( $s . 'bd4bf065-2e91-427c-8063-f1d25efa746f.webp', 'Ręcznik KB Motion szary' ),
		'p_towel_black' => array( $s . '5e38d2b9-9f6f-4ce0-9045-39ca8433a9c2.webp', 'Ręcznik KB Motion czarny' ),
		'p_kawa_m'      => array( $s . 'eb1866e5-d53c-4c33-943a-ade99de3add5.jpg', 'Kawa mała' ),
		'p_kawa_b'      => array( $s . 'dd4b6a68-9701-4f4f-88fc-05b40ad8f697.jpg', 'Kawa duża biała' ),
		'p_kawa_c'      => array( $s . 'ac86fff4-5e93-43ff-a669-9587d8c81bfa.jpg', 'Kawa duża czarna' ),
		'p_wpc'         => array( $s . '651587ed-d941-4de2-b9f9-98c8102b6b9a.webp', 'KFD Premium WPC 82' ),
	);
}

function kbm_trainers_data() {
	// name, role, tags, owner, order, price per training (PLN, 0 = no personal training)
	return array(
		array( 'Piotr Brzóstowicz', 'Właściciel · trener personalny', '', 1, 1, 200 ),
		array( 'Tomasz Kluzek', 'Właściciel · trener personalny', 'redukcja, siła, dietetyka', 1, 2, 200 ),
		array( 'Patryk Górecki', 'Treningi personalne', '', 0, 3, 200 ),
		array( 'Kasia Górecka', 'Treningi personalne', '', 0, 4, 200 ),
		array( 'Gustave Cordonnier', 'Treningi personalne', '', 0, 5, 150 ),
		array( 'Aleksander Przychodzeń', 'Treningi personalne', '', 0, 6, 150 ),
		array( 'Paweł', 'Treningi personalne', '', 0, 7, 150 ),
		array( 'Agata Kublicka', 'Zajęcia grupowe', '', 0, 8, 0 ),
	);
}

/**
 * Products. type: simple|variable. For variable: 'vars' => [ [label, price, unit], ... ].
 */
function kbm_products_data() {
	$ul = function ( array $li ) {
		return '<ul><li>' . implode( '</li><li>', $li ) . '</li></ul>';
	};
	return array(
		/* ----- Karnety ----- */
		array( 'slug' => 'silownia', 'name' => 'Siłownia', 'cat' => 'karnety', 'type' => 'variable', 'order' => 1, 'sold_ind' => 1, 'attr' => 'Okres',
			'vars' => array( array( 'Bez zobowiązań', 399, '/ mies.' ), array( 'Umowa 6 miesięcy', 379, '/ mies.' ), array( 'Umowa 12 miesięcy', 369, '/ mies.' ) ),
			'short' => $ul( array( 'Siłownia i sala fitness codziennie 6:00–23:00', 'Wejście na kod QR z aplikacji', 'Bez zajęć grupowych' ) ),
			'desc' => '<p>Karnet uprawniający do korzystania z siłowni wraz z salą fitness, bez zajęć grupowych. Wariant bez zobowiązań możesz zakończyć w dowolnym momencie. Warianty z umową na 6 lub 12 miesięcy mają niższą cenę miesięczną: online płacisz za pierwszy miesiąc, a kolejne opłaty rozliczamy co miesiąc zgodnie z umową.</p>' ),
		array( 'slug' => 'silownia-z-trenerem', 'name' => 'Siłownia z trenerem', 'cat' => 'karnety', 'type' => 'simple', 'price' => 219, 'unit' => '/ mies.', 'order' => 2, 'featured' => 1, 'sold_ind' => 1,
			'short' => $ul( array( 'Dla osób trenujących z trenerem personalnym', 'Na czas nieokreślony, bez okresu wypowiedzenia', 'Dostęp do siłowni i sali fitness', 'Trener rozliczany osobno' ) ),
			'desc' => '<p>Karnet umożliwiający korzystanie z siłowni osobom, które trenują z trenerem personalnym KB Motion. Karnet jest na czas nieokreślony i nie ma okresu wypowiedzenia: płacisz co miesiąc i możesz zrezygnować w dowolnym momencie. Treningi z trenerem kupujesz osobno jako pojedyncze treningi lub pakiet.</p>' ),
		array( 'slug' => 'zajecia-grupowe', 'name' => 'Zajęcia grupowe', 'cat' => 'karnety', 'type' => 'variable', 'order' => 3, 'sold_ind' => 1, 'attr' => 'Wejścia',
			'vars' => array( array( '4 wejścia w miesiącu', 149, '' ), array( '8 wejść w miesiącu', 279, '' ) ),
			'short' => $ul( array( 'Zapisy na zajęcia w aplikacji', 'Małe grupy i uważny instruktor', 'Ważny 1 miesiąc' ) ),
			'desc' => '<p>Karnet miesięczny na zajęcia grupowe: cztery lub osiem wejść w miesiącu. Miejsca rezerwujesz w aplikacji KB Motion.</p>' ),
		array( 'slug' => 'silownia-4-wejscia', 'name' => 'Siłownia 4 wejścia w miesiącu', 'cat' => 'karnety', 'type' => 'simple', 'price' => 149, 'unit' => '/ mies.', 'order' => 4, 'sold_ind' => 1,
			'short' => $ul( array( '4 wejścia na siłownię w miesiącu', 'Tylko na treningi personalne z trenerem', 'Najtańszy dostęp do pakietu treningów' ) ),
			'desc' => '<p>Karnet umożliwiający wejście i korzystanie z siłowni cztery razy w miesiącu, przeznaczony wyłącznie na treningi personalne z trenerem.</p>' ),
		array( 'slug' => 'wejscie-jednorazowe', 'name' => 'Wejście jednorazowe', 'cat' => 'karnety', 'type' => 'variable', 'order' => 5, 'attr' => 'Rodzaj',
			'vars' => array( array( 'Siłownia', 49, '' ), array( 'Zajęcia grupowe', 45, '' ) ),
			'short' => $ul( array( 'Jedno wejście w godzinach otwarcia', 'Idealne na start lub dla gości', 'Bez zobowiązań' ) ),
			'desc' => '<p>Jednorazowe wejście na siłownię w godzinach otwarcia studia albo na jedne dowolnie wybrane zajęcia grupowe.</p>' ),
		array( 'slug' => 'karnet-trenerski', 'name' => 'Karnet trenerski', 'cat' => 'dla-trenerow', 'type' => 'simple', 'price' => 1300, 'unit' => '/ mies.', 'order' => 6, 'sold_ind' => 1, 'cta' => 'Kup karnet trenerski',
			'short' => $ul( array( 'Prowadzenie treningów personalnych w studiu', 'Ważny 1 miesiąc', 'Dla trenerów z własnymi podopiecznymi' ) ),
			'desc' => '<p>Karnet umożliwiający prowadzenie treningów personalnych na terenie studia KB Motion przez trenerów zewnętrznych.</p>' ),

		/* ----- Treningi personalne ----- */
		array( 'slug' => 'trening-wdrozeniowy-konsultacja', 'name' => 'Trening wdrożeniowy + konsultacja', 'cat' => 'treningi-personalne', 'type' => 'simple', 'price' => 99, 'unit' => '', 'order' => 1, 'featured' => 1, 'cta' => 'Zacznij od tego',
			'short' => $ul( array( 'Ok. 60 minut z wybranym trenerem', 'Konsultacja celu i zdrowia', 'Ocena techniki i plan na start' ) ),
			'desc' => '<p>Najlepszy sposób, aby zacząć. Trening wdrożeniowy wraz z konsultacją z dowolnie wybranym trenerem (około 60 minut). Poznamy Twój cel, sprawdzimy technikę i zaproponujemy plan.</p>' ),
		array( 'slug' => 'trening-personalny', 'name' => 'Trening personalny', 'cat' => 'treningi-personalne', 'type' => 'variable', 'order' => 2, 'attr' => 'Trener',
			'vars' => 'TRAINERS',
			'short' => $ul( array( 'Ty wybierasz trenera', 'Cena zależy od trenera: 150 lub 200 zł', 'Bez zobowiązań' ) ),
			'desc' => '<p>Trening personalny z wybranym trenerem KB Motion. Cena zależy od trenera: 200 zł (Piotr, Tomasz, Patryk, Kasia) lub 150 zł (Gustave, Aleksander, Paweł). Termin potwierdzamy telefonicznie w ciągu jednego dnia roboczego.</p>' ),
		array( 'slug' => 'pakiet-10-treningow-personalnych', 'name' => 'Pakiet 10 treningów personalnych', 'cat' => 'treningi-personalne', 'type' => 'simple', 'price' => 1500, 'unit' => '', 'order' => 3,
			'short' => $ul( array( '10 treningów z trenerem klubowym', 'Trenera dobiera klub', 'Wymagany aktywny karnet' ) ),
			'desc' => '<p>Pakiet 10 treningów personalnych z trenerem klubowym. Trenera dobiera klub, dopasowując go do Twojego celu i dostępnych terminów. Jeśli chcesz trenować z konkretną osobą, wybierz pojedynczy trening personalny z wybranym trenerem. Przy pakiecie wymagany jest aktywny karnet (np. Siłownia z trenerem).</p>' ),

		/* ----- Bufet ----- */
		array( 'slug' => 'woda-naleczowianka-niegazowana-0-5-l', 'name' => 'Woda Nałęczowianka niegazowana 0,5 l', 'cat' => 'napoje', 'type' => 'simple', 'price' => 4, 'img' => 'p_woda05', 'stock' => 10, 'deposit' => 1, 'order' => 1,
			'desc' => '<p>Naturalna woda mineralna Nałęczowianka, średniozmineralizowana, idealna do codziennego nawadniania i na trening. Do ceny doliczana jest kaucja 0,50 zł za butelkę.</p>' ),
		array( 'slug' => 'woda-naleczowianka-niegazowana-1-5-l', 'name' => 'Woda Nałęczowianka niegazowana 1,5 l', 'cat' => 'napoje', 'type' => 'simple', 'price' => 6, 'img' => 'p_woda15', 'stock' => 10, 'deposit' => 1, 'order' => 2,
			'desc' => '<p>Nałęczowianka to naturalna woda mineralna z optymalną kompozycją 7 minerałów i niską zawartością sodu. Doskonale orzeźwia i gasi pragnienie. Do ceny doliczana jest kaucja 0,50 zł za butelkę.</p>' ),
		array( 'slug' => 'dobry-material-wrclw-spritz-bezalkoholowy', 'name' => 'Dobry Materiał – WRCLW Spritz bezalkoholowy', 'cat' => 'napoje', 'type' => 'simple', 'price' => 10, 'img' => 'p_spritz', 'stock' => 10, 'order' => 3,
			'desc' => '<p>WRCLW Spritz od rzemieślniczej marki Dobry Materiał to bezalkoholowa alternatywa dla klasycznego aperitifu: soczyste cytrusy, nuta ziołowej goryczki i orzeźwiające nagazowanie.</p>' ),
		array( 'slug' => 'kawa-mala', 'name' => 'Kawa mała', 'cat' => 'kawa', 'type' => 'simple', 'price' => 6, 'img' => 'p_kawa_m', 'order' => 4, 'desc' => '<p>Mała czarna kawa przygotowywana na miejscu w studiu.</p>' ),
		array( 'slug' => 'kawa-duza-czarna', 'name' => 'Kawa duża – czarna', 'cat' => 'kawa', 'type' => 'simple', 'price' => 8, 'img' => 'p_kawa_c', 'order' => 5, 'desc' => '<p>Duża czarna kawa przygotowywana na miejscu w studiu.</p>' ),
		array( 'slug' => 'kawa-duza-biala', 'name' => 'Kawa duża – biała', 'cat' => 'kawa', 'type' => 'simple', 'price' => 9, 'img' => 'p_kawa_b', 'order' => 6, 'desc' => '<p>Duża kawa z mlekiem przygotowywana na miejscu w studiu.</p>' ),
		array( 'slug' => 'kfd-premium-wpc-82-900-g', 'name' => 'KFD Premium WPC 82 – 900 g', 'cat' => 'suplementy', 'type' => 'simple', 'price' => 129.99, 'img' => 'p_wpc', 'stock' => 10, 'order' => 7,
			'desc' => '<p>Wysokiej jakości, instantyzowany koncentrat białka serwatkowego. Świetnie się rozpuszcza i smakuje, wspiera regenerację i budowę masy mięśniowej.</p>' ),
		array( 'slug' => 'kfd-premium-creatine-500-g', 'name' => 'KFD Premium Creatine 500 g', 'cat' => 'suplementy', 'type' => 'simple', 'price' => 49.99, 'img' => 'p_creatine', 'stock' => 10, 'order' => 8,
			'desc' => '<p>Monohydrat kreatyny najwyższej jakości dla osób aktywnych, które chcą zwiększyć siłę, wytrzymałość i beztłuszczową masę mięśniową. Świetna rozpuszczalność i szybka przyswajalność.</p>' ),
		array( 'slug' => 'kfd-premium-eaa-375-g', 'name' => 'KFD Premium EAA 375 g', 'cat' => 'suplementy', 'type' => 'simple', 'price' => 59.99, 'img' => 'p_eaa', 'stock' => 10, 'order' => 9,
			'desc' => '<p>Pełen profil aminokwasów egzogennych wspierających regenerację i budowę mięśni. Doskonała rozpuszczalność i orzeźwiający owocowy smak.</p>' ),
		array( 'slug' => 'kfd-premium-x-gnr-1000-g', 'name' => 'KFD Premium X-GNR 1000 g', 'cat' => 'suplementy', 'type' => 'simple', 'price' => 69.99, 'img' => 'p_xgnr', 'stock' => 10, 'order' => 10,
			'desc' => '<p>Gainer dla osób budujących masę mięśniową i dbających o szybką regenerację. Zbilansowane połączenie węglowodanów i pełnowartościowego białka.</p>' ),
		array( 'slug' => 'kfd-vitamin-d3-4000-iu-200-kaps', 'name' => 'KFD Vitamin D3 4000 IU – 200 kaps.', 'cat' => 'suplementy', 'type' => 'simple', 'price' => 29.99, 'img' => 'p_d3', 'stock' => 10, 'order' => 11,
			'desc' => '<p>Suplement diety z naturalną witaminą D pozyskiwaną z lanoliny. Kapsułki z olejem zapewniają dobrą biodostępność. Wspiera odporność oraz zdrowe kości i mięśnie.</p>' ),
		array( 'slug' => 'kfd-vitamin-c-zinc-120-kaps', 'name' => 'KFD Vitamin C + Zinc – 120 kaps.', 'cat' => 'suplementy', 'type' => 'simple', 'price' => 29.99, 'img' => 'p_czinc', 'stock' => 10, 'order' => 12,
			'desc' => '<p>Połączenie witaminy C i cynku wspomagające prawidłowe funkcjonowanie układu odpornościowego i ochronę komórek przed stresem oksydacyjnym.</p>' ),
		array( 'slug' => 'recznik-kb-motion-czarny-50x100', 'name' => 'Ręcznik KB Motion czarny 50×100 – mikrofibra', 'cat' => 'akcesoria', 'type' => 'simple', 'price' => 45, 'img' => 'p_towel_black', 'stock' => 10, 'order' => 13,
			'desc' => '<p>Miękki ręcznik z mikrofibry o doskonałej chłonności i błyskawicznym czasie schnięcia. Czarny design ze złotym logo KB Motion i praktyczna pętelka do zawieszania.</p>' ),
		array( 'slug' => 'recznik-kb-motion-szary-50x100', 'name' => 'Ręcznik KB Motion szary 50×100 – mikrofibra', 'cat' => 'akcesoria', 'type' => 'simple', 'price' => 45, 'img' => 'p_towel_grey', 'stock' => 10, 'order' => 14,
			'desc' => '<p>Lekki, szybkoschnący ręcznik treningowy KB Motion z wysokiej jakości mikrofibry. Kompaktowy rozmiar i pętelka do zawieszania.</p>' ),
	);
}

function kbm_posts_data() {
	return array(
		array(
			'slug'  => 'jak-wybrac-trenera-personalnego-we-wroclawiu',
			'title' => 'Jak wybrać trenera personalnego we Wrocławiu? 7 pytań, które warto zadać',
			'cat'   => 'Trening personalny',
			'img'   => 'studio3',
			'desc'  => 'Jak wybrać dobrego trenera personalnego we Wrocławiu? Sprawdź 7 pytań, które warto zadać przed pierwszym treningiem, i czego unikać.',
			'body'  => '<p>Dobry trener personalny potrafi skrócić drogę do celu o miesiące, a źle dobrany sprawi, że po kilku tygodniach odpuścisz. Zanim kupisz pakiet, zadaj te pytania. Odpowiedzi szybko pokażą, czy trafiłeś na właściwą osobę.</p>
<h2>1. Od czego zaczniemy?</h2><p>Profesjonalny trener nie zaczyna od „wyciskania potów”. Najpierw pyta o cel, zdrowie, kontuzje, styl życia i dotychczasowe treningi. W KB Motion pierwsze spotkanie to konsultacja i trening wdrożeniowy: sprawdzamy technikę podstawowych ruchów i ustalamy realny plan.</p>
<h2>2. Jak będziemy mierzyć postępy?</h2><p>Bez pomiarów trudno mówić o efektach. Zapytaj, co będzie sprawdzane: obwody, skład ciała, ciężary, wytrzymałość, samopoczucie. Dobry plan ma punkty kontrolne co kilka tygodni.</p>
<h2>3. Czy plan uwzględni mój kalendarz?</h2><p>Najlepszy plan to taki, który zrealizujesz. Jeśli masz czas na dwa treningi w tygodniu, plan na pięć dni nie ma sensu. Trener powinien dopasować liczbę i długość sesji do Twojego tygodnia.</p>
<h2>4. Co z dietą?</h2><p>Trening to połowa sukcesu, szczególnie przy redukcji tkanki tłuszczowej. Zapytaj, czy trener pomoże w podstawach żywienia: ile jeść, jak komponować posiłki, jak nie wpadać w efekt jo-jo. Uważaj na obietnice „cudownych diet”.</p>
<h2>5. Jak pracujesz z kontuzjami?</h2><p>Ból pleców, kolana czy barku nie wyklucza treningu, ale wymaga doświadczenia. Trener powinien znać zasady bezpiecznej progresji i wiedzieć, kiedy odesłać do fizjoterapeuty.</p>
<h2>6. Gdzie i w jakich warunkach trenujemy?</h2><p>Zatłoczona siłownia to kolejki do sprzętu i rozproszenie. Kameralne studio pozwala trenować bez czekania i w spokoju, co przekłada się na jakość każdej minuty treningu.</p>
<h2>7. Ile to kosztuje i co dostaję w cenie?</h2><p>Porównuj nie tylko cenę za godzinę, ale też to, co zawiera: plan, kontakt między treningami, pomiary. W KB Motion pierwszy trening wdrożeniowy z konsultacją kosztuje 99 zł, kolejne treningi od 150 zł, a pakiet 10 treningów z trenerem klubowym 1500 zł.</p>
<h2>Podsumowanie</h2><p>Wybieraj trenera, który zadaje pytania, słucha i tłumaczy, co i dlaczego robicie. Najprościej sprawdzić to na konsultacji. <a href="/trening-probny/">Umów bezpłatną konsultację</a> albo zacznij od <a href="/treningi-personalne/">treningu wdrożeniowego za 99 zł</a>.</p>',
		),
		array(
			'slug'  => 'redukcja-tkanki-tluszczowej-od-czego-zaczac',
			'title' => 'Redukcja tkanki tłuszczowej: od czego zacząć, żeby nie wrócić do punktu wyjścia',
			'cat'   => 'Redukcja',
			'img'   => 'studio2',
			'desc'  => 'Redukcja tkanki tłuszczowej bez głodówek: deficyt kalorii, trening siłowy, białko, sen i kroki. Praktyczny plan od trenerów KB Motion we Wrocławiu.',
			'body'  => '<p>Większość prób odchudzania kończy się po kilku tygodniach nie dlatego, że brakuje silnej woli, ale dlatego, że plan był nie do utrzymania. Oto podstawy, które działają i nie wymagają rewolucji.</p>
<h2>Deficyt kaloryczny, ale umiarkowany</h2><p>Tkanka tłuszczowa spada wtedy, gdy przez dłuższy czas jesz trochę mniej energii, niż zużywasz. Zbyt duży deficyt kończy się głodem, spadkiem energii i utratą mięśni. Bezpieczne tempo to zwykle około 0,5–1% masy ciała tygodniowo.</p>
<h2>Trening siłowy zamiast samego cardio</h2><p>Trening siłowy pomaga zachować mięśnie podczas redukcji, a to one decydują o tym, jak wyglądasz po schudnięciu i ile energii spalasz na co dzień. Dwa–trzy treningi siłowe w tygodniu to świetna baza.</p>
<h2>Białko w każdym posiłku</h2><p>Białko syci na dłużej i chroni mięśnie. Dobrym punktem wyjścia jest porcja białka (mięso, ryby, jaja, nabiał, rośliny strączkowe) w każdym głównym posiłku.</p>
<h2>Kroki i codzienny ruch</h2><p>Codzienna aktywność poza treningiem często spala więcej niż sam trening. Zacznij od zwiększenia liczby kroków o 2–3 tysiące dziennie względem obecnej średniej.</p>
<h2>Sen i stres</h2><p>Niewyspanie zwiększa apetyt i utrudnia regenerację. 7–9 godzin snu to jeden z najtańszych „suplementów” na redukcji.</p>
<h2>Cierpliwość i pomiary</h2><p>Waga waha się z dnia na dzień. Patrz na średnią tygodniową, obwody i zdjęcia. Jeśli przez 2–3 tygodnie nic się nie zmienia, skoryguj plan zamiast go porzucać.</p>
<h2>Kiedy warto skorzystać z pomocy trenera?</h2><p>Jeśli zaczynasz po raz kolejny, masz mało czasu albo kontuzje, trener oszczędzi Ci błędów i dopasuje plan do Twojego życia. W KB Motion zaczynamy od konsultacji i treningu wdrożeniowego. <a href="/treningi-personalne/">Zobacz treningi personalne</a> lub <a href="/trening-probny/">zapisz się na bezpłatną konsultację</a>.</p>',
		),
		array(
			'slug'  => 'pierwszy-trening-na-silowni-jak-sie-przygotowac',
			'title' => 'Pierwszy trening na siłowni: jak się przygotować i czego się spodziewać',
			'cat'   => 'Poradnik',
			'img'   => 'studio4',
			'desc'  => 'Pierwszy raz na siłowni? Co zabrać, co zjeść, jak wygląda rozgrzewka i pierwszy plan treningowy. Poradnik dla początkujących od KB Motion Wrocław.',
			'body'  => '<p>Pierwszy trening na siłowni potrafi stresować: nowe miejsce, nieznany sprzęt, obawa przed oceną. Dobra wiadomość jest taka, że wystarczy kilka prostych zasad, żeby czuć się pewnie od pierwszej minuty.</p>
<h2>Co zabrać?</h2><ul><li>Wygodny strój sportowy i czyste obuwie zmienne.</li><li>Ręcznik (na ławki i maty) oraz butelkę wody.</li><li>Telefon z aplikacją klubu, jeśli wejście odbywa się na kod QR.</li></ul>
<h2>Co zjeść przed treningiem?</h2><p>Lekki posiłek z węglowodanami i białkiem 1,5–3 godziny wcześniej, np. jogurt z owocami lub kanapka z jajkiem. Na pusty żołądek łatwo o zawroty głowy.</p>
<h2>Rozgrzewka</h2><p>5–10 minut lekkiego cardio, a potem ruchy mobilizujące biodra, barki i kręgosłup. Pierwszą serię każdego ćwiczenia wykonaj z niewielkim obciążeniem.</p>
<h2>Prosty plan na start</h2><p>Na początek wystarczą podstawowe wzorce ruchowe: przysiad, zawias biodrowy (np. martwy ciąg z hantlami), wypychanie (pompki, wyciskanie), przyciąganie (wiosłowanie) i ćwiczenie na brzuch. 2–3 serie po 8–12 powtórzeń, z zapasem 2–3 powtórzeń w każdej serii.</p>
<h2>Technika przed ciężarem</h2><p>Na początku liczy się nauka ruchu. Poprawna technika chroni przed kontuzją i sprawia, że szybciej zobaczysz efekty. Dlatego warto, by pierwsze treningi poprowadził trener.</p>
<h2>Po treningu</h2><p>Zakwasy przez 1–3 dni są normalne. Pij wodę, zjedz posiłek z białkiem i wyśpij się. Kolejny trening zaplanuj po 48 godzinach.</p>
<h2>Zacznij spokojnie</h2><p>W kameralnym studiu nie ma tłumów, kolejek i oceniających spojrzeń. Jeśli chcesz zacząć z pomocą profesjonalisty, wybierz <a href="/treningi-personalne/">trening wdrożeniowy z konsultacją</a>, a jeśli wolisz trenować sam, sprawdź <a href="/karnety/">karnety</a>.</p>',
		),
	);
}
