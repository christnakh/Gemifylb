document.addEventListener("DOMContentLoaded", function () {
  const sliders = document.querySelectorAll(".image-slider");

  sliders.forEach((slider) => {
    const container = slider.querySelector(".slider-container");
    const prevButton = slider.querySelector(".prev");
    const nextButton = slider.querySelector(".next");
    const dotsContainer = slider.querySelector(".slider-dots");
    const images = container.querySelectorAll("img");
    let currentIndex = 0;

    // Create dots
    images.forEach((_, index) => {
      const dot = document.createElement("div");
      dot.classList.add("dot");
      if (index === 0) dot.classList.add("active");
      dot.addEventListener("click", () => goToSlide(index));
      dotsContainer.appendChild(dot);
    });

    function updateDots() {
      dotsContainer.querySelectorAll(".dot").forEach((dot, index) => {
        dot.classList.toggle("active", index === currentIndex);
      });
    }

    function goToSlide(index) {
      currentIndex = index;
      container.style.transform = `translateX(-${index * 100}%)`;
      updateDots();
    }

    prevButton.addEventListener("click", () => {
      currentIndex = (currentIndex - 1 + images.length) % images.length;
      goToSlide(currentIndex);
    });

    nextButton.addEventListener("click", () => {
      currentIndex = (currentIndex + 1) % images.length;
      goToSlide(currentIndex);
    });

    let startX, moveX, initialTransform;
    let isDragging = false;

    function getTransformX() {
      const transform = window
        .getComputedStyle(container)
        .getPropertyValue("transform");
      const matrix = new DOMMatrix(transform);
      return matrix.m41;
    }

    function handleDragStart(e) {
      isDragging = true;
      startX = e.type.includes("mouse") ? e.clientX : e.touches[0].clientX;
      initialTransform = getTransformX();
      container.style.transition = "none";
    }

    function handleDragMove(e) {
      if (!isDragging) return;
      e.preventDefault();
      moveX = e.type.includes("mouse") ? e.clientX : e.touches[0].clientX;
      const diff = moveX - startX;
      container.style.transform = `translateX(${initialTransform + diff}px)`;
    }

    function handleDragEnd() {
      if (!isDragging) return;
      isDragging = false;
      container.style.transition = "transform 0.3s ease";
      const containerWidth = container.offsetWidth;
      const movedRatio = (moveX - startX) / containerWidth;
      if (Math.abs(movedRatio) > 0.2) {
        if (movedRatio > 0) {
          currentIndex = Math.max(currentIndex - 1, 0);
        } else {
          currentIndex = Math.min(currentIndex + 1, images.length - 1);
        }
      }
      goToSlide(currentIndex);
    }

    // Touch events
    slider.addEventListener("touchstart", handleDragStart);
    slider.addEventListener("touchmove", handleDragMove);
    slider.addEventListener("touchend", handleDragEnd);

    // Mouse events
    slider.addEventListener("mousedown", handleDragStart);
    slider.addEventListener("mousemove", handleDragMove);
    slider.addEventListener("mouseup", handleDragEnd);
    slider.addEventListener("mouseleave", handleDragEnd);

    // Prevent default drag behavior
    slider.addEventListener("dragstart", (e) => e.preventDefault());
  });
});
