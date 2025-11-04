<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SocialX') }}</title>

    {{-- Vite assets (Tailwind + app JS) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-gray-100 font-sans antialiased">

    {{-- Header / Nav --}}
    <nav class="bg-gray-950 border-b border-gray-800 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-lg font-bold tracking-tight text-white hover:text-blue-400">
                        Social<span class="text-blue-400">X</span>
                    </a>

                    <div class="hidden sm:flex items-center gap-4">
                        <a href="{{ route('home') }}" class="text-sm hover:text-blue-400">Home</a>
                        <a href="{{ route('explore') }}" class="text-sm hover:text-blue-400">Explore</a>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    {{-- Auth-aware links --}}
                    @auth
                        <a href="{{ route('profile.edit') }}" class="hidden sm:inline-block text-sm hover:text-blue-400">Profile</a>

                        {{-- Optional dashboard link for admins --}}
                        @if(Route::has('dashboard'))
                            <a href="{{ route('dashboard') }}" class="hidden sm:inline-block text-sm hover:text-blue-400">Dashboard</a>
                        @endif

                        {{-- User dropdown (mobile-friendly: keep simple) --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 text-sm focus:outline-none">
                                <span class="sr-only">Open user menu</span>
                                <span>{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M5.23 7.21a.75.75 0 011.06.02L10 11.584l3.71-4.353a.75.75 0 111.14.98l-4 4.7a.75.75 0 01-1.14 0l-4-4.7a.75.75 0 01.02-1.06z"/></svg>
                            </button>

                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-40 bg-gray-800 rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-gray-700">My profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-700">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm hover:text-blue-400">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm hover:text-blue-400">Register</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash messages & errors --}}
    <div class="max-w-3xl mx-auto px-4 mt-6">
        @if(session('status'))
            <div class="mb-4 p-3 rounded-md bg-green-800 text-green-100">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 rounded-md bg-red-800 text-red-100">
                <strong class="block font-semibold mb-1">There were some problems:</strong>
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- Main content --}}
    <main class="max-w-3xl mx-auto mt-4 px-4 pb-12">
        @yield('content')
    </main>

    {{-- Footer (optional) --}}
    <footer class="max-w-3xl mx-auto px-4 pb-8 text-sm text-gray-500">
        <div class="border-t border-gray-800 pt-4 mt-8">
            © {{ date('Y') }} {{ config('app.name', 'SocialX') }} — built with Laravel.
        </div>
    </footer>

    {{-- Alpine (optional): only include if you use x-data in templates --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
