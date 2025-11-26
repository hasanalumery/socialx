@extends('layouts.app')

@section('title', ($user->name ?? 'Profile') . ' — SocialX')

@section('content')
<div class="space-y-6">

    {{-- Profile header --}}
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
                    @if(method_exists($user, 'posts'))
                        <span class="text-sm text-gray-400">· {{ $user->posts()->count() ?? 0 }} posts</span>
                    @endif
                </div>
                @if(!empty($user->bio))
                    <p class="text-sm text-gray-300 mt-1 leading-relaxed break-words">{{ $user->bio }}</p>
                @endif
            </div>
        </div>

        {{-- Follow/Edit buttons --}}
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
                            <button type="submit" class="px-4 py-2 rounded-full bg-gray-600 hover:bg-gray-700 text-white w-full md:w-auto transition">Unfollow</button>
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

    {{-- Posts feed (Twitter style: single column) --}}
    <div class="flex flex-col gap-4">
        @foreach($posts as $post)
            @php
                $media = $post->media ?? null;
                $isVideo = false;
                if ($media) {
                    $ext = strtolower(pathinfo($media, PATHINFO_EXTENSION) ?? '');
                    $isVideo = in_array($ext, ['mp4','webm']);
                }
            @endphp

            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 flex flex-col gap-3 shadow-sm hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    @if($post->user->profile_picture)
                        <img src="{{ asset('storage/' . $post->user->profile_picture) }}" alt="{{ $post->user->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-700">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center text-white font-bold border border-gray-700">
                            {{ strtoupper(substr($post->user->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h3 class="text-white font-semibold">{{ $post->user->name }}</h3>
                        <span class="text-gray-400 text-sm">{{ $post->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                {{-- Post content --}}
                <div class="flex flex-col gap-2">
                    @if($media)
                        @if($isVideo)
                            <div class="w-full rounded-lg overflow-hidden bg-black">
                                <video class="w-full" muted playsinline controls>
                                    <source src="{{ asset('storage/' . $media) }}" type="video/{{ $ext }}">
                                </video>
                            </div>
                        @else
                            <img src="{{ asset('storage/' . $media) }}" alt="Post media" class="w-full rounded-lg object-cover">
                        @endif
                    @endif

                    @if($post->content ?? $post->body)
                        <p class="text-gray-300 whitespace-pre-line break-words">{{ $post->content ?? $post->body }}</p>
                    @endif
                </div>

                {{-- Likes/comments --}}
                <div class="flex items-center gap-4 text-sm text-gray-400">
                    <span>{{ $post->likes()->count() ?? 0 }} ❤</span>
                    <span>{{ $post->comments()->count() ?? 0 }} 💬</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if(method_exists($posts, 'links'))
        <div class="mt-4">
            {{ $posts->links('pagination::tailwind') }}
        </div>
    @endif

</div>
@endsection
