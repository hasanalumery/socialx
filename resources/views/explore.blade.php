@extends('layouts.app')

@section('title', 'Explore — SocialX')

@section('content')
<div class="space-y-6">

    @forelse($posts as $post)
    <div class="bg-gray-800 rounded-2xl p-4 shadow-md space-y-3">

        {{-- Post Header --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center text-white font-bold">
                {{ strtoupper(substr($post->user?->name ?? 'U',0,1)) }}
            </div>
            <div>
                <p class="text-sm font-semibold text-white">{{ $post->user?->name ?? 'Unknown' }}</p>
                <p class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</p>
            </div>
        </div>

        {{-- Post Content --}}
        <p class="text-gray-200">{{ $post->content }}</p>

        {{-- Post Media --}}
        @if($post->media)
            <img src="{{ asset('storage/' . $post->media) }}" class="rounded-2xl max-h-80 w-full object-cover mt-2">
        @endif

        {{-- Actions: Likes & Comments --}}
        <div class="flex items-center gap-4 pt-2 border-t border-gray-700">
            @auth
            <button type="button" class="like-btn flex items-center gap-1 px-3 py-1 rounded-full bg-gray-700 hover:bg-blue-500 transition-transform transform"
                data-post-id="{{ $post->id }}">
                <span class="like-text">
                    {{ method_exists($post, 'isLikedBy') && $post->isLikedBy(auth()->id()) ? 'Liked' : 'Like' }}
                </span>
                <span class="text-sm text-gray-300">({{ $post->likes->count() }})</span>
            </button>
            @else
            <span class="text-sm text-gray-500">{{ $post->likes->count() }} likes</span>
            @endauth

            <button type="button" class="comment-toggle-btn flex items-center gap-1 px-3 py-1 rounded-full bg-gray-700 hover:bg-gray-600 transition">
                Comment ({{ $post->comments->count() }})
            </button>
        </div>

        {{-- Comments Section --}}
        <div class="comments-section mt-2 hidden space-y-2">
            @foreach($post->comments->take(3) as $comment)
                <div class="bg-gray-700 rounded-full p-2 text-sm text-gray-100">
                    <span class="font-semibold">{{ $comment->user?->name ?? 'Unknown' }}</span>: {{ $comment->body }}
                </div>
            @endforeach

            @if($post->comments->count() > 3)
                <button class="text-xs text-blue-400 mt-1 view-all-comments-btn">View all comments</button>
            @endif

            @auth
            <form action="{{ route('comments.store', $post->id) }}" method="POST" class="mt-1 flex gap-2">
                @csrf
                <input type="text" name="body" placeholder="Add a comment..." class="flex-1 p-2 rounded bg-gray-900 border border-gray-700 text-gray-100">
                <button type="submit" class="px-3 py-1 bg-blue-600 rounded">Post</button>
            </form>
            @endauth
        </div>

    </div>
    @empty
        <div class="text-center text-gray-500 mt-10">No posts yet.</div>
    @endforelse

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $posts->links('pagination::tailwind') }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Likes AJAX
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            const postId = btn.dataset.postId;
            fetch(`/posts/${postId}/like`, { 
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } 
            })
            .then(res => res.json())
            .then(data => {
                btn.querySelector('.like-text').textContent = data.status === 'liked' ? 'Liked' : 'Like';
                if(data.status === 'liked'){
                    btn.classList.add('bg-blue-500','text-white','scale-105');
                    setTimeout(() => btn.classList.remove('scale-105'), 150);
                } else {
                    btn.classList.remove('bg-blue-500','text-white');
                }
            });
        });
    });

    // Toggle comments section
    document.querySelectorAll('.comment-toggle-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const section = btn.closest('.space-y-3').querySelector('.comments-section');
            section.classList.toggle('hidden');
        });
    });

    // View all / hide comments
    document.querySelectorAll('.view-all-comments-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const list = btn.closest('.space-y-3').querySelector('.comments-section');
            list.classList.toggle('max-h-36');
            btn.textContent = btn.textContent === 'View all comments' ? 'Hide comments' : 'View all comments';
        });
    });
</script>
@endpush
