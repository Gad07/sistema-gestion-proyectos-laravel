@extends('layouts.app')

@section('title', 'Gantt - ' . $project->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 mb-0">{{ $project->name }} - Vista Gantt</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Proyectos</a></li>
                <li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></li>
                <li class="breadcrumb-item active">Gantt</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('projects.kanban', $project) }}" class="btn btn-success me-2">
            Vista Kanban
        </a>
        <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#newTaskModal">
            Nueva Tarea
        </button>
        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
            Volver al Proyecto
        </a>
    </div>
</div>

<!-- Información del proyecto -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5>Información del Proyecto</h5>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Fecha de Inicio:</strong> 
                        {{ $project->start_date ? $project->start_date->format('d/m/Y') : 'No definida' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Fecha de Fin:</strong> 
                        {{ $project->end_date ? $project->end_date->format('d/m/Y') : 'No definida' }}
                    </div>
                </div>
                <div class="mt-2">
                    <strong>Progreso:</strong>
                    <div class="progress mt-1" style="height: 8px;">
                        <div class="progress-bar" style="width: {{ $project->progress }}%"></div>
                    </div>
                    <small class="text-muted">{{ $project->progress }}% completado</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h5>Estadísticas</h5>
                <div class="row">
                    <div class="col-6">
                        <div class="h4 text-primary">{{ $tasks->count() }}</div>
                        <small class="text-muted">Tareas</small>
                    </div>
                    <div class="col-6">
                        <div class="h4 text-success">{{ $tasks->where('kanban_column', 'Done')->count() }}</div>
                        <small class="text-muted">Completadas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Controles del Gantt -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary active" onclick="changeView('month')">
                        Mensual
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="changeView('week')">
                        Semanal
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="changeView('day')">
                        Diario
                    </button>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="btn btn-outline-secondary" onclick="zoomIn()">
                    Acercar
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="zoomOut()">
                    Alejar
                </button>
                <button type="button" class="btn btn-outline-info" onclick="fitToScreen()">
                    Ajustar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico Gantt -->
<div class="card">
    <div class="card-body">
        @if($tasks->count() > 0)
            <div class="gantt-container">
                <div class="gantt-timeline" id="ganttChart">
                    <!-- El gráfico se generará aquí con JavaScript -->
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <div class="h1 text-muted">📊</div>
                <h4>No hay tareas con fechas definidas</h4>
                <p class="text-muted">Para ver el gráfico Gantt, las tareas deben tener fechas de inicio y fin.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTaskModal">
                    Crear Primera Tarea
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Lista de tareas -->
@if($tasks->count() > 0)
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Lista de Tareas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tarea</th>
                            <th>Estado</th>
                            <th>Prioridad</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Duración</th>
                            <th>Progreso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                            <tr>
                                <td>
                                    <strong>{{ $task->name }}</strong>
                                    @if($task->description)
                                        <br><small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $task->kanban_column }}</span>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: {{ $task->priority_color }}">
                                        {{ $task->priority_text }}
                                    </span>
                                </td>
                                <td>
                                    {{ $task->start_date ? $task->start_date->format('d/m/Y') : '-' }}
                                </td>
                                <td>
                                    @if($task->due_date)
                                        <span class="{{ $task->is_overdue ? 'text-danger' : '' }}">
                                            {{ $task->due_date->format('d/m/Y') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($task->start_date && $task->due_date)
                                        {{ $task->start_date->diffInDays($task->due_date) + 1 }} días
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $progress = $task->kanban_column === 'Done' ? 100 : 
                                                   ($task->kanban_column === 'Review' ? 80 : 
                                                   ($task->kanban_column === 'In Progress' ? 50 : 0));
                                    @endphp
                                    <div class="progress" style="height: 8px; width: 80px;">
                                        <div class="progress-bar" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <small>{{ $progress }}%</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" onclick="editTask({{ $task->id }})">
                                            Editar
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" onclick="deleteTask({{ $task->id }})">
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

<!-- Modal para nueva tarea -->
<div class="modal fade" id="newTaskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="newTaskForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="task_name" class="form-label">Nombre de la Tarea</label>
                                <input type="text" class="form-control" id="task_name" name="name" required>
                            </div>
                        </div>
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
                    </div>
                    
                    <div class="mb-3">
                        <label for="task_description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="task_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="task_start_date" class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="task_start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="task_due_date" class="form-label">Fecha de Fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="task_due_date" name="due_date" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="task_column" class="form-label">Estado Inicial</label>
                        <select class="form-select" id="task_column" name="kanban_column" required>
                            <option value="To Do" selected>Por Hacer</option>
                            <option value="In Progress">En Progreso</option>
                            <option value="Review">En Revisión</option>
                            <option value="Done">Completada</option>
                        </select>
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
@endsection

@push('styles')
<style>
    .gantt-container {
        overflow-x: auto;
        overflow-y: hidden;
    }
    
    .gantt-timeline {
        min-width: 800px;
        background-color: var(--white);
        border: 1px solid var(--gray);
        border-radius: 8px;
        padding: 20px;
    }
    
    .gantt-header {
        display: flex;
        border-bottom: 2px solid var(--gray);
        padding-bottom: 10px;
        margin-bottom: 20px;
        font-weight: 600;
    }
    
    .gantt-task-name {
        width: 200px;
        flex-shrink: 0;
        padding-right: 20px;
    }
    
    .gantt-timeline-header {
        flex: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .gantt-row {
        display: flex;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
        min-height: 50px;
    }
    
    .gantt-task-info {
        width: 200px;
        flex-shrink: 0;
        padding-right: 20px;
    }
    
    .gantt-timeline-row {
        flex: 1;
        position: relative;
        height: 30px;
        background-color: #f8f9fa;
        border-radius: 4px;
    }
    
    .gantt-bar {
        position: absolute;
        height: 100%;
        background-color: var(--primary-red);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        font-weight: 500;
        min-width: 20px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .gantt-bar:hover {
        background-color: #d40008;
        transform: scaleY(1.1);
    }
    
    .gantt-bar.priority-high {
        background-color: var(--primary-red);
    }
    
    .gantt-bar.priority-medium {
        background-color: #FFA500;
    }
    
    .gantt-bar.priority-low {
        background-color: #28A745;
    }
    
    .gantt-bar.completed {
        background-color: #6C757D;
    }
    
    .gantt-today-line {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: var(--primary-red);
        z-index: 10;
    }
    
    .gantt-date-labels {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 12px;
        color: #666;
    }
    
    @media (max-width: 768px) {
        .gantt-task-name,
        .gantt-task-info {
            width: 150px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
let ganttData = @json($tasks);
let startDate = new Date('{{ $startDate->format('Y-m-d') }}');
let endDate = new Date('{{ $endDate->format('Y-m-d') }}');
let currentView = 'month';

$(document).ready(function() {
    if (ganttData.length > 0) {
        renderGanttChart();
    }
    
    // Crear nueva tarea
    $('#newTaskForm').on('submit', function(e) {
        e.preventDefault();
        createTask();
    });
    
    // Validación de fechas
    $('#task_start_date, #task_due_date').on('change', function() {
        validateDates();
    });
});

function renderGanttChart() {
    const container = $('#ganttChart');
    container.empty();
    
    // Calcular duración total del proyecto
    const totalDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
    
    // Crear encabezado
    const header = $(`
        <div class="gantt-header">
            <div class="gantt-task-name">Tarea</div>
            <div class="gantt-timeline-header">
                <span>${startDate.toLocaleDateString()}</span>
                <span>Cronograma</span>
                <span>${endDate.toLocaleDateString()}</span>
            </div>
        </div>
    `);
    container.append(header);
    
    // Crear etiquetas de fechas
    const dateLabels = createDateLabels(startDate, endDate, totalDays);
    container.append(dateLabels);
    
    // Crear filas de tareas
    ganttData.forEach(task => {
        if (task.start_date && task.due_date) {
            const row = createTaskRow(task, startDate, totalDays);
            container.append(row);
        }
    });
    
    // Agregar línea de hoy
    addTodayLine(container, startDate, totalDays);
}

function createDateLabels(start, end, totalDays) {
    const labels = $('<div class="gantt-date-labels"></div>');
    const interval = Math.max(1, Math.floor(totalDays / 10));
    
    for (let i = 0; i <= totalDays; i += interval) {
        const date = new Date(start);
        date.setDate(date.getDate() + i);
        
        const label = $(`<span>${date.getDate()}/${date.getMonth() + 1}</span>`);
        labels.append(label);
    }
    
    return labels;
}

function createTaskRow(task, projectStart, totalDays) {
    const taskStart = new Date(task.start_date);
    const taskEnd = new Date(task.due_date);
    
    // Calcular posición y ancho de la barra
    const startOffset = Math.max(0, (taskStart - projectStart) / (1000 * 60 * 60 * 24));
    const duration = Math.max(1, (taskEnd - taskStart) / (1000 * 60 * 60 * 24) + 1);
    
    const leftPercent = (startOffset / totalDays) * 100;
    const widthPercent = (duration / totalDays) * 100;
    
    // Determinar clase de prioridad
    let priorityClass = 'priority-' + task.priority;
    if (task.kanban_column === 'Done') {
        priorityClass += ' completed';
    }
    
    const row = $(`
        <div class="gantt-row">
            <div class="gantt-task-info">
                <strong>${task.name}</strong>
                <br>
                <small class="text-muted">${task.priority_text}</small>
                <span class="badge bg-secondary ms-1">${task.kanban_column}</span>
            </div>
            <div class="gantt-timeline-row">
                <div class="gantt-bar ${priorityClass}" 
                     style="left: ${leftPercent}%; width: ${widthPercent}%;"
                     title="${task.name} (${taskStart.toLocaleDateString()} - ${taskEnd.toLocaleDateString()})"
                     onclick="editTask(${task.id})">
                    ${duration}d
                </div>
            </div>
        </div>
    `);
    
    return row;
}

function addTodayLine(container, projectStart, totalDays) {
    const today = new Date();
    const todayOffset = (today - projectStart) / (1000 * 60 * 60 * 24);
    
    if (todayOffset >= 0 && todayOffset <= totalDays) {
        const leftPercent = (todayOffset / totalDays) * 100;
        
        const todayLine = $(`
            <div class="gantt-today-line" style="left: calc(200px + 20px + ${leftPercent}%);" 
                 title="Hoy: ${today.toLocaleDateString()}">
            </div>
        `);
        
        container.css('position', 'relative');
        container.append(todayLine);
    }
}

function changeView(view) {
    currentView = view;
    $('.btn-group .btn').removeClass('active');
    $(`button[onclick="changeView('${view}')"]`).addClass('active');
    
    // Aquí podrías implementar diferentes vistas
    // Por simplicidad, mantenemos la misma vista
    renderGanttChart();
}

function zoomIn() {
    const container = $('.gantt-timeline');
    const currentWidth = container.width();
    container.css('min-width', currentWidth * 1.2 + 'px');
}

function zoomOut() {
    const container = $('.gantt-timeline');
    const currentWidth = container.width();
    container.css('min-width', Math.max(800, currentWidth * 0.8) + 'px');
}

function fitToScreen() {
    $('.gantt-timeline').css('min-width', '800px');
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
                location.reload();
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
    // Redirigir a la página de edición de tarea
    window.location.href = `/projects/{{ $project->id }}/tasks/${taskId}/edit`;
}

function deleteTask(taskId) {
    if (confirm('¿Estás seguro de que deseas eliminar esta tarea?')) {
        $.ajax({
            url: `/projects/{{ $project->id }}/tasks/${taskId}`,
            method: 'DELETE',
            success: function(response) {
                if (response.success) {
                    showToast('Tarea eliminada exitosamente', 'success');
                    location.reload();
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

function validateDates() {
    const startDate = $('#task_start_date').val();
    const endDate = $('#task_due_date').val();
    
    if (startDate && endDate && startDate > endDate) {
        alert('La fecha de fin debe ser posterior a la fecha de inicio.');
        $('#task_due_date').val('');
    }
}

function showToast(message, type) {
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
    
    setTimeout(function() {
        $('.toast').last().toast('hide');
    }, 3000);
}
</script>
@endpush
