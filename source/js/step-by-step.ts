/**
 * Modularity Step By Step - Frontend JavaScript
 *
 * Custom accordion logic with pure CSS animations and timeline updates
 */

(function () {
  "use strict";

  /**
   * Close a section (helper function)
   */
  function closeSection(section: HTMLElement) {
    const button = section.querySelector(".c-accordion__button") as HTMLElement;
    if (!button) return;

    const contentId = button.getAttribute("aria-controls");
    const contentElement = contentId
      ? document.getElementById(contentId)
      : null;

    // Update button state
    button.setAttribute("aria-expanded", "false");
    button.classList.remove("c-accordion__button--expanded");

    // Update section state
    section.classList.remove("c-step-by-step-timeline__section--open");

    // Update content visibility
    if (contentElement) {
      contentElement.setAttribute("aria-hidden", "true");
    }
  }

  /**
   * Toggle accordion section open/closed
   * Only one section can be open at a time
   */
  function toggleSection(button: HTMLElement) {
    const section = button.closest(".c-step-by-step-timeline__section");
    if (!section) return;

    const container = section.closest(".c-step-by-step-timeline");
    if (!container) return;

    const isExpanded = button.getAttribute("aria-expanded") === "true";
    const isOpening = !isExpanded;

    // Close all other sections in this timeline
    const allSections = container.querySelectorAll(
      ".c-step-by-step-timeline__section"
    );
    allSections.forEach((otherSection) => {
      if (otherSection !== section) {
        closeSection(otherSection as HTMLElement);
      }
    });

    // Toggle the clicked section
    if (isOpening) {
      // Opening: update button state
      button.setAttribute("aria-expanded", "true");
      button.classList.add("c-accordion__button--expanded");

      // Update section state
      section.classList.add("c-step-by-step-timeline__section--open");

      // Update content visibility
      const contentId = button.getAttribute("aria-controls");
      if (contentId) {
        const contentElement = document.getElementById(contentId);
        if (contentElement) {
          contentElement.setAttribute("aria-hidden", "false");
        }
      }
    } else {
      // Closing: use helper function
      closeSection(section as HTMLElement);
    }

    // Update timeline line after state changes
    updateTimelineLine(container as HTMLElement);
  }

  /**
   * Update timeline section state (for dot appearance)
   */
  function updateTimelineSection(section: HTMLElement, isExpanded: boolean) {
    if (isExpanded) {
      section.classList.add("c-step-by-step-timeline__section--open");
    } else {
      section.classList.remove("c-step-by-step-timeline__section--open");
    }
  }

  /**
   * Update timeline line: draw a line between the top and bottom dots
   */
  function updateTimelineLine(container: HTMLElement) {
    const line = container.querySelector(
      ".c-step-by-step-timeline__line"
    ) as HTMLElement;
    const sections = container.querySelectorAll(
      ".c-step-by-step-timeline__section"
    );

    if (!line || sections.length === 0) return;

    const firstSection = sections[0] as HTMLElement;
    const lastSection = sections[sections.length - 1] as HTMLElement;

    // Get section positions relative to container
    const containerRect = container.getBoundingClientRect();
    const firstSectionRect = firstSection.getBoundingClientRect();
    const lastSectionRect = lastSection.getBoundingClientRect();

    // Calculate dot top positions: section top + dot offset (0.75rem) + dot center (6px)
    const containerStyle = window.getComputedStyle(container);
    const fontSize = parseFloat(containerStyle.fontSize) || 16;
    const dotTopOffset = 0.75 * fontSize + 6; // 0.75rem + 6px (half of 12px dot)
    const firstDotCenter =
      firstSectionRect.top - containerRect.top + dotTopOffset;
    const lastDotCenter =
      lastSectionRect.top - containerRect.top + dotTopOffset;

    // Set line top and height (left is handled by CSS)
    line.style.top = `${firstDotCenter}px`;
    line.style.height = `${lastDotCenter - firstDotCenter}px`;
  }

  /**
   * Initialize timeline accordion
   */
  function initTimelineAccordion() {
    const containers = document.querySelectorAll(".c-step-by-step-timeline");

    containers.forEach((container) => {
      const containerEl = container as HTMLElement;

      // Initial line update
      updateTimelineLine(containerEl);

      // Set up click handlers for buttons
      const buttons = container.querySelectorAll(
        ".c-accordion__button"
      ) as NodeListOf<HTMLElement>;

      buttons.forEach((button) => {
        // Set initial state based on aria-expanded
        const isInitiallyExpanded =
          button.getAttribute("aria-expanded") === "true";
        const section = button.closest(
          ".c-step-by-step-timeline__section"
        ) as HTMLElement;
        if (section) {
          updateTimelineSection(section, isInitiallyExpanded);
        }

        // Add click handler
        button.addEventListener("click", (e) => {
          e.preventDefault();
          toggleSection(button);
        });
      });

      // Observe content wrapper for timeline line updates during animations
      const contentWrapper = container.querySelector(
        ".c-step-by-step-timeline__content"
      ) as HTMLElement;

      if (contentWrapper) {
        const resizeObserver = new ResizeObserver(() => {
          updateTimelineLine(containerEl);
        });
        resizeObserver.observe(contentWrapper);

        // Also observe all sections for position changes
        const sections = container.querySelectorAll(
          ".c-step-by-step-timeline__section"
        );
        sections.forEach((section) => {
          resizeObserver.observe(section as HTMLElement);
        });
      }
    });
  }

  /**
   * Initialize on DOM ready
   */
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initTimelineAccordion);
  } else {
    initTimelineAccordion();
  }

  // Also listen for dynamically loaded content (if ACF is available)
  if (typeof (window as any).acf !== "undefined") {
    (window as any).acf.addAction("render", initTimelineAccordion);
  }

  // Recalculate line on window resize
  let resizeTimeout: ReturnType<typeof setTimeout>;
  window.addEventListener("resize", function () {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(function () {
      const containers = document.querySelectorAll(".c-step-by-step-timeline");
      containers.forEach((container) => {
        updateTimelineLine(container as HTMLElement);
      });
    }, 250);
  });
})();
