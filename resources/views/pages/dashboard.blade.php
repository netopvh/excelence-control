@extends('layouts.backend')

@section('js')
    <script src="{{ asset('js/lib/jquery.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.canvasjs.com/jquery.canvasjs.min.js"></script>
    @vite(['resources/js/pages/dashboard.js'])
@endsection

@section('css')
    <style>
        .canvasjs-chart-credit {
            display: none !important;
        }
    </style>
@endsection

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-12 col-xl-3">
                <a class="block block-rounded block-link-rotate text-end"
                    href="{{ route('dashboard.order.index', ['status' => 'approved']) }}">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <i class="fa fa-thumbs-up fa-2x text-success"></i>
                        </div>
                        <div class="text-end">
                            <div class="fs-3 fw-semibold" id="container-approved"></div>
                            <div class="fs-sm fw-semibold text-uppercase text-muted">Aprovados</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-xl-3">
                <a class="block block-rounded block-link-rotate text-end"
                    href="{{ route('dashboard.order.index', ['status' => 'waiting_approval']) }}">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <i class="fa fa-hourglass-half fa-2x text-warning"></i>
                        </div>
                        <div class="text-end">
                            <div class="fs-3 fw-semibold" id="container-waiting"></div>
                            <div class="fs-sm fw-semibold text-uppercase text-muted">Aguard. Aprov.</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-xl-3">
                <a class="block block-rounded block-link-rotate text-end"
                    href="{{ route('dashboard.order.index', ['status' => 'waiting_design']) }}">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <i class="fa fa-id-badge fa-2x text-info"></i>
                        </div>
                        <div class="text-end">
                            <div class="fs-3 fw-semibold" id="container-design"></div>
                            <div class="fs-sm fw-semibold text-uppercase text-muted">Aguard. Arte</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-xl-3">
                <a class="block block-rounded block-link-rotate text-end" href="{{ route('dashboard.purchase.index') }}">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <i class="fa fa-cart-shopping fa-2x text-info"></i>
                        </div>
                        <div class="text-end">
                            <div class="fs-3 fw-semibold" id="container-to-buy"></div>
                            <div class="fs-sm fw-semibold text-uppercase text-muted">Itens para Compra</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-xl-3">
                <a class="block block-rounded block-link-rotate text-end"
                    href="{{ route('dashboard.order.index', ['type' => 'late']) }}">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center"
                        id="container-card-late">
                        <div class="d-none d-sm-block">
                            <i class="fa fa-file-excel fa-2x text-danger"></i>
                        </div>
                        <div class="text-end">
                            <div class="fs-3 fw-semibold" id="container-late"></div>
                            <div class="fs-sm fw-semibold text-uppercase text-muted">Pedidos Atrasados</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-xl-3">
                <a class="block block-rounded block-link-rotate text-end"
                    href="{{ route('dashboard.purchase.index', ['type' => 'late']) }}">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center"
                        id="container-card-product-late">
                        <div class="d-none d-sm-block">
                            <i class="fa fa-cart-arrow-down fa-2x text-danger"></i>
                        </div>
                        <div class="text-end">
                            <div class="fs-3 fw-semibold" id="container-late-products"></div>
                            <div class="fs-sm fw-semibold text-uppercase text-muted">Produtos Atrasados</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="block block-rounded">
                    <div class="block-header block-header-default d-flex justify-content-between flex-row">
                        <div>
                            <h3 class="block-title fw-bold">Pedidos por Etapa</h3>
                        </div>
                        <div>
                            <a href="{{ route('dashboard.order.kanban') }}" class="btn btn-sm btn-primary">Processos</a>
                        </div>
                    </div>
                    <div class="block-content block-content-full">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 20%">Artes e Design</th>
                                        <th class="text-center" style="width: 20%">Em Produção</th>
                                        <th class="text-center" style="width: 20%">Concluídos</th>
                                        <th class="text-center" style="width: 20%">Para Entrega</th>
                                        <th class="text-center" style="width: 20%">Para Retirada</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="bg-info text-center" style="cursor: pointer">
                                            <a href="{{ route('dashboard.order.index', ['step' => 'in_design']) }}"
                                                class="w-100">
                                                <span class="fw-bold text-white h3">
                                                    <i class="fa-solid fa-pen-ruler me-3"></i>
                                                    {{ $stepInDesign }}
                                                </span>
                                            </a>
                                        </td>
                                        <td class="bg-elegance text-center" style="cursor: pointer">
                                            <a href="{{ route('dashboard.order.index', ['step' => 'in_production']) }}">
                                                <span class="fw-bold text-white h3">
                                                    <i class="fa-solid fa-tarp-droplet me-3"></i>
                                                    {{ $stepInProduction }}
                                                </span>
                                            </a>
                                        </td>
                                        <td class="bg-success text-center" style="cursor: pointer">
                                            <a href="{{ route('dashboard.order.index', ['step' => 'finished']) }}">
                                                <span class="fw-bold text-white h3">
                                                    <i class="fa-solid fa-clipboard-check me-3"></i>
                                                    {{ $stepFinished }}
                                                </span>
                                            </a>
                                        </td>
                                        <td class="bg-earth text-center" style="cursor: pointer">
                                            <a href="{{ route('dashboard.order.index', ['step' => 'shipping']) }}">
                                                <span class="fw-bold text-white h3">
                                                    <i class="fa-solid fa-truck-ramp-box me-3"></i>
                                                    {{ $stepShipped }}
                                                </span>
                                            </a>
                                        </td>
                                        <td class="bg-gd-cherry text-center h3" style="cursor: pointer">
                                            <a href="{{ route('dashboard.order.index', ['step' => 'pickup']) }}">
                                                <span class="fw-bold text-white">
                                                    <i class="fa-solid fa-box me-3"></i>
                                                    {{ $stepPickup }}
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div id="chartContainer" style="height: 370px; width: 100%;"></div>
        </div>
    </div>
@endsection
