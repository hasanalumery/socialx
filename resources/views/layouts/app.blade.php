<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'SocialX'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-gray-100 font-sans antialiased">

    {{-- Header / Navigation --}}
    <nav class="bg-gray-950 border-b border-gray-800 sticky top-0 z-50 shadow-sm" x-data="{ mobileOpen: false }">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo + Desktop Links --}}
                <div class="flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-lg font-bold tracking-tight text-white hover:text-blue-400">
                        Social<span class="text-blue-400">X</span>
                    </a>

                    {{-- Desktop Links --}}
                    <div class="hidden sm:flex items-center gap-4">
                        <a href="{{ route('home') }}" class="text-sm hover:text-blue-400 {{ request()->routeIs('home') ? 'text-blue-400 font-semibold' : '' }}">Home</a>
                        <a href="{{ route('explore') }}" class="text-sm hover:text-blue-400 {{ request()->routeIs('explore') ? 'text-blue-400 font-semibold' : '' }}">Explore</a>
                        {{-- Additional links placeholder --}}
                    </div>
                </div>

                {{-- Right side: Auth / User dropdown --}}
                <div class="hidden sm:flex items-center gap-4">
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 text-sm focus:outline-none">
                                <span>{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M5.23 7.21a.75.75 0 011.06.02L10 11.584l3.71-4.353a.75.75 0 111.14.98l-4 4.7a.75.75 0 01-1.14 0l-4-4.7a.75.75 0 01.02-1.06z"/>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition
                                class="absolute right-0 mt-2 w-44 bg-gray-800 rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5">
                                <a href="{{ route('profile.show', auth()->user()) }}" class="block px-4 py-2 text-sm hover:bg-gray-700">My profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-700">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm hover:text-blue-400">Login</a>
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm hover:text-blue-400">Register</a>
                        @endif
                    @endauth
                </div>

                {{-- Mobile menu button --}}
                <div class="sm:hidden flex items-center">
                    <button @click="mobileOpen = !mobileOpen" class="text-gray-200 hover:text-white focus:outline-none">
                        <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Menu --}}
            <div x-show="mobileOpen" x-transition class="sm:hidden mt-2 space-y-2 px-2">
                <a href="{{ route('home') }}" class="block px-4 py-2 text-white hover:bg-gray-800 rounded {{ request()->routeIs('home') ? 'bg-gray-800 font-semibold' : '' }}">Home</a>
                <a href="{{ route('explore') }}" class="block px-4 py-2 text-white hover:bg-gray-800 rounded {{ request()->routeIs('explore') ? 'bg-gray-800 font-semibold' : '' }}">Explore</a>

                @auth
                    <a href="{{ route('profile.show', auth()->user()) }}" class="block px-4 py-2 text-white hover:bg-gray-800 rounded">My Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-white hover:bg-gray-800 rounded">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-4 py-2 text-white hover:bg-gray-800 rounded">Login</a>
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="block px-4 py-2 text-white hover:bg-gray-800 rounded">Register</a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    <div class="max-w-3xl mx-auto px-4 mt-6 space-y-4">
        @if(session('status'))
            <div class="p-3 rounded-md bg-green-800 text-green-100">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-3 rounded-md bg-red-800 text-red-100">
                <strong class="block font-semibold mb-1">There were some problems:</strong>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- Main Content --}}
    <main class="max-w-3xl mx-auto mt-6 px-4 pb-12 space-y-6">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="max-w-3xl mx-auto px-4 pb-8 text-sm text-gray-400">
        <div class="border-t border-gray-800 pt-4 mt-8 flex justify-between items-center">
            <span>© {{ date('Y') }} {{ config('app.name', 'SocialX') }}</span>
            <span class="text-blue-400">Made by Ika Studio</span>
        </div>
    </footer>

    {{-- AlpineJS --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    {{-- Page-specific AJAX scripts --}}
    @stack('scripts')

</body>
</html>
