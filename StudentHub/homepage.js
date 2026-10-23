document.addEventListener('DOMContentLoaded', () => {
  const toggleBtn = document.getElementById('themeToggleBtn');
  const themeIcon = document.getElementById('themeIcon');
  const themeLabel = document.getElementById('themeLabel');
  const savedTheme = localStorage.getItem('charusat_theme') || 'light';
  document.documentElement.setAttribute('data-theme', savedTheme);
  updateThemeDisplay(savedTheme);

  toggleBtn?.addEventListener('click', () => {
    const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('charusat_theme', next);
    updateThemeDisplay(next);
  });

  function updateThemeDisplay(theme) {
    if (themeIcon) themeIcon.textContent = theme === 'dark' ? '☀️' : '🌙';
    if (themeLabel) themeLabel.textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode';
  }

  const hamburgerBtn = document.getElementById('hamburgerBtn');
  const navMenu = document.getElementById('portalNav');
  hamburgerBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    navMenu?.classList.toggle('is-open', hamburgerBtn.classList.toggle('is-active'));
  });
  document.addEventListener('click', (e) => {
    if (navMenu && !navMenu.contains(e.target) && !hamburgerBtn?.contains(e.target)) {
      hamburgerBtn?.classList.remove('is-active');
      navMenu?.classList.remove('is-open');
    }
  });
  const banner = document.getElementById('notificationBanner');
  if (sessionStorage.getItem('dismissed_home_banner') === 'true') {
    if (banner) banner.style.display = 'none';
  }
  document.getElementById('closeBannerBtn')?.addEventListener('click', () => {
    banner?.classList.add('dismissed');
    sessionStorage.setItem('dismissed_home_banner', 'true');
    setTimeout(() => { if (banner) banner.style.display = 'none'; }, 300);
  });
  const track = document.querySelector('.slider-track');
  const slides = document.querySelectorAll('.slide');
  const dotsContainer = document.querySelector('.slider-dots');
  let slideIndex = 0;

  if (track && slides.length > 0) {
    slides.forEach((_, i) => {
      const dot = document.createElement('button');
      dot.className = `slider-dot ${i === 0 ? 'active' : ''}`;
      dot.addEventListener('click', () => goToSlide(i));
      dotsContainer?.appendChild(dot);
    });

    function goToSlide(i) {
      slideIndex = (i + slides.length) % slides.length;
      track.style.transform = `translateX(-${slideIndex * 100}%)`;
      dotsContainer?.querySelectorAll('.slider-dot').forEach((dot, idx) => {
        dot.classList.toggle('active', idx === slideIndex);
      });
    }

    document.querySelector('.slider-arrow-btn.next')?.addEventListener('click', () => goToSlide(slideIndex + 1));
    document.querySelector('.slider-arrow-btn.prev')?.addEventListener('click', () => goToSlide(slideIndex - 1));

    let timer = setInterval(() => goToSlide(slideIndex + 1), 5000);
    const container = document.querySelector('.slider-container');
    container?.addEventListener('mouseenter', () => clearInterval(timer));
    container?.addEventListener('mouseleave', () => timer = setInterval(() => goToSlide(slideIndex + 1), 5000));
  }
  document.querySelectorAll('.accordion-header').forEach(header => {
    header.addEventListener('click', () => {
      const item = header.closest('.accordion-item');
      const wasOpen = item.classList.contains('active');
      document.querySelectorAll('.accordion-item').forEach(i => i.classList.remove('active'));
      if (!wasOpen) item.classList.add('active');
    });
  });
  document.querySelectorAll('[data-modal-target]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      document.getElementById(btn.getAttribute('data-modal-target'))?.classList.add('is-active');
    });
  });
  document.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', () => btn.closest('.modal-overlay')?.classList.remove('is-active'));
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') document.querySelector('.modal-overlay.is-active')?.classList.remove('is-active');
  });
});