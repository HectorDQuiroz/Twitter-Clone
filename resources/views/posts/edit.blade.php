@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Post</h1>

    <form id="edit-post-form" action="{{ route('posts.update', $post) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="content" class="form-label">Contenido</label>
            <textarea name="content" id="content" class="form-control" rows="3">{{ $post->content }}</textarea>
            <div id="error-message" class="alert alert-danger mt-2" style="display: none;"></div>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#edit-post-form').submit(function(event) {
        event.preventDefault();

        var postId = {{ $post->id }}; 
        var formData = $(this).serialize();

        $.ajax({
            url: '/posts/' + postId,
            method: 'PUT',
            data: formData,
            success: function(response) {

                window.location.href = "{{ route('posts.index') }}";
            },
            error: function(error) {
                // Mostrar mensaje de error
                if (error.responseJSON && error.responseJSON.errors && error.responseJSON.errors.content) {
                    var errorMessage = error.responseJSON.errors.content[0];
                    $('#error-message').text(errorMessage).show();
                } else {
                    console.error("Error al actualizar el post:", error);
                    alert('Ocurrió un error al actualizar el post.');
                }
            }
        });
    });
});
</script>
@endsection