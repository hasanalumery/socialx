@extends('layouts.app')

@section('title', 'Explore — SocialX')

@section('content')
<div class="space-y-6">

    {{-- Search Form --}}
    <form method="GET" action="{{ route('explore') }}" class="flex gap-2 mb-6">
        <input type="text" name="q" value="{{ $query ?? '' }}" placeholder="Search…"
               class="flex-1 px-4 py-2 rounded-full border border-white text-gray-900 bg-white dark:text-gray-100 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
               style="min-width: 300px;">
        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-full hover:bg-blue-600 transition">Search</button>
    </form>

    {{-- Users Results --}}
    @if(!empty($query))
        <h2 class="text-xl font-semibold mb-2">Users</h2>
        @if($users->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach($users as $user)
                    <div class="border rounded-2xl p-4 flex flex-col justify-between bg-gray-800 hover:bg-gray-700 transition shadow-sm min-h-[100px]">
                        <div class="flex items-start gap-3">
                            @if($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gray-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('profile.show', $user) }}" class="font-semibold text-white hover:underline">{{ $user->name }}</a>
                                @if($user->bio)
                                    <p class="text-sm text-gray-300 truncate-lines-2 break-words">{{ $user->bio }}</p>
                                @endif
                            </div>
                        </div>
                        @auth
                        <div class="mt-3">
                            @if(auth()->user()->following->contains($user->id))
                                <form method="POST" action="{{ route('unfollow', $user) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="w-full bg-red-500 text-white px-3 py-1 rounded-full hover:bg-red-600 transition">Unfollow</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('follow', $user) }}">
                                    @csrf
                                    <button class="w-full bg-blue-500 text-white px-3 py-1 rounded-full hover:bg-blue-600 transition">Follow</button>
                                </form>
                            @endif
                        </div>
                        @endauth
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400 mb-6">No users found.</p>
        @endif
    @endif

    {{-- Posts --}}
    <h2 class="text-xl font-semibold mb-2">Posts</h2>
    @forelse($posts as $post)
        <div class="bg-gray-800 rounded-2xl p-4 shadow-md space-y-3" data-post-card data-post-id="{{ $post->id }}">

            {{-- Post Header --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('profile.show', $post->user->id) }}" class="flex items-center gap-3">
                    @if($post->user->profile_picture)
                        <img src="{{ asset('storage/'.$post->user->profile_picture) }}" class="w-10 h-10 rounded-full object-cover">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr($post->user?->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <div class="text-sm text-gray-400">
                        <div class="font-semibold text-white">{{ $post->user?->name ?? 'Unknown' }}</div>
                        <div class="text-xs">{{ $post->created_at->diffForHumans() }}</div>
                    </div>
                </a>
            </div>

            {{-- Post Content --}}
            <p class="text-gray-200 break-words">{{ $post->content }}</p>

            {{-- Post Media --}}
            @if($post->media)
                @php $ext = pathinfo($post->media, PATHINFO_EXTENSION); @endphp
                @if(in_array(strtolower($ext), ['mp4','webm']))
                    <div class="post-media-wrapper mt-2 w-full rounded-2xl overflow-hidden bg-black shadow-md">
                        <video controls class="w-full h-auto max-h-60 object-contain">
                            <source src="{{ asset('storage/' . $post->media) }}" type="video/{{ $ext }}">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @else
                    <div class="post-media-wrapper mt-2 w-full rounded-2xl overflow-hidden bg-gray-900 shadow-md">
                        <img src="{{ asset('storage/' . $post->media) }}"
                             alt="Post media"
                             class="w-full h-auto object-contain p-1 rounded-lg transition duration-200 ease-in-out
                                    filter brightness-105 contrast-105 saturate-105 hover:brightness-110 hover:contrast-110 hover:saturate-110">
                    </div>
                @endif
            @endif

            {{-- Actions --}}
            <div class="flex items-center gap-4 pt-2 border-t border-gray-700">
                @auth
                    <button type="button" class="like-btn flex items-center gap-1 px-3 py-1 rounded-full bg-gray-700 hover:bg-gray-600 transition"
                            aria-pressed="{{ $post->isLikedBy(auth()->user()) ? 'true' : 'false' }}">
                        <span class="like-text">{{ $post->isLikedBy(auth()->user()) ? '❤️' : '🤍' }}</span>
                        <span class="likes-count text-sm text-gray-300">{{ $post->likes->count() }}</span>
                    </button>
                @else
                    <span class="text-sm text-gray-500">{{ $post->likes->count() }} likes</span>
                @endauth

                <button type="button" class="comment-toggle-btn flex items-center gap-1 px-3 py-1 rounded-full bg-gray-700 hover:bg-gray-600 transition">
                    Comments {{ $post->comments->count() }}
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
                        <button class="comment-like-btn ml-2 text-sm text-gray-300"
                                data-comment-id="{{ $comment->id }}">
                            {{ $comment->likes->where('user_id', auth()->id())->count() ? '❤️' : '🤍' }}
                            ({{ $comment->likes->count() }})
                        </button>
                        @endauth
                    </div>
                @endforeach

                @auth
                    <form class="comment-form flex items-center mt-2">
                        <input type="text" name="body" class="flex-1 rounded-full border-gray-700 bg-gray-900 px-3 py-1 text-sm text-gray-100" placeholder="Add a comment..." />
                        <button class="ml-2 text-blue-600 font-semibold">Post</button>
                    </form>
                @endauth
            </div>

        </div>
    @empty
        <div class="text-center text-gray-500 mt-10">No posts found.</div>
    @endforelse

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $posts->links('pagination::tailwind') }}
    </div>

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
            const res = await fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
            });
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
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept':'application/json'
                },
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
        finally { submitBtn.disabled = false; }
    });

    // COMMENT LIKE
    document.body.addEventListener('click', async e => {
        const btn = e.target.closest('.comment-like-btn');
        if(!btn) return;

        const commentId = btn.dataset.commentId;
        btn.disabled = true;

        try {
            const res = await fetch(`/comments/${commentId}/like`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept':'application/json' }
            });
            const data = await res.json();
            btn.textContent = `${data.status === 'liked' ? '❤️' : '🤍'} (${data.likes_count})`;
        } catch(err){ console.error(err); }
        finally { btn.disabled = false; }
    });
});
</script>
@endpush
@endsection
