@extends('layouts.backend')

@section('meta')
    <meta name="order-id" content="{{ $order->id }}">
@endsection

@section('js')
    @vite(['resources/js/pages/order/show.js'])
@endsection

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="bg-body-light border-bottom mb-4">
            <div class="content py-1 text-center">
                <nav class="breadcrumb bg-body-light py-2 mb-0">
                    <a class="breadcrumb-item" href="{{ route('dashboard.index') }}">Painel</a>
                    <a class="breadcrumb-item" href="#">Pedidos</a>
                    <span class="breadcrumb-item active">{{ $order->number }}</span>
                </nav>
            </div>
        </div>

        <div class="d-flex flex-row mb-3">
            <div class="me-2">
                <a href="{{ route('dashboard.order.index') }}" class="btn bg-flat">
                    <i class="fa fa-fw fa-chevron-left text-white me-1"></i>
                    <span class="d-none d-sm-inline text-white">Voltar</span>
                </a>
            </div>
            <div class="me-2">
                <button type="button" class="btn bg-gray-dark">
                    <i class="fa fa-fw fa-pen text-white me-1"></i>
                    <span class="d-none d-sm-inline text-white">Editar</span>
                </button>
            </div>
        </div>
        <div class="block block-rounded">
            <div class="block-content block-content-full">
                <fieldset class="border px-2 pb-2 mb-2">
                    <legend class="float-none w-auto h5">Ações</legend>
                    <div class="row">
                        <div class="col-12 d-none">
                            <span class="fw-bold">Etapa:</span>
                            <div class="dropdown">
                                <button type="button" class="btn btn-success dropdown-toggle text-white w-100"
                                    id="arrived-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    @if ($order->arrived)
                                        Chegou
                                    @else
                                        Não Chegou
                                    @endif
                                </button>
                                <div class="dropdown-menu fs-sm" aria-labelledby="dropdown-default-primary">
                                    <a class="dropdown-item arrived" data-value="1" href="javascript:void(0)">Chegou</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item arrived" data-value="0" href="javascript:void(0)">Não Chegou</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <span class="fw-bold">Etapa:</span>
                            <div class="dropdown">
                                <button type="button" class="btn btn-success dropdown-toggle text-white w-100"
                                    id="step-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-fw fa-chevron-down text-white me-1"></i>
                                    {{ get_step($order->step) }}
                                </button>
                                <div class="dropdown-menu fs-sm" aria-labelledby="dropdown-default-primary">
                                    @foreach ($step as $key => $value)
                                        <a class="dropdown-item step" data-value="{{ $key }}"
                                            href="javascript:void(0)">{{ $value }}</a>
                                        <div class="dropdown-divider"></div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <span class="fw-bold">Arte Finalista:</span>
                            <div class="dropdown">
                                <button type="button" class="btn btn-info dropdown-toggle text-white w-100"
                                    id="designer-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    @if ($order->designer)
                                        <i class="fa fa-fw fa-chevron-down text-white me-1"></i>
                                        <span class="d-sm-inline">{{ $order->designer->name }}</span>
                                    @else
                                        Nenhum
                                    @endif

                                </button>
                                <div class="dropdown-menu fs-sm" aria-labelledby="dropdown-default-primary">
                                    @foreach ($users as $user)
                                        <a class="dropdown-item designer" data-value="{{ $user->name }}"
                                            href="javascript:void(0)">{{ $user->name }}</a>
                                        <div class="dropdown-divider"></div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title fw-bold">
                    Informações do Cliente
                </h3>
            </div>
            <div class="block-content">
                <div class="row items-push">
                    <div class="col-md-12">
                        <div class="block block-rounded h-100 mb-0">
                            <div class="block-content fs-md">
                                <div class="fw-bold mb-1">{{ $order->customer->name }}</div>
                                <address>
                                    <i class="fa fa-phone me-1"></i>
                                    {{ $order->customer->phone ? $order->customer->phone : 'Não cadastrado' }}<br>
                                    <i class="far fa-envelope me-1"></i> <a
                                        href="javascript:void(0)">{{ $order->customer->email ? $order->customer->email : 'Não cadastrado' }}</a>
                                </address>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title fw-bold">
                    Produtos
                </h3>
            </div>
            <div class="block-content block-content-full p-0 px-2 pb-2">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-responsive list-latest"
                        id="table-products">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th class="text-center">Quantidade</th>
                                <th>Estoque</th>
                                <th>Fornecedor</th>
                                <th>Link</th>
                                <th>Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title fw-bold">
                    Arquivos de Design
                </h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row items-push">
                    <div class="col-md-12">
                        <div class="block block-rounded mb-0">
                            <div class="block-content fs-md">
                                <div id="error-msg"></div>
                                <div class="row mb-4">
                                    @foreach ($order->orderProducts as $item)
                                        @php
                                            $colSize = max(12 / $order->orderProducts->count(), 3);
                                        @endphp
                                        <div class="col-12 col-md-{{ $colSize }}">
                                            <div class="card mb-3">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">{{ $item->product->name }}</h5>
                                                    @if ($item->preview)
                                                        <img src="{{ $item->preview }}" alt=""
                                                            class="img-fluid">
                                                    @else
                                                        <img src="{{ asset('media/photos/noimage.jpg') }}"
                                                            alt="Pré-visualização" class="img-fluid" />
                                                    @endif
                                                    @if (is_null($item->design_file))
                                                        <form
                                                            action="{{ route('dashboard.order.upload.design', $order->id) }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="mb-3">
                                                                <input type="file" name="design_file"
                                                                    class="form-control">
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">Upload</button>
                                                        </form>
                                                    @else
                                                        <a href="#" class="btn btn-success">Download Design File</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="productsModal" data-bs-backdrop='static' tabindex="-1" role="dialog"
        aria-labelledby="detalhesModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Produtos do Pedido</h3>
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
    <!-- END Page Content -->
@endsection
