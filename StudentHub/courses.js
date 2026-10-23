// Collapsible FAQ Accordion
document.querySelectorAll('.accordion-header').forEach(header => {
  header.addEventListener('click', () => {
    const item = header.closest('.accordion-item');
    const wasActive = item.classList.contains('active');

    // Close others
    document.querySelectorAll('.accordion-item').forEach(i => i.classList.remove('active'));

    // Toggle current
    item.classList.toggle('active', !wasActive);
    header.setAttribute('aria-expanded', !wasActive);
  });
});