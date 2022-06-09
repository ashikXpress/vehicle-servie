<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');

    // Warehouse
    Route::get('warehouse', 'WarehouseController@index')->name('warehouse')->middleware('permission:warehouse');
    Route::get('warehouse/add', 'WarehouseController@add')->name('warehouse.add')->middleware('permission:warehouse');
    Route::post('warehouse/add', 'WarehouseController@addPost')->middleware('permission:warehouse');
    Route::get('warehouse/edit/{warehouse}', 'WarehouseController@edit')->name('warehouse.edit')->middleware('permission:warehouse');
    Route::post('warehouse/edit/{warehouse}', 'WarehouseController@editPost')->middleware('permission:warehouse');

    // Bank
    Route::get('bank', 'BankController@index')->name('bank')->middleware('permission:bank');
    Route::get('bank/add', 'BankController@add')->name('bank.add')->middleware('permission:bank');
    Route::post('bank/add', 'BankController@addPost')->middleware('permission:bank');
    Route::get('bank/edit/{bank}', 'BankController@edit')->name('bank.edit')->middleware('permission:bank');
    Route::post('bank/edit/{bank}', 'BankController@editPost')->middleware('permission:bank');

    // Bank Branch
    Route::get('bank-branch', 'BranchController@index')->name('branch')->middleware('permission:branch');
    Route::get('bank-branch/add', 'BranchController@add')->name('branch.add')->middleware('permission:branch');
    Route::post('bank-branch/add', 'BranchController@addPost')->middleware('permission:branch');
    Route::get('bank-branch/edit/{branch}', 'BranchController@edit')->name('branch.edit')->middleware('permission:branch');
    Route::post('bank-branch/edit/{branch}', 'BranchController@editPost')->middleware('permission:branch');

    // Bank Account
    Route::get('bank-account', 'BankAccountController@index')->name('bank_account')->middleware('permission:account');
    Route::get('bank-account/add', 'BankAccountController@add')->name('bank_account.add')->middleware('permission:account');
    Route::post('bank-account/add', 'BankAccountController@addPost')->middleware('permission:account');
    Route::get('bank-account/edit/{account}', 'BankAccountController@edit')->name('bank_account.edit')->middleware('permission:account');
    Route::post('bank-account/edit/{account}', 'BankAccountController@editPost')->middleware('permission:account');
    Route::get('bank-account/get-branches', 'BankAccountController@getBranches')->name('bank_account.get_branch')->middleware('permission:account');

    // Department
    Route::get('department', 'DepartmentController@index')->name('department')->middleware('permission:department');
    Route::get('department/add', 'DepartmentController@add')->name('department.add')->middleware('permission:department');
    Route::post('department/add', 'DepartmentController@addPost')->middleware('permission:department');
    Route::get('department/edit/{department}', 'DepartmentController@edit')->name('department.edit')->middleware('permission:department');
    Route::post('department/edit/{department}', 'DepartmentController@editPost')->middleware('permission:department');

    // Designation
    Route::get('designation', 'DesignationController@index')->name('designation')->middleware('permission:designation');
    Route::get('designation/add', 'DesignationController@add')->name('designation.add')->middleware('permission:designation');
    Route::post('designation/add', 'DesignationController@addPost')->middleware('permission:designation');
    Route::get('designation/edit/{designation}', 'DesignationController@edit')->name('designation.edit')->middleware('permission:designation');
    Route::post('designation/edit/{designation}', 'DesignationController@editPost')->middleware('permission:designation');

    // HR
    Route::get('employee', 'HRController@employeeIndex')->name('employee.all')->middleware('permission:employee');
    Route::get('employee/datatable', 'HRController@employeeDatatable')->name('employee.datatable');
    Route::get('employee/add', 'HRController@employeeAdd')->name('employee.add')->middleware('permission:employee');
    Route::post('employee/add', 'HRController@employeeAddPost')->middleware('permission:employee');
    Route::get('employee/edit/{employee}', 'HRController@employeeEdit')->name('employee.edit')->middleware('permission:employee');
    Route::post('employee/edit/{employee}', 'HRController@employeeEditPost')->middleware('permission:employee');
    Route::get('employee/details/{employee}', 'HRController@employeeDetails')->name('employee.details')->middleware('permission:employee');
    Route::post('employee/designation/update', 'HRController@employeeDesignationUpdate')->name('employee.designation_update');
    Route::post('payroll/get-leave', 'HRController@getLeave')->name('employee.get_leaves');

    // Payroll - Salary Update
    Route::get('payroll/salary-update', 'PayrollController@salaryUpdateIndex')->name('payroll.salary_update.index')->middleware('permission:salary_update');
    Route::post('payroll/salary-update/update', 'PayrollController@salaryUpdatePost')->name('payroll.salary_update.post');
    Route::get('payroll/salary-update/datatable', 'PayrollController@salaryUpdateDatatable')->name('payroll.salary_update.datatable');

    // Payroll - Salary Process
    Route::get('payroll/salary-process', 'PayrollController@salaryProcessIndex')->name('payroll.salary_process.index')->middleware('permission:salary_process');
    Route::post('payroll/salary-process', 'PayrollController@salaryProcessPost')->middleware('permission:salary_process');

    // Payroll - Leave
    Route::get('payroll/leave', 'PayrollController@leaveIndex')->name('payroll.leave.index')->middleware('permission:leave');
    Route::post('payroll/leave', 'PayrollController@leavePost')->middleware('permission:leave');



    // Supplier
    Route::get('supplier', 'SupplierController@index')->name('supplier')->middleware('permission:supplier');
    Route::get('supplier/add', 'SupplierController@add')->name('supplier.add')->middleware('permission:supplier');
    Route::post('supplier/add', 'SupplierController@addPost')->middleware('permission:supplier');
    Route::get('supplier/edit/{supplier}', 'SupplierController@edit')->name('supplier.edit')->middleware('permission:supplier');
    Route::post('supplier/edit/{supplier}', 'SupplierController@editPost')->middleware('permission:supplier');

    // Purchase Product
    Route::get('purchase-product', 'PurchaseProductController@index')->name('purchase_product')->middleware('permission:purchase_product');
    Route::get('purchase-product/add', 'PurchaseProductController@add')->name('purchase_product.add')->middleware('permission:purchase_product');
    Route::post('purchase-product/add', 'PurchaseProductController@addPost')->middleware('permission:purchase_product');
    Route::get('purchase-product/edit/{product}', 'PurchaseProductController@edit')->name('purchase_product.edit')->middleware('permission:purchase_product');
    Route::post('purchase-product/edit/{product}', 'PurchaseProductController@editPost')->middleware('permission:purchase_product');

    // Purchase Order
    Route::get('purchase-order', 'PurchaseController@purchaseOrder')->name('purchase_order.create')->middleware('permission:purchase_order');
    Route::post('purchase-order', 'PurchaseController@purchaseOrderPost')->middleware('permission:purchase_order');
    Route::get('purchase-product-json', 'PurchaseController@purchaseProductJson')->name('purchase_product.json');

    // Purchase Receipt
    Route::get('purchase-receipt', 'PurchaseController@purchaseReceipt')->name('purchase_receipt.all')->middleware('permission:purchase_receipt');
    Route::get('purchase-receipt/details/{order}', 'PurchaseController@purchaseReceiptDetails')->name('purchase_receipt.details');
    Route::get('purchase-receipt/print/{order}', 'PurchaseController@purchaseReceiptPrint')->name('purchase_receipt.print');
    Route::get('purchase-receipt/datatable', 'PurchaseController@purchaseReceiptDatatable')->name('purchase_receipt.datatable');
    Route::get('purchase-receipt/payment/details/{payment}', 'PurchaseController@purchasePaymentDetails')->name('purchase_receipt.payment_details');
    Route::get('purchase-receipt/payment/print/{payment}', 'PurchaseController@purchasePaymentPrint')->name('purchase_receipt.payment_print');
    Route::get('purchase-receipt/qr-code/{order}', 'PurchaseController@qrCode')->name('purchase_receipt.qr_code');
    Route::get('purchase-receipt/qr-code/print/{order}', 'PurchaseController@qrCodePrint')->name('purchase_receipt.qr_code_print');
    Route::get('purchase-receipt/edit/{order}', 'PurchaseController@purchaseReceiptEdit')->name('purchase_receipt.edit');
    Route::post('purchase-receipt/edit/{order}', 'PurchaseController@purchaseReceiptEditPost');
    Route::get('purchase-receipt/check-delete-status', 'PurchaseController@checkDeleteStatus')->name('purchase_receipt.check_delete_status');

    // Supplier Payment
    Route::get('supplier-payment', 'PurchaseController@supplierPayment')->name('supplier_payment.all')->middleware('permission:purchase_inventory');
    Route::get('supplier-payment/get-orders', 'PurchaseController@supplierPaymentGetOrders')->name('supplier_payment.get_orders');
    Route::get('supplier-payment/get-refund-orders', 'PurchaseController@supplierPaymentGetRefundOrders')->name('supplier_payment.get_refund_orders');
    Route::get('supplier-payment/order-details', 'PurchaseController@supplierPaymentOrderDetails')->name('supplier_payment.order_details');
    Route::post('supplier-payment/payment', 'PurchaseController@makePayment')->name('supplier_payment.make_payment');
    Route::post('supplier-payment/refund', 'PurchaseController@makeRefund')->name('supplier_payment.make_refund');

    // Purchase Inventory
    Route::get('purchase-inventory', 'PurchaseController@purchaseInventory')->name('purchase_inventory.all')->middleware('permission:purchase_inventory');
    Route::get('purchase-inventory/datatable', 'PurchaseController@purchaseInventoryDatatable')->name('purchase_inventory.datatable');
    Route::get('purchase-inventory/details/datatable', 'PurchaseController@purchaseInventoryDetailsDatatable')->name('purchase_inventory.details.datatable');
    Route::get('purchase-inventory/details/{product}/{warehouse}', 'PurchaseController@purchaseInventoryDetails')->name('purchase_inventory.details')->middleware('permission:purchase_inventory');
    Route::get('purchase-inventory/qr-code/{product}/{warehouse}', 'PurchaseController@purchaseInventoryQrCode')->name('purchase_inventory.qr_code')->middleware('permission:purchase_inventory');

    // Customer
    Route::get('customer', 'CustomerController@index')->name('customer')->middleware('permission:customer');
    Route::get('customer/add', 'CustomerController@add')->name('customer.add')->middleware('permission:customer');
    Route::post('customer/add', 'CustomerController@addPost')->middleware('permission:customer');
    Route::get('customer/edit/{customer}', 'CustomerController@edit')->name('customer.edit')->middleware('permission:customer');
    Route::post('customer/edit/{customer}', 'CustomerController@editPost')->middleware('permission:customer');
    Route::get('customer/datatable', 'CustomerController@datatable')->name('customer.datatable')->middleware('permission:customer');

    // Sales Order
    Route::get('sales-order', 'SaleController@salesOrder')->name('sales_order.create')->middleware('permission:sales_order');
    Route::post('sales-order', 'SaleController@salesOrderPost')->middleware('permission:sales_order');
    Route::get('sale-order/product/details', 'SaleController@saleProductDetails')->name('sale_product.details');

    // Sale Receipt
    Route::get('sale-receipt', 'SaleController@saleReceipt')->name('sale_receipt.all')->middleware('permission:sale_receipt');
    Route::get('sale-receipt/details/{order}', 'SaleController@saleReceiptDetails')->name('sale_receipt.details');
    Route::get('sale-receipt/print/{order}', 'SaleController@saleReceiptPrint')->name('sale_receipt.print');
    Route::get('sale-receipt/datatable', 'SaleController@saleReceiptDatatable')->name('sale_receipt.datatable');
    Route::post('sale-receipt/payment', 'SaleController@makePayment')->name('sale_receipt.make_payment');
    Route::get('sale-receipt/payment/details/{payment}', 'SaleController@salePaymentDetails')->name('sale_receipt.payment_details');
    Route::get('sale-receipt/payment/print/{payment}', 'SaleController@salePaymentPrint')->name('sale_receipt.payment_print');
    Route::get('sale-receipt/edit/{order}', 'SaleController@saleReceiptEdit')->name('sale_receipt.edit');
    Route::post('sale-receipt/edit/{order}', 'SaleController@saleReceiptEditPost');

    // Customer Payment
    Route::get('customer-payment', 'SaleController@customerPayment')->name('customer_payment.all')->middleware('permission:customer_payment');
    Route::get('customer-payment/datatable', 'SaleController@customerPaymentDatatable')->name('customer_payment.datatable');
    Route::get('customer-payment/get-orders', 'SaleController@customerPaymentGetOrders')->name('customer_payment.get_orders');
    Route::get('customer-payment/get-refund-orders', 'SaleController@customerPaymentGetRefundOrders')->name('customer_payment.get_refund_orders');
    Route::post('customer-payment/payment', 'SaleController@customerMakePayment')->name('customer_payment.make_payment');
    Route::post('customer-payment/refund', 'SaleController@customerMakeRefund')->name('customer_payment.make_refund');

    // Product Sale Information
    Route::get('sale-information', 'SaleController@saleInformation')->name('sale_information.index')->middleware('permission:product_sale_information');
    Route::post('sale-information/post', 'SaleController@saleInformationPost')->name('sale_information.post')->middleware('permission:product_sale_information');
    Route::get('sale-information/print/{purchaseOrder}/{saleOrder}', 'SaleController@saleInformationPrint')->name('sale_information.print')->middleware('permission:product_sale_information');

    // Account Head Type
    Route::get('account-head/type', 'AccountsController@accountHeadType')->name('account_head.type')->middleware('permission:account_head_type');
    Route::get('account-head/type/add', 'AccountsController@accountHeadTypeAdd')->name('account_head.type.add')->middleware('permission:account_head_type');
    Route::post('account-head/type/add', 'AccountsController@accountHeadTypeAddPost')->middleware('permission:account_head_type');
    Route::get('account-head/type/edit/{type}', 'AccountsController@accountHeadTypeEdit')->name('account_head.type.edit')->middleware('permission:account_head_type');
    Route::post('account-head/type/edit/{type}', 'AccountsController@accountHeadTypeEditPost')->middleware('permission:account_head_type');

    // Account Head Sub Type
    Route::get('account-head/sub-type', 'AccountsController@accountHeadSubType')->name('account_head.sub_type')->middleware('permission:account_head_sub_type');
    Route::get('account-head/sub-type/add', 'AccountsController@accountHeadSubTypeAdd')->name('account_head.sub_type.add')->middleware('permission:account_head_sub_type');
    Route::post('account-head/sub-type/add', 'AccountsController@accountHeadSubTypeAddPost')->middleware('permission:account_head_sub_type');
    Route::get('account-head/sub-type/edit/{subType}', 'AccountsController@accountHeadSubTypeEdit')->name('account_head.sub_type.edit')->middleware('permission:account_head_sub_type');
    Route::post('account-head/sub-type/edit/{subType}', 'AccountsController@accountHeadSubTypeEditPost')->middleware('permission:account_head_sub_type');

    // Transaction
    Route::get('transaction', 'AccountsController@transactionIndex')->name('transaction.all')->middleware('permission:transaction');
    Route::get('transaction/datatable', 'AccountsController@transactionDatatable')->name('transaction.datatable');
    Route::get('transaction/add', 'AccountsController@transactionAdd')->name('transaction.add')->middleware('permission:transaction');
    Route::post('transaction/add', 'AccountsController@transactionAddPost')->middleware('permission:transaction');
    Route::post('transaction/edit', 'AccountsController@transactionEditPost')->name('transaction.edit_post')->middleware('permission:transaction');
    Route::get('transaction/details/json', 'AccountsController@transactionDetailsJson')->name('transaction.details_json');
    Route::get('transaction/details/{transaction}', 'AccountsController@transactionDetails')->name('transaction.details');
    Route::get('transaction/print/{transaction}', 'AccountsController@transactionPrint')->name('transaction.print');

    // Balance Transfer
    Route::get('balance-transfer/add', 'AccountsController@balanceTransferAdd')->name('balance_transfer.add')->middleware('permission:balance_transfer');
    Route::post('balance-transfer/add', 'AccountsController@balanceTransferAddPost')->middleware('permission:balance_transfer');

    // Report
    Route::get('report/purchase', 'ReportController@purchase')->name('report.purchase')->middleware('permission:purchase_report');
    Route::get('report/sale', 'ReportController@sale')->name('report.sale')->middleware('permission:sale_report');
    Route::get('report/balance-sheet', 'ReportController@balanceSheet')->name('report.balance_sheet')->middleware('permission:balance_sheet');
    Route::get('report/profit-and-loss', 'ReportController@profitAndLoss')->name('report.profit_and_loss')->middleware('permission:profit_and_loss');
    Route::get('report/ledger', 'ReportController@ledger')->name('report.ledger')->middleware('permission:ledger');
    Route::get('report/transaction', 'ReportController@transaction')->name('report.transaction')->middleware('permission:transaction_report');

    // User Management
    Route::get('user', 'UserController@index')->name('user.all')->middleware('permission:users');
    Route::get('user/add', 'UserController@add')->name('user.add')->middleware('permission:users');
    Route::post('user/add', 'UserController@addPost')->middleware('permission:users');
    Route::get('user/edit/{user}', 'UserController@edit')->name('user.edit')->middleware('permission:users');
    Route::post('user/edit/{user}', 'UserController@editPost')->middleware('permission:users');

    // Common
    Route::get('get-branch', 'CommonController@getBranch')->name('get_branch');
    Route::get('get-bank-account', 'CommonController@getBankAccount')->name('get_bank_account');
    Route::get('get-bank-account-balance', 'CommonController@getBankAccountBalance')->name('get_bank_account_balance');
    Route::get('order-details', 'CommonController@orderDetails')->name('get_order_details');
    Route::get('get-account-head-type', 'CommonController@getAccountHeadType')->name('get_account_head_type');
    Route::get('get-account-head-sub-type', 'CommonController@getAccountHeadSubType')->name('get_account_head_sub_type');
    Route::get('get-serial-suggestion', 'CommonController@getSerialSuggestion')->name('get_serial_suggestion');
    Route::get('get-designation', 'CommonController@getDesignation')->name('get_designation');
    Route::get('get-employee-details', 'CommonController@getEmployeeDetails')->name('get_employee_details');
    Route::get('get-month', 'CommonController@getMonth')->name('get_month');
    Route::get('get-processed-month', 'CommonController@getProcessedMonth')->name('get_processed_month');

    //Route::get('vat-correction', 'CommonController@vatCorrection');
});

//php artisan cache:forget spatie.permission.cache


require __DIR__.'/auth.php';
