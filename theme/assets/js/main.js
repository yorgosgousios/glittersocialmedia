/**
 * Glitter front-end: header, mobile menu, smooth scroll, reveals,
 * testimonial slider, back-to-top.
 */
(() => {
	const $ = (sel, root = document) => root.querySelector(sel);
	const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];
	const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
	const finePointer = matchMedia('(hover: hover) and (pointer: fine)').matches;
	const FADE_MS = reducedMotion ? 0 : 600;

	/* Smooth scroll (desktop only) ----------------------------------------- */

	const lenis = !reducedMotion && finePointer && window.Lenis
		? new window.Lenis({ lerp: 0.12, anchors: { offset: -110 } })
		: null;
	if (lenis) {
		const raf = (t) => {
			lenis.raf(t);
			requestAnimationFrame(raf);
		};
		requestAnimationFrame(raf);
	}

	/* Header: hides on scroll down, returns on scroll up; back-to-top ------- */

	const header = $('[data-header]');
	const toTop = $('[data-to-top]');
	let lastY = scrollY;
	let ticking = false;

	const onScroll = () => {
		const y = scrollY;
		header?.classList.toggle('is-scrolled', y > 10);
		header?.classList.toggle('is-hidden', y > 400 && y > lastY && !document.body.classList.contains('is-locked'));
		toTop?.classList.toggle('is-visible', y > innerHeight);
		lastY = y;
		ticking = false;
	};
	addEventListener('scroll', () => {
		if (!ticking) {
			requestAnimationFrame(onScroll);
			ticking = true;
		}
	}, { passive: true });
	onScroll();

	toTop?.addEventListener('click', () => (lenis ? lenis.scrollTo(0) : scrollTo({ top: 0, behavior: reducedMotion ? 'auto' : 'smooth' })));

	/* Mobile menu ---------------------------------------------------------- */

	const nav = $('[data-offcanvas]');
	const navOpen = $('[data-nav-open]');
	if (nav && navOpen) {
		let open = false;
		const setOpen = (value) => {
			open = value;
			navOpen.setAttribute('aria-expanded', String(value));
			document.body.classList.toggle('is-locked', value);
			if (value) {
				nav.hidden = false;
				lenis?.stop();
				requestAnimationFrame(() => nav.classList.add('is-open'));
				$('a', nav)?.focus();
			} else {
				nav.classList.remove('is-open');
				lenis?.start();
				setTimeout(() => { if (!open) nav.hidden = true; }, FADE_MS);
				navOpen.focus();
			}
		};
		navOpen.addEventListener('click', () => setOpen(true));
		$('[data-nav-close]', nav)?.addEventListener('click', () => setOpen(false));
		$$('a', nav).forEach((a) => a.addEventListener('click', () => open && setOpen(false)));
		document.addEventListener('keydown', (e) => e.key === 'Escape' && open && setOpen(false));
		matchMedia('(min-width: 1021px)').addEventListener('change', (e) => e.matches && open && setOpen(false));
	}

	/* Scroll reveal --------------------------------------------------------- */

	const reveals = $$('.reveal');
	if (reveals.length && 'IntersectionObserver' in window) {
		const io = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible');
					io.unobserve(entry.target);
				}
			});
		}, { rootMargin: '0px 0px -8% 0px' });
		reveals.forEach((el, i) => {
			// Stagger siblings in the same grid.
			if (!el.style.getPropertyValue('--delay')) el.style.setProperty('--delay', `${(i % 3) * 80}ms`);
			io.observe(el);
		});
	} else {
		reveals.forEach((el) => el.classList.add('is-visible'));
	}

	/* Testimonials: one quote at a time ------------------------------------ */

	$$('[data-quotes]').forEach((box) => {
		const quotes = $$('.quote', box);
		const nav = $('.quotes__nav', box);
		const count = $('.quotes__count', box);
		if (quotes.length < 2 || !nav) {
			quotes[0]?.classList.add('is-active');
			return;
		}
		let index = 0;
		let timer = null;
		const pad = (n) => String(n).padStart(2, '0');
		const show = (i) => {
			index = (i + quotes.length) % quotes.length;
			quotes.forEach((q, n) => {
				q.classList.toggle('is-active', n === index);
				q.setAttribute('aria-hidden', String(n !== index));
			});
			count.textContent = `${pad(index + 1)} / ${pad(quotes.length)}`;
		};
		const autoplay = () => {
			if (reducedMotion) return;
			clearInterval(timer);
			timer = setInterval(() => show(index + 1), 7000);
		};
		nav.hidden = false;
		$('[data-quotes-prev]', nav).addEventListener('click', () => { show(index - 1); autoplay(); });
		$('[data-quotes-next]', nav).addEventListener('click', () => { show(index + 1); autoplay(); });
		box.addEventListener('mouseenter', () => clearInterval(timer));
		box.addEventListener('mouseleave', autoplay);
		show(0);
		autoplay();
	});
})();
