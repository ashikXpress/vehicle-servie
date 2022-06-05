<?php

namespace App\Http\Controllers;

use App\Model\AccountHeadSubType;
use App\Model\AccountHeadType;
use App\Model\BankAccount;
use App\Model\Cash;
use App\Model\Customer;
use App\Model\MobileBanking;
use App\Model\PurchaseInventory;
use App\Model\PurchaseOrder;
use App\Model\PurchaseProduct;
use App\Model\SalesOrder;
use App\Model\Supplier;
use App\Model\TransactionLog;
use Illuminate\Http\Request;
use DB;

class ReportController extends Controller
{
    public function purchase(Request $request) {
        $suppliers = Supplier::orderBy('name')->get();
        $products = PurchaseProduct::orderBy('name')->get();
        $appends = [];
        $query = PurchaseOrder::query();

        if ($request->date && $request->date != '') {
            $dates = explode(' - ', $request->date);
            if (count($dates) == 2) {
                $query->whereBetween('date', [$dates[0], $dates[1]]);
                $appends['date'] = $request->date;
            }
        }

        if ($request->supplier && $request->supplier != '') {
            $query->where('supplier_id', $request->supplier);
            $appends['supplier'] = $request->supplier;
        }

        if ($request->purchaseId && $request->purchaseId != '') {
            $query->where('order_no', $request->purchaseId);
            $appends['purchaseId'] = $request->purchaseId;
        }

        if ($request->product && $request->product != '') {
            $query->whereHas('products', function($q) use ($request) {
                $q->where('purchase_product_id', '=', $request->product);
            });

            $appends['product'] = $request->product;
        }

        $query->orderBy('date', 'desc')->orderBy('created_at', 'desc');

        $data = [
            'total' => $query->sum('total'),
            'due' => $query->sum('due'),
            'paid' => $query->sum('paid'),
        ];

        $orders = $query->paginate(10);



        return view('report.purchase', compact('orders', 'suppliers',
            'products', 'appends'))->with($data);
    }

    public function sale(Request $request) {
        $customers = Customer::orderBy('name')->get();
        $products = PurchaseProduct::orderBy('name')->get();
        $appends = [];
        $query = SalesOrder::query();

        if ($request->date && $request->date != '') {
            $dates = explode(' - ', $request->date);
            if (count($dates) == 2) {
                $query->whereBetween('date', [$dates[0], $dates[1]]);
                $appends['date'] = $request->date;
            }
        }

        if ($request->customer && $request->customer != '') {
            $query->where('customer_id', $request->customer);
            $appends['customer'] = $request->customer;
        }

        if ($request->saleId && $request->saleId != '') {
            $query->where('order_no', $request->saleId);
            $appends['saleId'] = $request->saleId;
        }

        if ($request->product && $request->product != '') {
            $query->whereHas('products', function($q) use ($request) {
                $q->where('purchase_product_id', '=', $request->product);
            });

            $appends['product'] = $request->product;
        }

        $query->orderBy('date', 'desc')->orderBy('created_at', 'desc');

        $data = [
            'total' => $query->sum('total'),
            'due' => $query->sum('due'),
            'paid' => $query->sum('paid'),
        ];

        $orders = $query->paginate(10);

        return view('report.sale', compact('customers', 'products',
            'appends', 'orders'))->with($data);
    }

    public function balanceSheet() {
        $bankAccounts = BankAccount::where('status', 1)->with('bank', 'branch')->get();
        $cash = Cash::first();
        $mobileBanking = MobileBanking::first();
        $customerTotalPaid = Customer::all()->sum('due');
        $suppliers = Supplier::all();
        $totalInventory = PurchaseInventory::select(DB::raw('SUM(`selling_price` * `quantity`) AS total'))
            ->get();

        return view('report.balance_sheet', compact('bankAccounts',
            'cash', 'mobileBanking', 'customerTotalPaid', 'suppliers', 'totalInventory'));
    }

    public function profitAndLoss(Request $request) {
        $incomes = null;
        $expenses = null;
        $transport_cost = 0;

        if ($request->start && $request->end) {
            $incomes = TransactionLog::where('transaction_type', 1)->whereBetween('date', [$request->start, $request->end])->get();
            $expenses = TransactionLog::whereIn('transaction_type', [4, 2])->whereBetween('date', [$request->start, $request->end])->get();
            $transport_cost = PurchaseOrder::whereBetween('date', [$request->start, $request->end])->sum('transport_cost');
        }

        return view('report.profit_and_loss', compact('incomes', 'expenses', 'transport_cost'));
    }

    public function ledger(Request $request) {
        $incomes = null;
        $expenses = null;

        if ($request->start && $request->end) {
            $incomes = TransactionLog::whereIn('transaction_type', [1, 5])->whereBetween('date', [$request->start, $request->end])->get();
            $expenses = TransactionLog::whereIn('transaction_type', [3, 2, 6])->whereBetween('date', [$request->start, $request->end])->get();
        }

        return view('report.ledger', compact('incomes', 'expenses'));
    }

    public function transaction(Request $request) {
        $result = null;
        $types = AccountHeadType::whereNotIn('id', [1, 2, 3, 4, 20, 21])->get();
        $subTypes = AccountHeadSubType::whereNotIn('id', [1, 2, 3, 4, 42, 43])->get();

        if ($request->start && $request->end) {
            $query = TransactionLog::query();
            $query->select(DB::raw('sum(amount) as amount, account_head_type_id, account_head_sub_type_id'));
            $query->whereBetween('date', [$request->start, $request->end]);
            $query->whereNotIn('account_head_type_id', [0, 1, 2, 3, 4, 20, 21]);
            $query->whereNotIn('account_head_sub_type_id', [0, 1, 2, 3, 4, 42, 43]);

            if ($request->type && $request->type != '')
                $query->where('account_head_type_id', $request->type);

            if ($request->sub_type && $request->sub_type != '')
                $query->where('account_head_sub_type_id', $request->sub_type);

            $query->groupBy('account_head_sub_type_id', 'account_head_type_id');
            $query->with('accountHead');

            $result = $query->get();
        }

        return view('report.transaction', compact('result', 'types',
            'subTypes'));
    }
}
