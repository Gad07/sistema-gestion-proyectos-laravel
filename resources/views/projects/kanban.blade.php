@extends('layouts.app')

@section('title', 'Kanban - ' . $project->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 mb-0">{{ $project->name }} - Vista Kanban</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Proyectos</a></li>
                <li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></li>
                <li class="breadcrumb-item active">Kanban</li>
            </ol>
        </nav>
    </div>
    <div>
        <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#newTaskModal">
            Nueva Tarea
        </button>
        <a href="{{ route('projects.gantt', $project) }}" class="btn btn-info me-2">
            Vista Gantt
        </a>
        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
            Volver al Proyecto
        </a>
    </div>
</div>

<!-- Estadísticas rápidas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-secondary text-white">
            <div class="card-body text-center">
                <h4>{{ $kanbanColumns['To Do']->count() }}</h4>
                <small>Por Hacer</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h4>{{ $kanbanColumns['In Progress']->count() }}</h4>
                <small>En Progreso</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4>{{ $kanbanColumns['Review']->count() }}</h4>
                <small>En Revisión</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h4>{{ $kanbanColumns['Done']->count() }}</h4>
                <small>Completadas</small>
            </div>
        </div>
    </div>
</div>

<!-- Tablero Kanban -->
<div class="kanban-board">
    <div class="row">
        @foreach($kanbanColumns as $columnName => $tasks)
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="kanban-column" data-column="{{ $columnName }}">
                    <div class="kanban-header p-3 mb-3 rounded">
                        <h5 class="mb-0 text-center">
                            {{ $columnName }}
                            <span class="badge bg-light text-dark ms-2">{{ $tasks->count() }}</span>
                        </h5>
                    </div>
                    
                    <div class="kanban-tasks" data-column="{{ $columnName }}" style="min-height: 400px;">
                        @foreach($tasks as $task)
                            <div class="kanban-card card mb-3" data-task-id="{{ $task->id }}" draggable="true">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0">{{ $task->name }}</h6>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                ⋮
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" onclick="editTask({{ $task->id }})">Editar</a></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteTask({{ $task->id }})">Eliminar</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    @if($task->description)
                                        <p class="card-text small text-muted">{{ Str::limit($task->description, 80) }}</p>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge" style="background-color: {{ $task->priority_color }}">
                                            {{ $task->priority_text }}
                                        </span>
                                        @if($task->due_date)
                                            <small class="text-muted {{ $task->is_overdue ? 'text-danger' : '' }}">
                                                {{ $task->due_date->format('d/m') }}
                                            </small>
                                        @endif
                                    </div>
                                    
                                    @if($task->media->count() > 0)
                                        <div class="mt-2">
                                            <small class="text-muted">📎 {{ $task->media->count() }} archivo(s)</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        
                        <!-- Zona de drop -->
                        <div class="drop-zone text-center p-3 border border-dashed rounded" style="display: none;">
                            <small class="text-muted">Soltar tarea aquí</small>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Modal para nueva tarea -->
<div class="modal fade" id="newTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="newTaskForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="task_name" class="form-label">Nombre de la Tarea</label>
                        <input type="text" class="form-control" id="task_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="task_description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="task_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="task_priority" class="form-label">Prioridad</label>
                                <select class="form-select" id="task_priority" name="priority" required>
                                    <option value="low">Baja</option>
                                    <option value="medium" selected>Media</option>
                                    <option value="high">Alta</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="task_column" class="form-label">Columna</label>
                                <select class="form-select" id="task_column" name="kanban_column" required>
                                    <option value="To Do" selected>Por Hacer</option>
                                    <option value="In Progress">En Progreso</option>
                                    <option value="Review">En Revisión</option>
                                    <option value="Done">Completada</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="task_start_date" class="form-label">Fecha de Inicio</label>
                                <input type="date" class="form-control" id="task_start_date" name="start_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="task_due_date" class="form-label">Fecha Límite</label>
                                <input type="date" class="form-control" id="task_due_date" name="due_date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar tarea -->
<div class="modal fade" id="editTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTaskForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_task_id" name="task_id">
                    
                    <div class="mb-3">
                        <label for="edit_task_name" class="form-label">Nombre de la Tarea</label>
                        <input type="text" class="form-control" id="edit_task_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_task_description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="edit_task_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_task_priority" class="form-label">Prioridad</label>
                                <select class="form-select" id="edit_task_priority" name="priority" required>
                                    <option value="low">Baja</option>
                                    <option value="medium">Media</option>
                                    <option value="high">Alta</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_task_column" class="form-label">Columna</label>
                                <select class="form-select" id="edit_task_column" name="kanban_column" required>
                                    <option value="To Do">Por Hacer</option>
                                    <option value="In Progress">En Progreso</option>
                                    <option value="Review">En Revisión</option>
                                    <option value="Done">Completada</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_task_start_date" class="form-label">Fecha de Inicio</label>
                                <input type="date" class="form-control" id="edit_task_start_date" name="start_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_task_due_date" class="form-label">Fecha Límite</label>
                                <input type="date" class="form-control" id="edit_task_due_date" name="due_date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .kanban-board {
        overflow-x: auto;
    }
    
    .kanban-column {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        min-height: 500px;
    }
    
    .kanban-header {
        background-color: var(--white);
        border: 2px solid var(--gray);
    }
    
    .kanban-card {
        cursor: move;
        transition: all 0.2s ease;
        border-left: 4px solid var(--primary-red);
    }
    
    .kanban-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .kanban-card.dragging {
        opacity: 0.5;
        transform: rotate(5deg);
    }
    
    .kanban-tasks.drag-over {
        background-color: rgba(251, 0, 9, 0.1);
        border: 2px dashed var(--primary-red);
        border-radius: 8px;
    }
    
    .drop-zone {
        background-color: rgba(251, 0, 9, 0.05);
        border-color: var(--primary-red) !important;
    }
    
    .drop-zone.active {
        display: block !important;
        background-color: rgba(251, 0, 9, 0.1);
    }
    
    @media (max-width: 768px) {
        .kanban-column {
            margin-bottom: 20px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
let draggedTask = null;
let draggedFrom = null;

$(document).ready(function() {
    initializeKanban();
    
    // Crear nueva tarea
    $('#newTaskForm').on('submit', function(e) {
        e.preventDefault();
        createTask();
    });
    
    // Editar tarea
    $('#editTaskForm').on('submit', function(e) {
        e.preventDefault();
        updateTask();
    });
});

function initializeKanban() {
    // Hacer las tarjetas arrastrables
    $('.kanban-card').each(function() {
        this.addEventListener('dragstart', handleDragStart);
        this.addEventListener('dragend', handleDragEnd);
    });
    
    // Configurar zonas de drop
    $('.kanban-tasks').each(function() {
        this.addEventListener('dragover', handleDragOver);
        this.addEventListener('drop', handleDrop);
        this.addEventListener('dragenter', handleDragEnter);
        this.addEventListener('dragleave', handleDragLeave);
    });
}

function handleDragStart(e) {
    draggedTask = this;
    draggedFrom = this.closest('.kanban-tasks').dataset.column;
    this.classList.add('dragging');
    
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', this.outerHTML);
}

function handleDragEnd(e) {
    this.classList.remove('dragging');
    
    // Ocultar todas las zonas de drop
    $('.drop-zone').hide();
    $('.kanban-tasks').removeClass('drag-over');
}

function handleDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    
    e.dataTransfer.dropEffect = 'move';
    return false;
}

function handleDragEnter(e) {
    this.classList.add('drag-over');
    $(this).find('.drop-zone').addClass('active');
}

function handleDragLeave(e) {
    // Solo remover si realmente salimos del contenedor
    if (!this.contains(e.relatedTarget)) {
        this.classList.remove('drag-over');
        $(this).find('.drop-zone').removeClass('active');
    }
}

function handleDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    
    const targetColumn = this.dataset.column;
    const taskId = draggedTask.dataset.taskId;
    
    if (draggedFrom !== targetColumn) {
        // Mover la tarea visualmente
        this.appendChild(draggedTask);
        
        // Actualizar en el servidor
        updateTaskColumn(taskId, targetColumn);
    }
    
    this.classList.remove('drag-over');
    $(this).find('.drop-zone').removeClass('active');
    
    return false;
}

