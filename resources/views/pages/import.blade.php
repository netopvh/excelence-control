@extends('layouts.backend')

@section('css')
@endsection

@section('js')
    <script src="{{ asset('js/lib/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#import').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "/api/import/order",
                    method: "POST",
                    data: new FormData(this),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $('#loading').show();
                        $('#import').attr('disabled', 'disabled');
                    },
                    success: function(data) {
                        $('#loading').hide();
                        $('#message').html(
                            '<div class="alert alert-success mx-4 alert-dismissible fade show" role="alert">' +
                            '<strong>' + data.message + '</strong>' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>'
                        );
                        $('#import')[0].reset();
                        $('#import').attr('disabled', false);
                    },
                    error: function(data) {
                        $('#loading').hide();
                        $('#message').html(
                            '<div class="alert alert-danger mx-4 alert-dismissible fade show" role="alert">' +
                            '<strong>' + data.responseJSON.message + '</strong>' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>'
                        );
                        $('#import').attr('disabled', false);
                    }
                })
            });
        });
    </script>
    @vite(['resources/js/pages/import/index.js'])
@endsection

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="row items-push">
            <div class="col-12 col-md-12">
                <div class="block block-rounded overflow-hidden">
                    <ul class="nav nav-tabs nav-tabs-block align-items-center" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-1" role="tab">
                                Pedidos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-2" role="tab">
                                Produtos
                            </button>
                        </li>
                    </ul>
                    <div class="block-content tab-content">
                        <div class="tab-pane active" id="tab-1" role="tabpanel" tabindex="0">
                            <div class="block-header">
                                <h4 class="block-title fw-bold">Importação de Pedidos</h4>
                            </div>
                            <div id="message"></div>
                            <div id="loading" class="mx-4 mt-4 d-none">
                                <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
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
                            <div class="block-content block-content-full">
                                <form action="" id="form-order" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row mb-4">
                                        <div class="col-md-12 col-xl-12 fw-bold">
                                            Selecione o arquivo que deseja importar para a base de dados
                                        </div>
                                    </div>
                                    <div class="row d-block">
                                        <div class="col-lg-8 col-xl-5 overflow-hidden">
                                            <div class="mb-4">
                                                <label class="form-label" for="example-file-input">Arquivo</label>
                                                <input class="form-control" type="file" name="file"
                                                    id="example-file-input">
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-xl-5" id="import-order-container">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-2" role="tabpanel" tabindex="0">
                            <div class="block-header">
                                <h4 class="block-title fw-bold">Importação de Produtos</h4>
                            </div>
                            <div class="block-content block-content-full">
                                <div class="row mb-4">
                                    <div class="col-md-12 col-xl-12 fw-bold">
                                        Os produtos serão importados a partir da plataforma de e-commerce.
                                    </div>
                                </div>
                                <div class="col-lg-8 col-xl-5" id="import-product-container">
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
