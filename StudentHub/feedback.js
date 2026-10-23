document.addEventListener('DOMContentLoaded', () => {
  const toggleBtn = document.getElementById('themeToggleBtn');
  toggleBtn?.addEventListener('click', () => {
    const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('charusat_theme', next);
  });

  document.getElementById('hamburgerBtn')?.addEventListener('click', () => {
    document.getElementById('portalNav')?.classList.toggle('is-open');
  });

  const form = document.getElementById('feedbackForm');
  form?.addEventListener('submit', (e) => {
    e.preventDefault();
    document.getElementById('feedbackSuccessModal')?.classList.add('is-active');
    form.reset();
  });

  document.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', () => btn.closest('.modal-overlay')?.classList.remove('is-active'));
  });
});