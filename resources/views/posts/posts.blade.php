@forelse ($posts as $post)
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ $post->user->name }} ({{ $post->user->username ?? 'N/A' }})</h5>
            <p class="card-text">{{ $post->content }}</p>
            <p class="card-text"><small class="text-muted updated-at">{{ $post->created_at->diffForHumans() }}</small></p>

            {{-- Formulario de edición (oculto inicialmente) --}}
            <form class="edit-post-form" style="display: none;" method="POST" action="{{ route('posts.update', $post) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <textarea name="content" class="form-control" rows="3">{{ $post->content }}</textarea>
                    <div class="error-message alert alert-danger mt-2" style="display: none;"></div>
                </div>
                <button type="submit" class="btn btn-primary btn-update" data-post-id="{{ $post->id }}">Actualizar</button>
                <button type="button" class="btn btn-secondary btn-cancel">Cancelar</button>
            </form>

            {{-- Botones de acción --}}
            <div class="post-actions">
                <a href="{{ route('posts.show', $post) }}" class="btn btn-info btn-view" data-post-id="{{ $post->id }}">Ver</a>
                @if(auth()->user()->id == $post->user_id)
                <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning btn-edit" data-post-id="{{ $post->id }}">Editar</a>
                <button class="btn btn-danger btn-delete" data-post-id="{{ $post->id }}">Eliminar</button>
                @endif
                {{-- Botón de Like/Unlike --}}
                <button class="btn btn-like {{ $post->likes->contains('user_id', auth()->id()) ? 'btn-secondary' : 'btn-primary' }}" data-post-id="{{ $post->id }}">
                    <span class="like-text">{{ $post->likes->contains('user_id', auth()->id()) ? 'Unlike' : 'Like' }}</span>
                    (<span class="likes-count">{{ $post->likesCount() }}</span>)
                </button>
            </div>
        </div>
    </div>
@empty
    <p>No se encontraron posts que coincidan con la búsqueda.</p>
@endforelse
{{-- Paginación --}}
<div class="d-flex justify-content-center">
    {{ $posts->links() }}
</div>