@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
@endsection

@section('js')
    {{-- <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script> --}}
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
    @vite(['resources/js/pages/order/create.js'])
    <script>
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
    </script>
    <script>
        let productIndex = 2;
        let route = "{{ route('dashboard.product.autocomplete') }}";

        jQuery('input.autocomplete-1').typeahead({
            source: function(query, process) {
                return jQuery.get(route, {
                    query: query
                }, function(data) {
                    return process(data);
                });
            }
        });

        function adicionarLinha() {
            const tabela = document.getElementById('tabelaProdutos').getElementsByTagName('tbody')[0];
            const novaLinha = tabela.insertRow();

            novaLinha.innerHTML = `
                <td><input type="text" class="form-control autocomplete-${productIndex}" name="product[${productIndex}][name]" placeholder="Informe o Produto" /></td>
                <td><input type="number" class="form-control" name="product[${productIndex}][qtd]" placeholder="Quantidade" /></td>
                <td>
                    <select class="form-select" name="product[${productIndex}][in_stock]" id="in-stock-${productIndex}">
                        <option value="yes">Sim</option>
                        <option value="no">Não</option>
                        <option value="partial">Parcial</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control" name="product[${productIndex}][supplier]"
                        placeholder="Fornecedor" id="supplier-${productIndex}">
                </td>
                <td>
                    <input type="text" class="form-control" name="product[${productIndex}][link]"
                        placeholder="Link" id="link-${productIndex}">
                </td>
                <td>
                    <input type="text" class="form-control" name="product[${productIndex}][obs]"
                        placeholder="Observação" id="obs-${productIndex}">
                </td>
                <td>
                    <button type="button" class="btn btn-danger" onclick="removerLinha(this)">
                        <i class="fas fa-minus"></i>
                    </button>
                </td>
            `;

            tabela.appendChild(novaLinha);

            jQuery('input.autocomplete-' + productIndex).typeahead({
                source: function(query, process) {
                    return jQuery.get(route, {
                        query: query
                    }, function(data) {
                        return process(data);
                    });
                }
            });

            productIndex++;
        }

        function removerLinha(botao) {
            const linha = botao.parentNode.parentNode;
            linha.parentNode.removeChild(linha);
        }
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
                    <span class="breadcrumb-item active">Criar</span>
                </nav>
            </div>
        </div>
        <div class="block block-rounded">
            <div class="block-content">
                <!-- Seletor de cliente -->
                <form action="{{ route('dashboard.order.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="d-flex flex-row justify-content-start gap-1 mb-4">
                            {{-- <div>
                                <a class="btn btn-outline-primary" href="{{ route('dashboard.order.index') }}">
                                    <i class="fa fa-arrow-left opacity-50 me-1"></i>Voltar
                                </a>
                            </div> --}}
                            <div>
                                <button type="submit" class="btn btn-sm btn-primary me-1 mb-1">
                                    <i class="fa fa-check me-1"></i> Salvar Pedido
                                </button>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-sm btn-outline-primary me-1 mb-1">
                                    <i class="fa-regular fa-note-sticky me-1"></i> Visualizar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="create-autoload">
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xl-12">
                            <label for="autocomplete-input" class="form-label text-uppercase">Cliente:</label>
                            <div class="input-group" id="group-autocomplete">
                                <input type="text" class="form-control" id="autocomplete-input"
                                    placeholder="Digite o nome do cliente" autocomplete="off">
                            </div>
                            <div class="divider my-2"></div>
                            <div id="autocomplete-suggestions" class="autocomplete-suggestions d-none"></div>
                        </div>
                    </div>
                    <div id="autocomplete-loading"></div>
                    <div class="row">
                        <div class="col-md-6" id="listar-clientes-group">
                            <a class="muted text-black listar-clientes" href="javascript:void(0)">
                                <span>
                                    <i class="fas fa-caret-down"></i>
                                </span>
                                Listar todos clientes
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3" id="cliente-selecionado"></div>
                    </div>

                    <!-- Campos de texto -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="numeroPedido" class="form-label text-uppercase">Número do Pedido:</label>
                            <input type="text" class="form-control" id="numeroPedido" name="number" autocomplete="off"
                                placeholder="Número do pedido">
                            @error('number')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="dataEmissao" class="form-label text-uppercase">Data da Emissão:</label>
                            <input type="text" class="js-datepicker form-control" id="dataEmissao" name="date"
                                data-week-start="1" data-autoclose="true" data-today-highlight="true"
                                data-date-format="dd/mm/yyyy" placeholder="Data de Emissão" autocomplete="off">
                            @error('date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="dataEntrega" class="form-label text-uppercase">Data da Entrega:</label>
                            <input type="text" class="js-datepicker form-control" id="dataEntrega" name="delivery_date"
                                data-week-start="1" data-autoclose="true" data-today-highlight="true"
                                data-date-format="dd/mm/yyyy" placeholder="Data de Entrega" autocomplete="off">
                            @error('delivery_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tabela de produtos -->
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered" id="tabelaProdutos">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th style="width: 160px">Quantidade</th>
                                        <th>Estoque</th>
                                        <th>Fornecedor</th>
                                        <th>Link</th>
                                        <th>Observação</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control autocomplete-1"
                                                name="product[1][name]" placeholder="Informe o Produto" autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="product[1][qtd]"
                                                placeholder="Quantidade">
                                        </td>
                                        <td>
                                            <select class="form-select" name="product[1][in_stock]" id="in-stock-1">
                                                <option value="yes">Sim</option>
                                                <option value="no">Não</option>
                                                <option value="partial">Parcial</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="product[1][supplier]"
                                                placeholder="Fornecedor">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="product[1][link]"
                                                placeholder="Link">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="product[1][obs]"
                                                placeholder="Observação">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-success" onclick="adicionarLinha()">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal" id="customer-modal" data-bs-backdrop='static' tabindex="-1" role="dialog"
        aria-labelledby="modal-normal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="block block-rounded shadow-none mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Cadastro de Cliente</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <form action="" id="form-customer">
                        <div class="block-content fs-sm">
                            <div class="alert alert-danger d-none" role="alert" id="error-container">
                                <h4 class="alert-heading fs-5 fw-bold mb-1">Ocorreram erros na solicitação</h4>
                                <div id="error-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="fw-semibold">Nome:</label>
                                <input type="text" class="form-control" id="customer-name" name="name"
                                    placeholder="Digite o nome do cliente">
                            </div>
                            <div class="form-group">
                                <label for="email" class="fw-semibold">E-mail:</label>
                                <input type="text" class="form-control" id="customer-email" name="email"
                                    placeholder="Digite o e-mail do cliente">
                            </div>
                            <div class="form-group">
                                <label for="phone" class="fw-semibold">Contato:</label>
                                <input type="text" class="form-control" id="customer-phone" name="phone"
                                    placeholder="Digite o contato do cliente">
                            </div>
                        </div>
                        <div class="block-content block-content-full border-top d-flex">
                            <div class="col-12 col-md-6">
                                <button type="submit" class="btn btn-primary w-100">
                                    Salvar
                                </button>
                            </div>
                            <div class="col-12 col-md-6">
                                <button type="button" class="btn btn-danger w-100" id="close-customer-modal">
                                    Fechar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END Page Content -->
@endsection
