@extends('layouts.backend')

@section('css')
@endsection

@section('js')
    @vite(['resources/js/pages/production/index.js'])
@endsection

@section('content')
    <div class="content">
        <div class="bg-body-light border-bottom mb-4">
            <div class="content py-1 text-center">
                <nav class="breadcrumb bg-body-light py-2 mb-0">
                    <a class="breadcrumb-item" href="{{ route('dashboard.index') }}">Painel</a>
                    <a class="breadcrumb-item" href="#">Produção</a>
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
                                Pedidos na Produção
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
                        <div class="row">
                            <fieldset class="border px-2 pb-2 mb-2">
                                <legend class="float-none w-auto px-4 h5">Filtros</legend>
                                <div class="row">
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="filterByStep" class="fw-bold mb-1">Filtrar por Etapa</label>
                                            <select class="form-control" id="filterByStep">
                                                <option value="all">Todos</option>
                                                <option value="in_design">Design e Arte</option>
                                                <option value="in_production" selected>Produção</option>
                                                <option value="finished">Concluídos</option>
                                                <option value="shipping">Para Entrega</option>
                                                <option value="pickup">Para Retirada</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
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
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label for="filterType" class="fw-bold mb-1">Tipo de Data</label>
                                            <select class="form-control" id="filterType">
                                                <option value="">Todos</option>
                                                <option value="delivery_date">Data de Entrega</option>
                                                <option value="date">Data do Pedido</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
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
                            <table class="table table-bordered table-striped table-vcenter list-production">
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal" id="productionProductModal" data-bs-backdrop='static' tabindex="-1" role="dialog"
        aria-labelledby="productionProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title"></h3>
                    </div>
                    <div class="block-content">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
