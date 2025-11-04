@extends('layouts.guest')

@section('content')
<h2 class="text-2xl font-bold text-white text-center mb-6">Confirm Your Password</h2>

<form method="POST" action="{{ route('password.confirm') }}">
    @csrf

    <input type="password" name="password" required placeholder="Password"
           class="w-full bg-gray-900 rounded-xl p-3 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">
    @error('password')
        <p class="text-red-400 text-sm mb-2">{{ $message }}</p>
    @enderror

    <button type="submit"
            class="w-full bg-blue-500 text-white py-2 rounded-xl hover:bg-blue-600 transition">
        Confirm Password
    </button>
</form>
@endsection
