@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('title')
    Purchase Receipt Details
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a target="_blank" href="{{ route('purchase_receipt.print', ['order' => $order->id]) }}" class="btn btn-warning">Print</a>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Order No.</th>
                                    <td>{{ $order->order_no }}</td>
                                </tr>
                                <tr>
                                    <th>Order Date</th>
                                    <td>{{ $order->date->format('j F, Y') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th colspan="2" class="text-center">Supplier Info</th>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $order->supplier->name }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile</th>
                                    <td>{{ $order->supplier->mobile }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $order->supplier->address }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Serial No.</th>
                                        <th>Warranty (Month)</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Including Price</th>
                                        <th>Selling Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($order->products as $product)
                                        <tr>
                                            <td>{{ $product->pivot->name }}</td>
                                            <td>{{ $product->pivot->serial_no }}</td>
                                            <td>{{ $product->pivot->warranty }}</td>
                                            <td>{{ $product->pivot->quantity.' '.$product->pivot->unit }}</td>
                                            <td>৳{{ number_format($product->pivot->unit_price, 2) }}</td>
                                            <td>৳{{ number_format($product->pivot->including_price, 2) }}</td>
                                            <td>৳{{ number_format($product->pivot->selling_price, 2) }}</td>
                                            <td>৳{{ number_format($product->pivot->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-offset-8 col-md-4">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Total Amount</th>
                                    <td>৳{{ number_format($order->total, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Paid</th>
                                    <td>৳{{ number_format($order->paid, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Due</th>
                                    <td>৳{{ number_format($order->due, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Transport Cost</th>
                                    <td>৳{{ number_format($order->transport_cost, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Refunds</th>
                                    <td>৳{{ number_format($order->refund, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <table id="table-payments" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Transaction Method</th>
                                    <th>Bank</th>
                                    <th>Branch</th>
                                    <th>Account</th>
                                    <th>Amount</th>
                                    <th>Note</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($order->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->date->format('Y-m-d') }}</td>
                                        <td>
                                            @if($payment->type == 1)
                                                Pay
                                            @elseif($payment->type == 2)
                                                Refund
                                            @endif
                                        </td>
                                        <td>
                                            @if($payment->transaction_method == 1)
                                                Cash
                                            @elseif($payment->transaction_method == 4)
                                                Condition (Cash)
                                            @elseif($payment->transaction_method == 3)
                                                Mobile Banking
                                            @else
                                                Bank
                                            @endif
                                        </td>
                                        <td>{{ $payment->bank ? $payment->bank->name : '' }}</td>
                                        <td>{{ $payment->branch ? $payment->branch->name : '' }}</td>
                                        <td>{{ $payment->account ? $payment->account->account_no : '' }}</td>
                                        <td>৳{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ $payment->note }}</td>
                                        <td>
                                            <a href="{{ route('purchase_receipt.payment_details', ['payment' => $payment->id]) }}" class="btn btn-warning btn-sm">Details</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

    <script>
        $(function () {
            $('#table-payments').DataTable({
                "order": [[ 0, "desc" ]],
            });
        });
    </script>
@endsection
