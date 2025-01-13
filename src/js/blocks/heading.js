import { gsap } from "gsap";

import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

function updateHeadingsContent() {
  const interactiveHeadings = document.querySelectorAll('.wp-block-heading.is-interactive');

  interactiveHeadings.forEach(heading => {
    const headingContent = heading.textContent;
    let newHeadingContent = `<span class="screen-reader-text">${headingContent}</span>`;

    newHeadingContent += '<span class="wp-block-heading__wrapper"><span aria-hidden="true" class="wp-block-heading__rail">';

    for(let i = 0; i < 5; i++){
      newHeadingContent += `<span class="wp-block-heading__asterisk"><svg class="icon"><use xlink:href="#icon_asterisk" href="#icon_asterisk"></use></svg></span><span class="wp-block-heading__duplicate-content">${headingContent}</span>`;
    }

    newHeadingContent += '</span></span>';

    heading.innerHTML = newHeadingContent;
  });
}

function animateInteractiveHeadings() {
  const interactiveHeadings = document.querySelectorAll('.wp-block-heading.is-interactive');

  interactiveHeadings.forEach(heading => {
    const headingRail = heading.querySelector('.wp-block-heading__rail');

    gsap.to(headingRail, {
      x: () => -(window.innerWidth * 0.8),
      transformOrigin: "left center",
      ease: "none",
      scrollTrigger: {
        start: "top bottom",
        trigger: headingRail,
        scrub: 0.25
      },
    });
  });
}

document.addEventListener("DOMContentLoaded", function() {
  updateHeadingsContent();
  animateInteractiveHeadings();
});