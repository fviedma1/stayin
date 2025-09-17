@extends('layouts.master')

@section('content')
<div class="container">
    <div class="dashboard">
        <!-- Botón para crear nueva noticia -->
        <a href="{{ route('news.create') }}" class="btn btn--submit mb-5">
            <i class="fas fa-plus-circle"></i> Nova Notícia
        </a>

        <!-- Tabla de noticias -->
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Títol</th>
                    <th>Desc. Curta</th>
                    <th>Estat</th>
                    <th>Hotels</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($news as $item)
                <tr>
                    <td>{{ $item->title }}</td>
                    <td>{{ Str::limit($item->short_description, 30) }}</td>
                    <td>
                        @if($item->published)
                            <span class="status-badge success"><i class="fas fa-check"></i> Publicada</span>
                        @else
                            <span class="status-badge warning"><i class="fas fa-clock"></i> Pendent</span>
                        @endif
                    </td>
                    <td>
                        @if($item->hotels && $item->hotels->count() > 0)
                            @foreach($item->hotels as $hotel)
                                <span class="badge">{{ $hotel->name }}</span>
                            @endforeach
                        @else
                            <span class="badge badge-light">Cap hotel</span>
                        @endif
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('news.edit', $item->id) }}" class="btn-icon edit" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('news.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon delete" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .dashboard-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--background-main);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .dashboard-table th, 
    .dashboard-table td {
        padding: 15px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
        text-align: center;
    }
    
    .dashboard-table th {
        background: var(--primary-color);
        color: white;
        text-transform: uppercase;
        font-size: 0.9em;
        letter-spacing: 0.05em;
    }
    
    .status-badge {
        display: inline-block;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.85em;
        font-weight: 500;
    }
    
    .status-badge.success {
        background: var(--status-success);
        color: white;
    }
    
    .status-badge.warning {
        background: var(--status-warning);
        color: var(--text-primary);
    }
    
    .badge {
        display: inline-block;
        background: var(--primary-color-light);
        color: var(--primary-color);
        padding: 6px 12px;
        border-radius: 15px;
        margin: 3px;
        font-size: 0.85em;
        font-weight: 500;
    }
    
    .table-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }
    
    .btn-icon {
        padding: 8px 12px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
    }
    
    .btn-icon.edit {
        background: var(--status-info);
        color: white;
    }
    
    .btn-icon.delete {
        background: var(--status-error);
        color: white;
    }
    
    .btn-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }
    
    .mb-5 {
        margin-bottom: 3rem !important;
    }
    
</style>

<script>
    // Funció per mostrar/amagar formulari
    function toggleFormVisibility(showForm = true) {
        document.getElementById('newsTable').style.display = showForm ? 'none' : 'block';
        document.getElementById('newsForm').style.display = showForm ? 'block' : 'none';
        document.getElementById('toggleCreate').style.display = showForm ? 'none' : 'block';
    }

    // Funció per resetar el formulari
    function resetForm() {
        document.getElementById('newsFormElement').reset();
        document.getElementById('existingImages').innerHTML = '';
        document.getElementById('newsFormElement').action = "{{ route('news.store') }}";
        document.getElementById('formMethodContainer').innerHTML = '';
        document.getElementById('formAction').textContent = 'Desar';
        Array.from(document.getElementById('hotels').options).forEach(option => {
            option.selected = false;
        });
    }

    // Event listeners
    document.getElementById('toggleCreate').addEventListener('click', () => {
        resetForm();
        toggleFormVisibility(true);
    });

    document.getElementById('cancelForm').addEventListener('click', () => {
        toggleFormVisibility(false);
        resetForm();
    });

    // Funció per carregar dades per edició
    async function loadNewsData(id) {
        try {
            const response = await fetch(`/news/${id}/edit`);
            if (!response.ok) throw new Error('Error en la sol·licitud');
            
            const data = await response.json();
            
            // Omplir camps del formulari
            document.getElementById('title').value = data.title;
            document.getElementById('short_description').value = data.short_description;
            document.getElementById('long_description').value = data.long_description;
            document.getElementById('published').checked = data.published;
            
            // Seleccionar hotels
            const hotelsSelect = document.getElementById('hotels');
            Array.from(hotelsSelect.options).forEach(option => {
                option.selected = data.hotels.includes(parseInt(option.value));
            });
            
            // Mostrar imatges existents
            const existingImages = document.getElementById('existingImages');
            existingImages.innerHTML = data.images.map(url => 
                `<img src="${url}" class="thumbnail-form">`
            ).join('');
            
            // Configurar formulari per edició
            document.getElementById('newsFormElement').action = `/news/${id}`;
            document.getElementById('formMethodContainer').innerHTML = 
                '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('formAction').textContent = 'Actualitzar';
            
            toggleFormVisibility(true);
            
        } catch (error) {
            console.error('Error:', error.message);
        }
    }
</script>
@endsection