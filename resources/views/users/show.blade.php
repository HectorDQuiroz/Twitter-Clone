@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Perfil de {{ $user->name }}</div>

                    <div class="card-body">
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
                        <p><strong>Nombre:</strong> {{ $user->name }}</p>
                        <p><strong>Nombre de usuario:</strong> {{ $user->username ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Siguiendo:</strong> {{ $user->followingCount() }}</p>
                        <p><strong>Seguidores:</strong> {{ $user->followersCount() }}</p>

                        @if (Auth::check() && auth()->user()->id == $user->id)
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary">Editar Perfil</a>
                        @elseif(Auth::check())
                            {{-- Botón de Seguir/Dejar de seguir --}}
                            @if (Auth::user()->isFollowing($user))
                                <button class="btn btn-danger btn-unfollow" data-user-id="{{ $user->id }}">Dejar de
                                    seguir</button>
                            @else
                                <button class="btn btn-primary btn-follow"
                                    data-user-id="{{ $user->id }}">Seguir</button>
                            @endif
                        @endif

                        <hr>

                        <h3>Posts de {{ $user->name }}:</h3>

                        <div id="post-list">
                            @forelse ($posts as $post)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $post->user->name }}
                                            ({{ $post->user->username ?? 'N/A' }})
                                        </h5>
                                        <p class="card-text">{{ $post->content }}</p>
                                        <p class="card-text"><small
                                                class="text-muted updated-at">{{ $post->created_at->diffForHumans() }}</small>
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p>Este usuario aún no ha publicado ningún post.</p>
                            @endforelse
                        </div>

                        {{-- Paginación --}}
                        <div class="d-flex justify-content-center">
                            {{ $posts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Seguir a un usuario
            $('.btn-follow').click(function() {
                var userId = $(this).data('user-id');
                var button = $(this);

                $.ajax({
                    url: '/users/' + userId + '/follow',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        button.removeClass('btn-primary').addClass('btn-danger').text(
                            'Dejar de seguir');
                        location.reload();
                    },
                    error: function(error) {
                        console.error('Error al seguir al usuario:', error);
                        alert('Ocurrió un error al seguir al usuario.');
                    }
                });
            });

            // Dejar de seguir a un usuario
            $('.btn-unfollow').click(function() {
                var userId = $(this).data('user-id');
                var button = $(this);

                $.ajax({
                    url: '/users/' + userId + '/unfollow',
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        button.removeClass('btn-danger').addClass('btn-primary').text('Seguir');
                        location.reload();
                    },
                    error: function(error) {
                        console.error('Error al dejar de seguir al usuario:', error);
                        alert('Ocurrió un error al dejar de seguir al usuario.');
                    }
                });
            });
        });
    </script>
@endsection
