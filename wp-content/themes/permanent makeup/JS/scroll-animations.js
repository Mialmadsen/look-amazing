document.addEventListener("DOMContentLoaded", function () {
  gsap.registerPlugin(ScrollTrigger);

  // Animate cards with a stagger on scroll
  gsap.utils.toArray(".fade-stagger").forEach((container) => {
    gsap.from(container.querySelectorAll(".card"), {
      y: 50,
      opacity: 0,
      duration: 0.8,
      ease: "power2.out",
      stagger: 0.2,
      scrollTrigger: {
        trigger: container,
        start: "top 80%",
      }
    });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  if (window.gsap) {
    const tl = gsap.timeline({ defaults: { ease: "power2.out", duration: 0.8 } });

    tl.from(".hero-title",    { y: 30, opacity: 0 })
      .from(".hero-subtitle", { y: 30, opacity: 0 }, "-=0.4")
      .from(".hero-cta",      { y: 20, opacity: 0 }, "-=0.4");
  } else {
    console.warn("GSAP not found");
  }
});
document.addEventListener('DOMContentLoaded', () => {
  const btn  = document.querySelector('.pm-nav__toggle');
  const menu = document.getElementById('pm-menu');
  if (!btn || !menu) return;

  btn.addEventListener('click', () => {
    const expanded = btn.getAttribute('aria-expanded') === 'true';
    btn.setAttribute('aria-expanded', String(!expanded));
    menu.classList.toggle('open');
  });

  // optional: close menu when clicking a link (mobile UX)
  menu.addEventListener('click', e => {
    if (e.target.matches('a')) {
      btn.setAttribute('aria-expanded', 'false');
      menu.classList.remove('open');
    }
  });
});



