{{-- resources/views/components/post-card.blade.php --}}
@props(['post'])

<article class="bg-gray-850 border border-gray-800 rounded-xl p-4 shadow-sm">
  <header class="flex gap-3 items-start">
    <img src="{{ optional($post->user->profile)->avatar ? asset('storage/'. $post->user->profile->avatar) : asset('images/default-avatar.png') }}"
         alt="{{ $post->user->name }} avatar"
         class="w-12 h-12 rounded-full object-cover flex-shrink-0">

    <div class="flex-1 min-w-0">
      <div class="flex items-center justify-between">
        <div class="truncate">
          <a href="{{ route('profile.show', $post->user) }}" class="font-semibold text-sm hover:underline">
            {{ $post->user->name }}
          </a>
          <span class="text-gray-400 text-xs ml-2">{{ $post->created_at->diffForHumans() }}</span>
        </div>
        <div class="text-sm text-gray-400">
          {{-- optional menu */}
        </div>
      </div>

      <div class="mt-2 text-gray-100 text-sm leading-relaxed break-words">
        {!! nl2br(e($post->content)) !!}
      </div>

      @if($post->media)
        <div class="mt-3">
          {{-- smart media rendering based on extension --}}
          @php $ext = pathinfo($post->media, PATHINFO_EXTENSION); @endphp
          @if(in_array(strtolower($ext), ['mp4','webm']))
            <video controls class="max-h-80 w-full rounded-md bg-black">
              <source src="{{ asset('storage/' . $post->media) }}" type="video/{{ $ext }}">
              Your browser does not support the video tag.
            </video>
          @else
            <img src="{{ asset('storage/' . $post->media) }}" alt="Post media" class="mt-2 w-full rounded-md object-cover max-h-96">
          @endif
        </div>
      @endif

      <footer class="mt-3 flex items-center gap-4 text-sm text-gray-300">
        {{-- Likes form (AJAX-ready) --}}
        <form class="like-form" action="{{ route('posts.like', $post) }}" method="POST" data-post-id="{{ $post->id }}">
          @csrf
          <button type="submit" class="flex items-center gap-2 px-2 py-1 rounded hover:bg-gray-800 focus:outline-none" aria-pressed="{{ auth()->check() && $post->isLikedBy(auth()->id()) ? 'true':'false' }}" aria-label="Like post">
            <span class="like-icon">
              @if(auth()->check() && $post->isLikedBy(auth()->id()))
                ❤️
              @else
                🤍
              @endif
            </span>
            <span class="like-count">{{ $post->likes()->count() }}</span>
          </button>
        </form>

        {{-- Comment form toggle (small inline) --}}
        <button class="comment-toggle px-2 py-1 rounded hover:bg-gray-800" data-post-id="{{ $post->id }}">Comment</button>

        <span class="text-gray-500 ml-auto">{{ $post->comments()->count() }} comments</span>
      </footer>

      {{-- hidden comment form (shown when user clicks comment button) --}}
      <div class="comment-box mt-3 hidden" data-post-id="{{ $post->id }}">
        @auth
          <form action="{{ route('comments.store', $post) }}" method="POST" class="comment-form">
            @csrf
            <div class="flex gap-2">
              <input name="body" required placeholder="Write a comment..." class="flex-1 rounded px-3 py-2 bg-gray-900 border border-gray-800 focus:outline-none" />
              <button type="submit" class="px-3 py-2 rounded bg-blue-600 hover:bg-blue-500">Send</button>
            </div>
          </form>
        @else
          <div class="text-sm text-gray-400"> <a href="{{ route('login') }}" class="text-blue-400 hover:underline">Login</a> to comment.</div>
        @endauth
      </div>
    </div>
  </header>
</article>
