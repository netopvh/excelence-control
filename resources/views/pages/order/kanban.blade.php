@extends('layouts.backend')

@section('css')
    <style type="text/css">
        .kanban-board {
            display: flex;
            gap: 10px;
        }

        .kanban-column {
            flex: 1;
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            min-width: 250px;
        }

        .kanban-card {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            cursor: pointer;
        }
    </style>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const apiEndpoint = '{{ url('/') }}'; // Replace with your API endpoint

            // Fetch and render cards from the API
            fetch(`${apiEndpoint}/dashboard/order/list-kanban`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(card => {
                        const cardElement = createCardElement(card);
                        const columnCards = document.getElementById(`${card.status}-cards`);
                        if (columnCards) {
                            columnCards.appendChild(cardElement);
                        } else {
                            console.error(`Column with ID ${card.status}-cards not found.`);
                        }
                    });
                })
                .catch(error => console.error('Error fetching cards:', error));

            // Create a card element
            function createCardElement(card) {
                const cardDiv = document.createElement('div');
                cardDiv.className = 'kanban-card';
                cardDiv.id = `card-${card.id}`;
                cardDiv.draggable = true;
                cardDiv.innerHTML =
                    `<strong>N. do Pedido:</strong> #${card.number} <br> <strong>Cliente:</strong> ${card.customer.name}`;
                cardDiv.addEventListener('dragstart', handleDragStart);
                return cardDiv;
            }

            // Handle drag start
            function handleDragStart(event) {
                event.dataTransfer.setData('text/plain', event.target.id);
            }

            // Handle drop event
            function handleDrop(event) {
                event.preventDefault();
                const cardId = event.dataTransfer.getData('text/plain');
                const cardElement = document.getElementById(cardId);
                const newColumn = event.target.closest('.kanban-column');

                if (newColumn) {
                    const newStatus = newColumn.id.replace('-cards', '');
                    newColumn.appendChild(cardElement);

                    // Update card status via API
                    const cardData = {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        status: newStatus
                    };
                    fetch(`${apiEndpoint}/dashboard/order/list-kanban/${cardId.replace('card-', '')}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(cardData)
                        })
                        .then(response => {
                            if (!response.ok) {
                                console.error('Error updating card status:', response.statusText);
                            }
                        })
                        .catch(error => console.error('Error updating card status:', error));
                }
            }

            // Handle drag over
            function handleDragOver(event) {
                event.preventDefault();
            }

            // Attach event listeners to columns
            const columns = document.querySelectorAll('.kanban-column');
            columns.forEach(column => {
                column.addEventListener('drop', handleDrop);
                column.addEventListener('dragover', handleDragOver);
            });
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
                                <a href="{{ route('dashboard.order.index') }}"
                                    class="btn btn-primary text-white">Listagem</a>
                            </div>
                            <div>
                                <button class="btn btn-primary text-white" disabled>Kanban</button>
                            </div>
                        </div>
                    </div>
                    <div class="block-content block-content-full">
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <a href="{{ route('dashboard.order.create') }}" class="btn btn-primary text-white">Novo
                                    Pedido</a>
                            </div>
                        </div>
                        <div class="kanban-board" id="kanban-board">
                            <div class="kanban-column" id="new-orders">
                                <h5 class="text-center">Novos Pedidos</h5>
                                <div class="kanban-cards" id="new-orders-cards"></div>
                            </div>
                            <div class="kanban-column" id="in-production">
                                <h5 class="text-center">Em Produção</h5>
                                <div class="kanban-cards" id="in-production-cards"></div>
                            </div>
                            <div class="kanban-column" id="finished">
                                <h5 class="text-center">Finalizados</h5>
                                <div class="kanban-cards" id="finished-cards"></div>
                            </div>
                            <div class="kanban-column" id="shipped">
                                <h5 class="text-center">Enviados</h5>
                                <div class="kanban-cards" id="shipped-cards"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
