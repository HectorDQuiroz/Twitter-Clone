@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Posts</h1>
        <form action="{{ route('posts.search') }}" method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" name="query" class="form-control" placeholder="Buscar posts..."
                    value="{{ request('query') }}">
                <button class="btn btn-outline-secondary" type="submit">Buscar</button>
            </div>
        </form>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <a href="{{ route('posts.create') }}" class="btn btn-primary mb-3">Crear Nuevo Post</a>

        <div id="post-list">
            @forelse ($posts as $post)
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><a href="{{ route('users.show', $post->user) }}">{{ $post->user->name }}</a>
                            ({{ $post->user->username ?? 'N/A' }})
                        </h5>
                        <p class="card-text">{{ $post->content }}</p>
                        <p class="card-text"><small
                                class="text-muted updated-at">{{ $post->created_at->diffForHumans() }}</small></p>

                        {{-- Formulario de edición (oculto inicialmente) --}}
                        <form class="edit-post-form" style="display: none;" method="POST"
                            action="{{ route('posts.update', $post) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <textarea name="content" class="form-control" rows="3">{{ $post->content }}</textarea>
                                <div class="error-message alert alert-danger mt-2" style="display: none;"></div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-update"
                                data-post-id="{{ $post->id }}">Actualizar</button>
                            <button type="button" class="btn btn-secondary btn-cancel">Cancelar</button>
                        </form>

                        {{-- Botones de acción --}}
                        <div class="post-actions">
                            <a href="#" class="btn btn-info btn-view" data-post-id="{{ $post->id }}">Ver</a>
                            @if (auth()->user()->id == $post->user_id)
                                <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning btn-edit"
                                    data-post-id="{{ $post->id }}">Editar</a>
                                <button class="btn btn-danger btn-delete"
                                    data-post-id="{{ $post->id }}">Eliminar</button>
                            @endif
                            {{-- Botón de Like/Unlike --}}
                            <button
                                class="btn btn-like {{ $post->likes->contains('user_id', auth()->id()) ? 'btn-secondary' : 'btn-primary' }}"
                                data-post-id="{{ $post->id }}">
                                <span
                                    class="like-text">{{ $post->likes->contains('user_id', auth()->id()) ? 'Unlike' : 'Like' }}</span>
                                (<span class="likes-count">{{ $post->likesCount() }}</span>)
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p>No se encontraron posts que coincidan con la búsqueda.</p>
            @endforelse
        </div>

        {{-- Paginación --}}
        <div class="d-flex justify-content-center">
            {{ $posts->links() }}
        </div>
    </div>

    <div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="postModalLabel">Detalles del Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script>
        $(document).ready(function() {

            $(document).on('submit', '#comment-form', function(event) {
                event.preventDefault();

                var form = $(this);
                var url = form.attr('action');
                var formData = form.serialize();

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        form.find('textarea[name="content"]').val('');
                        $('#comment-error').hide();

                        var newComment = `
                <div class="card mb-3">
                    <div class="card-body">
                        <p class="card-text"><strong>${response.user.name}:</strong> ${response.comment.content}</p>
                        <p class="card-text"><small class="text-muted">${response.comment.created_at}</small></p>
                    </div>
                </div>
            `;

                        $('#comments-list').append(newComment);

                    },
                    error: function(error) {

                        if (error.responseJSON && error.responseJSON.errors && error
                            .responseJSON.errors.content) {
                            $('#comment-error').text(error.responseJSON.errors.content[0])
                            .show();
                        } else {
                            console.error("Error al añadir comentario:", error);
                            alert('Ocurrió un error al añadir el comentario.');
                        }
                    }
                });
            });

            function searchPosts(query) {
                $.ajax({
                    url: "{{ route('posts.search') }}",
                    method: "GET",
                    data: {
                        query: query
                    },
                    success: function(response) {
                        $('#post-list').html(response);
                    },
                    error: function(error) {
                        console.error("Error al buscar posts:", error);
                        if (error.status === 404) {
                            $('#post-list').html(
                                '<p>No se encontraron posts que coincidan con la búsqueda.</p>');
                        } else {
                            alert("Ocurrió un error al buscar posts.");
                        }
                    }
                });
            }
            $('form[action="{{ route('posts.search') }}"]').submit(function(event) {
                event.preventDefault();
                var query = $('input[name="query"]').val();
                searchPosts(query);
            });

            $('form[action="{{ route('posts.search') }}"]').submit(function(event) {
                event.preventDefault();
                var query = $('input[name="query"]').val();
                searchPosts(query);
            });

            $('input[name="query"]').on('keyup', function() {
                var query = $(this).val();
                if (query.trim() !== "") {
                    searchPosts(query);
                } else {
                    searchPosts("");
                }
            });

            $('#post-list').on('click', '.btn-delete', function() {
                var postId = $(this).data('post-id');
                var postElement = $(this).closest('.card');

                var currentUrl = new URL(window.location.href);

                var queryParams = currentUrl.searchParams;

                var currentPage = queryParams.get('page') || 1;

                var url = '/posts/' + postId + '?page=' + currentPage;

                if (confirm('¿Estás seguro de eliminar este post?')) {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {

                            postElement.remove();
                            alert(response.message);


                            var currentPage = {{ $posts->currentPage() }};
                            var lastPage = {{ $posts->lastPage() }};
                            var currentUrl = new URL(window.location.href);

                            if (currentPage > 1 && $('#post-list').children().length === 0) {

                                currentUrl.searchParams.set('page', currentPage - 1);
                                window.location.href = currentUrl.href;
                            } else if (currentPage <= lastPage) {

                                location.reload();
                            }
                        },
                        error: function(error) {
                            console.error("Error al eliminar el post:", error);
                            alert('Ocurrió un error al eliminar el post.');
                        }
                    });
                }
            });


            $('#post-list').on('click', '.btn-view', function(event) {
                var postId = $(this).data('post-id');
                console.log("El valor de postId es:", postId);

                $.ajax({
                    url: '/posts/' + postId,
                    method: 'GET',
                    success: function(response) {

                        $('#postModal .modal-content').html(response);
                        // Mostrar el modal
                        $('#postModal').modal('show');
                    },
                    error: function(error) {
                        console.error("Error al obtener los detalles del post:", error);
                        alert('Ocurrió un error al obtener los detalles del post.');
                    }
                });
            });

            $('#post-list').on('click', '.btn-edit', function(event) {
                event.preventDefault();
                var postId = $(this).data('post-id');
                var cardBody = $(this).closest('.card-body');
                cardBody.find('.post-actions').hide();
                cardBody.find('.card-text').hide();
                cardBody.find('.edit-post-form').show();

                cardBody.find('.btn-cancel').click(function() {
                    cardBody.find('.edit-post-form').hide();
                    cardBody.find('.post-actions').show();
                    cardBody.find('.card-text').show();
                });
            });

            $('#post-list').on('submit', '.edit-post-form', function(event) {
                event.preventDefault();
                var postId = $(this).find('.btn-update').data('post-id');
                var cardBody = $(this).closest('.card-body');
                var formData = $(this).serialize();
                var url = "{{ route('posts.update', ':postId') }}";
                url = url.replace(':postId', postId);

                $.ajax({
                    url: url,
                    method: 'PUT',
                    data: formData,
                    success: function(response) {
                        cardBody.find('.card-text').first().html(response.post
                            .content);
                        cardBody.find('.card-text').show();

                        cardBody.find('.edit-post-form').hide();
                        cardBody.find('.post-actions').show();

                        cardBody.find('.text-muted.updated-at').text('Editado: ' + response.post
                            .updated_at);
                    },
                    error: function(error) {

                        var errorMessage = error.responseJSON.errors.content[0];
                        cardBody.find('.error-message').text(errorMessage).show();
                    }
                });
            });

            $('#post-list').on('click', '.btn-like', function() {
                var postId = $(this).data('post-id');
                var button = $(this);
                var likeText = button.find('.like-text');
                var likesCount = button.find('.likes-count');

                var url = "{{ route('posts.like', ':postId') }}";
                url = url.replace(':postId', postId);

                var isLiked = likeText.text() === 'Unlike';

                $.ajax({
                    url: url,
                    method: isLiked ? 'DELETE' : 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {

                        if (isLiked) {
                            likeText.text('Like');
                            button.removeClass('btn-secondary').addClass('btn-primary');
                        } else {
                            likeText.text('Unlike');
                            button.removeClass('btn-primary').addClass('btn-secondary');
                        }
                        likesCount.text(response.likes_count);
                    },
                    error: function(error) {
                        console.error("Error al dar/quitar like:", error);
                        alert('Ocurrió un error al dar/quitar like.');
                    }
                });
            });
        });
    </script>
@endsection
