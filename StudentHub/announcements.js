document.addEventListener('DOMContentLoaded', () => {
  // Theme Switcher & Hamburger Menu
  const toggleBtn = document.getElementById('themeToggleBtn');
  const themeIcon = document.getElementById('themeIcon');
  const themeLabel = document.getElementById('themeLabel');
  const savedTheme = localStorage.getItem('charusat_theme') || 'light';
  document.documentElement.setAttribute('data-theme', savedTheme);

  toggleBtn?.addEventListener('click', () => {
    const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('charusat_theme', next);
    if (themeIcon) themeIcon.textContent = next === 'dark' ? '☀️' : '🌙';
    if (themeLabel) themeLabel.textContent = next === 'dark' ? 'Light Mode' : 'Dark Mode';
  });

  const hamburgerBtn = document.getElementById('hamburgerBtn');
  const navMenu = document.getElementById('portalNav');
  hamburgerBtn?.addEventListener('click', () => {
    navMenu?.classList.toggle('is-open', hamburgerBtn.classList.toggle('is-active'));
  });

  // Collapsible Announcement Cards (Accordion)
  document.querySelectorAll('.accordion-header').forEach(header => {
    header.addEventListener('click', () => {
      const item = header.closest('.accordion-item');
      const wasOpen = item.classList.contains('active');
      document.querySelectorAll('.accordion-item').forEach(i => i.classList.remove('active'));
      if (!wasOpen) item.classList.add('active');
    });
  });

  // Modal Dialogs
  document.querySelectorAll('[data-modal-target]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      document.getElementById(btn.getAttribute('data-modal-target'))?.classList.add('is-active');
    });
  });
  document.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', () => btn.closest('.modal-overlay')?.classList.remove('is-active'));
  });
});