function updateTaskColumn(taskId, newColumn) {
    $.ajax({
        url: `/tasks/${taskId}/update-column`,
        method: 'POST',
        data: {
            kanban_column: newColumn,
            position: 0
        },
        success: function(response) {
            if (response.success) {
                showToast('Tarea movida exitosamente', 'success');
                // Actualizar contadores
                updateColumnCounts();
            } else {
                showToast('Error al mover tarea', 'error');
                // Revertir movimiento
                location.reload();
            }
        },
        error: function() {
            showToast('Error al mover tarea', 'error');
            location.reload();
        }
    });
}

function createTask() {
    const formData = new FormData($('#newTaskForm')[0]);
    
    $.ajax({
        url: '{{ route("projects.tasks.store", $project) }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                showToast('Tarea creada exitosamente', 'success');
                $('#newTaskModal').modal('hide');
                location.reload(); // Recargar para mostrar la nueva tarea
            } else {
                showToast('Error al crear tarea', 'error');
            }
        },
        error: function(xhr) {
            const errors = xhr.responseJSON?.errors;
            if (errors) {
                let errorMsg = 'Errores de validación:\n';
                Object.keys(errors).forEach(key => {
                    errorMsg += `- ${errors[key][0]}\n`;
                });
                alert(errorMsg);
            } else {
                showToast('Error al crear tarea', 'error');
            }
        }
    });
}

