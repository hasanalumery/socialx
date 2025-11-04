// resources/js/socialx-interactions.js
document.addEventListener('DOMContentLoaded', () => {
  // Like forms
  document.querySelectorAll('.like-form').forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const postId = form.dataset.postId;
      const url = form.action;
      const token = form.querySelector('input[name="_token"]').value;

      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });

        if (!res.ok) throw new Error('Network response not ok');

        const json = await res.json();
        // toggle heart and update count — re-fetch count or parse current
        // For simplicity, trigger a small UI toggle:
        const likeCountEl = form.querySelector('.like-count');
        const iconEl = form.querySelector('.like-icon');
        let count = parseInt(likeCountEl.textContent || '0', 10);
        if (json.status === 'liked') {
          count = count + 1;
          if (iconEl) iconEl.textContent = '❤️';
        } else if (json.status === 'unliked') {
          count = Math.max(0, count - 1);
          if (iconEl) iconEl.textContent = '🤍';
        }
        likeCountEl.textContent = count;
      } catch (err) {
        // fallback: submit the form normally (reload)
        form.removeEventListener('submit', () => {});
        form.submit();
      }
    });
  });

  // Toggle comment boxes
  document.querySelectorAll('.comment-toggle').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const id = btn.dataset.postId;
      const box = document.querySelector(`.comment-box[data-post-id="${id}"]`);
      if (box) box.classList.toggle('hidden');
    });
  });

  // Comment forms: allow progressive enhancement (let browser submit by default)
  document.querySelectorAll('.comment-form').forEach(form => {
    form.addEventListener('submit', (e) => {
      // optional: use AJAX here too (left as enhancement)
    });
  });
});
