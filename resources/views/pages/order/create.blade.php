@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
@endsection

@section('js')
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
    <script>
        function addRow() {
            let productIndex = 1;

            var newRow = `<tr>
                            <td><input type="text" class="form-control autocomplete-${productIndex}" name="product[${productIndex}][name]" /></td>
                            <td><input type="number" class="form-control" name="product[${productIndex}][qtd]" /></td>
                            <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">Remover</button></td>
                        </tr>`;

            jQuery('#productTable tbody').append(newRow);

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

        function removeRow(button) {
            jQuery(button).closest('tr').remove();
        }

        jQuery(function() {

            // Select2
            jQuery('.customer-select').select2({
                placeholder: 'Selecione um cliente..',
                allowClear: true
            });

            jQuery('#employee').select2({
                placeholder: 'Selecione o Arte Finalista..',
                allowClear: true
            });

            Codebase.helpersOnLoad(['jq-datepicker']);
        });

        let route = "{{ route('dashboard.product.autocomplete') }}";

        jQuery('input.autocomplete').typeahead({
            source: function(query, process) {
                return jQuery.get(route, {
                    query: query
                }, function(data) {
                    return process(data);
                });
            }
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
    </script>
    {{-- <script type="module">
        import Autocomplete from "https://cdn.jsdelivr.net/gh/lekoala/bootstrap5-autocomplete@master/autocomplete.js";
        Autocomplete.init("input.autocomplete", {
            items: ,
            valueField: "id",
            labelField: "title",
            highlightTyped: true,
            onSelectItem: console.log,
        });
    </script> --}}
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
            <div class="block-header block-header-default">
                <h3 class="block-title">Cadastro de Pedido em Produção</h3>
            </div>
            <div class="block-content">
                <form action="{{ route('dashboard.order.store') }}" method="POST">
                    @csrf
                    <div class="row push">
                        <div class="col-lg-3">
                            <p class="fw-bold">
                                Informações Gerais
                            </p>
                        </div>
                        <div class="col-lg-9">
                            <!-- Form Grid -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <select class="customer-select form-select" id="customer_id" name="customer_id"
                                        style="width: 80%;" data-placeholder="Selecione um cliente..">
                                        <option></option>
                                        <!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ strtoupper($customer->name) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#modal-normal"
                                        class="btn btn-md btn-alt-primary">Novo
                                        Cliente</button>
                                    @error('customer_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror

                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-4">
                                    <input type="text" class="js-datepicker form-control" id="date" name="date"
                                        data-week-start="1" data-autoclose="true" data-today-highlight="true"
                                        data-date-format="dd/mm/yyyy" placeholder="Data de Emissão">
                                    @error('date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-4">
                                    <input type="number" name="number" placeholder="N. do Pedido" class="form-control">
                                    @error('number')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-4">
                                    <input type="text" class="js-datepicker form-control" id="delivery_date"
                                        name="delivery_date" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true" data-date-format="dd/mm/yyyy"
                                        placeholder="Data de Entrega">
                                    @error('delivery_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                            <p class="fw-bold">
                                Produtos
                            </p>
                        </div>
                        <div class="col-lg-9">
                            <table class="table table-bordered" id="productTable">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th style="width: 150px">Quantidade</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" class="form-control autocomplete"
                                                name="product[0][name]" />
                                        </td>
                                        <td><input type="number" class="form-control" name="product[0][qtd]" /></td>
                                        <td><button type="button" class="btn btn-danger"
                                                onclick="removeRow(this)">Remover</button></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end">
                                            <button type="button" class="btn btn-success" onclick="addRow()">Adicionar
                                                Produto</button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="d-flex flex-row justify-content-end gap-1 my-4">
                        <div>
                            <a class="btn btn-warning" href="{{ route('dashboard.order.index') }}">
                                <i class="fa fa-arrow-left opacity-50 me-1"></i>Voltar
                            </a>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary me-1 mb-1">
                                <i class="fa fa-check opacity-50 me-1"></i> Salvar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal" id="modal-normal" tabindex="-1" role="dialog" aria-labelledby="modal-normal" aria-hidden="true">
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
                    <form action="">
                        <div class="block-content fs-sm">
                            <div class="form-group">
                                <label for="name">Nome</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Digite o nome do cliente">
                            </div>
                        </div>
                        <div class="block-content block-content-full block-content-sm text-end border-top">
                            <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">
                                Fechar
                            </button>
                            <button type="button" class="btn btn-alt-primary" data-bs-dismiss="modal">
                                Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END Page Content -->
@endsection
