@extends('layouts.app')

@section('title')
    Dashboard
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Today's Sale</span>
                    <span class="info-box-number">৳{{ number_format($todaySale, 2) }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-dollar"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Today's Cash Sale</span>
                    <span class="info-box-number">৳{{ number_format($todayCashSale, 2) }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-star-half"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Today's Due</span>
                    <span class="info-box-number">৳{{ number_format($todayDue, 2) }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
    </div>

    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-files-o"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Today's Due Collection</span>
                    <span class="info-box-number">৳{{ number_format($todayDueCollection, 2) }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-dollar"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Today's Expense</span>
                    <span class="info-box-number">৳{{ number_format($todayExpense, 2) }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->


    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Today's Sales Order</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table no-margin">
                            <thead>
                            <tr>
                                <th>Order No.</th>
                                <th>Customer Name</th>
                                <th>Total</th>
                                <th>Created At</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($todaySaleReceipt as $receipt)
                            <tr>
                                <td><a href="{{ route('sale_receipt.details', ['order' => $receipt->id]) }}">{{ $receipt->order_no }}</a></td>
                                <td>{{ $receipt->customer->name }}</td>
                                <td>৳{{ number_format($receipt->total, 2) }}</td>
                                <td>{{ $receipt->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                    {{ $todaySaleReceipt->links() }}
                </div>
                <!-- /.box-footer -->
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Today's Purchase Order</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table no-margin">
                            <thead>
                            <tr>
                                <th>Order No.</th>
                                <th>Supplier</th>
                                <th>Total</th>
                                <th>Created At</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($todayPurchaseReceipt as $receipt)
                                <tr>
                                    <td><a href="{{ route('purchase_receipt.details', ['order' => $receipt->id]) }}">{{ $receipt->order_no }}</a></td>
                                    <td>{{ $receipt->supplier->name }}</td>
                                    <td>৳{{ number_format($receipt->total, 2) }}</td>
                                    <td>{{ $receipt->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                    {{ $todaySaleReceipt->links() }}
                </div>
                <!-- /.box-footer -->
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Best Selling Products</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table no-margin">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Count</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($bestSellingProducts as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->code }}</td>
                                    <td>{{ $product->count }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.box-body -->
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Recently Added Products</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table no-margin">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Created At</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($recentlyProducts as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->code }}</td>
                                    <td>{{ $product->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Sales History</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="">
                    <canvas id="chart-sales-amount" width="100%" height="30"></canvas>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Order Count History</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="">
                    <canvas id="chart-order-count" width="100%" height="30"></canvas>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('themes/backend/plugins/chartjs/Chart.bundle.min.js') }}"></script>
    <script>
        var saleAmountLabel = <?php echo $saleAmountLabel; ?>;
        var saleAmount = <?php echo $saleAmount; ?>;
        var orderCount = <?php echo $orderCount; ?>;

        var ctx = document.getElementById('chart-sales-amount');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: saleAmountLabel,
                datasets: [{
                    label: 'Sales Amount',
                    data: saleAmount,
                    backgroundColor: 'rgba(60, 141, 188, 0.2)',
                    borderColor:  'rgba(60,141,188,1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                legend: {
                    display: false,
                },
                tooltips: {
                    displayColors: false,
                    callbacks: {
                        label: function (tooltipItems, data) {
                            return   "৳" + tooltipItems.yLabel;
                        }
                    }
                }
            }
        });

        var ctx2 = document.getElementById("chart-order-count").getContext('2d');
        var myChart2 = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: saleAmountLabel,
                datasets: [{
                    data: orderCount,
                    backgroundColor: 'rgba(60, 141, 188, 0.2)',
                    borderColor:  'rgba(60,141,188,1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                legend: {
                    display: false,
                },
                tooltips: {
                    displayColors: false
                }
            }
        });
    </script>
@endsection
