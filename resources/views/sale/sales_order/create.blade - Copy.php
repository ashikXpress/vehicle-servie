@extends('layouts.app')

@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Sales Order
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Order Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form method="POST" action="{{ route('sales_order.create') }}">
                    @csrf

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('customer_name') ? 'has-error' :'' }}">
                                    <label>Customer Name</label>

                                    <input type="text" class="form-control" name="customer_name" value="{{ old('customer_name') }}">

                                    @error('customer_name')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('phone') ? 'has-error' :'' }}">
                                    <label>Phone</label>

                                    <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">

                                    @error('phone')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('date') ? 'has-error' :'' }}">
                                    <label>Date</label>

                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="date" name="date" value="{{ empty(old('date')) ? ($errors->has('date') ? '' : date('Y-m-d')) : old('date') }}" autocomplete="off">
                                    </div>
                                    <!-- /.input group -->

                                    @error('date')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group {{ $errors->has('warehouse') ? 'has-error' :'' }}">
                                    <label>Warehouse</label>

                                    <select class="form-control" name="warehouse" id="warehouse">
                                        <option value="">Select Warehouse</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ old('warehouse') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('warehouse')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Serial</th>
                                    <th>Product Name</th>
                                    <th width="10%">Quantity</th>
                                    <th width="15%">Unit Price</th>
                                    <th>Total Cost</th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody id="product-container">
                                @if (old('serial') != null && sizeof(old('serial')) > 0)
                                    @foreach(old('serial') as $item)
                                        <tr class="product-item">
                                            <td>
                                                <div class="form-group {{ $errors->has('serial.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control serial" name="serial[]" value="{{ old('serial.'.$loop->index) }}" autocomplete="off">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group {{ $errors->has('product_name.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control product_name" name="product_name[]" value="{{ old('product_name.'.$loop->index) }}" readonly>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group {{ $errors->has('quantity.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="number" step="any" class="form-control quantity" name="quantity[]" value="{{ old('quantity.'.$loop->index) }}">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group {{ $errors->has('unit_price.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control unit_price" name="unit_price[]" value="{{ old('unit_price.'.$loop->index) }}">
                                                </div>
                                            </td>

                                            <td class="total-cost">৳0.00</td>
                                            <td class="text-center">
                                                <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="product-item">
                                        <td>
                                            <input type="text" class="form-control serial" name="serial[]" autocomplete="off">
                                        </td>

                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control product_name" name="product_name[]" readonly>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-group">
                                                <input type="number" step="any" class="form-control quantity" name="quantity[]">
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control unit_price" name="unit_price[]">
                                            </div>
                                        </td>

                                        <td class="total-cost">৳0.00</td>
                                        <td class="text-center">
                                            <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>

                                <tfoot>
                                <tr>
                                    <td>
                                        <a role="button" class="btn btn-warning btn-sm" id="btn-add-product">Add Product</a>
                                    </td>
                                    <th colspan="3" class="text-right">Sub Total</th>
                                    <th id="total-amount">৳0.00</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-right">VAT</th>
                                    <td>
                                        <div class="form-group {{ $errors->has('vat') ? 'has-error' :'' }}">
                                            <input type="text" class="form-control" name="vat" id="vat" value="{{ empty(old('vat')) ? ($errors->has('vat') ? '' : '0') : old('vat') }}">
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-right">Discount</th>
                                    <td>
                                        <div class="form-group {{ $errors->has('discount') ? 'has-error' :'' }}">
                                            <input type="text" class="form-control" name="discount" id="discount" value="{{ empty(old('discount')) ? ($errors->has('discount') ? '' : '0') : old('discount') }}">
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-right">Total</th>
                                    <th id="final-amount">৳0.00</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-right">Paid</th>
                                    <td>
                                        <div class="form-group {{ $errors->has('paid') ? 'has-error' :'' }}">
                                            <input type="text" class="form-control" name="paid" id="paid" value="{{ empty(old('paid')) ? ($errors->has('paid') ? '' : '0') : old('paid') }}">
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-right">Due</th>
                                    <th id="due">৳0.00</th>
                                    <td></td>
                                </tr>
                                <tr id="tr-next-payment">
                                    <th colspan="4" class="text-right">Next Payment Date</th>
                                    <td>
                                        <div class="form-group {{ $errors->has('next_payment') ? 'has-error' :'' }}">
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="text" class="form-control pull-right" id="next_payment" name="next_payment" value="{{ old('next_payment') }}" autocomplete="off">
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-warning">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <template id="template-product">
        <tr class="product-item">
            <td>
                <input type="text" class="form-control serial" name="serial[]" autocomplete="off">
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control product_name" name="product_name[]" readonly>
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="number" step="any" class="form-control quantity" name="quantity[]">
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control unit_price" name="unit_price[]">
                </div>
            </td>

            <td class="total-cost">৳0.00</td>
            <td class="text-center">
                <a role="button" class="btn btn-danger btn-sm btn-remove">X</a>
            </td>
        </tr>
    </template>
@endsection

@section('script')
    <!-- Select2 -->
    <script src="{{ asset('themes/backend/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <!-- sweet alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script>
        $(function () {
            //Date picker
            $('#date, #next_payment').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            var message = '{{ session('message') }}';

            if (!window.performance || window.performance.navigation.type != window.performance.navigation.TYPE_BACK_FORWARD) {
                if (message != '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: message,
                    });
                }
            }

            var serials = [];

            $( ".serial" ).each(function( index ) {
                if ($(this).val() != '') {
                    serials.push($(this).val());
                }
            });

            $('#btn-add-product').click(function () {
                var html = $('#template-product').html();
                var item = $(html);

                $('#product-container').append(item);

                if ($('.product-item').length >= 1 ) {
                    $('.btn-remove').show();
                }
            });

            $('body').on('click', '.btn-remove', function () {
                var serial = $(this).closest('tr').find('.serial').val();
                $(this).closest('.product-item').remove();
                calculate();

                if ($('.product-item').length <= 1 ) {
                    $('.btn-remove').hide();
                }

                serials = $.grep(serials, function(value) {
                    return value != serial;
                });
            });

            $('body').on('keyup', '.quantity, .unit_price, #vat, #discount, #paid', function () {
                calculate();
            });

            $('body').on('change', '.quantity, .unit_price', function () {
                calculate();
            });

            if ($('.product-item').length <= 1 ) {
                $('.btn-remove').hide();
            } else {
                $('.btn-remove').show();
            }

            calculate();

            $('body').on('keypress', '.serial', function (e) {
                if (e.keyCode == 13) {
                    var warehouseId = $('#warehouse').val();
                    var serial = $(this).val();
                    $this = $(this);

                    if($.inArray(serial, serials) != -1) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Already exist in list.',
                        });

                        return false;
                    }

                    if (warehouseId == '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Select Warehouse.',
                        });
                    } else {
                        $.ajax({
                            method: "GET",
                            url: "{{ route('sale_product.details') }}",
                            data: { warehouseId: warehouseId, serial: serial }
                        }).done(function( response ) {
                            if (response.success) {
                                $this.closest('tr').find('.product_name').val(response.data.product.name);
                                $this.closest('tr').find('.quantity').val(1);
                                $this.closest('tr').find('.unit_price').val(response.data.selling_price);
                                serials.push(response.data.serial_no);
                                calculate();
                            } else {
                                $this.closest('tr').find('.product_name').val('');
                                $this.closest('tr').find('.quantity').val('');
                                $this.closest('tr').find('.unit_price').val('');
                                calculate();
                            }
                        });
                    }
                    return false; // prevent the button click from happening
                }
            });

        });

        function calculate() {
            var total = 0;

            var vat = $('#vat').val();
            var discount = $('#discount').val();
            var paid = $('#paid').val();

            if (vat == '' || vat < 0 || !$.isNumeric(vat))
                vat = 0;

            if (discount == '' || discount < 0 || !$.isNumeric(discount))
                discount = 0;

            if (paid == '' || paid < 0 || !$.isNumeric(paid))
                paid = 0;

            $('.product-item').each(function(i, obj) {
                var quantity = $('.quantity:eq('+i+')').val();
                var unit_price = $('.unit_price:eq('+i+')').val();


                if (quantity == '' || quantity < 0 || !$.isNumeric(quantity))
                    quantity = 0;

                if (unit_price == '' || unit_price < 0 || !$.isNumeric(unit_price))
                    unit_price = 0;

                $('.total-cost:eq('+i+')').html('৳' + (quantity * unit_price).toFixed(2) );
                total += quantity * unit_price;
            });

            var final = parseFloat(total) + parseFloat(vat) - parseFloat(discount);
            var due = parseFloat(final) - parseFloat(paid);

            $('#total-amount').html('৳' + total.toFixed(2));
            $('#final-amount').html('৳' + final.toFixed(2));
            $('#due').html('৳' + due.toFixed(2));

            if (due > 0) {
                $('#tr-next-payment').show();
            } else {
                $('#tr-next-payment').hide();
            }
        }
    </script>
@endsection
