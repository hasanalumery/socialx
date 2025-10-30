@extends('layouts.app')

@section('title', 'Explore — SocialX')

@section('content')
<h1 class="text-2xl font-bold mb-4">Explore</h1>

<div class="space-y-6">
    @forelse($posts as $post)
    <div class="bg-gray-800 rounded-2xl p-4 shadow space-y-3">

        {{-- Post Header --}}
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <span class="font-semibold text-white">{{ $post->user?->name ?? 'Unknown' }}</span>
            <span>· {{ $post->created_at->diffForHumans() }}</span>
        </div>

        {{-- Post Content --}}
        <div class="text-gray-200">{{ $post->content }}</div>

        {{-- Post Media --}}
        @if($post->media)
            <img src="{{ asset('storage/' . $post->media) }}" class="rounded-xl mt-2 w-full max-h-[600px] object-cover">
        @endif

        {{-- Likes --}}
        <div class="flex items-center gap-3">
            @auth
            <form action="{{ route('posts.like', $post->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-sm text-blue-500 font-medium">
                    {{ $post->likes->count() }} 
                    {{ method_exists($post, 'isLikedBy') && $post->isLikedBy(auth()->id()) ? 'Unlike' : 'Like' }}
                </button>
            </form>
            @else
            <span class="text-sm text-gray-500">{{ $post->likes->count() }} likes</span>
            @endauth
        </div>

        {{-- Comments --}}
        <div class="mt-2 space-y-1">
            @foreach($post->comments as $comment)
                <p class="text-sm text-gray-400">
                    <span class="font-semibold">{{ $comment->user?->name ?? 'Unknown' }}</span> 
                    {{ $comment->body }}
                </p>
            @endforeach

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
