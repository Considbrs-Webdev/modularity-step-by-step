/**
 * Keeps one step open and redraws the timeline rail between marker centers.
 */
(function () {
  "use strict";

  function closeSiblings(section: HTMLDetailsElement) {
    const container = section.closest(".c-step-by-step-timeline");
    if (!container) return;

    container
      .querySelectorAll<HTMLDetailsElement>(
        "details.c-step-by-step-timeline__section[open]"
      )
      .forEach((other) => {
        if (other !== section) {
          other.open = false;
        }
      });
  }

  function markerCenter(section: HTMLElement, containerTop: number): number {
    const summary = section.querySelector("summary");
    const rect = (summary ?? section).getBoundingClientRect();
    return rect.top - containerTop + rect.height / 2;
  }

  function updateTimelineLine(container: HTMLElement) {
    const line = container.querySelector(
      ".c-step-by-step-timeline__line"
    ) as HTMLElement | null;
    const sections = container.querySelectorAll<HTMLElement>(
      ".c-step-by-step-timeline__section"
    );

    if (!line || sections.length === 0) return;

    container.classList.toggle(
      "c-step-by-step-timeline--single",
      sections.length === 1
    );

    if (sections.length === 1) return;

    const containerTop = container.getBoundingClientRect().top;
    const firstCenter = markerCenter(sections[0], containerTop);
    const lastCenter = markerCenter(sections[sections.length - 1], containerTop);

    line.style.top = `${firstCenter}px`;
    line.style.height = `${Math.max(0, lastCenter - firstCenter)}px`;
  }

  function onToggle(event: Event) {
    const section = event.currentTarget as HTMLDetailsElement;
    if (section.open) {
      closeSiblings(section);
    }

    const container = section.closest(".c-step-by-step-timeline");
    if (container) {
      updateTimelineLine(container as HTMLElement);
    }
  }

  function initTimeline() {
    document.querySelectorAll(".c-step-by-step-timeline").forEach((container) => {
      const containerEl = container as HTMLElement;
      updateTimelineLine(containerEl);

      const sections = container.querySelectorAll<HTMLDetailsElement>(
        "details.c-step-by-step-timeline__section"
      );

      sections.forEach((section) => {
        section.addEventListener("toggle", onToggle);
      });

      const contentWrapper = container.querySelector(
        ".c-step-by-step-timeline__content"
      );
      if (!contentWrapper) return;

      const resizeObserver = new ResizeObserver(() => {
        updateTimelineLine(containerEl);
      });
      resizeObserver.observe(contentWrapper);
      sections.forEach((section) => resizeObserver.observe(section));
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initTimeline);
  } else {
    initTimeline();
  }

  if (typeof (window as any).acf !== "undefined") {
    (window as any).acf.addAction("render", initTimeline);
  }

  let resizeTimeout: ReturnType<typeof setTimeout>;
  window.addEventListener("resize", function () {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(function () {
      document.querySelectorAll(".c-step-by-step-timeline").forEach((container) => {
        updateTimelineLine(container as HTMLElement);
      });
    }, 250);
  });
})();
