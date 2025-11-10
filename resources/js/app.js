// resources/js/app.js
document.addEventListener('DOMContentLoaded', () => {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  // Delegated handler: listens at document level and matches .comment-toggle clicks
  document.addEventListener('click', (e) => {
    const toggle = e.target.closest('.comment-toggle');
    if (!toggle) return;

    const postId = toggle.dataset.postId;
    const commentBlock = document.querySelector(`#comments-block-${postId}`);
    if (!commentBlock) {
      console.warn('Comments block not found for post', postId);
      return;
    }

    // Toggle visibility
    commentBlock.classList.toggle('hidden');

    // Focus input if shown
    const input = commentBlock.querySelector('textarea, input');
    if (input && !commentBlock.classList.contains('hidden')) input.focus();
  });

  // Lazy-load comments on first open
  document.addEventListener('click', async (e) => {
    const toggle = e.target.closest('.comment-toggle');
    if (!toggle) return;

    const postId = toggle.dataset.postId;
    const commentBlock = document.querySelector(`#comments-block-${postId}`);
    if (!commentBlock || commentBlock.dataset.loaded === 'true') return;

    try {
      const resp = await fetch(`/posts/${postId}/comments`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
      });

      if (!resp.ok) throw new Error('Network error');

      commentBlock.innerHTML = await resp.text();
      commentBlock.dataset.loaded = 'true';
    } catch (err) {
      console.error('Failed to load comments for post', postId, err);
    }
  });
});

// Like a comment (used by delegated click handler already in the HTML)
async function toggleLikeComment(commentId, btn) {
  try {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const response = await fetch(`/comments/${commentId}/like`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({})
    });

    if (!response.ok) {
      const text = await response.text();
      console.error('Like failed', response.status, text);
      return;
    }

    const json = await response.json();
    const countNode = btn.querySelector('.like-count');
    if (countNode && json.likes_count !== undefined) countNode.textContent = json.likes_count;
    btn.classList.toggle('liked', json.liked);
  } catch (err) {
    console.error(err);
  }
}
