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
    <script src="{{ asset('js/plugins/datatables-buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-buttons-jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-buttons-pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-buttons-pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-buttons/buttons.print.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-buttons/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    @vite(['resources/js/pages/order.js'])
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
                                <button class="btn btn-primary" disabled>Listagem</button>
                            </div>
                            <div>
                                <a href="{{ route('dashboard.order.kanban') }}" class="btn btn-primary">Kanban</a>
                            </div>
                        </div>
                    </div>
                    <div class="block-content block-content-full">
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <a href="{{ route('dashboard.order.create') }}" class="btn btn-primary">Novo Pedido</a>
                            </div>
                        </div>
                        <fieldset class="border px-2 pb-2 mb-2">
                            <legend class="float-none w-auto px-4 h5">Filtros</legend>
                            <div class="row">
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-group">
                                        <label for="filterByStatus" class="fw-bold mb-1">Filtrar por status</label>
                                        <select class="form-control" id="filterByStatus">
                                            <option value="all">Todos</option>
                                            <option value="aguard. aprov">Aguardando Aprovação</option>
                                            <option value="aguard. arte">Aguardando Arte</option>
                                            <option value="aprovado">Aprovado</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xl-4">
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
                                <div class="col-md-6 col-xl-4">
                                    <div class="mb-4">
                                        <label for="filterByMonth" class="fw-bold mb-1">Filtrar por data</label>
                                        <div class="input-daterange input-group" data-date-format="dd/mm/yyyy"
                                            data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                            <input type="text" class="form-control" id="from"
                                                name="example-daterange1" placeholder="De" data-week-start="1"
                                                data-autoclose="true" data-today-highlight="true">
                                            <span class="input-group-text fw-semibold">
                                                <i class="fa fa-fw fa-arrow-right"></i>
                                            </span>
                                            <input type="text" class="form-control" id="to"
                                                name="example-daterange2" placeholder="Até" data-week-start="1"
                                                data-autoclose="true" data-today-highlight="true">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xl-2">
                                    <button type="button" class="btn btn-primary btn-block" id="btnCleanFilters">
                                        <i class="fa fa-fw fa-broom mr-1"></i> Limpar Filtros
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                        <div class="table-responsive">
                            <table
                                class="table table-bordered table-striped table-vcenter js-dataTable-responsive list-latest">
                                <thead>
                                    <tr>
                                        <th style="width: 8%;"></th>
                                        <th style="width: 120px;">Data</th>
                                        <th style="width: 100px;">Pedido</th>
                                        <th>Cliente</th>
                                        <th class="text-center" style="width: 110px">Status</th>
                                        <th class="text-center" style="width: 20%;">Vendedor</th>
                                        <th class="text-center" style="width: 100px;">Mercadoria</th>
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
@endsection
