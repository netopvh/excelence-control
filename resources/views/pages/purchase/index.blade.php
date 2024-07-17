@extends('layouts.backend')

@section('css')
@endsection

@section('js')
    @vite(['resources/js/pages/purchase/index.js'])
@endsection

@section('content')
    <div class="content">
        <div class="bg-body-light border-bottom mb-4">
            <div class="content py-1 text-center">
                <nav class="breadcrumb bg-body-light py-2 mb-0">
                    <a class="breadcrumb-item" href="{{ route('dashboard.index') }}">Painel</a>
                    <a class="breadcrumb-item" href="#">Compras</a>
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
                                Pedidos para Compras
                            </h3>
                        </div>
                    </div>
                    <div class="block-content block-content-full">
                        <fieldset class="border px-2 pb-2 mb-2">
                            <legend class="float-none w-auto px-4 h5">Filtros</legend>
                            <div class="row">
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="filterByStatus" class="fw-bold mb-1">Filtrar por status</label>
                                        <select class="form-control" id="filterByStatus">
                                            <option value="all">Todos</option>
                                            <option value="Y">Comprados</option>
                                            <option value="N" selected>Não Comprados</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="filterByMonth" class="fw-bold mb-1">Filtrar por mês</label>
                                        <select class="form-control" id="filterByMonth">
                                            <option value="all">Todos</option>
                                            <option value="1">Janeiro</option>
                                            <option value="2">Fevereiro</option>
                                            <option value="3">Março</option>
                                            <option value="4">Abril</option>
                                            <option value="5">Maio</option>
                                            <option value="6">Junho</option>
                                            <option value="7">Julho</option>
                                            <option value="8">Agosto</option>
                                            <option value="9">Setembro</option>
                                            <option value="10">Outubro</option>
                                            <option value="11">Novembro</option>
                                            <option value="12">Dezembro</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 align-content-center mt-4">
                                    <button type="button" class="btn btn-primary btn-block text-white"
                                        id="btnCleanFilters">
                                        <i class="fa fa-fw fa-broom mr-1"></i> Limpar Filtros
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-vcenter list-latest">
                                <thead>
                                    <tr>
                                        <th style="width: 8%;"></th>
                                        <th style="width: 120px;">Data</th>
                                        <th style="width: 100px;">Pedido</th>
                                        <th>Cliente</th>
                                        <th style="width: 220px;">Entrega do Pedido</th>
                                        <th class="text-center" style="width: 20%;">Vendedor</th>
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
    <div class="modal" id="purchaseModal" data-bs-backdrop='static' tabindex="-1" role="dialog"
        aria-labelledby="purchaseModalLabel" aria-hidden="true">
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
                    <div class="block-content" style="background: #f2f2f2">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="purchaseProductModal" data-bs-backdrop='static' tabindex="-1" role="dialog"
        aria-labelledby="purchaseProductModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Alterar produtos do pedido</h3>
                    </div>
                    <div class="block-content">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
