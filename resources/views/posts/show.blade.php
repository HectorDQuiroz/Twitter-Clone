@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $post->user->name }} ({{ $post->user->username ?? 'N/A' }})</div>

                    <div class="card-body">
                        <p class="card-text">{{ $post->content }}</p>
                        <p class="card-text"><small
                                class="text-muted updated-at">{{ $post->created_at->diffForHumans() }}</small></p>

                        <hr>

                        <h4>Comentarios</h4>

                        <div id="comments-list">
                            @forelse ($post->comments as $comment)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <p class="card-text"><strong>{{ $comment->user->name }}:</strong>
                                            {{ $comment->content }}</p>
                                        <p class="card-text"><small
                                                class="text-muted">{{ $comment->created_at->diffForHumans() }}</small></p>
                                    </div>
                                </div>
                            @empty
                                <p>No hay comentarios en este post.</p>
                            @endforelse
                        </div>

                        @if (Auth::check())
                            <form id="comment-form" action="{{ route('comments.store', $post) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="content" class="form-label">Añadir comentario:</label>
                                    <textarea name="content" id="content" class="form-control" rows="3" required></textarea>
                                    <div id="comment-error" class="alert alert-danger mt-2" style="display: none;"></div>
                                </div>
                                <button type="submit" class="btn btn-primary">Comentar</button>
                            </form>
                        @else
                            <p><a href="{{ route('login') }}">Inicia sesión</a> para comentar.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
