@extends('layouts.backend')

@section('css')
@endsection

@section('js')
    <script src="{{ asset('js/lib/jquery.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#upload-preview').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('dashboard.order.upload.preview', "$order->id") }}",
                    method: "POST",
                    data: new FormData(this),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $('#loading-preview').show();
                        $('#upload-preview').attr('disabled', 'disabled');
                    },
                    success: function(data) {
                        location.reload();
                    },
                    error: function(data) {
                        $('#loading-preview').hide();
                        $('#error-msg').html(
                            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            '<strong>' + data.responseJSON.message + '</strong>' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>'
                        );
                        // $('#upload-preview')[0].reset();
                        // $('#upload-preview').attr('disabled', false);
                    }
                })
            });

            $('#upload-design').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('dashboard.order.upload.design', "$order->id") }}",
                    method: "POST",
                    data: new FormData(this),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $('#loading-design').show();
                        $('#upload-design').attr('disabled', 'disabled');
                    },
                    success: function(data) {
                        location.reload();
                    },
                    error: function(data) {
                        $('#loading-design').hide();
                        $('#error-msg').html(
                            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            '<strong>' + data.responseJSON.message + '</strong>' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>'
                        );
                    }
                })
            });

            $('.dropdown-item.status').click(function() {
                var status = $(this).data('value');
                $.ajax({
                    url: "{{ route('dashboard.order.update.status', "$order->id") }}",
                    method: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        status: status
                    },
                    dataType: 'JSON',
                    beforeSend: function() {
                        $('#status-dropdown').html(
                            '<div class="spinner-border spinner-border-sm text-white" role="status">' +
                            '<span class="visually-hidden">Loading...</span>' +
                            '</div>'
                        );
                    },
                    success: function(data) {
                        location.reload();
                    },
                    error: function(data) {
                        console.log(data);
                        // $('#status-dropdown').html(
                        //     '<i class="fa fa-fw fa-chevron-down text-white me-1"></i>' +
                        //     '<span class="d-none d-sm-inline">Alterar Status</span>'
                        // );
                    }
                })
            });

            $('.dropdown-item.employee').click(function() {
                var status = $(this).data('value');
                $.ajax({
                    url: "{{ route('dashboard.order.update.employee', "$order->id") }}",
                    method: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        employee: status
                    },
                    dataType: 'JSON',
                    beforeSend: function() {
                        $('#employee-dropdown').html(
                            '<div class="spinner-border spinner-border-sm text-white" role="status">' +
                            '<span class="visually-hidden">Loading...</span>' +
                            '</div>'
                        );
                    },
                    success: function(data) {
                        location.reload();
                    },
                    error: function(data) {
                        console.log(data);
                        // $('#status-dropdown').html(
                        //     '<i class="fa fa-fw fa-chevron-down text-white me-1"></i>' +
                        //     '<span class="d-none d-sm-inline">Alterar Status</span>'
                        // );
                    }
                })
            });

            $('.dropdown-item.arrived').click(function() {
                var status = $(this).data('value');
                $.ajax({
                    url: "{{ route('dashboard.order.update.arrived', "$order->id") }}",
                    method: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        arrived: status
                    },
                    dataType: 'JSON',
                    beforeSend: function() {
                        $('#arrived-dropdown').html(
                            '<div class="spinner-border spinner-border-sm text-white" role="status">' +
                            '<span class="visually-hidden">Loading...</span>' +
                            '</div>'
                        );
                    },
                    success: function(data) {
                        location.reload();
                    },
                    error: function(data) {
                        console.log(data);
                        // $('#status-dropdown').html(
                        //     '<i class="fa fa-fw fa-chevron-down text-white me-1"></i>' +
                        //     '<span class="d-none d-sm-inline">Alterar Status</span>'
                        // );
                    }
                })
            });

            @if (session('success'))
                jQuery.notify({
                    icon: 'fa fa-fw fa-check',
                    message: '{{ session('success') }}'
                }, {
                    element: 'body',
                    type: 'success',
                    placement: {
                        from: 'top',
                        align: 'right'
                    },
                    allow_dismiss: true,
                    newest_on_top: true,
                    showProgressbar: false,
                    offset: 20,
                    spacing: 10,
                    z_index: 1033,
                    delay: 5000,
                    timer: 1000,
                    animate: {
                        enter: 'animated fadeIn',
                        exit: 'animated fadeOutDown'
                    },
                    template: `<div data-notify="container" class="col-11 col-sm-4 alert alert-{0} alert-dismissible" role="alert">
                        <p class="mb-0">
                            <span data-notify="icon"></span>
                            <span data-notify="title">{1}</span>
                            <span data-notify="message">{2}</span>
                        </p>
                        <div class="progress" data-notify="progressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar progress-bar-{0}" style="width: 0%;"></div>
                        </div>
                        <a href="{3}" target="{4}" data-notify="url"></a>
                        <a class="p-2 m-1 text-dark" href="javascript:void(0)" aria-label="Close" data-notify="dismiss">
                            <i class="fa fa-times"></i>
                        </a>
                        </div>
                        `
                });
            @endif
        });
    </script>
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

        <div class="d-flex flex-row">
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
            <div class="me-2">
                <div class="dropdown">
                    @php
                        $color = 'btn-primary';
                        if ($order->status == 'aprovado') {
                            $color = 'btn-success';
                        } elseif ($order->status == 'aguard. aprov') {
                            $color = 'btn-warning';
                        } elseif ($order->status == 'aguard. arte') {
                            $color = 'btn-info';
                        }
                    @endphp
                    <button type="button" class="btn {{ $color }} dropdown-toggle" id="status-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @if ($order->status == 'aprovado')
                            Aprovado
                        @elseif ($order->status == 'aguard. aprov')
                            Aguardando Aprov.
                        @elseif ($order->status == 'aguard. arte')
                            Aguardando Arte
                        @else
                            Status não definido
                        @endif
                    </button>
                    <div class="dropdown-menu fs-sm" aria-labelledby="dropdown-default-primary">
                        <a class="dropdown-item status" data-value="aprovado" href="javascript:void(0)">Aprovado</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item status" data-value="aguard. aprov" href="javascript:void(0)">Aguardando
                            Aprov.</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item status" data-value="aguard. arte" href="javascript:void(0)">Aguardando
                            Arte</a>
                    </div>
                </div>
            </div>
            <div class="me-2">
                <div class="dropdown">
                    @php
                        $bgColor = 'btn-primary';
                        if ($order->employee == 'bruno') {
                            $bgColor = 'bg-elegance';
                        } elseif ($order->employee == 'ricardo') {
                            $bgColor = 'bg-pulse';
                        } elseif ($order->employee == 'rubens') {
                            $bgColor = 'bg-flat';
                        }
                    @endphp
                    <button type="button" class="btn {{ $bgColor }} dropdown-toggle text-white"
                        id="employee-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @if ($order->employee == 'bruno')
                            Bruno
                        @elseif ($order->employee == 'ricardo')
                            Ricardo
                        @elseif ($order->employee == 'rubens')
                            Rubens
                        @else
                            Arte finalista não definido
                        @endif
                    </button>
                    <div class="dropdown-menu fs-sm" aria-labelledby="dropdown-default-primary">
                        <a class="dropdown-item employee" data-value="bruno" href="javascript:void(0)">Bruno</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item employee" data-value="ricardo" href="javascript:void(0)">Ricardo</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item employee" data-value="rubens" href="javascript:void(0)">Rubens</a>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="dropdown">
                    @php
                        $bgColorArrived = 'btn-primary';
                        if ($order->arrived) {
                            $bgColorArrived = 'btn-success';
                        } else {
                            $bgColorArrived = 'btn-danger';
                        }
                    @endphp
                    <button type="button" class="btn {{ $bgColorArrived }} dropdown-toggle text-white"
                        id="arrived-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
        </div>

        <h2 class="content-heading">Informações do Cliente</h2>
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

        <h2 class="content-heading">Produtos</h2>
        <div class="block block-rounded">
            <div class="block-content block-content-full">
                <div class="table-responsive">
                    <table class="table table-borderless table-striped mb-0">
                        <thead>
                            <tr>
                                <th style="width: 100px;">ID</th>
                                <th>Produto</th>
                                <th class="text-center">Quantidade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderProducts as $product)
                                <tr>
                                    <td>
                                        {{ $product->id }}
                                    </td>
                                    <td>
                                        {{ strtoupper($product->name) }}
                                    </td>
                                    <td class="text-center">
                                        {{ $product->qtd }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <h2 class="content-heading">Arquivos</h2>
        <div class="row items-push">
            <div class="col-md-12">
                <div class="block block-rounded h-100 mb-0">
                    <div class="block-content fs-md">
                        <div id="error-msg"></div>
                        <div class="d-flex flex-row mb-4">
                            <div>
                                <div class="fw-bold">
                                    Pré-visualização
                                </div>
                                <div>
                                    @if ($order->preview)
                                        <img src="{{ asset('preview/' . $order->preview) }}" alt="Pré-visualização"
                                            class="img-fluid w-50" />
                                    @else
                                        <img src="{{ asset('media/photos/noimage.jpg') }}" alt="Pré-visualização"
                                            class="img-fluid" />
                                    @endif
                                </div>
                                <div id="loading-preview" class="mx-4 mt-4" style="display: none">
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
                                        <circle class="spinner_DupU spinner_GWtZ" cx="16.50" cy="4.21" r="0" />
                                        <circle class="spinner_DupU spinner_n0Yb" cx="7.50" cy="4.21" r="0" />
                                        <circle class="spinner_DupU spinner_dwN6" cx="19.79" cy="7.50" r="0" />
                                        <circle class="spinner_DupU spinner_GIL4" cx="4.21" cy="7.50" r="0" />
                                        <circle class="spinner_DupU spinner_46QP" cx="21.00" cy="12.00" r="0" />
                                        <circle class="spinner_DupU spinner_DQhX" cx="3.00" cy="12.00" r="0" />
                                        <circle class="spinner_DupU spinner_PD82" cx="19.79" cy="16.50" r="0" />
                                        <circle class="spinner_DupU spinner_tVmX" cx="4.21" cy="16.50" r="0" />
                                        <circle class="spinner_DupU spinner_eUgh" cx="16.50" cy="19.79" r="0" />
                                        <circle class="spinner_DupU spinner_j38H" cx="7.50" cy="19.79" r="0" />
                                        <circle class="spinner_DupU spinner_eUaP" cx="12" cy="21" r="0" />
                                    </svg>
                                </div>
                                @if (!$order->preview)
                                    <div>
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
                                    </div>
                                @endif
                            </div>

                            <div class="">
                                <div class="fw-bold">
                                    Arquivo de Design PDF
                                </div>
                                <div>
                                    @if ($order->design_file)
                                        <div class="mt-5">
                                            <a href="{{ asset('design/' . $order->design_file) }}" target="_blank"
                                                class="btn btn-primary">
                                                <i class="fa fa-fw fa-download text-white me-1"></i>
                                                <span class="d-none d-sm-inline">Baixar Arquivo</span>
                                            </a>
                                        </div>
                                    @else
                                        <img src="{{ asset('media/photos/noimage.jpg') }}" alt="Pré-visualização"
                                            class="img-fluid" />
                                    @endif
                                </div>
                                <div id="loading-design" class="mx-4 mt-4" style="display: none">
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
                                        <circle class="spinner_DupU spinner_GWtZ" cx="16.50" cy="4.21" r="0" />
                                        <circle class="spinner_DupU spinner_n0Yb" cx="7.50" cy="4.21" r="0" />
                                        <circle class="spinner_DupU spinner_dwN6" cx="19.79" cy="7.50" r="0" />
                                        <circle class="spinner_DupU spinner_GIL4" cx="4.21" cy="7.50" r="0" />
                                        <circle class="spinner_DupU spinner_46QP" cx="21.00" cy="12.00" r="0" />
                                        <circle class="spinner_DupU spinner_DQhX" cx="3.00" cy="12.00" r="0" />
                                        <circle class="spinner_DupU spinner_PD82" cx="19.79" cy="16.50" r="0" />
                                        <circle class="spinner_DupU spinner_tVmX" cx="4.21" cy="16.50" r="0" />
                                        <circle class="spinner_DupU spinner_eUgh" cx="16.50" cy="19.79" r="0" />
                                        <circle class="spinner_DupU spinner_j38H" cx="7.50" cy="19.79" r="0" />
                                        <circle class="spinner_DupU spinner_eUaP" cx="12" cy="21" r="0" />
                                    </svg>
                                </div>
                                @if (!$order->design_file)
                                    <div>
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
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Page Content -->
@endsection
