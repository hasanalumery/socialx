@extends('layouts.app')

@section('title', ($user->name ?? 'Profile') . ' — SocialX')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">

    {{-- Profile Header --}}
    <div class="bg-gray-900 rounded-2xl p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div class="flex items-center gap-4">
            @if($user->profile_picture)
                <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full object-cover shadow-sm border border-gray-800">
            @else
                <div class="w-20 h-20 rounded-full bg-gray-700 flex items-center justify-center text-white text-2xl font-bold shadow-sm border border-gray-800">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </div>
            @endif
            <div class="min-w-0">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-semibold text-white truncate">{{ $user->name }}</h1>
                    <span class="text-sm text-gray-400">· {{ $user->posts()->count() ?? 0 }} posts</span>
                </div>
                @if(!empty($user->bio))
                    <p class="text-sm text-gray-300 mt-1 leading-relaxed break-words">{{ $user->bio }}</p>
                @endif
            </div>
        </div>

        {{-- Follow/Edit Buttons --}}
        <div class="flex items-center gap-3">
            @auth
                @if(auth()->id() === $user->id)
                    <a href="{{ route('profile.edit') }}" class="px-4 py-2 rounded-full border border-gray-700 bg-gray-800 text-white hover:bg-gray-700 transition">Edit profile</a>
                @else
                    @php $isFollowing = auth()->user()->following->contains($user->id); @endphp
                    @if($isFollowing)
                        <form method="POST" action="{{ route('unfollow', $user) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 rounded-full bg-gray-700 hover:bg-gray-600 text-white w-full md:w-auto transition">Unfollow</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('follow', $user) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-full bg-blue-500 hover:bg-blue-600 text-white w-full md:w-auto transition">Follow</button>
                        </form>
                    @endif
                @endif
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-full bg-blue-500 hover:bg-blue-600 text-white transition">Login to follow</a>
            @endauth
        </div>
    </div>

    {{-- Posts Feed --}}
    <div class="flex flex-col gap-4">
        @forelse($posts as $post)
            @php
                $media = $post->media ?? null;
                $ext = $media ? strtolower(pathinfo($media, PATHINFO_EXTENSION)) : null;
                $isVideo = in_array($ext, ['mp4','webm']);
            @endphp

            <div class="bg-gray-800 rounded-2xl p-4 shadow space-y-3" data-post-card data-post-id="{{ $post->id }}">

                {{-- Post Header --}}
                <div class="flex items-center gap-3">
                    @if($post->user->profile_picture)
                        <img src="{{ asset('storage/' . $post->user->profile_picture) }}" class="w-12 h-12 rounded-full object-cover">
                    @else
                        <div class="w-12 h-12 rounded-full bg-gray-700 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr($post->user->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <div class="text-sm text-gray-400">
                        <div class="font-semibold text-white">{{ $post->user->name }}</div>
                        <div class="text-xs">{{ $post->created_at->diffForHumans() }}</div>
                    </div>
                </div>

                {{-- Post Content --}}
                <div class="text-gray-200 text-sm sm:text-base leading-relaxed">
                    {{ $post->content ?? '' }}
                </div>

                {{-- Post Media --}}
                @if($media)
                    @if($isVideo)
                        <div class="post-media-wrapper mt-3 w-full rounded-2xl overflow-hidden bg-black shadow-md">
                            <video controls class="w-full h-auto max-h-72 object-contain">
                                <source src="{{ asset('storage/' . $media) }}" type="video/{{ $ext }}">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    @else
                        <div class="post-media-wrapper mt-3 w-full rounded-2xl overflow-hidden bg-gray-900 shadow-md">
                            <img src="{{ asset('storage/' . $media) }}" 
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
                        <div class="bg-gray-700 rounded-full px-3 py-1 text-sm text-gray-100 flex items-center justify-between">
                            <span>
                                <span class="font-semibold">{{ $comment->user?->name ?? 'Unknown' }}</span>: {{ $comment->body }}
                            </span>
                            @auth
                                <button class="comment-like-btn ml-2 text-sm text-gray-300" data-comment-id="{{ $comment->id }}">
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
            <div class="text-center text-gray-500 mt-10 text-sm sm:text-base">This user hasn't posted yet.</div>
        @endforelse

    </div>

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

    // LIKE BUTTON
    document.body.addEventListener('click', async e => {
        const likeBtn = e.target.closest('.like-btn');
        if(!likeBtn) return;
        const postCard = likeBtn.closest('[data-post-card]');
        const postId = postCard.dataset.postId;
        likeBtn.disabled = true;

        try {
            const res = await fetch(`/posts/${postId}/like`, { method:'POST', headers:{ 'X-CSRF-TOKEN': token, 'Accept':'application/json' } });
            const data = await res.json();
            likeBtn.querySelector('.like-text').textContent = data.status === 'liked' ? '❤️' : '🤍';
            likeBtn.querySelector('.likes-count').textContent = data.likes_count;
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
                newComment.className = 'bg-gray-700 rounded-full px-3 py-1 text-sm text-gray-100 flex items-center justify-between';
                newComment.innerHTML = `<span><span class="font-semibold">${data.comment.user_name}</span>: ${data.comment.body}</span>
                                        <button class="comment-like-btn ml-2 text-sm text-gray-300" data-comment-id="${data.comment.id}">🤍 (0)</button>`;
                form.closest('.comments-section').insertBefore(newComment, form);
                input.value = '';
                const toggleBtn = postCard.querySelector('.comment-toggle-btn');
                const count = postCard.querySelectorAll('.comments-section > div').length - 1;
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
