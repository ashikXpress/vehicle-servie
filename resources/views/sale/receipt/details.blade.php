@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('title')
    Sale Receipt Details
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <a target="_blank" href="{{ route('sale_receipt.print', ['order' => $order->id]) }}" class="btn btn-warning">Print</a>
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
                                <tr>
                                    <th>Received By</th>
                                    <td>{{ $order->received_by }}</td>
                                </tr>
                                <tr>
                                    <th>Next Payment Date</th>
                                    <td>{{ $order->next_payment ? $order->next_payment->format('j F, Y') : '' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th colspan="2" class="text-center">Customer Info</th>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $order->customer->name }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $order->customer->mobile_no }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $order->customer->address }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if(count($order->products) > 0)
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Serial</th>
                                        <th>Warranty (Month)</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($order->products as $product)
                                        <tr>
                                            <td>{{ $product->pivot->name }}</td>
                                            <td>{{ $product->pivot->serial }}</td>
                                            <td>{{ $product->pivot->warranty }}</td>
                                            <td>{{ $product->pivot->quantity.' '.$product->pivot->unit }}</td>
                                            <td>৳{{ number_format($product->pivot->unit_price, 2) }}</td>
                                            <td>৳{{ number_format($product->pivot->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if(count($order->services) > 0)
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($order->services as $services)
                                        <tr>
                                            <td>{{ $services->name }}</td>
                                            <td>{{ $services->quantity }}</td>
                                            <td>৳{{ number_format($services->unit_price, 2) }}</td>
                                            <td>৳{{ number_format($services->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-offset-8 col-md-4">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Product Sub Total</th>
                                    <td>৳{{ number_format($order->sub_total, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Service Sub Total</th>
                                    <td>৳{{ number_format($order->service_sub_total, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Product Vat ({{ $order->vat_percentage }}%)</th>
                                    <td>৳{{ number_format($order->vat, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Service Vat ({{ $order->service_vat_percentage }}%)</th>
                                    <td>৳{{ number_format($order->service_vat, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Product Discount</th>
                                    <td>৳{{ number_format($order->discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Service Discount</th>
                                    <td>৳{{ number_format($order->service_discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total</th>
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
                                    <th>Refund</th>
                                    <td>৳{{ number_format($order->refund, 2) }}</td>
                                </tr>
                                <tr>
                                    <th> Vat Status </th>
                                    <td>
                                        @if ($order->vat_status==1)
                                            With VAT
                                        @else
                                            Without VAT
                                        @endif
                                    </td>
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
                                            <a href="{{ route('sale_receipt.payment_details', ['payment' => $payment->id]) }}" class="btn btn-warning btn-sm">Details</a>
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
