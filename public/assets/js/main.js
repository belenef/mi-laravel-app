document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const span = btn.querySelector('.likes');
      let n = parseInt(span.textContent || '0');
      span.textContent = ++n;
      btn.classList.toggle('btn-success');
    });
  });

  const searchBtn = document.getElementById('searchBtn');
  if (searchBtn) {
    searchBtn.addEventListener('click', () => {
      const q = document.getElementById('searchInput').value.trim();
      if (q) alert('Búsqueda simulada: ' + q);
    });
  }
});
