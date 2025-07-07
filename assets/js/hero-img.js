document.addEventListener("DOMContentLoaded", () => {
  const heroImg = document.querySelector(".hero-img");

  const backgrounds = [
    "../../assets/img/slide-1.jpg",
    "../../assets/img/slide-2.jpg",
    "../../assets/img/slide-3.jpg"
  ];

  const dots = document.querySelectorAll(".dot");
  let index = 0;

  heroImg.style.backgroundImage = `url('${backgrounds[index]}')`;
  dots[index].classList.add("active");

 
  function showSlide(i) {
    index = i % backgrounds.length;
    if (index < 0) index = backgrounds.length - 1;
    heroImg.style.backgroundImage = `url('${backgrounds[index]}')`;
    updateDots();
  }

  function updateDots() {
    dots.forEach((dot, i) => {
      dot.classList.toggle("active", i === index);
    });
  }


  let slideInterval = setInterval(() => {
    showSlide(index + 1);
  }, 5000);

  
  function resetInterval() {
    clearInterval(slideInterval);
    slideInterval = setInterval(() => {
      showSlide(index + 1);
    }, 3000);
  }

 
  dots.forEach((dot, i) => {
    dot.addEventListener("click", () => {
      showSlide(i);
      resetInterval();
    });
  });
});
