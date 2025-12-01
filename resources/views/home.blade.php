@extends('layouts.app')

@section('title', 'Home — SocialX')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">

    {{-- Create Post --}}
    @auth
    <div class="bg-gray-800 rounded-2xl pt-2 pb-8 px-6 shadow-lg transition hover:shadow-xl">
        <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <textarea name="content" rows="3"
                      class="w-full bg-gray-900 border border-gray-700 rounded-2xl p-3 resize-none text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="What's on your mind?">{{ old('content') }}</textarea>
            @error('content')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            <div class="flex justify-between items-center mt-1">
                <label class="cursor-pointer text-blue-400 hover:text-blue-300 flex items-center gap-2">
                    📷 Add media
                    <input type="file" name="media" class="hidden" accept="image/*,video/*">
                </label>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 px-5 py-2 rounded-2xl font-semibold text-sm transition">Post</button>
            </div>
        </form>
    </div>
    @endauth

    {{-- Posts Feed --}}
    @forelse ($posts as $post)
    <div class="bg-gray-800 rounded-2xl p-4 shadow space-y-3" data-post-card data-post-id="{{ $post->id }}">

        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('profile.show', $post->user->id) }}" class="flex items-center gap-3">
                @if($post->user->profile_picture)
                    <img src="{{ asset($post->user->profile_picture) }}" class="w-12 h-12 rounded-full object-cover">
                @else
                    <div class="w-12 h-12 rounded-full bg-gray-700 flex items-center justify-center text-white font-semibold">
                        {{ strtoupper(substr($post->user?->name ?? 'U', 0, 1)) }}
                    </div>
                @endif
                <div class="text-sm text-gray-400">
                    <div class="font-semibold text-white">{{ $post->user?->name ?? 'Unknown' }}</div>
                    <div class="text-xs">{{ $post->created_at->diffForHumans() }}</div>
                </div>
            </a>
        </div>

        {{-- Content --}}
        <div class="text-gray-200 text-sm sm:text-base leading-relaxed break-words">{{ $post->content }}</div>

        {{-- Media --}}
        @if($post->media)
            @php $ext = pathinfo($post->media, PATHINFO_EXTENSION); @endphp
            @if(in_array(strtolower($ext), ['mp4','webm']))
                <div class="post-media-wrapper mt-3 w-full rounded-2xl overflow-hidden bg-black shadow-md">
                    <video controls class="w-full h-auto max-h-[50vh] sm:max-h-72 object-contain">
                        <source src="{{ asset($post->media) }}" type="video/{{ $ext }}">
                        Your browser does not support the video tag.
                    </video>
                </div>
            @else
                <div class="post-media-wrapper mt-3 w-full rounded-2xl overflow-hidden bg-gray-900 shadow-md">
                    <img src="{{ asset($post->media) }}" 
                         alt="Post media"
                         class="w-full h-auto object-contain p-1 rounded-lg transition duration-200 ease-in-out
                                filter brightness-105 contrast-105 saturate-105 hover:brightness-110 hover:contrast-110 hover:saturate-110">
                </div>
            @endif
        @endif

        {{-- Actions --}}
        <div class="flex flex-wrap items-center gap-3 mt-3">
            @auth
            <button type="button"
                    class="like-btn flex items-center gap-1 px-3 py-1 rounded-full bg-gray-700 hover:bg-gray-600 transition"
                    aria-pressed="{{ $post->isLikedBy(auth()->user()) ? 'true' : 'false' }}">
                <span class="like-text">{{ $post->isLikedBy(auth()->user()) ? '❤️' : '🤍' }}</span>
                <span class="likes-count text-sm text-gray-300">{{ $post->likes->count() }}</span>
            </button>
            @else
            <span class="text-sm text-gray-500">{{ $post->likes->count() }} likes</span>
            @endauth

            <button type="button"
                    class="comment-toggle-btn flex items-center gap-1 px-3 py-1 rounded-full bg-gray-700 hover:bg-gray-600 transition">
                Comments ({{ $post->comments->count() }})
            </button>
        </div>

        {{-- Comments Section --}}
        <div class="comments-section mt-2 hidden space-y-2">
            @foreach($post->comments as $comment)
            <div class="comment-item bg-gray-700 rounded-full px-3 py-1 text-sm text-gray-100 flex items-center justify-between">
                <span>
                    <span class="font-semibold">{{ $comment->user?->name ?? 'Unknown' }}</span>: {{ $comment->body }}
                </span>
                @auth
                <button class="comment-like-btn ml-2 text-sm text-gray-300"
                        data-comment-id="{{ $comment->id }}">
                    {{ $comment->likes->where('user_id', auth()->id())->count() ? '❤️' : '🤍' }}
                    ({{ $comment->likes->count() }})
                </button>
                @endauth
            </div>
            @endforeach

            @auth
            <form class="comment-form flex items-center mt-2 gap-2">
                <input type="text" name="body"
                       class="flex-1 rounded-full border-gray-700 bg-gray-900 px-3 py-1 text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Add a comment..." />
                <button class="px-3 py-1 text-blue-600 font-semibold rounded-full hover:bg-gray-700 transition">Post</button>
            </form>
            @endauth
        </div>

    </div>
    @empty
    <div class="text-center text-gray-500 mt-10 text-sm sm:text-base">No posts yet — be the first to post.</div>
    @endforelse

    {{-- Pagination --}}
    @if($posts->hasPages())
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-gray-300">

        <div class="text-sm hidden sm:block">
            Showing {{ $posts->firstItem() }}–{{ $posts->lastItem() }} of {{ $posts->total() }}
        </div>

        <nav class="flex flex-wrap items-center gap-2">
            @if ($posts->onFirstPage())
                <span class="px-3 py-1 rounded-md bg-gray-700 text-gray-500 cursor-not-allowed">&laquo; Prev</span>
            @else
                <a href="{{ $posts->previousPageUrl() }}" class="px-3 py-1 rounded-md bg-gray-700 hover:bg-gray-600 text-white transition">&laquo; Prev</a>
            @endif

            @foreach ($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
                @if ($page == $posts->currentPage())
                    <span class="px-3 py-1 rounded-md bg-blue-600 text-white font-semibold">{{ $page }}</span>
                @elseif($page == 1 || $page == $posts->lastPage() || ($page >= $posts->currentPage() - 1 && $page <= $posts->currentPage() + 1))
                    <a href="{{ $url }}" class="px-3 py-1 rounded-md bg-gray-700 hover:bg-gray-600 text-white transition">{{ $page }}</a>
                @endif
            @endforeach

            @if ($posts->hasMorePages())
                <a href="{{ $posts->nextPageUrl() }}" class="px-3 py-1 rounded-md bg-gray-700 hover:bg-gray-600 text-white transition">Next &raquo;</a>
            @else
                <span class="px-3 py-1 rounded-md bg-gray-700 text-gray-500 cursor-not-allowed">Next &raquo;</span>
            @endif
        </nav>
    </div>
    @endif

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const token = document.querySelector('meta[name="csrf-token"]').content;

    // POST LIKE
    document.body.addEventListener('click', async e => {
        const likeBtn = e.target.closest('.like-btn');
        if(!likeBtn) return;
        const postCard = likeBtn.closest('[data-post-card]');
        const postId = postCard.dataset.postId;
        likeBtn.disabled = true;

        try {
            const res = await fetch(`/posts/${postId}/like`, {
                method:'POST',
                headers:{ 'X-CSRF-TOKEN': token, 'Accept':'application/json' }
            });
            const data = await res.json();
            const text = likeBtn.querySelector('.like-text');
            const count = likeBtn.querySelector('.likes-count');
            text.textContent = data.status === 'liked' ? '❤️' : '🤍';
            count.textContent = data.likes_count;
            likeBtn.setAttribute('aria-pressed', data.status === 'liked' ? 'true' : 'false');
        } catch(err){ console.error(err); }
        finally{ likeBtn.disabled = false; }
    });

    // COMMENT TOGGLE
    document.body.addEventListener('click', e => {
        const toggleBtn = e.target.closest('.comment-toggle-btn');
        if(!toggleBtn) return;
        const postCard = toggleBtn.closest('[data-post-card]');
        const section = postCard.querySelector('.comments-section');
        section?.classList.toggle('hidden');
    });

    // COMMENT SUBMIT
    document.body.addEventListener('submit', async e => {
        const form = e.target.closest('.comment-form');
        if(!form) return;
        e.preventDefault();

        const postCard = form.closest('[data-post-card]');
        const postId = postCard.dataset.postId;
        const input = form.querySelector("input[name='body']");
        if(!input || !input.value.trim()) return;

        const submitBtn = form.querySelector('button');
        submitBtn.disabled = true;

        try {
            const res = await fetch(`/posts/${postId}/comments`, {
                method:'POST',
                headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': token, 'Accept':'application/json' },
                body: JSON.stringify({ body: input.value.trim() })
            });

            if(!res.ok) throw new Error('Comment failed');

            const data = await res.json();
            if(data && data.comment){
                const newComment = document.createElement('div');
                newComment.className = 'comment-item bg-gray-700 rounded-full px-3 py-1 text-sm text-gray-100 flex items-center justify-between';
                newComment.innerHTML = `<span><span class="font-semibold">${data.comment.user_name}</span>: ${data.comment.body}</span>
                                        <button class="comment-like-btn ml-2 text-sm text-gray-300" data-comment-id="${data.comment.id}">🤍 (0)</button>`;
                form.closest('.comments-section').insertBefore(newComment, form);
                input.value = '';

                const toggleBtn = postCard.querySelector('.comment-toggle-btn');
                const count = postCard.querySelectorAll('.comments-section > .comment-item').length;
                toggleBtn.textContent = `Comments (${count})`;
            } else location.reload();
        } catch(err){ console.error(err); }
        finally{ submitBtn.disabled = false; }
    });

    // COMMENT LIKE
    document.body.addEventListener('click', async e => {
        const btn = e.target.closest('.comment-like-btn');
        if(!btn) return;
        const commentId = btn.dataset.commentId;
        btn.disabled = true;

        try {
            const res = await fetch(`/comments/${commentId}/like`, { method:'POST', headers:{ 'X-CSRF-TOKEN': token, 'Accept':'application/json' } });
            const data = await res.json();
            btn.textContent = `${data.status === 'liked' ? '❤️' : '🤍'} (${data.likes_count})`;
        } catch(err){ console.error(err); }
        finally{ btn.disabled = false; }
    });
});
</script>
@endpush
@endsection
