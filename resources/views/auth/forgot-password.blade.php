@extends('layouts.guest')

@section('content')
<h2 class="text-2xl font-bold text-white text-center mb-6">Reset Your Password</h2>

@if (session('status'))
    <div class="p-3 mb-4 rounded-md bg-green-800 text-green-100 text-sm">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <input type="email" name="email" required placeholder="Email"
           class="w-full bg-gray-900 rounded-xl p-3 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">
    @error('email')
        <p class="text-red-400 text-sm mb-2">{{ $message }}</p>
    @enderror

    <button type="submit"
            class="w-full bg-blue-500 text-white py-2 rounded-xl hover:bg-blue-600 transition">
        Send Password Reset Link
    </button>

    <p class="text-gray-300 text-sm mt-6 text-center">
        <a href="{{ route('login') }}" class="text-blue-400 hover:underline">Back to Login</a>
    </p>
</form>
@endsection
