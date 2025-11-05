@extends('layouts.app')

@section('title', $user->name . ' — Profile')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Profile header --}}
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

        {{-- edit button small --}}
        @auth
            @if(auth()->id() == $user->id)
                <a href="{{ route('profile.edit') }}" class="ml-4 px-3 py-2 bg-gray-700 hover:bg-gray-600 text-sm text-gray-100 rounded-xl">
                    Edit profile
                </a>
            @endif
        @endauth
    </div>

    {{-- Posts --}}
    <div class="space-y-4">
        @forelse($posts as $post)
            <div class="bg-gray-800 rounded-2xl p-4 shadow-md space-y-3">
                {{-- same simplified post card as home (avatar + content) --}}
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($post->user->name,0,1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold">{{ $post->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <p class="text-gray-100 text-base">{{ $post->content }}</p>
                @if($post->media)
                    <img src="{{ asset('storage/'.$post->media) }}" class="rounded-2xl max-h-80 w-full object-cover">
                @endif

                <div class="flex items-center gap-4 pt-2 border-t border-gray-700">
                    <span class="text-sm text-gray-400">Likes: {{ $post->likes->count() }}</span>
                    <span class="text-sm text-gray-400">Comments: {{ $post->comments->count() }}</span>
                </div>
            </div>
        @empty
            <p class="text-gray-400 text-center">This user hasn't posted anything yet.</p>
        @endforelse

        {{ $posts->links() }}
    </div>
</div>
@endsection
