@extends('layouts.guest')

@section('content')
<h2 class="text-2xl font-bold text-white text-center mb-6">Set a New Password</h2>

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <input type="email" name="email" value="{{ old('email', $request->email) }}" required
           placeholder="Email"
           class="w-full bg-gray-900 rounded-xl p-3 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">
    @error('email')
        <p class="text-red-400 text-sm mb-2">{{ $message }}</p>
    @enderror

    <input type="password" name="password" required placeholder="New Password"
           class="w-full bg-gray-900 rounded-xl p-3 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">
    @error('password')
        <p class="text-red-400 text-sm mb-2">{{ $message }}</p>
    @enderror

    <input type="password" name="password_confirmation" required placeholder="Confirm New Password"
           class="w-full bg-gray-900 rounded-xl p-3 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">

    <button type="submit"
            class="w-full bg-blue-500 text-white py-2 rounded-xl hover:bg-blue-600 transition">
        Reset Password
    </button>

    <p class="text-gray-300 text-sm mt-6 text-center">
        <a href="{{ route('login') }}" class="text-blue-400 hover:underline">Back to Login</a>
    </p>
</form>
@endsection
