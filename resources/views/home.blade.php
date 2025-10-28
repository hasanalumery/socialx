@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Welcome to SocialX</h1>

    @auth
        {{-- ✅ Post creation form --}}
        <form method="POST" action="{{ route('posts.store') }}" class="mb-6">
            @csrf
            <textarea 
                name="content" 
                rows="2" 
                class="w-full border rounded p-2" 
                placeholder="What's on your mind?"
                required></textarea>
            <button 
                type="submit" 
                class="mt-2 px-4 py-2 bg-blue-600 text-white rounded">
                Post
            </button>
        </form>
    @endauth

    {{-- ✅ Loop through posts --}}
    @foreach($posts as $post)
        <div class="p-4 mb-3 border rounded shadow-sm bg-white">
            <p class="font-semibold">{{ $post->user?->name ?? 'Unknown' }}</p>
            <p>{{ $post->content }}</p>
            <p class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</p>

            {{-- ✅ Like/Unlike button inside the loop --}}
            <form action="{{ route('posts.like', $post->id) }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="text-blue-600">
                    {{ $post->likes->count() }} 
                    {{ $post->likes->where('user_id', auth()->id())->count() ? 'Unlike' : 'Like' }}
                </button>
            </form>
        </div>
    @endforeach
@endsection
