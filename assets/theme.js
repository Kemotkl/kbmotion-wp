/* KB Motion theme: header state + reveal-on-scroll. */
(function () {
	var h = document.querySelector('.kbm-header');
	if (h) {
		var on = function () { h.classList.toggle('is-scrolled', window.scrollY > 10); };
		on();
		window.addEventListener('scroll', on, { passive: true });
	}
	if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
	var els = document.querySelectorAll('.kbm-reveal');
	var io = new IntersectionObserver(function (entries) {
		entries.forEach(function (e) {
			if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
		});
	}, { rootMargin: '0px 0px -10% 0px' });
	els.forEach(function (el) { el.classList.add('kbm-reveal--ready'); io.observe(el); });
})();
