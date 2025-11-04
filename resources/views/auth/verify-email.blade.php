@extends('layouts.guest')

@section('content')
<h2 class="text-2xl font-bold text-white text-center mb-6">Verify Your Email</h2>

<p class="text-gray-300 mb-4">
    Before continuing, please check your email for a verification link.
    If you did not receive the email, you can request another.
</p>

@if (session('status') == 'verification-link-sent')
    <div class="p-3 mb-4 rounded-md bg-green-800 text-green-100 text-sm">
        A new verification link has been sent to your email.
    </div>
@endif

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit"
            class="w-full bg-blue-500 text-white py-2 rounded-xl hover:bg-blue-600 transition mb-2">
        Resend Verification Email
    </button>
</form>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit"
            class="w-full bg-gray-700 text-white py-2 rounded-xl hover:bg-gray-600 transition">
        Logout
    </button>
</form>
@endsection
