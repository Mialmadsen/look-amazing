document.addEventListener('DOMContentLoaded', () => {
  if (!window.gsap || !window.ScrollTrigger) return;
  gsap.registerPlugin(ScrollTrigger);

  gsap.utils.toArray('ul.products').forEach((grid) => {
    const cards = grid.querySelectorAll('li.product');
    if (!cards.length) return;

    // start hidden + slightly shifted
    gsap.set(cards, { autoAlpha: 0, y: 14 });

    // reveal ALL cards together when the grid scrolls into view
    gsap.to(cards, {
      autoAlpha: 1,
      y: 0,
      duration: 0.6,
      ease: 'power2.out',
      scrollTrigger: {
        trigger: grid,
        start: 'top 80%',
        once: true
      }
    });
  });
});