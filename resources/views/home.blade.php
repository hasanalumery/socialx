@extends('layouts.app')

@section('content')
  <h1 class="text-2xl font-bold mb-4">Welcome to SocialX</h1>

  @auth
    <form method="POST" action="{{ route('posts.store') }}" class="mb-6">
      @csrf
      <textarea name="content" rows="2" class="w-full border rounded p-2" placeholder="What's on your mind?" required>{{ old('content') }}</textarea>
      <x-input-error :messages="$errors->get('content')" class="mt-1" />
      <button type="submit" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded">Post</button>
    </form>
  @else
    <p class="mb-4">Please <a href="{{ route('login') }}" class="text-blue-600">log in</a> to post and like.</p>
  @endauth

  @foreach($posts as $post)
    <div class="p-4 mb-3 border rounded shadow-sm bg-white">
      <p class="font-semibold">{{ $post->user?->name ?? 'Unknown' }}</p>
      <p>{{ $post->content }}</p>
      <p class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</p>

      @auth
        <form action="{{ route('posts.like', $post->id) }}" method="POST" class="mt-2 inline">
          @csrf
          <button type="submit" class="text-blue-600">
            {{ $post->likes->count() }}
            {{ $post->isLikedBy(auth()->id()) ? 'Unlike' : 'Like' }}
          </button>
        </form>
      @else
        <span class="text-gray-500 mt-2 inline">{{ $post->likes->count() }} likes</span>
      @endauth
    </div>
  @endforeach

  {{ $posts->links() }} {{-- pagination links if using paginate() --}}
@endsection
