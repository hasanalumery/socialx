{{-- resources/views/components/post-card.blade.php --}}
@props(['post'])

<article class="bg-gray-850 border border-gray-800 rounded-xl p-4 shadow-sm" id="post-{{ $post->id }}">
  <header class="flex gap-3 items-start">
    {{-- Post author avatar --}}
    <a href="{{ route('profile.show', $post->user->id) }}">
      <img src="{{ $post->user->profile_picture_url }}" alt="{{ $post->user->name }}" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
    </a>

    <div class="flex-1 min-w-0">
      {{-- User info and timestamp --}}
      <div class="flex items-center justify-between">
        <div class="truncate">
          <a href="{{ route('profile.show', $post->user->id) }}" class="font-semibold text-sm hover:underline">
            {{ $post->user->name }}
          </a>
          <span class="text-gray-400 text-xs ml-2">{{ $post->created_at->diffForHumans() }}</span>
        </div>
      </div>

      {{-- Post content --}}
      <div class="mt-2 text-gray-100 text-sm leading-relaxed break-words">
        {!! nl2br(e($post->content)) !!}
      </div>

      {{-- Post media --}}
      @if($post->media)
        @php $ext = pathinfo($post->media, PATHINFO_EXTENSION); @endphp
        @if(in_array(strtolower($ext), ['mp4','webm']))
          <video controls class="mt-3 max-h-80 w-full rounded-md bg-black">
            <source src="{{ asset('storage/' . $post->media) }}" type="video/{{ $ext }}">
            Your browser does not support the video tag.
          </video>
        @else
          <img src="{{ asset('storage/' . $post->media) }}" alt="Post media" class="mt-3 w-full rounded-md object-cover max-h-96">
        @endif
      @endif

      {{-- Likes & comments --}}
      <footer class="mt-3 flex items-center gap-4 text-sm text-gray-300">
        {{-- Like button (AJAX) --}}
        <button class="like-button flex items-center gap-2 px-2 py-1 rounded hover:bg-gray-800" 
                data-post-id="{{ $post->id }}">
          <span class="like-icon">
            {{ auth()->check() && $post->isLikedBy(auth()->user()) ? '❤️' : '🤍' }}
          </span>
          <span class="like-count">{{ $post->likes()->count() }}</span>
        </button>

        {{-- Toggle comment form --}}
        <button class="comment-toggle px-2 py-1 rounded hover:bg-gray-800" data-post-id="{{ $post->id }}">
          Comment
        </button>

        <span class="ml-auto text-gray-500 comment-total">{{ $post->comments()->count() }} comments</span>
      </footer>

      {{-- Comment form (hidden by default) --}}
      <div class="comment-box mt-3 hidden" data-post-id="{{ $post->id }}">
        @auth
          <form class="comment-form flex gap-2" data-post-id="{{ $post->id }}">
            <textarea name="comment" placeholder="Write a comment..." required 
                      class="flex-1 rounded px-3 py-2 bg-gray-900 border border-gray-800 focus:outline-none"></textarea>
            <button type="submit" class="px-3 py-2 rounded bg-blue-600 hover:bg-blue-500">Send</button>
          </form>
        @else
          <div class="text-sm text-gray-400">
            <a href="{{ route('login') }}" class="text-blue-400 hover:underline">Login</a> to comment.
          </div>
        @endauth
      </div>

      {{-- Display comments --}}
      <div class="mt-3 space-y-2 comment-list" id="comments-{{ $post->id }}">
        @foreach ($post->comments as $comment)
          <div class="flex items-start gap-2 text-gray-300">
            <a href="{{ route('profile.show', $comment->user->id) }}">
              <img src="{{ $comment->user->profile_picture_url }}" alt="{{ $comment->user->name }}" class="w-8 h-8 rounded-full object-cover">
            </a>
            <div>
              <strong class="text-sm">{{ $comment->user->name }}</strong>
              <p class="text-sm">{{ $comment->body }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </header>
</article>

{{-- AJAX scripts --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

  // Toggle comment form
  document.querySelectorAll('.comment-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const postId = btn.dataset.postId;
      const box = document.querySelector(`.comment-box[data-post-id="${postId}"]`);
      box.classList.toggle('hidden');
    });
  });

  // AJAX like
  document.querySelectorAll('.like-button').forEach(btn => {
    btn.addEventListener('click', async () => {
      const postId = btn.dataset.postId;
      const token = document.querySelector('meta[name="csrf-token"]').content;

      try {
        const res = await fetch(`/posts/${postId}/like`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({})
        });
        const data = await res.json();

        // Update UI
        btn.querySelector('.like-icon').textContent = data.liked ? '❤️' : '🤍';
        btn.querySelector('.like-count').textContent = data.likes_count;

      } catch(err) { console.error(err); }
    });
  });

  // AJAX comment form
  document.querySelectorAll('.comment-form').forEach(form => {
    form.addEventListener('submit', async e => {
      e.preventDefault();
      const postId = form.dataset.postId;
      const textarea = form.querySelector('textarea[name="comment"]');
      const body = textarea.value.trim();
      if (!body) return;

      const token = document.querySelector('meta[name="csrf-token"]').content;

      try {
        const res = await fetch(`/posts/${postId}/comment`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ comment: body })
        });
        const data = await res.json();

        // Append new comment
        const container = document.getElementById(`comments-${postId}`);
        const commentEl = document.createElement('div');
        commentEl.classList.add('flex', 'items-start', 'gap-2', 'text-gray-300');
        commentEl.innerHTML = `
          <a href="/profile/${data.user_id}">
            <img src="${data.user_avatar}" class="w-8 h-8 rounded-full object-cover">
          </a>
          <div>
            <strong class="text-sm">${data.user_name}</strong>
            <p class="text-sm">${data.body}</p>
          </div>
        `;
        container.prepend(commentEl);

        // Update comment count
        form.closest('article').querySelector('.comment-total').textContent = `${data.comments_count} comments`;

        textarea.value = '';
      } catch(err) { console.error(err); }
    });
  });

});
</script>
@endpush
