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
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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
                                                    <select class="form-control select2 product" style="width: 100%;" name="product[]" data-placeholder="Select Product" required>
                                                        <option value="">Select Product</option>

                                                        @if (old('product.'.$loop->index) != '')
                                                            <option value="{{ old('product.'.$loop->index) }}" selected>{{ old('product-name.'.$loop->index) }}</option>
                                                        @endif
                                                    </select>

                                                    <input type="hidden" name="product-name[]" class="product-name" value="{{ old('product-name.'.$loop->index) }}">
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
                                                <select class="form-control select2 product" style="width: 100%;" name="product[]" data-placeholder="Select Product" required>
                                                    <option value="">Select Product</option>

                                                </select>

                                                <input type="hidden" name="product-name[]" class="product-name">
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
                                        <th colspan="6">Total Amount</th>
                                        <th id="total-amount">৳0.00</th>
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
                <div class="form-group">
                    <select class="form-control select2 product" style="width: 100%;" name="product[]" data-placeholder="Select Product" required>
                        <option value="">Select Product</option>

                    </select>

                    <input type="hidden" name="product-name[]" class="product-name">
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

            //Date picker
            $('#date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
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

            $('body').on('keyup', '.quantity, .unit_price', function () {
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

                if (type == '1') {
                    $(this).closest('tr').find('.quantity').val('1');
                    $(this).closest('tr').find('.quantity').prop('readonly', true);

                    if (count > 1) {
                        for(i=1; i<count; i++) {
                            var html = $('#template-product').html();
                            var item = $(html);

                            item.find('.serial').val('CGSP' + Math.floor((Math.random() * 100000)));
                            $('#product-container').append(item);

                            initProduct();
                            item.find('.product').select2('data', {id: '123', text: 'res_data.primary_email'});
                        }
                    }
                } else {
                    $(this).closest('tr').find('.quantity').prop('readonly', false);
                }
            });

            $('.type').trigger('change');
            initProduct();
            calculate();
        });

        function calculate() {
            var total = 0;

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

            $('#total-amount').html('৳' + total.toFixed(2));
        }

        function initProduct() {
            $('.product').select2({
                ajax: {
                    url: "{{ route('purchase_product.json') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });

            $('.product').on('select2:select', function (e) {
                var data = e.params.data;

                var index = $(".product").index(this);
                $('.product-name:eq('+index+')').val(data.text);
            });
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
