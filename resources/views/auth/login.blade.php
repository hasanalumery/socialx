@extends('layouts.guest')

@section('content')
<h2 class="text-2xl font-bold text-white text-center mb-6">Welcome Back to SocialX</h2>

<!-- Session Status -->
@if (session('status'))
    <div class="p-3 mb-4 rounded-md bg-green-800 text-green-100 text-sm">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    <input type="email" name="email" value="{{ old('email') }}" required autofocus
           placeholder="Email"
           class="w-full bg-gray-900 rounded-xl p-3 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">
    @error('email')
        <p class="text-red-400 text-sm mb-2">{{ $message }}</p>
    @enderror

    <input type="password" name="password" required
           placeholder="Password"
           class="w-full bg-gray-900 rounded-xl p-3 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">
    @error('password')
        <p class="text-red-400 text-sm mb-2">{{ $message }}</p>
    @enderror

    <label class="flex items-center gap-2 mb-4 text-sm text-gray-300">
        <input type="checkbox" name="remember" class="rounded">
        Remember me
    </label>

    <button type="submit"
            class="w-full bg-blue-500 text-white py-2 rounded-xl hover:bg-blue-600 transition">
        Log In
    </button>

    @if (Route::has('password.request'))
        <p class="text-sm text-gray-400 mt-4 text-center">
            <a href="{{ route('password.request') }}" class="text-blue-400 hover:underline">
                Forgot your password?
            </a>
        </p>
    @endif
</form>

<p class="text-gray-300 text-sm mt-6 text-center">
    Don't have an account? 
    <a href="{{ route('register') }}" class="text-blue-400 hover:underline">Register</a>
</p>
@endsection
