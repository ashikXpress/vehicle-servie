<?php

namespace App\Http\Controllers;

use App\Model\AccountHeadSubType;
use App\Model\AccountHeadType;
use App\Model\BankAccount;
use App\Model\Branch;
use App\Model\Designation;
use App\Model\Employee;
use App\Model\PurchaseProduct;
use App\Model\SalaryProcess;
use App\Model\SalesOrder;
use Illuminate\Http\Request;
use DB;

class CommonController extends Controller
{
    public function getDesignation(Request $request) {
        $designations = Designation::where('department_id', $request->departmentId)
            ->where('status', 1)
            ->orderBy('name')->get()->toArray();

        return response()->json($designations);
    }

    public function getEmployeeDetails(Request $request) {
        $employee = Employee::where('id', $request->employeeId)
            ->with('department', 'designation')->first();

        return response()->json($employee);
    }

    public function getMonth(Request $request) {
        $salaryProcess = SalaryProcess::select('month')
            ->where('year', $request->year)
            ->get();

        $proceedMonths = [];
        $result = [];

        foreach ($salaryProcess as $item)
            $proceedMonths[] = $item->month;

        for($i=1; $i <=12; $i++) {
            if (!in_array($i, $proceedMonths)) {
                $result[] = [
                    'id' => $i,
                    'name' => date('F', mktime(0, 0, 0, $i, 10)),
                ];
            }
        }

        return response()->json($result);
    }

    public function getProcessedMonth(Request $request) {
        $salaryProcess = SalaryProcess::select('month')
            ->where('year', $request->year)
            ->get();

        $proceedMonths = [];
        $result = [];

        foreach ($salaryProcess as $item)
            $proceedMonths[] = $item->month;

        for($i=1; $i <=12; $i++) {
            if (in_array($i, $proceedMonths)) {
                $result[] = [
                    'id' => $i,
                    'name' => date('F', mktime(0, 0, 0, $i, 10)),
                ];
            }
        }

        return response()->json($result);
    }
    public function getBankAccountBalance(Request $request) {
        $balance = BankAccount::where('id', $request->accountId)->first();
        return response()->json($balance);
    }
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
