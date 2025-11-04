@extends('layouts.app')

@section('title', 'Home — SocialX')

@section('content')
<div class="space-y-6">

    {{-- Create Post --}}
    @auth
    <div class="bg-gray-800 rounded-2xl p-4 shadow-md">
        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <textarea name="content" rows="2" placeholder="What's on your mind?" 
                      class="w-full bg-gray-900 rounded-2xl p-3 text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                      maxlength="1000">{{ old('content') }}</textarea>
            <div class="flex items-center justify-between mt-2">
                <label class="bg-gray-700 text-gray-200 px-3 py-1 rounded-full cursor-pointer hover:bg-gray-600 transition">
                    Add Media
                    <input type="file" name="media" class="hidden" accept="image/*,video/*">
                </label>
                <button type="submit" 
                        class="bg-blue-500 text-white px-4 py-1 rounded-2xl hover:bg-blue-600 transition">
                    Post
                </button>
            </div>
        </form>
    </div>
    @endauth

    {{-- Posts Feed --}}
    @forelse($posts ?? [] as $post)
    <div class="bg-gray-800 rounded-2xl p-4 shadow-md space-y-3">

        {{-- Post header --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center text-white font-bold">
                {{ strtoupper(substr($post->user?->name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-semibold">{{ $post->user?->name ?? 'Unknown' }}</p>
                <p class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</p>
            </div>
        </div>

        {{-- Post content --}}
        <p class="text-gray-100 text-base">{{ $post->content }}</p>

        {{-- Media --}}
        @if($post->media)
        <img src="{{ asset('storage/' . $post->media) }}" alt="post media" class="rounded-2xl max-h-80 w-full object-cover">
        @endif

        <button class="js-like-btn inline-flex items-center gap-2 px-3 py-1 rounded-full"
        data-post-id="{{ $post->id }}"
        aria-pressed="{{ $post->isLikedBy(auth()->id()) ? 'true' : 'false' }}">
    <span class="js-like-text">{{ $post->isLikedBy(auth()->id()) ? 'Liked' : 'Like' }}</span>
    <span class="js-likes-count">({{ $post->likes->count() }})</span>
</button>


        {{-- Actions --}}
        <div class="flex items-center gap-4 pt-2 border-t border-gray-700">
            <button class="like-btn flex items-center gap-1 px-3 py-1 rounded-full bg-gray-700 hover:bg-blue-500 transition-transform transform"
                    data-post-id="{{ $post->id }}">
                <span class="like-text">
                    @auth
                        {{ $post->isLikedBy(auth()->id()) ? 'Liked' : 'Like' }}
                    @else
                        Like
                    @endauth
                </span>
                <span class="text-sm text-gray-300">({{ $post->likes->count() }})</span>
            </button>
            <button class="comment-toggle-btn flex items-center gap-1 px-3 py-1 rounded-full bg-gray-700 hover:bg-gray-600 transition">
                Comment ({{ $post->comments->count() }})
            </button>
        </div>

        {{-- Comments --}}
        <div class="comments-section mt-2 hidden space-y-2">
            <form action="{{ route('comments.store', $post) }}" method="POST" class="mb-2">
                @csrf
                <input type="text" name="body" placeholder="Add a comment..."
                       class="w-full bg-gray-900 rounded-full p-2 text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </form>

            <div class="space-y-2 max-h-36 overflow-hidden">
                @foreach($post->comments->take(3) as $comment)
                    <div class="bg-gray-700 rounded-full p-2 text-sm text-gray-100">
                        <span class="font-semibold">{{ $comment->user?->name ?? 'Unknown' }}</span>: {{ $comment->body }}
                    </div>
                @endforeach
                @if($post->comments->count() > 3)
                    <button class="text-xs text-blue-400 mt-1 view-all-comments-btn">View all comments</button>
                @endif
            </div>
        </div>
    </div>
    @empty
    <p class="text-gray-400 text-center mt-4">No posts found.</p>
    @endforelse
</div>
@endsection
