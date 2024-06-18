@extends('layouts.backend')

@section('css')
    <style type="text/css">
        .kanban-column {
            margin-bottom: 20px;
        }

        .kanban-column h5 {
            margin-bottom: 0px !important;
        }

        .kanban-tasks {
            min-height: 200px;
            z-index: 9999;
        }

        .kanban-task {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
            margin: 5px 0;
            cursor: pointer;
        }
    </style>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {
            // Example JSON data
            var tasks = [{
                    "id": 1,
                    "description": "Pedido #15678",
                    "status": "todo"
                },
                {
                    "id": 2,
                    "description": "Pedido #15556",
                    "status": "in-progress"
                },
                {
                    "id": 3,
                    "description": "Pedido #15668",
                    "status": "done"
                }
            ];

            // Function to load tasks from the JSON data
            function loadTasks() {
                tasks.forEach(task => {
                    var taskElement = $('<div class="kanban-task"></div>').text(task.description);
                    taskElement.data('id', task.id);
                    $('[data-status="' + task.status + '"] .kanban-tasks').append(taskElement);
                });
                makeTasksDraggable();
            }

            // Function to add a new task
            function addTask(column) {
                var taskText = prompt("Enter task description:");
                if (taskText) {
                    var newTask = {
                        id: tasks.length + 1,
                        description: taskText,
                        status: column.data('status')
                    };
                    tasks.push(newTask);
                    var taskElement = $('<div class="kanban-task"></div>').text(newTask.description);
                    taskElement.data('id', newTask.id);
                    column.find('.kanban-tasks').append(taskElement);
                    makeTasksDraggable();
                }
            }

            // Add task button click event
            $('.add-task').click(function() {
                var column = $(this).closest('.kanban-column');
                addTask(column);
            });

            // Make tasks draggable
            function makeTasksDraggable() {
                $('.kanban-task').draggable({
                    revert: "invalid",
                    helper: "clone",
                    start: function(event, ui) {
                        $(this).css('opacity', '0.5');
                    },
                    stop: function(event, ui) {
                        $(this).css('opacity', '1');
                    }
                });

                $('.kanban-tasks').droppable({
                    accept: '.kanban-task',
                    drop: function(event, ui) {
                        var task = ui.helper.clone();
                        var newStatus = $(this).closest('.kanban-column').data('status');
                        var taskId = ui.helper.data('id');

                        // Update task status in the JSON data
                        tasks.forEach(t => {
                            if (t.id == taskId) {
                                t.status = newStatus;
                            }
                        });

                        ui.helper.remove();
                        $(this).append(task);
                        makeTasksDraggable();
                    }
                });
            }

            // Load tasks on page load
            loadTasks();
        });
    </script>
@endsection

@section('content')
    <div class="content">
        <div class="bg-body-light border-bottom mb-4">
            <div class="content py-1 text-center">
                <nav class="breadcrumb bg-body-light py-2 mb-0">
                    <a class="breadcrumb-item" href="{{ route('dashboard.index') }}">Painel</a>
                    <a class="breadcrumb-item" href="#">Pedidos</a>
                    <span class="breadcrumb-item active">Kanban</span>
                </nav>
            </div>
        </div>
        <div class="row items-push">
            <div class="col-md-12 col-xl-12">
                <div class="block block-rounded">
                    <div class="block-header block-header-default d-flex justify-content-between flex-row">
                        <div>
                            <h3 class="block-title">
                                Listagem Kanban
                            </h3>
                        </div>
                        <div class="d-flex flex-row gap-2">
                            <div>
                                <a href="{{ route('dashboard.order.index') }}" class="btn btn-primary">Listagem</a>
                            </div>
                            <div>
                                <button class="btn btn-primary" disabled>Kanban</button>
                            </div>
                        </div>
                    </div>
                    <div class="block-content block-content-full">
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <a href="{{ route('dashboard.order.create') }}" class="btn btn-primary">Novo Pedido</a>
                            </div>
                        </div>
                        <div class="row" id="kanban-board">
                            <div class="col-md-3 kanban-column" data-status="todo">
                                <div class="card">
                                    <div class="card-header bg-info">
                                        <h5 class="text-center text-uppercase text-white">Novos Pedidos</h5>
                                    </div>
                                    <div class="card-body kanban-tasks"></div>
                                </div>
                            </div>
                            <div class="col-md-3 kanban-column" data-status="in-progress">
                                <div class="card">
                                    <div class="card-header bg-earth">
                                        <h5 class="text-center text-uppercase text-white">Em Produção</h5>
                                    </div>
                                    <div class="card-body kanban-tasks"></div>
                                </div>
                            </div>
                            <div class="col-md-3 kanban-column" data-status="done">
                                <div class="card">
                                    <div class="card-header bg-elegance">
                                        <h5 class="text-center text-uppercase text-white">Finalizados</h5>
                                    </div>
                                    <div class="card-body kanban-tasks"></div>
                                </div>
                            </div>
                            <div class="col-md-3 kanban-column" data-status="send">
                                <div class="card">
                                    <div class="card-header bg-success">
                                        <h5 class="text-center text-uppercase text-white">Enviado</h5>
                                    </div>
                                    <div class="card-body kanban-tasks"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
