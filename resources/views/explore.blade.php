@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Explore</h1>

<div class="space-y-6">
    @foreach($posts as $post)
        <x-post-card :post="$post" />
    @endforeach

    {{ $posts->links() }}
</div>
@endsection
