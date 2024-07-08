@extends('layouts.backend')

@section('css')
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
@endsection

@section('js')
    <!-- jQuery (required for DataTables plugin) -->
    <script src="{{ asset('js/lib/jquery.min.js') }}"></script>

    <!-- Page JS Plugins -->
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    @vite(['resources/js/pages/order/index.js'])
@endsection

@section('content')
    <div class="content">
        <div class="bg-body-light border-bottom mb-4">
            <div class="content py-1 text-center">
                <nav class="breadcrumb bg-body-light py-2 mb-0">
                    <a class="breadcrumb-item" href="{{ route('dashboard.index') }}">Painel</a>
                    <a class="breadcrumb-item" href="#">Pedidos</a>
                    <span class="breadcrumb-item active">Listagem</span>
                </nav>
            </div>
        </div>
        <div class="row items-push">
            <div class="col-md-12 col-xl-12">
                <div class="block block-rounded">
                    <div class="block-header block-header-default d-flex justify-content-between flex-row">
                        <div>
                            <h3 class="block-title">
                                Listagem de Pedidos
                            </h3>
                        </div>
                        <div class="d-flex flex-row gap-2">
                            <div>
                                <button class="btn btn-primary text-white" disabled>Listagem</button>
                            </div>
                            <div>
                                <a href="{{ route('dashboard.order.kanban') }}"
                                    class="btn btn-primary text-white">Kanban</a>
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
                        <div class="row">
                            <fieldset class="border px-2 pb-2 mb-2">
                                <legend class="float-none w-auto px-4 h5">Filtros</legend>
                                <div class="row">
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="filterByStatus" class="fw-bold mb-1">Filtrar por status</label>
                                            <select class="form-control" id="filterByStatus">
                                                <option value="all">Todos</option>
                                                <option value="waiting_approval">Aguardando Aprovação</option>
                                                <option value="waiting_design">Aguardando Arte</option>
                                                <option value="approved">Aprovado</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="filterByStep" class="fw-bold mb-1">Filtrar por Etapa</label>
                                            <select class="form-control" id="filterByStep">
                                                <option value="all">Todos</option>
                                                <option value="in_design">Design e Arte</option>
                                                <option value="in_production">Produção</option>
                                                <option value="finished">Concluídos</option>
                                                <option value="shipping">Para Entrega</option>
                                                <option value="pickup">Para Retirada</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <div class="form-group">
                                            <label for="filterByMonth" class="fw-bold mb-1">Filtrar por mês</label>
                                            <select class="form-control" id="filterByMonth">
                                                <option value="all">Todos</option>
                                                <option value="01">Janeiro</option>
                                                <option value="02">Fevereiro</option>
                                                <option value="03">Março</option>
                                                <option value="04">Abril</option>
                                                <option value="05">Maio</option>
                                                <option value="06">Junho</option>
                                                <option value="07">Julho</option>
                                                <option value="08">Agosto</option>
                                                <option value="09">Setembro</option>
                                                <option value="10">Outubro</option>
                                                <option value="11">Novembro</option>
                                                <option value="12">Dezembro</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <div class="form-group">
                                            <label for="filterType" class="fw-bold mb-1">Tipo de Data</label>
                                            <select class="form-control" id="filterType">
                                                <option value="">Todos</option>
                                                <option value="delivery_date">Data de Entrega</option>
                                                <option value="date">Data do Pedido</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <div class="form-group">
                                            <label for="filterDate" class="fw-bold mb-1">Data</label>
                                            <input type="date" class="form-control" id="filterDate"
                                                name="example-daterange1" placeholder="Data">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4 align-content-center mt-3">
                                        <button type="button" class="btn btn-primary btn-block text-white"
                                            id="btnCleanFilters">
                                            <i class="fa fa-fw fa-broom mr-1"></i> Limpar Filtros
                                        </button>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="table-responsive">
                            <table
                                class="table table-bordered table-striped table-vcenter js-dataTable-responsive list-latest">
                                <thead>
                                    <tr>
                                        <th style="width: 8%;"></th>
                                        <th style="width: 120px;">Data</th>
                                        <th style="width: 100px;">Pedido</th>
                                        <th>Cliente</th>
                                        <th class="text-center" style="width: 110px">Etapa</th>
                                        <th class="text-center" style="width: 20%;">Vendedor</th>
                                        <th class="text-center" style="width: 100px;">Status</th>
                                        <th style="width: 120px;">Entrega</th>
                                        <th class="text-center" style="width: 10%;">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal" id="detalhesModal" data-bs-backdrop='static' tabindex="-1" role="dialog"
        aria-labelledby="detalhesModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Detalhes do Pedido</h3>
                    </div>
                    <div class="block-content">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
