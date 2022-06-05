@extends('layouts.app')

@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/select2/dist/css/select2.min.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Purchase Order
@endsection

@section('content')
    @if(Session::has('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ Session::get('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Order Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form method="POST" action="{{ route('purchase_order.create') }}">
                    @csrf

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('supplier') ? 'has-error' :'' }}">
                                    <label>Supplier</label>

                                    <select class="form-control select2" style="width: 100%;" name="supplier" data-placeholder="Select Supplier">
                                        <option value="">Select Supplier</option>

                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('supplier')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('warehouse') ? 'has-error' :'' }}">
                                    <label>Warehouse</label>

                                    <select class="form-control select2" style="width: 100%;" name="warehouse" data-placeholder="Select Warehouse">
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

                            <div class="col-md-4">
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
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Type</th>
                                        <th>Serial Number</th>
                                        <th width="10%">Warranty (Month)</th>
                                        <th width="10%">Quantity</th>
                                        <th width="10%">Unit Price</th>
                                        <th width="10%">Including Price</th>
                                        <th width="10%">Selling Price</th>
                                        <th>Total Cost</th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <tbody id="product-container">
                                @if (old('product') != null && sizeof(old('product')) > 0)
                                    @foreach(old('product') as $item)
                                        <tr class="product-item">
                                            <td>
                                                <div class="form-group {{ $errors->has('product.'.$loop->index) ? 'has-error' :'' }}">
                                                    <select class="form-control product" style="width: 100%;" name="product[]" required>
                                                        <option value="">Select Product</option>

                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" {{ old('product.'.$loop->parent->index) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>

                                            <td>
                                                <select class="form-control {{ $errors->has('type.'.$loop->index) ? 'has-error' :'' }} type" name="type[]">
                                                    <option value="2">Multiple</option>
                                                    <option value="1" {{ old('type.'.$loop->index) == '1' ? 'selected' : '' }}>Single</option>
                                                </select>
                                            </td>

                                            <td>
                                                <div class="form-group {{ $errors->has('serial.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control serial" name="serial[]" value="{{ old('serial.'.$loop->index) }}">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group {{ $errors->has('warranty.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control warranty" name="warranty[]" value="{{ old('warranty.'.$loop->index) }}">
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

                                            <td>
                                                <div class="form-group {{ $errors->has('including_price.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control including_price" name="including_price[]" value="{{ old('including_price.'.$loop->index) }}">
                                                </div>
                                            </td>

                                            <td>
                                                <div class="form-group {{ $errors->has('selling_price.'.$loop->index) ? 'has-error' :'' }}">
                                                    <input type="text" class="form-control selling_price" name="selling_price[]" value="{{ old('selling_price.'.$loop->index) }}">
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
                                            <div class="form-group">
                                                <select class="form-control product" style="width: 100%;" name="product[]" required>
                                                    <option value="">Select Product</option>

                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>

                                        <td>
                                            <select class="form-control type" name="type[]">
                                                <option value="2">Multiple</option>
                                                <option value="1">Single</option>
                                            </select>
                                        </td>

                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control serial" name="serial[]" value="CGSP{{ rand(10000, 99999) }}">
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control warranty" name="warranty[]">
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

                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control including_price" name="including_price[]">
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control selling_price" name="selling_price[]">
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
                                        <th colspan="7" class="text-right">Total Amount</th>
                                        <th id="total-amount">৳0.00</th>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Payment</h3>
                                </div>
                                <!-- /.box-header -->

                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Payment Type</label>
                                                <select class="form-control select2" id="modal-pay-type" name="payment_type">
                                                    <option value="1" {{ old('payment_type') == '1' ? 'selected' : '' }}>Cash</option>
                                                    <option value="2" {{ old('payment_type') == '2' ? 'selected' : '' }}>Bank</option>
                                                    <option value="3" {{ old('payment_type') == '3' ? 'selected' : '' }}>Mobile Banking</option>
                                                    <option value="4" {{ old('payment_type') == '4' ? 'selected' : '' }}> Condition (Cash) </option>
                                                </select>
                                            </div>

                                            <div id="modal-bank-info">
                                                <div class="form-group {{ $errors->has('bank') ? 'has-error' :'' }}">
                                                    <label>Bank</label>
                                                    <select class="form-control select2" id="modal-bank" name="bank">
                                                        <option value="">Select Bank</option>

                                                        @foreach($banks as $bank)
                                                            <option value="{{ $bank->id }}" {{ old('bank') == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group {{ $errors->has('branch') ? 'has-error' :'' }}">
                                                    <label>Branch</label>
                                                    <select class="form-control select2" id="modal-branch" name="branch">
                                                        <option value="">Select Branch</option>
                                                    </select>
                                                </div>

                                                <div class="form-group {{ $errors->has('account') ? 'has-error' :'' }}">
                                                    <label>Account</label>
                                                    <select class="form-control select2" id="modal-account" name="account">
                                                        <option value="">Select Account</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>Cheque No.</label>
                                                    <input class="form-control" type="text" name="cheque_no" placeholder="Enter Cheque No." value="{{ old('cheque_no') }}">
                                                </div>

                                                <div class="form-group {{ $errors->has('cheque_image') ? 'has-error' :'' }}">
                                                    <label>Cheque Image</label>
                                                    <input class="form-control" name="cheque_image" type="file">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                            <tr>
                                                <th colspan="4" class="text-right"> Total </th>
                                                <th id="final-amount"> ৳0.00 </th>
                                            </tr>
                                            <tr>
                                                <th colspan="4" class="text-right"> Transport Cost *</th>
                                                <td>
                                                    <div class="form-group {{ $errors->has('transport_cost') ? 'has-error' :'' }}">
                                                        <input type="text" class="form-control" name="transport_cost" id="transport_cost" value="{{ old('transport_cost',0) }}" required>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th colspan="4" class="text-right"> Paid *</th>
                                                <td>
                                                    <div class="form-group {{ $errors->has('paid') ? 'has-error' :'' }}">
                                                        <input type="text" class="form-control" name="paid" id="paid" value="{{ old('paid',0) }}" required>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th colspan="4" class="text-right">Due</th>
                                                <th id="due">৳0.00</th>
                                            </tr>
                                            <tr>
                                                <th colspan="4" class="text-right"> Note </th>
                                                <td>
                                                    <div class="form-group {{ $errors->has('note') ? 'has-error' :'' }}">
                                                        <input type="text" class="form-control" name="note" id="note" value="{{ old('note') }}">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr id="tr-next-payment">
                                                <th colspan="4" class="text-right">Next Payment Date</th>
                                                <td>
                                                    <div class="form-group {{ $errors->has('next_payment') ? 'has-error' :'' }}">
                                                        <div class="input-group date">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="text" class="form-control pull-right" id="next_payment" name="next_payment" value="{{ old('next_payment', date('Y-m-d', strtotime('+1 month'))) }}" autocomplete="off">
                                                        </div>
                                                        <!-- /.input group -->
                                                    </div>
                                                </td>
                                            </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <input type="hidden" name="total" id="total">
                        <button type="submit" class="btn btn-warning">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <template id="template-product">
        <tr class="product-item">
            <td>
                <div class="form-group">
                    <select class="form-control product" style="width: 100%;" name="product[]" required>
                        <option value="">Select Product</option>

                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
            </td>

            <td>
                <select class="form-control type" name="type[]">
                    <option value="2">Multiple</option>
                    <option value="1">Single</option>
                </select>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control serial" name="serial[]">
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control warranty" name="warranty[]">
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

            <td>
                <div class="form-group">
                    <input type="text" class="form-control including_price" name="including_price[]">
                </div>
            </td>

            <td>
                <div class="form-group">
                    <input type="text" class="form-control selling_price" name="selling_price[]">
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
    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2();
            $('.product').select2();

            //Date picker
            $('#date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
            $('#next_payment').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                startDate: new Date()
            });

            $('#btn-add-product').click(function () {
                var html = $('#template-product').html();
                var item = $(html);

                item.find('.serial').val('CGSP' + Math.floor((Math.random() * 100000)));
                $('#product-container').append(item);

                initProduct();

                if ($('.product-item').length >= 1 ) {
                    $('.btn-remove').show();
                }

                $('.type').trigger('change');
            });

            $('body').on('click', '.btn-remove', function () {
                $(this).closest('.product-item').remove();
                calculate();

                if ($('.product-item').length <= 1 ) {
                    $('.btn-remove').hide();
                }
            });

            $('body').on('keyup', '.quantity, .unit_price,#paid', function () {
                calculate();
            });

            if ($('.product-item').length <= 1 ) {
                $('.btn-remove').hide();
            } else {
                $('.btn-remove').show();
            }

            $('body').on('change', '.type', function () {
                var type = $(this).val();
                var count = $(this).closest('tr').find('.quantity').val();
                var productId = $(this).closest('tr').find('.product').val();
                var warranty = $(this).closest('tr').find('.warranty').val();
                var unitPrice = $(this).closest('tr').find('.unit_price').val();
                var includingPrice = $(this).closest('tr').find('.including_price').val();
                var sellingPrice = $(this).closest('tr').find('.selling_price').val();

                if (type == '1') {
                    $(this).closest('tr').find('.quantity').val('1');
                    $(this).closest('tr').find('.quantity').prop('readonly', true);

                    if (count > 1) {
                        for(i=1; i<count; i++) {
                            var html = $('#template-product').html();
                            var item = $(html);

                            item.find('.product').val(productId);
                            item.find('.serial').val('CGSP' + Math.floor((Math.random() * 100000)));
                            item.find('.type').val('1');
                            item.find('.warranty').val(warranty);
                            item.find('.unit_price').val(unitPrice);
                            item.find('.including_price').val(includingPrice);
                            item.find('.selling_price').val(sellingPrice);
                            $('#product-container').append(item);

                            initProduct();
                        }

                        $('.type').trigger('change');
                        calculate();
                    }
                } else {
                    $(this).closest('tr').find('.quantity').prop('readonly', false);
                }
            });

            $('.type').trigger('change');
            initProduct();

            // Payment Information
             $('#modal-pay-type').change(function () {
                if ($(this).val() == '1' || $(this).val() == '3' || $(this).val() == '4') {
                    $('#modal-bank-info').hide();
                } else {
                    $('#modal-bank-info').show();
                }
            });

            $('#modal-pay-type').trigger('change');

            var selectedBranch = '{{ old('branch') }}';
            var selectedAccount = '{{ old('account') }}';

            $('#modal-bank').change(function () {
                var bankId = $(this).val();
                $('#modal-branch').html('<option value="">Select Branch</option>');
                $('#modal-account').html('<option value="">Select Account</option>');

                if (bankId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_branch') }}",
                        data: { bankId: bankId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            if (selectedBranch == item.id)
                                $('#modal-branch').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#modal-branch').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });

                        $('#modal-branch').trigger('change');
                    });
                }

                $('#modal-branch').trigger('change');
            });

            $('#modal-branch').change(function () {
                var branchId = $(this).val();
                $('#modal-account').html('<option value="">Select Account</option>');

                if (branchId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_bank_account') }}",
                        data: { branchId: branchId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            if (selectedAccount == item.id)
                                $('#modal-account').append('<option value="'+item.id+'" selected>'+item.account_no+'</option>');
                            else
                                $('#modal-account').append('<option value="'+item.id+'">'+item.account_no+'</option>');
                        });
                    });
                }
            });

            $('#modal-bank').trigger('change');

            calculate();
        });

        function calculate() {
            var total = 0;
            var paid = $('#paid').val() || 0;

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
            var due = parseFloat(total)-parseFloat(paid);
            $('#final-amount').html('৳' + total.toFixed(2));
            $('#total-amount').html('৳' + total.toFixed(2));
            $('#total').val(total);
            $('#due').html('৳' + due.toFixed(2));

            if (due > 0) {
                $('#tr-next-payment').show();
            } else {
                $('#tr-next-payment').hide();
            }
        }

        function initProduct() {
            $('.product').select2();
        }

        $(document).ready(function() {
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>
@endsection
