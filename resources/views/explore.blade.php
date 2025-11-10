@extends('layouts.app')

@section('title', 'Explore — SocialX')

@section('content')
<div class="space-y-6">

    @forelse($posts as $post)
    <div class="bg-gray-800 rounded-2xl p-4 shadow-md space-y-3" data-post-card>

        {{-- Header --}}
        <div class="flex items-center gap-3">
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
        </div>

        {{-- Post Content --}}
        <p class="text-gray-200">{{ $post->content }}</p>

        {{-- Post Media --}}
        @if($post->media)
            <img src="{{ asset('storage/' . $post->media) }}" class="rounded-2xl max-h-80 w-full object-cover mt-2">
        @endif

        {{-- Actions --}}
        <div class="flex items-center gap-4 pt-2 border-t border-gray-700">
            @auth
            <button type="button" class="like-btn flex items-center gap-1 px-3 py-1 rounded-full bg-gray-700 hover:bg-gray-600 transition"
                    data-post-id="{{ $post->id }}">
                <span class="like-text">{{ $post->isLikedBy(auth()->id()) ? 'Liked' : 'Like' }}</span>
                <span class="likes-count text-sm text-gray-300">({{ $post->likes->count() }})</span>
            </button>
            @else
            <span class="text-sm text-gray-500">{{ $post->likes->count() }} likes</span>
            @endauth

            <button type="button" class="comment-toggle-btn flex items-center gap-1 px-3 py-1 rounded-full bg-gray-700 hover:bg-gray-600 transition">
                Comments ({{ $post->comments->count() }})
            </button>
        </div>

        {{-- Comments Section --}}
        <div class="comments-section mt-2 hidden space-y-2">
            @foreach($post->comments as $comment)
            <div class="bg-gray-700 rounded-full px-3 py-1 text-sm text-gray-100">
                <span class="font-semibold">{{ $comment->user?->name ?? 'Unknown' }}</span>: {{ $comment->body }}
            </div>
            @endforeach

            @auth
            <form class="comment-form flex items-center mt-2" data-post-id="{{ $post->id }}">
                <input type="text" name="body" class="flex-1 rounded-full border-gray-700 bg-gray-900 px-3 py-1 text-sm text-gray-100" placeholder="Add a comment..." />
                <button class="ml-2 text-blue-600 font-semibold">Post</button>
            </form>
            @endauth
        </div>

    </div>
    @empty
    <div class="text-center text-gray-500 mt-10">No posts yet.</div>
    @endforelse

    <div class="mt-4">
        {{ $posts->links('pagination::tailwind') }}
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('click', async (e) => {

    // LIKE BUTTON
    const likeBtn = e.target.closest('.like-btn');
    if(likeBtn){
        e.preventDefault();
        const postId = likeBtn.dataset.postId;
        const token = document.querySelector('meta[name="csrf-token"]').content;
        likeBtn.disabled = true;

        try {
            const res = await fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            });
            const data = await res.json();
            likeBtn.querySelector('.like-text').textContent = data.status === 'liked' ? 'Liked' : 'Like';
            likeBtn.querySelector('.likes-count').textContent = `(${data.likes_count})`;
            likeBtn.classList.toggle('bg-blue-500', data.status === 'liked');
            likeBtn.classList.toggle('text-white', data.status === 'liked');
        } catch(err){ console.error(err); }
        finally{ likeBtn.disabled = false; }
    }

    // COMMENT TOGGLE
    const toggleBtn = e.target.closest('.comment-toggle-btn');
    if(toggleBtn){
        const card = toggleBtn.closest('[data-post-card]');
        if(!card) return;
        const section = card.querySelector('.comments-section');
        if(section) section.classList.toggle('hidden');
    }

});

// COMMENT SUBMIT AJAX
document.addEventListener('submit', async (e) => {
    const form = e.target.closest('.comment-form');
    if(!form) return;
    e.preventDefault();

    const postId = form.dataset.postId;
    const input = form.querySelector("input[name='body']");
    if(!input || !input.value.trim()) return;

    const token = document.querySelector('meta[name="csrf-token"]').content;
    const submitBtn = form.querySelector('button');
    submitBtn.disabled = true;

    try {
        const res = await fetch(`/posts/${postId}/comments`, {
            method:'POST',
            headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':token,'Accept':'application/json' },
            body: JSON.stringify({ body: input.value.trim() })
        });

        if(!res.ok) throw new Error('Comment failed');

        const data = await res.json();
        if(data && data.comment){
            const newComment = document.createElement('div');
            newComment.className = 'bg-gray-700 rounded-full px-3 py-1 text-sm text-gray-100';
            newComment.innerHTML = `<span class="font-semibold">${data.comment.user_name}</span>: ${data.comment.body}`;
            form.closest('.comments-section').insertBefore(newComment, form);
            input.value = '';
        } else location.reload();

    } catch(err){ console.error(err); }
    finally{ submitBtn.disabled = false; }
});
</script>
@endpush
@endsection
