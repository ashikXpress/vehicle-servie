<?php

namespace App\Http\Controllers;

use App\Model\AccountHeadSubType;
use App\Model\AccountHeadType;
use App\Model\BankAccount;
use App\Model\Branch;
use App\Model\PurchaseProduct;
use App\Model\SalesOrder;
use Illuminate\Http\Request;
use DB;

class CommonController extends Controller
{
    public function getBranch(Request $request) {
        $branches = Branch::where('bank_id', $request->bankId)
            ->where('status', 1)
            ->orderBy('name')
            ->get()->toArray();

        return response()->json($branches);
    }

    public function getBankAccount(Request $request) {
        $accounts = BankAccount::where('branch_id', $request->branchId)
            ->where('status', 1)
            ->orderBy('account_no')
            ->get()->toArray();

        return response()->json($accounts);
    }

    public function orderDetails(Request $request) {
        $order = SalesOrder::where('id', $request->orderId)->with('customer')->first()->toArray();

        return response()->json($order);
    }

    public function getAccountHeadType(Request $request) {
        $types = AccountHeadType::where('transaction_type', $request->type)
            ->where('status', 1)
            ->whereNotIn('id', [1, 2, 3, 4, 20, 21])
            ->orderBy('name')
            ->get()->toArray();

        return response()->json($types);
    }

    public function getAccountHeadSubType(Request $request) {
        $subTypes = AccountHeadSubType::where('account_head_type_id', $request->typeId)
            ->where('status', 1)
            ->whereNotIn('id', [1, 2, 3, 4, 42, 43])
            ->orderBy('name')
            ->get()->toArray();

        return response()->json($subTypes);
    }

    public function getSerialSuggestion(Request $request) {
        if ($request->has('term')) {
            return DB::table('purchase_order_purchase_product')->where('serial_no', 'like', '%'.$request->input('term').'%')->get();
        }
    }

    public function vatCorrection() {
        $orders = SalesOrder::where('vat_percentage', '!=', 0)->get();

        foreach ($orders as $order) {
            $total = $order->sub_total + $order->vat - $order->discount;
            $order->total = $total;

            if ($order->due == 0) {
                $order->paid = $total;
            } else {
                $order->due = $total;
            }

            $order->save();
        }
    }
}
