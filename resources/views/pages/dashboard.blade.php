@extends('layouts.backend')

@section('js')
    <script src="{{ asset('js/lib/jquery.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.canvasjs.com/jquery.canvasjs.min.js"></script>
    @vite(['resources/js/pages/dashboard.js'])
    <script>
        window.onload = function() {
            // Fazer requisição GET para o endpoint /dashboard/data
            fetch('/dashboard/chart')
                .then(response => response.json())
                .then(data => {
                    // Construir options do gráfico com os dados recebidos
                    var options = {
                        title: {
                            text: "Pedidos no mês atual"
                        },
                        data: [{
                            type: "column",
                            dataPoints: data.map(item => ({
                                label: item.order_date_formatted,
                                y: item.total_orders
                            }))
                        }]
                    };

                    // Renderizar o gráfico usando CanvasJS
                    $("#chartContainer").CanvasJSChart(options);
                })
                .catch(error => {
                    console.error('Erro ao obter dados do endpoint:', error);
                });
        }
    </script>
@endsection

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div class="row">
            <div class="col-6 col-xl-3">
                <a class="block block-rounded block-link-rotate text-end" href="javascript:void(0)">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <i class="fa fa-thumbs-up fa-2x text-success"></i>
                        </div>
                        <div class="text-end">
                            <div class="fs-3 fw-semibold">{{ $approved }}</div>
                            <div class="fs-sm fw-semibold text-uppercase text-muted">Aprovados</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-xl-3">
                <a class="block block-rounded block-link-rotate text-end" href="javascript:void(0)">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <i class="fa fa-hourglass-half fa-2x text-warning"></i>
                        </div>
                        <div class="text-end">
                            <div class="fs-3 fw-semibold">{{ $waitingApproval }}</div>
                            <div class="fs-sm fw-semibold text-uppercase text-muted">Aguard. Aprov.</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-xl-3">
                <a class="block block-rounded block-link-rotate text-end" href="javascript:void(0)">
                    <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                        <div class="d-none d-sm-block">
                            <i class="fa fa-id-badge fa-2x text-info"></i>
                        </div>
                        <div class="text-end">
                            <div class="fs-3 fw-semibold">{{ $waitingArt }}</div>
                            <div class="fs-sm fw-semibold text-uppercase text-muted">Aguard. Arte</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="row">
            <div id="chartContainer" style="height: 370px; width: 100%;"></div>
        </div>
    </div>
    <!-- END Page Content -->
@endsection