function editTask(taskId) {
    // Obtener datos de la tarea (podrías hacer una llamada AJAX aquí)
    // Por simplicidad, extraemos los datos del DOM
    const taskCard = $(`.kanban-card[data-task-id="${taskId}"]`);
    const taskName = taskCard.find('.card-title').text();
    
    $('#edit_task_id').val(taskId);
    $('#edit_task_name').val(taskName);
    
    $('#editTaskModal').modal('show');
}

function updateTask() {
    const taskId = $('#edit_task_id').val();
    const formData = new FormData($('#editTaskForm')[0]);
    
    $.ajax({
        url: `/projects/{{ $project->id }}/tasks/${taskId}`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                showToast('Tarea actualizada exitosamente', 'success');
                $('#editTaskModal').modal('hide');
                location.reload();
            } else {
                showToast('Error al actualizar tarea', 'error');
            }
        },
        error: function() {
            showToast('Error al actualizar tarea', 'error');
        }
    });
}

function deleteTask(taskId) {
    if (confirm('¿Estás seguro de que deseas eliminar esta tarea?')) {
        $.ajax({
            url: `/projects/{{ $project->id }}/tasks/${taskId}`,
            method: 'DELETE',
            success: function(response) {
                if (response.success) {
                    showToast('Tarea eliminada exitosamente', 'success');
                    $(`.kanban-card[data-task-id="${taskId}"]`).fadeOut(300, function() {
                        $(this).remove();
                        updateColumnCounts();
                    });
                } else {
                    showToast('Error al eliminar tarea', 'error');
                }
            },
            error: function() {
                showToast('Error al eliminar tarea', 'error');
            }
        });
    }
}

function updateColumnCounts() {
    $('.kanban-column').each(function() {
        const column = $(this);
        const columnName = column.data('column');
        const count = column.find('.kanban-card').length;
        column.find('.badge').text(count);
    });
}

function showToast(message, type) {
    // Crear toast dinámicamente
    const toastHtml = `
        <div class="toast show" role="alert">
            <div class="toast-header ${type === 'success' ? 'bg-success' : 'bg-danger'} text-white">
                <strong class="me-auto">${type === 'success' ? 'Éxito' : 'Error'}</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    $('.toast-container').append(toastHtml);
    
    // Auto-hide después de 3 segundos
    setTimeout(function() {
        $('.toast').last().toast('hide');
    }, 3000);
}
</script>
@endpush
