/* KB Motion Core – forms, pricing variants, map, cookie consent, AI chat. No dependencies. */
(function () {
	'use strict';
	var C = window.KBM || {};

	/* ---------- Pricing: keep hidden attribute inputs in sync with the chosen variant ---------- */
	document.querySelectorAll('.kbm-price__form').forEach(function (form) {
		form.addEventListener('change', function (e) {
			if (e.target.name !== 'variation_id') return;
			form.querySelectorAll('.kbm-attr').forEach(function (i) { i.remove(); });
			var attrs = {};
			try { attrs = JSON.parse(e.target.getAttribute('data-attrs') || '{}'); } catch (err) {}
			Object.keys(attrs).forEach(function (k) {
				var i = document.createElement('input');
				i.type = 'hidden'; i.className = 'kbm-attr'; i.name = k; i.value = attrs[k];
				form.appendChild(i);
			});
		});
	});

	/* ---------- Forms ---------- */
	document.querySelectorAll('[data-kbm-form]').forEach(function (form) {
		var status = form.querySelector('.kbm-form__status');
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var bad = null;
			form.querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
			form.querySelectorAll('[required]').forEach(function (el) {
				var ok = el.type === 'checkbox' ? el.checked : el.value.trim() !== '' && el.checkValidity();
				if (!ok) {
					(el.type === 'checkbox' ? el.closest('.kbm-check') : el).classList.add('is-invalid');
					bad = bad || el;
				}
			});
			if (bad) {
				status.className = 'kbm-form__status is-error';
				status.textContent = bad.type === 'checkbox' ? 'Zaznacz wymaganą zgodę.' : 'Uzupełnij wymagane pola.';
				bad.focus();
				return;
			}
			var data = {};
			new FormData(form).forEach(function (v, k) { data[k] = v; });
			form.classList.add('is-loading');
			status.className = 'kbm-form__status';
			status.textContent = 'Wysyłanie…';
			fetch(C.rest + 'form', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) })
				.then(function (r) { return r.json().then(function (j) { return { ok: r.ok && j.ok, j: j }; }); })
				.then(function (res) {
					form.classList.remove('is-loading');
					status.textContent = res.j.message || (res.ok ? 'Dziękujemy!' : 'Coś poszło nie tak.');
					status.className = 'kbm-form__status ' + (res.ok ? 'is-ok' : 'is-error');
					if (res.ok) {
						form.classList.add('is-done');
						track('generate_lead', { form_type: data.kbm_type });
					}
				})
				.catch(function () {
					form.classList.remove('is-loading');
					form.submit(); // no-JS style fallback via admin-post.php
				});
		});
	});

	/* ---------- Map: click to load ---------- */
	document.querySelectorAll('.kbm-map__load').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var box = btn.closest('.kbm-map');
			var f = document.createElement('iframe');
			f.src = box.getAttribute('data-src');
			f.loading = 'lazy';
			f.title = 'Mapa dojazdu do KB Motion';
			f.referrerPolicy = 'no-referrer-when-downgrade';
			box.innerHTML = '';
			box.appendChild(f);
		});
	});

	/* ---------- Cookie consent ---------- */
	var KEY = 'kbm_consent_v1';
	function readConsent() { try { return JSON.parse(localStorage.getItem(KEY) || 'null'); } catch (e) { return null; } }
	function saveConsent(c) { try { localStorage.setItem(KEY, JSON.stringify(c)); } catch (e) {} document.cookie = KEY + '=' + (c.analytics ? 1 : 0) + (c.marketing ? 1 : 0) + ';path=/;max-age=31536000;SameSite=Lax'; }
	var loaded = {};
	function applyConsent(c) {
		if (!c) return;
		if (window.gtag) {
			gtag('consent', 'update', {
				analytics_storage: c.analytics ? 'granted' : 'denied',
				ad_storage: c.marketing ? 'granted' : 'denied',
				ad_user_data: c.marketing ? 'granted' : 'denied',
				ad_personalization: c.marketing ? 'granted' : 'denied'
			});
		}
		if (c.analytics && C.ga4 && !loaded.ga) {
			loaded.ga = 1;
			var s = document.createElement('script');
			s.async = true; s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(C.ga4);
			document.head.appendChild(s);
			gtag('js', new Date()); gtag('config', C.ga4, { anonymize_ip: true });
		}
		if (c.marketing && C.pixel && !loaded.px) {
			loaded.px = 1;
			!function (f, b, e, v, n, t, s) { if (f.fbq) return; n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments); }; if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = []; t = b.createElement(e); t.async = !0; t.src = v; s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s); }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
			fbq('init', C.pixel); fbq('track', 'PageView');
		}
	}
	function track(ev, params) {
		if (window.gtag && loaded.ga) gtag('event', ev, params || {});
		if (window.fbq && loaded.px) fbq('track', ev === 'generate_lead' ? 'Lead' : ev);
	}
	window.kbmTrack = track;
	var banner = document.getElementById('kbm-consent');
	if (banner) {
		var prefs = banner.querySelector('.kbm-consent__prefs');
		var current = readConsent();
		if (current) applyConsent(current); else banner.hidden = false;
		banner.addEventListener('click', function (e) {
			var a = e.target.getAttribute('data-consent-action');
			if (!a) return;
			var c;
			if (a === 'settings') {
				if (prefs.hidden) { prefs.hidden = false; e.target.textContent = 'Zapisz wybór'; return; }
				c = { analytics: banner.querySelector('[data-consent="analytics"]').checked, marketing: banner.querySelector('[data-consent="marketing"]').checked };
			} else {
				c = { analytics: a === 'accept', marketing: a === 'accept' };
			}
			c.ts = Date.now();
			saveConsent(c); applyConsent(c); banner.hidden = true;
		});
		document.addEventListener('click', function (e) {
			var o = e.target.closest('[data-consent-open]');
			if (!o) return;
			e.preventDefault();
			var c = readConsent() || {};
			banner.querySelector('[data-consent="analytics"]').checked = !!c.analytics;
			banner.querySelector('[data-consent="marketing"]').checked = !!c.marketing;
			prefs.hidden = false;
			banner.hidden = false;
		});
	}

	/* WooCommerce events for analytics */
	document.addEventListener('submit', function (e) {
		if (e.target.classList && e.target.classList.contains('kbm-price__form')) track('add_to_cart');
	}, true);
	if (document.body.classList.contains('woocommerce-order-received')) track('purchase');

	/* ---------- AI chat ---------- */
	if (!C.chat) return;
	var fab = document.createElement('button');
	fab.className = 'kbm-chat-fab';
	fab.type = 'button';
	fab.setAttribute('aria-label', 'Otwórz czat z asystentem KB Motion');
	fab.setAttribute('aria-expanded', 'false');
	fab.innerHTML = '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12Z"/></svg><span class="kbm-chat-fab__dot"></span>';
	var box = document.createElement('section');
	box.className = 'kbm-chat';
	box.id = 'czat';
	box.hidden = true;
	box.setAttribute('aria-label', 'Czat z asystentem KB Motion');
	box.innerHTML = '<header class="kbm-chat__head"><span class="kbm-chat__avatar">KB</span><div><div class="kbm-chat__title">' + C.chatName + '</div><div class="kbm-chat__sub">Odpowiadam od razu, 24/7</div></div><button class="kbm-chat__close" type="button" aria-label="Zamknij czat">×</button></header>' +
		'<div class="kbm-chat__log" role="log" aria-live="polite"></div>' +
		'<div class="kbm-chat__chips"><button type="button">Ile kosztuje karnet?</button><button type="button">Jak zacząć z trenerem?</button><button type="button">Godziny otwarcia</button></div>' +
		'<form class="kbm-chat__form"><label class="screen-reader-text" for="kbm-chat-input">Twoje pytanie</label><input id="kbm-chat-input" type="text" maxlength="1000" placeholder="Zadaj pytanie…" autocomplete="off"><button type="submit" aria-label="Wyślij"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 20.5 21 12 3 3.5 5.5 12 3 20.5Z"/></svg></button></form>' +
		'<p class="kbm-chat__legal">Asystent AI może się pomylić. Nie podawaj w czacie danych wrażliwych. <a href="' + C.privacy + '">Prywatność</a></p>';
	document.body.appendChild(fab);
	document.body.appendChild(box);
	var log = box.querySelector('.kbm-chat__log');
	var input = box.querySelector('input');
	var chips = box.querySelector('.kbm-chat__chips');
	var history = [];
	try { history = JSON.parse(sessionStorage.getItem('kbm_chat') || '[]'); } catch (e) {}

	function bubble(text, who, actions) {
		var m = document.createElement('div');
		m.className = 'kbm-msg kbm-msg--' + who;
		m.textContent = text;
		log.appendChild(m);
		if (actions && actions.length) {
			var a = document.createElement('div');
			a.className = 'kbm-chat__actions';
			actions.forEach(function (x) {
				var l = document.createElement('a');
				l.href = x.url; l.textContent = x.label;
				a.appendChild(l);
			});
			log.appendChild(a);
		}
		log.scrollTop = log.scrollHeight;
		return m;
	}
	function persist() { try { sessionStorage.setItem('kbm_chat', JSON.stringify(history.slice(-20))); } catch (e) {} }
	function greet() {
		if (log.childElementCount) return;
		bubble('Cześć! Jestem asystentem KB Motion. Pomogę Ci wybrać karnet, umówić trening personalny albo odpowiem na pytania o studio.', 'bot');
		history.forEach(function (h) { bubble(h.content, h.role === 'user' ? 'user' : 'bot'); });
		if (history.length) chips.hidden = true;
	}
	function toggle(open) {
		box.hidden = !open;
		fab.setAttribute('aria-expanded', open ? 'true' : 'false');
		if (open) { greet(); setTimeout(function () { input.focus(); }, 50); }
	}
	fab.addEventListener('click', function () { toggle(box.hidden); });
	box.querySelector('.kbm-chat__close').addEventListener('click', function () { toggle(false); fab.focus(); });
	document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !box.hidden) toggle(false); });
	document.querySelectorAll('[data-kbm-chat-open], a[href="#czat"]').forEach(function (el) {
		el.addEventListener('click', function (e) { e.preventDefault(); toggle(true); });
	});
	if (location.hash === '#czat') { toggle(true); }
	chips.addEventListener('click', function (e) {
		if (e.target.tagName === 'BUTTON') send(e.target.textContent);
	});
	box.querySelector('form').addEventListener('submit', function (e) {
		e.preventDefault();
		send(input.value);
	});
	var busy = false;
	function send(text) {
		text = (text || '').trim();
		if (!text || busy) return;
		busy = true;
		chips.hidden = true;
		input.value = '';
		bubble(text, 'user');
		history.push({ role: 'user', content: text });
		persist();
		var t = bubble('', 'bot');
		t.classList.add('kbm-msg--typing');
		t.innerHTML = '<span></span><span></span><span></span>';
		fetch(C.rest + 'chat', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ messages: history.slice(-10) }) })
			.then(function (r) { return r.json(); })
			.then(function (j) {
				t.remove();
				var reply = j.reply || 'Przepraszam, nie udało mi się odpowiedzieć. Napisz do nas przez formularz kontaktowy.';
				bubble(reply, 'bot', j.actions);
				history.push({ role: 'assistant', content: reply });
				persist();
				track('chat_message');
			})
			.catch(function () {
				t.remove();
				bubble('Brak połączenia. Spróbuj ponownie za chwilę albo napisz do nas przez formularz kontaktowy.', 'bot', [{ label: 'Kontakt', url: C.buyUrl.replace(/karnety\/?$/, 'kontakt/') }]);
			})
			.finally(function () { busy = false; });
	}
})();
