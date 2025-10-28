@if(auth()->user()->following->contains($user->id))
  <form action="{{ route('user.unfollow', $user) }}" method="POST">
      @csrf
      @method('DELETE')
      <button class="bg-gray-300 px-3 py-1 rounded">Unfollow</button>
  </form>
@else
  <form action="{{ route('user.follow', $user) }}" method="POST">
      @csrf
      <button class="bg-blue-500 text-white px-3 py-1 rounded">Follow</button>
  </form>
@endif
