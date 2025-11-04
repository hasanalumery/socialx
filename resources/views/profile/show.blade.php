@extends('layouts.app')

@section('title', 'Profile — SocialX')

@section('content')
<div class="space-y-6 max-w-xl mx-auto">

    <div class="bg-gray-800 p-6 rounded-2xl flex items-center gap-6">
    <div class="w-24 h-24 rounded-full bg-gray-700 overflow-hidden flex-shrink-0">
        <img src="{{ $user->profile_picture ? asset('storage/'.$user->profile_picture) : 'https://via.placeholder.com/150' }}" 
             alt="Profile Pic" class="w-full h-full object-cover">
    </div>
    <div class="flex-1">
        <h2 class="text-2xl font-bold text-white">{{ $user->name }}</h2>
        <p class="text-gray-400 mt-1">{{ $user->bio ?? 'This user has no bio yet.' }}</p>
        <p class="text-gray-500 text-sm mt-1">{{ $user->posts->count() }} Posts</p>
    </div>

    {{-- smaller edit button, only visible to authenticated owner --}}
    @auth
        @if(auth()->id() == $user->id)
            <a href="{{ route('profile.edit') }}" class="ml-4 px-3 py-2 bg-gray-700 hover:bg-gray-600 text-sm text-gray-100 rounded-xl">
                Edit profile
            </a>
        @endif
    @endauth
</div>


        {{-- User Info --}}
        <div>
            <h1 class="text-xl font-semibold text-white">{{ auth()->user()->name }}</h1>
            <p class="text-gray-400 text-sm">{{ auth()->user()->email }}</p>
        </div>
    </div>

    {{-- Edit Profile Button --}}
    <div class="flex justify-end">
        <a href="{{ route('profile.edit') }}" 
           class="bg-blue-500 hover:bg-blue-600 px-5 py-2 rounded-2xl font-semibold text-white transition">
            Edit Profile
        </a>
    </div>

    {{-- User's Posts --}}
    @if($posts->count() > 0)
        <div class="space-y-6">
            @foreach($posts as $post)
                <div class="bg-gray-800 rounded-2xl p-4 shadow space-y-3">

                    {{-- Header --}}
                    <div class="flex items-center gap-2 text-sm text-gray-400">
                        <span class="font-semibold text-white">{{ auth()->user()->name }}</span>
                        <span>· {{ $post->created_at->diffForHumans() }}</span>
                    </div>

                    {{-- Content --}}
                    <div class="text-gray-200">{{ $post->content }}</div>

                    {{-- Media --}}
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
            @endforeach
        </div>
    @else
        <div class="text-center text-gray-500 mt-10">No posts yet.</div>
    @endif

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $posts->links('pagination::tailwind') }}
    </div>
</div>
@endsection
