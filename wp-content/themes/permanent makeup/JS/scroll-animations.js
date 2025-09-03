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