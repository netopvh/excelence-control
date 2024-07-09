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
            min-width: 100px;
            width: 16%;
        }

        .kanban-card {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 14px 10px;
            margin-bottom: 10px;
            cursor: pointer;
        }
    </style>
@endsection

@section('js')
    @vite(['resources/js/pages/order/kanban.js'])
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
                        <div class="table-responsive">
                            <div class="kanban-board" id="kanban-board">
                                <div class="kanban-column" id="created">
                                    <h5 class="text-center text-white bg-default py-4 mb-2 rounded-1">Novos</h5>
                                    <div class="mb-3">
                                        <input type="text" class="form-control kanban-filter" data-column="created"
                                            data-filter="number" placeholder="Filtrar por número do pedido">
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" class="form-control kanban-filter" data-column="created"
                                            data-filter="customer" placeholder="Filtrar por cliente">
                                    </div>
                                    <div class="kanban-cards" id="created-cards"></div>
                                </div>
                                <div class="kanban-column" id="in_design">
                                    <h5 class="text-center text-white bg-info py-4 mb-2 rounded-1">Design</h5>
                                    <div class="mb-3">
                                        <input type="text" class="form-control kanban-filter" data-column="in_design"
                                            data-filter="number" placeholder="Filtrar por número do pedido">
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" class="form-control kanban-filter" data-column="in_design"
                                            data-filter="customer" placeholder="Filtrar por cliente">
                                    </div>
                                    <div class="kanban-cards" id="in_design-cards"></div>
                                </div>
                                <div class="kanban-column" id="in_production">
                                    <h5 class="text-center text-white bg-elegance py-4 mb-2 rounded-1">Produção</h5>
                                    <div class="mb-3">
                                        <input type="text" class="form-control kanban-filter" data-column="in_production"
                                            data-filter="number" placeholder="Filtrar por número do pedido">
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" class="form-control kanban-filter" data-column="in_production"
                                            data-filter="customer" placeholder="Filtrar por cliente">
                                    </div>
                                    <div class="kanban-cards" id="in_production-cards"></div>
                                </div>
                                <div class="kanban-column" id="finished">
                                    <h5 class="text-center text-white bg-success py-4 mb-2 rounded-1">Concluídos</h5>
                                    <div class="mb-3">
                                        <input type="text" class="form-control kanban-filter" data-column="finished"
                                            data-filter="number" placeholder="Filtrar por número do pedido">
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" class="form-control kanban-filter" data-column="finished"
                                            data-filter="customer" placeholder="Filtrar por cliente">
                                    </div>
                                    <div class="kanban-cards" id="finished-cards"></div>
                                </div>
                                <div class="kanban-column" id="shipping">
                                    <h5 class="text-center text-white bg-earth py-4 mb-2 rounded-1">Enviados</h5>
                                    <div class="mb-3">
                                        <input type="text" class="form-control kanban-filter" data-column="shipping"
                                            data-filter="number" placeholder="Filtrar por número do pedido">
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" class="form-control kanban-filter" data-column="shipping"
                                            data-filter="customer" placeholder="Filtrar por cliente">
                                    </div>
                                    <div class="kanban-cards" id="shipping-cards"></div>
                                </div>
                                <div class="kanban-column" id="pickup">
                                    <h5 class="text-center text-white bg-gd-cherry py-4 mb-2 rounded-1">Retirada</h5>
                                    <div class="mb-3">
                                        <input type="text" class="form-control kanban-filter" data-column="pickup"
                                            data-filter="number" placeholder="Filtrar por número do pedido">
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" class="form-control kanban-filter" data-column="pickup"
                                            data-filter="customer" placeholder="Filtrar por cliente">
                                    </div>
                                    <div class="kanban-cards" id="pickup-cards"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="orderInfoModal" data-bs-backdrop='static' tabindex="-1" role="dialog"
        aria-labelledby="orderInfoModal" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Detalhes do Pedido</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
