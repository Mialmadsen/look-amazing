document.addEventListener('DOMContentLoaded', () => {
  const mqlReduce = window.matchMedia('(prefers-reduced-motion: reduce)');
  const hasGSAP   = !!window.gsap;

  // Always: mobile menu toggle
  {
    const btn  = document.querySelector('.pm-nav__toggle');
    const menu = document.getElementById('pm-menu');
    if (btn && menu) {
      btn.addEventListener('click', () => {
        const expanded = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', String(!expanded));
        menu.classList.toggle('open');
      });
      menu.addEventListener('click', e => {
        if (e.target.matches('a')) {
          btn.setAttribute('aria-expanded', 'false');
          menu.classList.remove('open');
        }
      });
    }
  }

  // If user prefers reduced motion or GSAP missing → skip animations
  if (mqlReduce.matches || !hasGSAP) {
    if (!hasGSAP) console.warn('GSAP not found — animations skipped.');
    return;
  }

  // Register ScrollTrigger only if present
  if (window.ScrollTrigger) {
    gsap.registerPlugin(ScrollTrigger);
  }

  // --- Fade-stagger cards on scroll ---
  // (only if we actually have a .fade-stagger on the page)
  const staggerContainers = gsap.utils.toArray('.fade-stagger');
  if (staggerContainers.length) {
    staggerContainers.forEach((container) => {
      const cards = container.querySelectorAll('.card');
      if (!cards.length) return;

      gsap.from(cards, {
        y: 50,
        opacity: 0,
        duration: 0.8,
        ease: 'power2.out',
        stagger: 0.2,
        // Use ScrollTrigger if available; otherwise play once
        ...(window.ScrollTrigger ? {
          scrollTrigger: {
            trigger: container,
            start: 'top 80%',
            toggleActions: 'play none none none'
          }
        } : {})
      });
    });
  }

  // --- Hero intro animation ---
  
  const heroH1  = document.querySelector('.hero-title'); 
  const heroH3  = document.querySelector('.hero-subtitle');   
  const heroCTA = document.querySelector('.hero-cta');       

  if (heroH1 || heroH3 || heroCTA) {
    const tl = gsap.timeline({ defaults: { ease: 'power2.out', duration: 0.8 } });
    if (heroH1)  tl.from(heroH1,  { y: 30, opacity: 0 });
    if (heroH3)  tl.from(heroH3,  { y: 30, opacity: 0 }, '-=0.4');
    if (heroCTA) tl.from(heroCTA, { y: 20, opacity: 0 }, '-=0.4');
  }
});


