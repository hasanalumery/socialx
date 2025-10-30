<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social X</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-gray-100 font-sans">

    <!-- Header -->
    <nav class="border-b border-gray-800 bg-gray-950 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto flex justify-between items-center p-4">
            <a href="/" class="text-xl font-bold text-white">Social<span class="text-blue-400">X</span></a>
            <div class="flex gap-6 items-center">
                <a href="/feed" class="hover:text-blue-400">Home</a>
                <a href="/explore" class="hover:text-blue-400">Explore</a>
                <a href="/profile" class="hover:text-blue-400">Profile</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto mt-6 px-4">
        @yield('content')
    </main>

</body>
</html>
