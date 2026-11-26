<?php
/**
 * Legal pages. Stored as classic HTML (editable in the Classic block).
 * Company data come from KB Motion settings via shortcodes, so a change of legal entity is one edit.
 */
defined( 'ABSPATH' ) || exit;

function kbm_ol( array $items ) {
	return '<ol><li>' . implode( '</li><li>', $items ) . '</li></ol>';
}

function kbm_legal_pages() {
	$org   = esc_html( kbm_opt( 'company' ) . ', NIP ' . kbm_opt( 'nip' ) . ', ' . kbm_opt( 'company_address' ) );
	$mail  = '<a href="mailto:' . esc_attr( kbm_opt( 'email' ) ) . '">' . esc_html( kbm_opt( 'email' ) ) . '</a>';
	$pages = array();

	/* ---------- Regulamin studia (tekst z obecnej strony kbmotion.pl) ---------- */
	$r  = '<p><strong>Regulamin studia treningowego KB MOTION</strong><br>KB MOTION – Studio Treningowe, ul. Nyska 61A, Wrocław</p>';
	$r .= '<h2>§1. Postanowienia ogólne</h2>' . kbm_ol( array(
		'Niniejszy regulamin określa zasady korzystania ze studia treningowego KB MOTION, zwanego dalej „Studiem”.',
		'Właścicielem i podmiotem prowadzącym Studio jest ' . $org . ', zwany dalej „Organizatorem”.',
		'Regulamin obowiązuje wszystkie osoby przebywające na terenie Studia, w szczególności klientów korzystających z treningów personalnych, karnetów, zajęć grupowych oraz innych oferowanych usług.',
		'Każda osoba korzystająca z usług Studia zobowiązana jest do zapoznania się z regulaminem i przestrzegania jego postanowień.',
		'Korzystanie z usług Studia oznacza akceptację regulaminu.',
	) );
	$r .= '<h2>§2. Godziny funkcjonowania Studia</h2>' . kbm_ol( array(
		'Studio funkcjonuje w godzinach określonych przez Organizatora.',
		'Godziny otwarcia mogą ulegać zmianom z przyczyn organizacyjnych lub technicznych.',
		'Organizator zastrzega sobie prawo do czasowego ograniczenia dostępności wybranych stref Studia, np. z powodu prac konserwacyjnych, napraw lub organizacji wydarzeń.',
		'Informacje o zmianach godzin funkcjonowania będą przekazywane klientom za pośrednictwem dostępnych kanałów komunikacji.',
	) );
	$r .= '<h2>§3. Zasady korzystania ze Studia</h2>' . kbm_ol( array(
		'Z usług Studia mogą korzystać osoby posiadające ważny karnet, opłacone wejście jednorazowe lub wykupioną usługę treningową.',
		'Karnety i wejścia są przypisane do konkretnego użytkownika i nie mogą być udostępniane osobom trzecim.',
		'Klient zobowiązany jest do korzystania ze sprzętu i wyposażenia zgodnie z jego przeznaczeniem.',
		'Po zakończonym ćwiczeniu klient zobowiązany jest do odłożenia sprzętu na wyznaczone miejsce.',
		'Podczas korzystania ze sprzętu treningowego obowiązuje używanie ręcznika. Ręcznik należy umieszczać na wszystkich powierzchniach mających bezpośredni kontakt z ciałem, w szczególności na ławkach, siedziskach, oparciach oraz matach.',
		'Zabrania się rzucania sprzętem, niszczenia wyposażenia oraz wykonywania ćwiczeń w sposób stwarzający zagrożenie dla innych osób.',
		'Na terenie strefy treningowej należy używać odpowiedniego stroju sportowego i czystego obuwia zmiennego.',
		'Ćwiczenia typu martwy ciąg i wiosłowania wykonujemy w wyznaczonej strefie (podest) lub korzystamy z poduch amortyzujących.',
		'Zabrania się wnoszenia na salę treningową szklanych opakowań oraz jedzenia.',
		'Klient zobowiązany jest do zachowania porządku oraz poszanowania komfortu pozostałych użytkowników.',
	) );
	$r .= '<h2>§4. Treningi personalne</h2>' . kbm_ol( array(
		'Treningi personalne odbywają się po wcześniejszym ustaleniu terminu z trenerem.',
		'Klient zobowiązany jest do punktualnego przybycia na umówiony trening.',
		'W przypadku spóźnienia trening może zostać skrócony o czas spóźnienia, bez obowiązku przedłużenia go poza pierwotnie ustalony termin.',
		'Odwołanie lub zmiana terminu treningu powinny nastąpić najpóźniej 24 godziny przed planowanym rozpoczęciem.',
		'W przypadku nieodwołania treningu w wymaganym terminie lub niepojawienia się klienta trener może uznać trening za zrealizowany.',
		'Trening personalny powinien być dostosowany do możliwości, aktualnego stanu zdrowia i poziomu sprawności klienta.',
		'Klient zobowiązany jest poinformować trenera o urazach, przebytych kontuzjach, ograniczeniach ruchowych oraz innych okolicznościach mogących mieć wpływ na bezpieczeństwo treningu.',
		'Trener ma prawo odmówić przeprowadzenia treningu lub zmienić jego przebieg, jeżeli uzna, że jego realizacja może zagrażać zdrowiu lub bezpieczeństwu klienta.',
	) );
	$r .= '<h2>§5. Zajęcia grupowe</h2>' . kbm_ol( array(
		'Udział w zajęciach grupowych odbywa się na podstawie obowiązującego karnetu, opłaconego wejścia lub innej formy uprawnienia.',
		'Na wybrane zajęcia może obowiązywać wcześniejsza rezerwacja miejsca.',
		'Liczba miejsc na zajęciach może być ograniczona.',
		'Klient powinien pojawić się na zajęciach punktualnie.',
		'Instruktor ma prawo odmówić udziału w zajęciach osobie, której stan zdrowia lub zachowanie mogą stanowić zagrożenie dla niej samej lub pozostałych uczestników.',
		'Organizator zastrzega sobie możliwość zmiany grafiku, instruktora lub odwołania zajęć z przyczyn organizacyjnych.',
		'Informacje o zmianach będą przekazywane za pośrednictwem dostępnych kanałów komunikacji.',
	) );
	$r .= '<h2>§6. Zdrowie i bezpieczeństwo</h2>' . kbm_ol( array(
		'Każdy klient korzysta z usług Studia z uwzględnieniem własnego stanu zdrowia i możliwości fizycznych.',
		'Klient oświadcza, że jego stan zdrowia pozwala na podejmowanie aktywności fizycznej albo że korzysta z niej zgodnie z zaleceniami właściwego specjalisty.',
		'Klient zobowiązany jest niezwłocznie poinformować trenera o złym samopoczuciu, bólu, zawrotach głowy lub innych niepokojących objawach pojawiających się podczas treningu.',
		'W przypadku wystąpienia niepokojących objawów trening należy przerwać.',
		'Na terenie Studia zabrania się: przebywania pod wpływem alkoholu lub środków odurzających; palenia tytoniu oraz używania papierosów elektronicznych; zachowań agresywnych i stwarzających zagrożenie; samodzielnego naprawiania lub ingerowania w sprzęt; korzystania z urządzeń w sposób niezgodny z ich przeznaczeniem.',
		'W przypadku zagrożenia zdrowia lub życia należy niezwłocznie powiadomić personel Studia.',
	) );
	$r .= '<h2>§7. Karnety, płatności i rezygnacja z usług</h2>' . kbm_ol( array(
		'Aktualny cennik usług dostępny jest na stronie <a href="/karnety/">www.kbmotion.pl/karnety</a>.',
		'Opłaty za usługi należy uiszczać zgodnie z obowiązującym cennikiem i zasadami płatności.',
		'Karnety mają określony termin ważności, o którym klient jest informowany podczas zakupu.',
		'Karnety są imienne i nie mogą być przekazywane innym osobom bez zgody Organizatora.',
		'Niewykorzystanie karnetu w okresie jego ważności nie stanowi automatycznej podstawy do zwrotu pieniędzy.',
		'Zawieszenie lub przedłużenie ważności karnetu jest możliwe wyłącznie na zasadach określonych przez Organizatora i przedstawionych przy zakupie.',
		'Zasady zwrotów, odstąpienia od umowy i reklamacji określają właściwe przepisy prawa, <a href="/regulamin-sklepu/">regulamin sklepu</a> oraz warunki danej usługi.',
		'W przypadku wystąpienia uzasadnionych okoliczności klient może skontaktować się z Organizatorem w celu indywidualnego rozpatrzenia sprawy.',
	) );
	$r .= '<h2>§8. Odpowiedzialność</h2>' . kbm_ol( array(
		'Klient ponosi odpowiedzialność za szkody wyrządzone z własnej winy w wyposażeniu lub infrastrukturze Studia.',
		'Klient zobowiązany jest niezwłocznie zgłosić personelowi zauważone uszkodzenia sprzętu lub wyposażenia.',
		'Studio nie ponosi odpowiedzialności za rzeczy pozostawione bez nadzoru poza przypadkami wynikającymi z obowiązujących przepisów prawa.',
		'Klient powinien przechowywać rzeczy wartościowe w sposób zabezpieczający je przed utratą.',
		'Organizator nie ponosi odpowiedzialności za skutki zatajenia przez klienta informacji dotyczących jego stanu zdrowia, z zastrzeżeniem bezwzględnie obowiązujących przepisów prawa.',
		'Postanowienia regulaminu nie wyłączają ani nie ograniczają odpowiedzialności Organizatora w zakresie, w jakim takie wyłączenie lub ograniczenie byłoby niedopuszczalne na podstawie przepisów prawa.',
	) );
	$r .= '<h2>§9. Dzieci i osoby niepełnoletnie</h2>' . kbm_ol( array(
		'Osoby niepełnoletnie mogą korzystać z usług Studia za zgodą rodzica lub opiekuna prawnego, z uwzględnieniem charakteru danej usługi.',
		'W przypadku osób poniżej 16. roku życia korzystanie z treningów wymaga wcześniejszego uzgodnienia z Organizatorem i odpowiedniego nadzoru.',
		'Rodzic lub opiekun prawny odpowiada za przekazanie informacji o ograniczeniach zdrowotnych osoby niepełnoletniej oraz udzielenie wymaganych zgód.',
		'Dzieci przebywające na terenie Studia pozostają pod opieką rodzica lub opiekuna, chyba że uczestniczą w zorganizowanych zajęciach prowadzonych przez uprawnioną osobę.',
	) );
	$r .= '<h2>§10. Monitoring i ochrona danych osobowych</h2>' . kbm_ol( array(
		'Na terenie Studia może być prowadzony monitoring wizyjny w celu zapewnienia bezpieczeństwa osób i ochrony mienia.',
		'Monitoring nie obejmuje pomieszczeń, w których jego stosowanie naruszałoby prywatność klientów, w szczególności toalet i szatni.',
		'Administratorem danych osobowych klientów jest Organizator.',
		'Dane osobowe przetwarzane są w celach związanych z realizacją usług, obsługą płatności, kontaktem z klientami oraz wypełnianiem obowiązków prawnych.',
		'Szczegółowe informacje dotyczące przetwarzania danych osobowych znajdują się w <a href="/polityka-prywatnosci/">polityce prywatności</a>.',
	) );
	$r .= '<h2>§11. Zachowanie i poszanowanie innych klientów</h2>' . kbm_ol( array(
		'Każdy klient zobowiązany jest do kulturalnego zachowania i poszanowania innych osób korzystających ze Studia.',
		'Zabrania się: używania wulgaryzmów i zachowań obraźliwych; nagrywania lub fotografowania innych klientów bez ich zgody; utrudniania innym osobom korzystania ze sprzętu; zajmowania urządzeń bez ich faktycznego użytkowania.',
		'Nagrywanie materiałów na potrzeby prywatne lub promocyjne nie może naruszać prywatności innych klientów.',
		'Personel Studia ma prawo zwrócić uwagę osobom naruszającym zasady regulaminu.',
	) );
	$r .= '<h2>§12. Naruszenie regulaminu</h2>' . kbm_ol( array(
		'W przypadku naruszenia regulaminu Organizator może: udzielić klientowi upomnienia; zażądać zaprzestania określonego zachowania; czasowo ograniczyć możliwość korzystania z wybranych stref; odmówić dalszego świadczenia usług w przypadku poważnego lub powtarzającego się naruszenia regulaminu.',
		'Decyzje podejmowane są z uwzględnieniem charakteru naruszenia i obowiązujących przepisów prawa.',
		'Powyższe postanowienia nie ograniczają praw klienta wynikających z zawartej umowy i przepisów prawa.',
	) );
	$r .= '<h2>§13. Reklamacje</h2>' . kbm_ol( array(
		'Klient ma prawo składać reklamacje dotyczące usług świadczonych przez KB MOTION.',
		'Reklamacje można składać osobiście w recepcji Studia lub drogą elektroniczną na adres ' . $mail . '.',
		'Reklamacja powinna zawierać opis sytuacji oraz dane umożliwiające kontakt z klientem.',
		'Reklamacje będą rozpatrywane w terminach wynikających z obowiązujących przepisów prawa.',
	) );
	$r .= '<h2>§14. Postanowienia końcowe</h2>' . kbm_ol( array(
		'Organizator zastrzega sobie prawo do aktualizacji regulaminu z ważnych przyczyn organizacyjnych, prawnych lub technicznych.',
		'O zmianach regulaminu klienci będą informowani poprzez publikację aktualnej wersji w Studiu lub na stronie internetowej.',
		'Zmiany nie naruszają praw klientów wynikających z wcześniej zawartych umów.',
		'W sprawach nieuregulowanych niniejszym regulaminem zastosowanie mają odpowiednie przepisy prawa polskiego.',
		'Regulamin obowiązuje od dnia 15.11.2026.',
	) );
	$pages['regulamin-studia'] = array( 'Regulamin studia', $r, '', 'Regulamin studia treningowego KB Motion we Wrocławiu: zasady korzystania, treningi personalne, zajęcia grupowe, karnety, bezpieczeństwo.', 'Regulamin studia | KB Motion' );

	/* ---------- Regulamin sklepu internetowego ---------- */
	$s  = '<p>Regulamin określa zasady zakupów w sklepie internetowym KB Motion dostępnym pod adresem tej strony (dalej „Sklep”). Obowiązuje od dnia 15.11.2026.</p>';
	$s .= '<h2>§1. Sprzedawca</h2>' . kbm_ol( array(
		'Sprzedawcą jest ' . $org . ' (dalej „Sprzedawca”).',
		'Kontakt ze Sprzedawcą: e-mail ' . $mail . ', adres studia: ul. Nyska 61A, 50-505 Wrocław.',
	) );
	$s .= '<h2>§2. Definicje</h2>' . kbm_ol( array(
		'<strong>Klient</strong> – osoba fizyczna, osoba prawna lub jednostka organizacyjna dokonująca zakupu w Sklepie.',
		'<strong>Konsument</strong> – Klient będący osobą fizyczną dokonującą zakupu niezwiązanego bezpośrednio z jej działalnością gospodarczą lub zawodową; przepisy dotyczące Konsumenta stosuje się także do przedsiębiorcy na prawach konsumenta w zakresie przewidzianym prawem.',
		'<strong>Produkty</strong> – oferowane w Sklepie usługi (karnety, wejścia jednorazowe, treningi personalne i ich pakiety, karnet trenerski) oraz towary bufetu (napoje, kawa, suplementy diety, akcesoria).',
		'<strong>Studio</strong> – studio treningowe KB Motion przy ul. Nyskiej 61A we Wrocławiu.',
	) );
	$s .= '<h2>§3. Wymagania techniczne</h2>' . kbm_ol( array(
		'Do korzystania ze Sklepu potrzebne jest urządzenie z dostępem do Internetu, aktualna przeglądarka internetowa z włączoną obsługą JavaScript i plików cookies oraz aktywny adres e-mail.',
		'Zakazane jest dostarczanie przez Klienta treści o charakterze bezprawnym.',
	) );
	$s .= '<h2>§4. Składanie zamówień</h2>' . kbm_ol( array(
		'Ceny w Sklepie są cenami brutto w złotych polskich. Ceny wariantów z umową na 6 lub 12 miesięcy podane są za jeden miesiąc.',
		'Aby złożyć zamówienie, Klient wybiera Produkt, przechodzi do formularza zamówienia, podaje imię, nazwisko, adres e-mail i numer telefonu (a w razie potrzeby dane do faktury), akceptuje Regulamin i wybiera metodę płatności.',
		'Kliknięcie przycisku potwierdzającego zamówienie oznacza złożenie zamówienia z obowiązkiem zapłaty. Umowa zostaje zawarta z chwilą otrzymania przez Klienta e-maila potwierdzającego przyjęcie zamówienia.',
		'Karnety są imienne. Przy zakupie karnetu Klient wskazuje datę rozpoczęcia, od której Sprzedawca aktywuje karnet w systemie klubowym i przesyła zaproszenie do aplikacji umożliwiającej wejście do Studia.',
		'Pakiety treningów personalnych realizowane są w terminach uzgodnionych z trenerem zgodnie z <a href="/regulamin-studia/">Regulaminem studia</a>. Przy pakietach treningów wymagany jest aktywny karnet uprawniający do wejścia do Studia.',
	) );
	$s .= '<h2>§5. Płatności</h2>' . kbm_ol( array(
		'Dostępne metody płatności: karta płatnicza, BLIK oraz szybkie przelewy online, obsługiwane przez operatora płatności wskazanego w formularzu zamówienia.',
		'Klient zobowiązany jest do zapłaty niezwłocznie po złożeniu zamówienia. Zamówienie nieopłacone w ciągu 24 godzin może zostać anulowane.',
		'Dowód zakupu (paragon lub faktura) przekazywany jest Klientowi w formie elektronicznej lub w Studiu.',
	) );
	$s .= '<h2>§6. Realizacja zamówienia</h2>' . kbm_ol( array(
		'Karnety aktywowane są od wskazanej daty rozpoczęcia, nie później niż w ciągu 1 dnia roboczego od zaksięgowania płatności, jeżeli data rozpoczęcia już minęła.',
		'Termin treningu personalnego Sprzedawca uzgadnia z Klientem telefonicznie lub e-mailowo w ciągu 1 dnia roboczego od zaksięgowania płatności.',
		'Towary bufetu wydawane są wyłącznie w Studiu, w godzinach jego otwarcia, po okazaniu numeru zamówienia. Sklep nie prowadzi wysyłki towarów.',
	) );
	$s .= '<h2>§7. Prawo odstąpienia od umowy</h2>' . kbm_ol( array(
		'Konsument może odstąpić od umowy zawartej na odległość bez podania przyczyny w terminie 14 dni. Termin biegnie od dnia zawarcia umowy (usługi) albo od dnia objęcia towaru w posiadanie (towary bufetu).',
		'Aby odstąpić od umowy, wystarczy wysłać jednoznaczne oświadczenie na adres ' . $mail . '. Można skorzystać ze wzoru formularza odstąpienia stanowiącego załącznik nr 2 do ustawy o prawach konsumenta.',
		'Jeżeli Konsument zażąda rozpoczęcia świadczenia usługi przed upływem terminu na odstąpienie (np. wskazując datę rozpoczęcia karnetu przypadającą przed upływem 14 dni lub umawiając trening), a następnie odstąpi od umowy, zapłaci kwotę proporcjonalną do zakresu usługi spełnionej do chwili poinformowania o odstąpieniu.',
		'Prawo odstąpienia nie przysługuje w odniesieniu do usług w pełni wykonanych za wyraźną zgodą Konsumenta, który został poinformowany przed rozpoczęciem świadczenia, że po spełnieniu świadczenia utraci prawo odstąpienia, a także w odniesieniu do towarów dostarczanych w zapieczętowanym opakowaniu, których po otwarciu nie można zwrócić ze względu na ochronę zdrowia lub względy higieniczne (np. suplementy diety, napoje), jeżeli opakowanie zostało otwarte po dostarczeniu, oraz towarów ulegających szybkiemu zepsuciu (np. kawa przygotowywana na miejscu).',
		'Sprzedawca zwraca płatność niezwłocznie, nie później niż w terminie 14 dni od otrzymania oświadczenia o odstąpieniu, przy użyciu takiego samego sposobu zapłaty, jakiego użył Konsument, chyba że Konsument zgodzi się na inny sposób.',
	) );
	$s .= '<h2>§8. Reklamacje</h2>' . kbm_ol( array(
		'Sprzedawca odpowiada za zgodność Produktu z umową na zasadach określonych w ustawie o prawach konsumenta oraz Kodeksie cywilnym.',
		'Reklamację można złożyć e-mailowo na adres ' . $mail . ' lub osobiście w Studiu. Reklamacja powinna zawierać dane kontaktowe, numer zamówienia i opis problemu.',
		'Sprzedawca odpowiada na reklamację w terminie 14 dni od jej otrzymania.',
	) );
	$s .= '<h2>§9. Pozasądowe sposoby rozpatrywania reklamacji</h2>' . kbm_ol( array(
		'Konsument może skorzystać z pomocy miejskiego lub powiatowego rzecznika konsumentów, stałych polubownych sądów konsumenckich przy wojewódzkich inspektoratach Inspekcji Handlowej oraz z informacji dostępnych na stronie Urzędu Ochrony Konkurencji i Konsumentów (uokik.gov.pl).',
	) );
	$s .= '<h2>§10. Dane osobowe</h2>' . kbm_ol( array(
		'Administratorem danych osobowych Klientów jest Sprzedawca. Zasady przetwarzania danych opisuje <a href="/polityka-prywatnosci/">Polityka prywatności</a>.',
	) );
	$s .= '<h2>§11. Postanowienia końcowe</h2>' . kbm_ol( array(
		'Sprzedawca może zmienić Regulamin z ważnych przyczyn (zmiana prawa, zmiana metod płatności, zmiana zakresu usług). Zmiany nie dotyczą zamówień złożonych przed ich wejściem w życie.',
		'W sprawach nieuregulowanych Regulaminem stosuje się przepisy prawa polskiego, w szczególności Kodeksu cywilnego oraz ustawy o prawach konsumenta.',
	) );
	$pages['regulamin-sklepu'] = array( 'Regulamin sklepu', $s, '', 'Regulamin sklepu internetowego KB Motion: zamówienia karnetów, treningów i produktów bufetu, płatności, odstąpienie od umowy, reklamacje.', 'Regulamin sklepu | KB Motion' );

	/* ---------- Polityka prywatności ---------- */
	$pp  = '<p>Szanujemy Twoją prywatność. Poniżej wyjaśniamy, jakie dane zbieramy, po co i jakie masz prawa.</p>';
	$pp .= '<h2>1. Administrator danych</h2><p>Administratorem Twoich danych osobowych jest ' . $org . ' (dalej „my”). Kontakt w sprawach danych osobowych: ' . $mail . ', ul. Nyska 61A, 50-505 Wrocław.</p>';
	$pp .= '<h2>2. Jakie dane i w jakich celach przetwarzamy</h2><ul>'
		. '<li><strong>Formularze kontaktowe, zapis na konsultację, rezerwacja trenera</strong> – imię i nazwisko, e-mail, telefon, treść wiadomości; cel: odpowiedź i umówienie usługi; podstawa: Twoja zgoda (art. 6 ust. 1 lit. a RODO) oraz działania przed zawarciem umowy (art. 6 ust. 1 lit. b RODO).</li>'
		. '<li><strong>Zamówienia w sklepie</strong> – dane do zamówienia i faktury; cel: zawarcie i wykonanie umowy, aktywacja karnetu w systemie klubowym, obsługa płatności i reklamacji; podstawa: art. 6 ust. 1 lit. b i c RODO (obowiązki podatkowe i rachunkowe).</li>'
		. '<li><strong>Korzystanie ze studia i aplikacji</strong> – dane konta w aplikacji klubowej, historia wejść i rezerwacji, informacje zdrowotne przekazane w ankiecie PAR-Q; cel: realizacja usług i bezpieczeństwo treningu; podstawa: art. 6 ust. 1 lit. b RODO, a dla danych o zdrowiu Twoja wyraźna zgoda (art. 9 ust. 2 lit. a RODO).</li>'
		. '<li><strong>Monitoring wizyjny w studiu</strong> – wizerunek; cel: bezpieczeństwo osób i mienia; podstawa: nasz prawnie uzasadniony interes (art. 6 ust. 1 lit. f RODO); nagrania przechowujemy nie dłużej niż 3 miesiące.</li>'
		. '<li><strong>Marketing</strong> – e-mail lub telefon, jeśli wyrazisz zgodę na informacje o promocjach; podstawa: zgoda.</li>'
		. '<li><strong>Analityka i reklama</strong> – dane z plików cookies (Google Analytics, Meta Pixel) wyłącznie po wyrażeniu zgody w banerze cookies; podstawa: zgoda.</li>'
		. '<li><strong>Asystent AI na stronie</strong> – treść pytań wpisanych w czacie; cel: udzielenie odpowiedzi; podstawa: nasz prawnie uzasadniony interes. Nie podawaj w czacie danych wrażliwych.</li>'
		. '<li><strong>Dochodzenie roszczeń i obrona</strong> – podstawa: prawnie uzasadniony interes (art. 6 ust. 1 lit. f RODO).</li></ul>';
	$pp .= '<h2>3. Odbiorcy danych</h2><p>Twoje dane mogą otrzymać podmioty, które pomagają nam prowadzić studio i stronę: dostawca hostingu (Hostinger), dostawca systemu klubowego i aplikacji (eFitness), operator płatności (np. Stripe), dostawca usług poczty e-mail, biuro rachunkowe, a po wyrażeniu zgody także Google i Meta. Pytania wpisane w czacie przetwarza dostawca modelu AI (Anthropic). Część tych podmiotów może przetwarzać dane poza Europejskim Obszarem Gospodarczym; odbywa się to na podstawie standardowych klauzul umownych lub decyzji Komisji Europejskiej stwierdzającej odpowiedni poziom ochrony.</p>';
	$pp .= '<h2>4. Jak długo przechowujemy dane</h2><ul><li>Dane z formularzy – do czasu obsługi zgłoszenia, a następnie maksymalnie 12 miesięcy.</li><li>Dane z zamówień – przez okres wymagany przepisami podatkowymi (5 lat od końca roku podatkowego).</li><li>Dane przetwarzane na podstawie zgody – do jej wycofania.</li><li>Dane klubowe – przez czas korzystania z usług, a potem do upływu terminów przedawnienia roszczeń.</li></ul>';
	$pp .= '<h2>5. Twoje prawa</h2><p>Masz prawo dostępu do danych, ich sprostowania, usunięcia, ograniczenia przetwarzania, przenoszenia, wniesienia sprzeciwu oraz wycofania zgody w dowolnym momencie (bez wpływu na zgodność z prawem przetwarzania przed wycofaniem). Masz też prawo wnieść skargę do Prezesa Urzędu Ochrony Danych Osobowych (ul. Stawki 2, 00-193 Warszawa). Aby skorzystać z praw, napisz na ' . $mail . '.</p>';
	$pp .= '<h2>6. Dobrowolność podania danych</h2><p>Podanie danych jest dobrowolne, ale niezbędne do odpowiedzi na wiadomość, realizacji zamówienia lub świadczenia usług.</p>';
	$pp .= '<h2>7. Pliki cookies</h2><p>Szczegóły dotyczące plików cookies opisujemy w <a href="/polityka-cookies/">Polityce cookies</a>.</p>';
	$pages['polityka-prywatnosci'] = array( 'Polityka prywatności', $pp, '', 'Polityka prywatności KB Motion: administrator danych, cele i podstawy przetwarzania, odbiorcy, okres przechowywania, prawa osób (RODO).', 'Polityka prywatności | KB Motion' );

	/* ---------- Polityka cookies ---------- */
	$pc  = '<p>Strona używa plików cookies i podobnych technologii (np. localStorage). Cookies to małe pliki zapisywane na Twoim urządzeniu.</p>';
	$pc .= '<h2>Rodzaje cookies</h2><ul><li><strong>Niezbędne</strong> – umożliwiają działanie koszyka, zamówień, logowania i zabezpieczeń (np. woocommerce_cart_hash, wp_woocommerce_session_*, kbm_consent_v1). Nie wymagają zgody.</li><li><strong>Analityczne</strong> – Google Analytics 4 (_ga, _ga_*), pomagają nam zrozumieć, jak korzystasz ze strony. Uruchamiamy je tylko po Twojej zgodzie.</li><li><strong>Marketingowe</strong> – Meta Pixel (_fbp), pozwalają mierzyć skuteczność reklam i docierać z ofertą. Uruchamiamy je tylko po Twojej zgodzie.</li><li><strong>Zewnętrzne treści</strong> – mapa Google ładuje się dopiero po kliknięciu „Pokaż mapę”; wtedy Google może zapisać własne cookies.</li></ul>';
	$pc .= '<h2>Zarządzanie zgodą</h2><p>Zgodę możesz zmienić w każdej chwili: [kbm_cookie_settings]. Możesz też usunąć lub zablokować cookies w ustawieniach przeglądarki, co może jednak utrudnić korzystanie ze sklepu.</p>';
	$pc .= '<h2>Administrator</h2><p>Administratorem danych zbieranych za pomocą cookies jest ' . $org . '. Więcej informacji znajdziesz w <a href="/polityka-prywatnosci/">Polityce prywatności</a>.</p>';
	$pages['polityka-cookies'] = array( 'Polityka cookies', $pc, '', 'Polityka cookies strony KB Motion: rodzaje plików cookies, Google Analytics, Meta Pixel, zarządzanie zgodą.', 'Polityka cookies | KB Motion' );

	/* ---------- Regulamin aplikacji (z obecnej strony) ---------- */
	$ra  = '<p><strong>' . $org . '</strong> · e-mail: ' . $mail . ' · studio: ul. Nyska 61A, Wrocław.</p>';
	$ra .= '<p>Obowiązuje od 21 listopada 2026 r. Niniejszy dokument określa zasady korzystania z aplikacji mobilnej KB Motion, rejestracji konta, rezerwacji zajęć oraz wstępu do studia treningu personalnego we Wrocławiu.</p>';
	$ra .= '<h2>1. Cel aplikacji i rejestracja</h2><p>Aplikacja służy do planowania treningów, kontaktu z trenerem, rezerwacji zajęć, obsługi karnetów i wejść do klubu. Konto zakłada się przez aplikację, z kodem zaproszenia od studia.</p>';
	$ra .= '<h2>2. Karnety i opłaty</h2><p>Rodzaje, ceny i okres ważności określa <a href="/karnety/">cennik</a>. Karnet jest imienny i nie można go przekazać innej osobie.</p>';
	$ra .= '<h2>3. Wejście do klubu (QR)</h2><p>Wejście do klubu odbywa się na podstawie kodu QR z aplikacji. Udostępnianie kodu innym osobom jest zabronione.</p>';
	$ra .= '<h2>4. Rezerwacje i odwołania</h2><p>Rezerwacje zajęć można odwołać najpóźniej w terminie podanym w aplikacji. Nieobecność bez odwołania może skutkować wykorzystaniem wejścia.</p>';
	$ra .= '<h2>5. Zdrowie i ankieta PAR-Q</h2><p>Klient oświadcza, że jego stan zdrowia pozwala na udział w treningach, a o przeciwwskazaniach informuje trenera (ankieta PAR-Q). Propozycje ciężarów w aplikacji mają charakter pomocniczy; o obciążeniu decyduje klient wspólnie z trenerem.</p>';
	$ra .= '<h2>6. Reklamacje i odstąpienie</h2><p>Reklamacje dotyczące usług i aplikacji można zgłaszać na adres ' . $mail . '. Odpowiadamy w ciągu 14 dni. Konsument może odstąpić od umowy zawartej na odległość w terminie 14 dni, z zastrzeżeniem przepisów o usługach rozpoczętych za wyraźną zgodą konsumenta.</p>';
	$ra .= '<h2>Zarządzanie kontem</h2><p>Użytkownik może w każdej chwili usunąć konto w aplikacji (ścieżka: Profil → Moje dane → Usuń konto).</p><p>Usługodawca może zmienić regulamin z ważnych przyczyn. O zmianie użytkownik zostanie poinformowany w aplikacji i poproszony o akceptację nowej wersji. W sprawach nieuregulowanych stosuje się przepisy prawa polskiego.</p><p>Masz pytania dotyczące polityki prywatności, przetwarzania danych lub aplikacji? Skontaktuj się bezpośrednio z obsługą studia KB Motion: ' . $mail . '.</p>';
	$pages['regulamin-aplikacji'] = array( 'Regulamin aplikacji', $ra, '', 'Regulamin aplikacji mobilnej KB Motion: rejestracja, karnety, wejście QR, rezerwacje i odwołania, ankieta PAR-Q, usuwanie konta.', 'Regulamin aplikacji | KB Motion' );

	return $pages;
}
