@extends('layouts.app')

@section('title', 'Edit Profile — SocialX')

@section('content')
<div class="space-y-6">

    {{-- Flash messages --}}
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

    <div class="bg-gray-800 rounded-2xl p-6 shadow-md max-w-xl mx-auto space-y-4">
        <h2 class="text-xl font-semibold text-white">Edit Your Profile</h2>

        {{-- ========================
             UPDATE FORM — POST
             action: profile.update (POST)
             ======================== --}}
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            {{-- NOTE: NO @method('DELETE') and NO @method('PUT') here --}}

            <div>
                <label class="block text-gray-300 mb-1" for="name">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="w-full p-3 rounded-2xl bg-gray-900 border border-gray-700 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-300 mb-1" for="bio">Bio</label>
                <textarea id="bio" name="bio" rows="3" class="w-full p-3 rounded-2xl bg-gray-900 border border-gray-700 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('bio', $user->bio) }}</textarea>
            </div>

            <div>
                <label class="block text-gray-300 mb-1" for="profile_picture">Profile Picture</label>
                <input id="profile_picture" type="file" name="profile_picture" accept="image/*"
                       class="w-full text-gray-100 bg-gray-900 border border-gray-700 rounded-2xl p-2 cursor-pointer">
                
                @if($user->profile_picture)
                    <div class="mt-2">
                        <p class="text-gray-400 text-sm">Current Picture:</p>
                        <img src="{{ asset('storage/' . $user->profile_picture) }}"
                             class="mt-1 rounded-2xl w-24 h-24 object-cover border border-gray-700">
                    </div>
                @endif
            </div>

            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-400">Max image size: 2MB</div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-2xl font-semibold transition">
                        Save Changes
                    </button>

                    <a href="{{ route('profile.show', $user) }}" class="px-4 py-2 rounded-2xl bg-gray-700 hover:bg-gray-600 text-gray-200">View profile</a>
                </div>
            </div>
        </form>

        {{-- ========================
             DELETE FORM — separate (DELETE -> profile.destroy)
             ======================== --}}
        <div class="pt-4 border-t border-gray-700">
            <form action="{{ route('profile.destroy') }}" method="POST" onsubmit="return confirm('Are you sure? This will permanently delete your account.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-2xl font-semibold">Delete account</button>
            </form>
        </div>
    </div>
</div>
@endsection
