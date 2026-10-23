document.addEventListener('DOMContentLoaded', () => {
  const toggleBtn = document.getElementById('themeToggleBtn');
  toggleBtn?.addEventListener('click', () => {
    const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('charusat_theme', next);
  });

  const togglePwBtn = document.getElementById('togglePasswordBtn');
  const pwField = document.getElementById('password');
  togglePwBtn?.addEventListener('click', () => {
    const isPw = pwField.type === 'password';
    pwField.type = isPw ? 'text' : 'password';
    togglePwBtn.textContent = isPw ? '🔒 Hide' : '👁️ Show';
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
});