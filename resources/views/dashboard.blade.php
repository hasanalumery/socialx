@extends('layouts.app')

@section('title', 'Dashboard — SocialX')

@section('content')
<div class="bg-gray-800 p-6 rounded-2xl shadow space-y-4">

    <h2 class="text-2xl font-bold text-white">You're logged in ✨</h2>

    <p class="text-gray-400">Welcome back! Choose where you want to go:</p>

    <div class="mt-4 flex gap-3">

        {{-- HOME FEED --}}
        <a href="{{ route('home') }}"
           class="px-4 py-2 bg-blue-600 rounded-xl font-semibold hover:bg-blue-700 transition">
            Go to Home
        </a>

        {{-- EXPLORE --}}
        <a href="/explore"
           class="px-4 py-2 bg-gray-700 rounded-xl font-semibold hover:bg-gray-600 transition">
            Explore Posts
        </a>

    </div>

</div>
@endsection
