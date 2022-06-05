@extends('layouts.app')

@section('style')
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
@endsection

@section('title')
    Sale Report
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <form action="{{ route('report.sale') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right"
                                               id="date" name="date" value="{{ request()->get('date')  }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Customer</label>

                                    <select class="form-control select2" name="customer">
                                        <option value="">All Customer</option>

                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ request()->get('customer') == $customer->id ? 'selected' : '' }}>{{ $customer->name.' - '.$customer->mobile_no }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Sale ID</label>

                                    <input type="text" class="form-control" name="saleId" value="{{ request()->get('saleId') }}">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Product</label>

                                    <select class="form-control select2" name="product">
                                        <option value="">All Product</option>

                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ request()->get('product') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>	&nbsp;</label>

                                    <input class="btn btn-warning form-control" type="submit" value="Search">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <section class="panel">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Order No.</th>
                                <th>Customer Name</th>
                                <th>Address</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->date->format('j F, Y') }}</td>
                                    <td>{{ $order->order_no }}</td>
                                    <td>{{ $order->customer->name }}</td>
                                    <td>{{ $order->customer->address }}</td>
                                    <td>{{ number_format($order->total, 2) }}</td>
                                    <td>{{ number_format($order->paid, 2) }}</td>
                                    <td>{{ number_format($order->due, 2) }}</td>
                                    <td><a href="{{ route('sale_receipt.details', ['order' => $order->id]) }}">View Invoice</a></td>
                                </tr>
                            @endforeach
                            </tbody>

                            <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total</th>
                                <td>{{ number_format($total, 2) }}</td>
                                <td>{{ number_format($paid, 2) }}</td>
                                <td>{{ number_format($due, 2) }}</td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>

                        {{ $orders->appends($appends)->links() }}
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@section('script')
    <!-- date-range-picker -->
    <script src="{{ asset('themes/backend/bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()

            var dates = '{{ request()->get('date') }}';
            if (dates != '') {
                dateExplode = dates.split(' - ');

                //Date range picker
                $('#date').daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear'
                    },
                    startDate: moment(dateExplode[0], "YYYY-MM-DD"),
                    endDate: moment(dateExplode[1], "YYYY-MM-DD"),
                });

                console.log('aise');
            } else {
                //Date range picker
                $('#date').daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear'
                    }
                });
            }

            $('#date').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                table.ajax.reload();
            });

            $('#date').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                table.ajax.reload();
            });

            $('#date, #type').change(function () {
                table.ajax.reload();
            });
        });
    </script>
@endsection
