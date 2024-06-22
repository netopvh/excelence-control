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
                        <div class="col-12 col-md-3">
                            <span class="fw-bold">Status:</span>
                            <div class="dropdown">
                                <button type="button" class="btn btn-warning dropdown-toggle w-100" id="status-dropdown"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-fw fa-chevron-down text-white me-1"></i>
                                    <span class="d-sm-inline">{{ status_type($order->status) }}</span>
                                </button>
                                <div class="dropdown-menu fs-sm" aria-labelledby="dropdown-default-primary">
                                    @foreach ($status as $key => $value)
                                        <a class="dropdown-item status" data-value="{{ $key }}"
                                            href="javascript:void(0)">{{ $value }}</a>
                                        <div class="dropdown-divider"></div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
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
                    Arquivos
                </h3>
            </div>
            <div class="block-content block-content-full">
                <div class="row items-push">
                    <div class="col-md-12">
                        <div class="block block-rounded h-100 mb-0">
                            <div class="block-content fs-md">
                                <div id="error-msg"></div>
                                <div class="row mb-4">
                                    <div class="col-12 col-md-6">
                                        <div class="fw-bold">
                                            Pré-visualização
                                        </div>
                                        <div id="preview-container">
                                            @if ($order->preview)
                                                <img src="{{ asset('preview/' . $order->preview) }}"
                                                    alt="Pré-visualização" class="img-fluid mt-4 gap-5" />
                                            @else
                                                <img src="{{ asset('media/photos/noimage.jpg') }}" alt="Pré-visualização"
                                                    class="img-fluid" />
                                            @endif
                                        </div>
                                        <div id="loading-preview" class="mx-4 mt-4 d-none">
                                            <svg width="32" height="32" viewBox="0 0 32 32"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <style>
                                                    .spinner_DupU {
                                                        animation: spinner_sM3D 1.2s infinite
                                                    }

                                                    .spinner_GWtZ {
                                                        animation-delay: .1s
                                                    }

                                                    .spinner_dwN6 {
                                                        animation-delay: .2s
                                                    }

                                                    .spinner_46QP {
                                                        animation-delay: .3s
                                                    }

                                                    .spinner_PD82 {
                                                        animation-delay: .4s
                                                    }

                                                    .spinner_eUgh {
                                                        animation-delay: .5s
                                                    }

                                                    .spinner_eUaP {
                                                        animation-delay: .6s
                                                    }

                                                    .spinner_j38H {
                                                        animation-delay: .7s
                                                    }

                                                    .spinner_tVmX {
                                                        animation-delay: .8s
                                                    }

                                                    .spinner_DQhX {
                                                        animation-delay: .9s
                                                    }

                                                    .spinner_GIL4 {
                                                        animation-delay: 1s
                                                    }

                                                    .spinner_n0Yb {
                                                        animation-delay: 1.1s
                                                    }

                                                    @keyframes spinner_sM3D {

                                                        0%,
                                                        50% {
                                                            animation-timing-function: cubic-bezier(0, 1, 0, 1);
                                                            r: 0
                                                        }

                                                        10% {
                                                            animation-timing-function: cubic-bezier(.53, 0, .61, .73);
                                                            r: 2px
                                                        }
                                                    }
                                                </style>
                                                <circle class="spinner_DupU" cx="12" cy="3" r="0" />
                                                <circle class="spinner_DupU spinner_GWtZ" cx="16.50" cy="4.21"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_n0Yb" cx="7.50" cy="4.21"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_dwN6" cx="19.79" cy="7.50"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_GIL4" cx="4.21" cy="7.50"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_46QP" cx="21.00" cy="12.00"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_DQhX" cx="3.00" cy="12.00"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_PD82" cx="19.79" cy="16.50"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_tVmX" cx="4.21" cy="16.50"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_eUgh" cx="16.50" cy="19.79"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_j38H" cx="7.50" cy="19.79"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_eUaP" cx="12" cy="21"
                                                    r="0" />
                                            </svg>
                                        </div>
                                        @if (!$order->preview)
                                            <form action="{{ route('dashboard.order.upload.preview', $order->id) }}"
                                                id="upload-preview" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row mb-4">
                                                    <div class="col-md-12 col-xl-12 fw-bold">
                                                        Nenhum arquivo foi enviado
                                                    </div>
                                                </div>
                                                <div class="row d-block">
                                                    <div class="col-lg-12 col-xl-12 overflow-hidden">
                                                        <div class="mb-4">
                                                            <input class="form-control" type="file" name="preview"
                                                                id="example-file-input">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-xl-12 overflow-hidden">
                                                        <button type="submit" class="btn btn-primary">Importar</button>
                                                    </div>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="fw-bold">
                                            Arquivo de Design PDF
                                        </div>
                                        <div id="design-container">
                                            @if ($order->design_file)
                                                <div class="mt-5">
                                                    <a href="{{ asset('design/' . $order->design_file) }}"
                                                        target="_blank" class="btn btn-primary">
                                                        <i class="fa fa-fw fa-download text-white me-1"></i>
                                                        <span class="d-none d-sm-inline">Baixar Arquivo</span>
                                                    </a>
                                                </div>
                                            @else
                                                <img src="{{ asset('media/photos/noimage.jpg') }}" alt="Pré-visualização"
                                                    class="img-fluid" />
                                            @endif
                                        </div>
                                        <div id="loading-design" class="mx-4 mt-4 d-none">
                                            <svg width="32" height="32" viewBox="0 0 32 32"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <style>
                                                    .spinner_DupU {
                                                        animation: spinner_sM3D 1.2s infinite
                                                    }

                                                    .spinner_GWtZ {
                                                        animation-delay: .1s
                                                    }

                                                    .spinner_dwN6 {
                                                        animation-delay: .2s
                                                    }

                                                    .spinner_46QP {
                                                        animation-delay: .3s
                                                    }

                                                    .spinner_PD82 {
                                                        animation-delay: .4s
                                                    }

                                                    .spinner_eUgh {
                                                        animation-delay: .5s
                                                    }

                                                    .spinner_eUaP {
                                                        animation-delay: .6s
                                                    }

                                                    .spinner_j38H {
                                                        animation-delay: .7s
                                                    }

                                                    .spinner_tVmX {
                                                        animation-delay: .8s
                                                    }

                                                    .spinner_DQhX {
                                                        animation-delay: .9s
                                                    }

                                                    .spinner_GIL4 {
                                                        animation-delay: 1s
                                                    }

                                                    .spinner_n0Yb {
                                                        animation-delay: 1.1s
                                                    }

                                                    @keyframes spinner_sM3D {

                                                        0%,
                                                        50% {
                                                            animation-timing-function: cubic-bezier(0, 1, 0, 1);
                                                            r: 0
                                                        }

                                                        10% {
                                                            animation-timing-function: cubic-bezier(.53, 0, .61, .73);
                                                            r: 2px
                                                        }
                                                    }
                                                </style>
                                                <circle class="spinner_DupU" cx="12" cy="3" r="0" />
                                                <circle class="spinner_DupU spinner_GWtZ" cx="16.50" cy="4.21"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_n0Yb" cx="7.50" cy="4.21"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_dwN6" cx="19.79" cy="7.50"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_GIL4" cx="4.21" cy="7.50"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_46QP" cx="21.00" cy="12.00"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_DQhX" cx="3.00" cy="12.00"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_PD82" cx="19.79" cy="16.50"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_tVmX" cx="4.21" cy="16.50"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_eUgh" cx="16.50" cy="19.79"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_j38H" cx="7.50" cy="19.79"
                                                    r="0" />
                                                <circle class="spinner_DupU spinner_eUaP" cx="12" cy="21"
                                                    r="0" />
                                            </svg>
                                        </div>
                                        @if (!$order->design_file)
                                            <form action="{{ route('dashboard.order.upload.design', $order->id) }}"
                                                id="upload-design" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row mb-4">
                                                    <div class="col-md-12 col-xl-12 fw-bold">
                                                        Nenhum arquivo foi enviado
                                                    </div>
                                                </div>
                                                <div class="row d-block">
                                                    <div class="col-lg-12 col-xl-12 overflow-hidden">
                                                        <div class="mb-4">
                                                            <input class="form-control" type="file" name="design"
                                                                id="example-file-input">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-xl-12 overflow-hidden">
                                                        <button type="submit" class="btn btn-primary">Importar</button>
                                                    </div>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Page Content -->
@endsection
