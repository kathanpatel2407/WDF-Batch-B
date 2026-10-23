// Content Slider / Carousel
const track = document.querySelector('.slider-track');
const slides = document.querySelectorAll('.slide');
const prev = document.querySelector('.slider-arrow-btn.prev');
const next = document.querySelector('.slider-arrow-btn.next');
let index = 0;

function showSlide(i) {
  index = (i + slides.length) % slides.length;
  if (track) track.style.transform = `translateX(-${index * 100}%)`;
}

next?.addEventListener('click', () => showSlide(index + 1));
prev?.addEventListener('click', () => showSlide(index - 1));

// Auto-slide every 5 seconds
let timer = setInterval(() => showSlide(index + 1), 5000);
document.querySelector('.slider-container')?.addEventListener('mouseenter', () => clearInterval(timer));
document.querySelector('.slider-container')?.addEventListener('mouseleave', () => {
  timer = setInterval(() => showSlide(index + 1), 5000);
});