@extends('layouts.guest')

@section('content')
<h2 class="text-2xl font-bold text-white text-center mb-6">Create a SocialX Account</h2>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <input type="text" name="name" value="{{ old('name') }}" required
           placeholder="Name"
           class="w-full bg-gray-900 rounded-xl p-3 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">
    @error('name')
        <p class="text-red-400 text-sm mb-2">{{ $message }}</p>
    @enderror

    <input type="email" name="email" value="{{ old('email') }}" required
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

    <input type="password" name="password_confirmation" required
           placeholder="Confirm Password"
           class="w-full bg-gray-900 rounded-xl p-3 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">

    <button type="submit"
            class="w-full bg-blue-500 text-white py-2 rounded-xl hover:bg-blue-600 transition">
        Register
    </button>

    <p class="text-gray-300 text-sm mt-6 text-center">
        Already have an account? 
        <a href="{{ route('login') }}" class="text-blue-400 hover:underline">Log In</a>
    </p>
</form>
@endsection
