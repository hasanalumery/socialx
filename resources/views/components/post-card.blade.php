<div class="post-card">
    <h2>{{ $post->title }}</h2>
    <p>{{ $post->body }}</p>
    <!-- Add likes/comments buttons if needed -->
    <form action="{{ route('posts.like', $post) }}" method="POST" style="display:inline">
  @csrf
  <button type="submit" aria-label="Like" class="btn-like">
    @if(auth()->check() && $post->isLikedBy(auth()->id()))
      Unlike
    @else
      Like
    @endif
  </button>
</form>
@auth
<form action="{{ route('comments.store', $post) }}" method="POST" class="comment-form">
  @csrf
  <input name="body" type="text" required placeholder="Write a comment..." />
  <button type="submit">Comment</button>
</form>
@endauth

</div>
