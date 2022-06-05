<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!--Favicon-->
    <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon" />

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/Ionicons/css/ionicons.min.css') }}">

    @yield('style')

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('themes/backend/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('themes/backend/css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/backend/css/custom.css') }}">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition skin-yellow sidebar-mini fixed">
<div class="wrapper">
    <header class="main-header">
        <!-- Logo -->
        <a href="{{ route('dashboard') }}" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>AP</b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Admin</b>Panel</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <h4 class="pull-left" style="color: white; margin-top: 15px; padding-left: 20px">{{ config('app.name') }}</h4>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- Notifications: style can be found in dropdown.less -->
                    <li class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bell-o"></i>
                            <span class="label label-warning">{{ count($layoutData['nextPayments']) + count($layoutData['stocks']) }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have {{ count($layoutData['nextPayments']) + count($layoutData['stocks']) }} notifications</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    @foreach($layoutData['nextPayments'] as $nextPayment)
                                    <li>
                                        <a href="{{ route('sale_receipt.details', ['order' => $nextPayment->id]) }}">
                                            <i class="fa fa-dollar text-success"></i> Order No. {{ $nextPayment->order_no }} payment date
                                        </a>
                                    </li>
                                    @endforeach

                                    @foreach($layoutData['stocks'] as $stock)
                                    <li>
                                        <a href="{{ route('purchase_inventory.all') }}" title="{{ $stock->product->name }} stock {{ $stock->quantity }} pcs in {{ $stock->warehouse->name }}">
                                            <i class="fa fa-calculator text-warning"></i> {{ $stock->product->name }} stock {{ $stock->quantity }} pcs in {{ $stock->warehouse->name }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="{{ asset('img/avatar.png') }}" class="user-image" alt="Avatar">
                            <span class="hidden-xs">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-right">
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();" class="btn btn-default btn-flat">Sign out</a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu" data-widget="tree">
                <li class="header">MAIN NAVIGATION</li>

                <?php
                $subMenu = ['warehouse', 'warehouse.add', 'warehouse.edit'];
                ?>

                @can('administrator')
                <li class="treeview {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-circle-o text-yellow"></i> <span>Administrator</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu {{ in_array(Route::currentRouteName(), $subMenu) ? 'active menu-open' : '' }}">
                        @can('warehouse')
                        <li class="{{ Route::currentRouteName() == 'warehouse' ? 'active' : '' }}">
                            <a href="{{ route('warehouse') }}"><i class="fa fa-circle-o"></i> Warehouse</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                <?php
                $subMenu = ['bank', 'bank.add', 'bank.edit', 'branch', 'branch.add', 'branch.edit',
                    'bank_account', 'bank_account.add', 'bank_account.edit'];
                ?>

                @can('bank_and_account')
                <li class="treeview {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-circle-o text-yellow"></i> <span>Bank & Account</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu {{ in_array(Route::currentRouteName(), $subMenu) ? 'active menu-open' : '' }}">
                        @can('bank')
                        <li class="{{ Route::currentRouteName() == 'bank' ? 'active' : '' }}">
                            <a href="{{ route('bank') }}"><i class="fa fa-circle-o"></i> Bank</a>
                        </li>
                        @endcan

                        @can('branch')
                        <li class="{{ Route::currentRouteName() == 'branch' ? 'active' : '' }}">
                            <a href="{{ route('branch') }}"><i class="fa fa-circle-o"></i> Branch</a>
                        </li>
                        @endcan

                        @can('account')
                        <li class="{{ Route::currentRouteName() == 'bank_account' ? 'active' : '' }}">
                            <a href="{{ route('bank_account') }}"><i class="fa fa-circle-o"></i> Account</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                <?php
                $subMenu = ['supplier', 'supplier.add', 'supplier.edit', 'purchase_product', 'purchase_product.add',
                    'purchase_product.edit', 'purchase_order.create', 'purchase_receipt.all',
                    'purchase_receipt.details', 'purchase_receipt.qr_code', 'supplier_payment.all',
                    'purchase_receipt.payment_details', 'purchase_inventory.all',
                    'purchase_inventory.details', 'purchase_inventory.qr_code',
                    'purchase_receipt.edit'];
                ?>

                @can('purchase')
                <li class="treeview {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-circle-o text-yellow"></i> <span>Purchase</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu {{ in_array(Route::currentRouteName(), $subMenu) ? 'active menu-open' : '' }}">
                        @can('supplier')
                        <li class="{{ Route::currentRouteName() == 'supplier' ? 'active' : '' }}">
                            <a href="{{ route('supplier') }}"><i class="fa fa-circle-o"></i> Supplier</a>
                        </li>
                        @endcan

                        @can('purchase_product')
                        <li class="{{ Route::currentRouteName() == 'purchase_product' ? 'active' : '' }}">
                            <a href="{{ route('purchase_product') }}"><i class="fa fa-circle-o"></i> Product</a>
                        </li>
                        @endcan

                        @can('purchase_order')
                        <li class="{{ Route::currentRouteName() == 'purchase_order.create' ? 'active' : '' }}">
                            <a href="{{ route('purchase_order.create') }}"><i class="fa fa-circle-o"></i> Purchase Order</a>
                        </li>
                        @endcan

                        @can('purchase_receipt')
                        <li class="{{ Route::currentRouteName() == 'purchase_receipt.all' ? 'active' : '' }}">
                            <a href="{{ route('purchase_receipt.all') }}"><i class="fa fa-circle-o"></i> Receipt</a>
                        </li>
                        @endcan

                        @can('supplier_payment')
                        <li class="{{ Route::currentRouteName() == 'supplier_payment.all' ? 'active' : '' }}">
                            <a href="{{ route('supplier_payment.all') }}"><i class="fa fa-circle-o"></i> Supplier Payment</a>
                        </li>
                        @endcan

                        @can('purchase_inventory')
                        <li class="{{ Route::currentRouteName() == 'purchase_inventory.all' ? 'active' : '' }}">
                            <a href="{{ route('purchase_inventory.all') }}"><i class="fa fa-circle-o"></i> Inventory</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                <?php
                $subMenu = ['sales_order.create', 'sale_receipt.all', 'sale_receipt.details',
                    'sale_receipt.payment_details', 'customer', 'customer.add',
                    'customer.edit', 'sale_information.index', 'customer_payment.all',
                    'sale_receipt.edit'];
                ?>

                @can('sale')
                <li class="treeview {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-circle-o text-yellow"></i> <span>Sale</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu {{ in_array(Route::currentRouteName(), $subMenu) ? 'active menu-open' : '' }}">
                        @can('customer')
                        <li class="{{ Route::currentRouteName() == 'customer' ? 'active' : '' }}">
                            <a href="{{ route('customer') }}"><i class="fa fa-circle-o"></i> Customer</a>
                        </li>
                        @endcan

                        @can('sales_order')
                        <li class="{{ Route::currentRouteName() == 'sales_order.create' ? 'active' : '' }}">
                            <a href="{{ route('sales_order.create') }}"><i class="fa fa-circle-o"></i> Sales Order</a>
                        </li>
                        @endcan

                        @can('sale_receipt')
                        <li class="{{ Route::currentRouteName() == 'sale_receipt.all' ? 'active' : '' }}">
                            <a href="{{ route('sale_receipt.all') }}"><i class="fa fa-circle-o"></i> Receipt</a>
                        </li>
                        @endcan

                        @can('product_sale_information')
                        <li class="{{ Route::currentRouteName() == 'sale_information.index' ? 'active' : '' }}">
                            <a href="{{ route('sale_information.index') }}"><i class="fa fa-circle-o"></i> Product Sale Information</a>
                        </li>
                        @endcan

                        @can('customer_payment')
                        <li class="{{ Route::currentRouteName() == 'customer_payment.all' ? 'active' : '' }}">
                            <a href="{{ route('customer_payment.all') }}"><i class="fa fa-circle-o"></i> Customer Payment</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                <?php
                $subMenu = ['account_head.type', 'account_head.type.add', 'account_head.type.edit',
                    'account_head.sub_type', 'account_head.sub_type.add', 'account_head.sub_type.edit',
                    'transaction.all', 'transaction.add', 'transaction.details', 'balance_transfer.add'];
                ?>

                @can('accounts')
                <li class="treeview {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-circle-o text-yellow"></i> <span>Accounts</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu {{ in_array(Route::currentRouteName(), $subMenu) ? 'active menu-open' : '' }}">
                        @can('account_head_type')
                        <li class="{{ Route::currentRouteName() == 'account_head.type' ? 'active' : '' }}">
                            <a href="{{ route('account_head.type') }}"><i class="fa fa-circle-o"></i> Account Head Type</a>
                        </li>
                        @endcan

                        @can('account_head_sub_type')
                        <li class="{{ Route::currentRouteName() == 'account_head.sub_type' ? 'active' : '' }}">
                            <a href="{{ route('account_head.sub_type') }}"><i class="fa fa-circle-o"></i> Account Head Sub Type</a>
                        </li>
                        @endcan

                        @can('transaction')
                        <li class="{{ Route::currentRouteName() == 'transaction.all' ? 'active' : '' }}">
                            <a href="{{ route('transaction.all') }}"><i class="fa fa-circle-o"></i> Transaction</a>
                        </li>
                        @endcan

                        @can('balance_transfer')
                        <li class="{{ Route::currentRouteName() == 'balance_transfer.add' ? 'active' : '' }}">
                            <a href="{{ route('balance_transfer.add') }}"><i class="fa fa-circle-o"></i> Balance Transfer</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                <?php
                $subMenu = ['report.purchase', 'report.sale', 'report.balance_sheet',
                    'report.profit_and_loss', 'report.ledger', 'report.transaction'];
                ?>

                @can('report')
                <li class="treeview {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-circle-o text-yellow"></i> <span>Report</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu {{ in_array(Route::currentRouteName(), $subMenu) ? 'active menu-open' : '' }}">
                        @can('purchase_report')
                        <li class="{{ Route::currentRouteName() == 'report.purchase' ? 'active' : '' }}">
                            <a href="{{ route('report.purchase') }}"><i class="fa fa-circle-o"></i> Purchase Report</a>
                        </li>
                        @endcan

                        @can('sale_report')
                        <li class="{{ Route::currentRouteName() == 'report.sale' ? 'active' : '' }}">
                            <a href="{{ route('report.sale') }}"><i class="fa fa-circle-o"></i> Sale Report</a>
                        </li>
                        @endcan

                        @can('balance_sheet')
                        <li class="{{ Route::currentRouteName() == 'report.balance_sheet' ? 'active' : '' }}">
                            <a href="{{ route('report.balance_sheet') }}"><i class="fa fa-circle-o"></i> Balance Sheet</a>
                        </li>
                        @endcan

                        @can('profit_and_loss')
                        <li class="{{ Route::currentRouteName() == 'report.profit_and_loss' ? 'active' : '' }}">
                            <a href="{{ route('report.profit_and_loss') }}"><i class="fa fa-circle-o"></i> Profit & Loss</a>
                        </li>
                        @endcan

                        @can('ledger')
                        <li class="{{ Route::currentRouteName() == 'report.ledger' ? 'active' : '' }}">
                            <a href="{{ route('report.ledger') }}"><i class="fa fa-circle-o"></i> Ledger</a>
                        </li>
                        @endcan

                        @can('transaction_report')
                        <li class="{{ Route::currentRouteName() == 'report.transaction' ? 'active' : '' }}">
                            <a href="{{ route('report.transaction') }}"><i class="fa fa-circle-o"></i> Transaction Report</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                <?php
                $subMenu = ['user.all', 'user.edit', 'user.add'];
                ?>

                @can('user_management')
                <li class="treeview {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-circle-o text-yellow"></i> <span>User Management</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu {{ in_array(Route::currentRouteName(), $subMenu) ? 'active menu-open' : '' }}">
                        @can('users')
                        <li class="{{ Route::currentRouteName() == 'user.all' ? 'active' : '' }}">
                            <a href="{{ route('user.all') }}"><i class="fa fa-circle-o"></i> Users</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                @yield('title')
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            @yield('content')
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Design & Developed By <a target="_blank" href="#">Ashik Khan</a></b>
        </div>
        <strong>Copyright &copy; {{ date('Y') }} <a href="{{ route('dashboard') }}">{{ config('app.name') }}</a>.</strong> All rights
        reserved.
    </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="{{ asset('themes/backend/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('themes/backend/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>

<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>

@yield('script')
<!-- AdminLTE App -->
<script src="{{ asset('themes/backend/js/adminlte.min.js') }}"></script>
</body>
</html>
