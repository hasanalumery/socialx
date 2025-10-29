@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl mb-4">Feed</h1>

    @forelse($posts as $post)
        <article class="mb-6 border rounded p-4">
            <div class="flex items-center mb-2">
                <div class="font-semibold mr-2">{{ $post->user->name ?? 'Unknown' }}</div>
                <div class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</div>
            </div>

            <div class="mb-3">
                {{-- Text content --}}
                <p>{{ $post->content }}</p>

                {{-- If you later support media, guard against missing relation --}}
                @if(method_exists($post, 'media') && $post->media->isNotEmpty())
                    <div class="mt-2 grid grid-cols-3 gap-2">
                        @foreach($post->media as $m)
                            <img src="{{ asset($m->path) }}" alt="" class="w-full h-32 object-cover">
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex items-center space-x-4">
                <form action="{{ route('posts.like', $post) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1 border rounded">@if($post->likes->where('user_id', auth()->id())->count()) Unlike @else Like @endif ({{ $post->likes->count() }})</button>
                </form>

                <a href="{{ route('posts.show', $post) }}" class="px-3 py-1 border rounded">View</a>
            </div>

            {{-- Comments preview --}}
            <div class="mt-3">
                @foreach($post->comments->take(2) as $comment)
                    <div class="text-sm text-gray-700 mb-1"><strong>{{ $comment->user->name ?? 'Anon' }}:</strong> {{ $comment->body }}</div>
                @endforeach
                @if($post->comments->count() > 2)
                    <div class="text-xs text-gray-500">+{{ $post->comments->count() - 2 }} more</div>
                @endif
            </div>
        </article>
    @empty
        <div class="text-gray-600">No posts yet. Create the first one.</div>
    @endforelse

    <div class="mt-4">
        {{ $posts->links() }}
    </div>
</div>
@endsection
