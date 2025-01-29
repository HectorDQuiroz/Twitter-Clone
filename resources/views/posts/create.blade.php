@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Nuevo Post</h1>

    <form id="create-post-form" action="{{ route('posts.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="content" class="form-label">Contenido</label>
            <textarea name="content" id="content" class="form-control" rows="3"></textarea>
            <div id="error-message" class="alert alert-danger mt-2" style="display: none;"></div>
        </div>
        <button type="submit" class="btn btn-primary">Crear</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#create-post-form').submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                data: $(this).serialize(),
                success: function(response) {
                    $('#content').val('');
                    $('#error-message').hide();

                    var newPost = $('<div class="card mb-3">' +
                        '<div class="card-body">' +
                        '<h5 class="card-title">' + response.user.name + ' (' + response.user.username + ')</h5>' +
                        '<p class="card-text">' + response.content + '</p>' +
                        '<p class="card-text"><small class="text-muted">Justo ahora</small></p>' +
                        '<button class="btn btn-info btn-view" data-post-id="' + response.id + '">Ver</button>' +
                        (response.user.id == {{ auth()->user()->id }} ?
                            '<button class="btn btn-warning btn-edit" data-post-id="' + response.id + '">Editar</button>' +
                            '<button class="btn btn-danger btn-delete" data-post-id="' + response.id + '">Eliminar</button>' :
                            '') +
                        '</div>' +
                        '</div>');

                    $('#post-list').prepend(newPost);

                    window.location.href = "{{ route('posts.index') }}?page=1";
                },
                error: function(error) {
                    var errorMessage = error.responseJSON.errors.content[0];
                    $('#error-message').text(errorMessage).show();
                }
            });
        });
    });
</script>
@endsection