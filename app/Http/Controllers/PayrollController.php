<?php

namespace App\Http\Controllers;

use App\Model\Bank;
use App\Model\BankAccount;
use App\Model\BankLog;
use App\Model\Employee;
use App\Model\Leave;
use App\Model\Salary;
use App\Model\SalaryChangeLog;
use App\Model\SalaryProcess;
use App\Model\TransactionLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;

class PayrollController extends Controller
{
    public function salaryUpdateIndex() {
        return view('payroll.salary_update.all');
    }

    public function salaryUpdatePost(Request $request) {
        $messages = [
            'required_if:type' => 'The increase amount field is required.',
        ];
        $rules = [
            'tax' => 'required|numeric|min:0',
            'others_deduct' => 'required|numeric|min:0',
            'date' => 'required|date',
            'type' => 'required',
            'increase_amount' => 'required_if:type,==,1,2,3,4',
        ];

        $validator = Validator::make($request->all(), $rules,$messages);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $employee = Employee::find($request->id);

        $gross_salary = $employee->gross_salary + $request->increase_amount;
        $employee->medical = round($gross_salary * .06);
        $employee->travel = round($gross_salary * .04);
        $employee->house_rent = round($gross_salary * .30);
        $employee->basic_salary = round($gross_salary * .60);
        $employee->tax = $request->tax;
        $employee->others_deduct = $request->others_deduct;
        $employee->increase_amount = $request->increase_amount ? $request->increase_amount : 0;
        $employee->gross_salary = round($gross_salary);
        $employee->payable = round($gross_salary - $request->tax - $request->others_deduct);
        $employee->save();

        $salaryChangeLog = new SalaryChangeLog();
        $salaryChangeLog->employee_id = $employee->id;
        $salaryChangeLog->date = $request->date;
        $salaryChangeLog->type = $request->type;
        $salaryChangeLog->basic_salary = round($gross_salary * .60);
        $salaryChangeLog->house_rent = round($gross_salary * .30);
        $salaryChangeLog->travel = round($gross_salary * .04);
        $salaryChangeLog->medical = round($gross_salary * .06);
        $salaryChangeLog->tax = $request->tax;
        $salaryChangeLog->others_deduct = $request->others_deduct;
        $salaryChangeLog->increase_amount = $request->increase_amount ? $request->increase_amount : 0;
        $salaryChangeLog->gross_salary = round($gross_salary);
        $salaryChangeLog->payable = round($gross_salary - $request->tax - $request->others_deduct);
        $salaryChangeLog->save();

        return response()->json(['success' => true, 'message' => 'Updates has been completed.']);
    }

    public function salaryProcessIndex() {
        $banks = Bank::where('status', 1)->orderBy('name')->get();

        return view('payroll.salary_process.index', compact('banks'));
    }

    public function salaryProcessPost(Request $request) {
        $totalSalary = Employee::where('salary_in_jolshiri', 1)->sum('payable');
        $bankAccount = BankAccount::find($request->account);

        if ($totalSalary > $bankAccount->balance) {
            return redirect()->route('payroll.salary_process.index')->with('error', 'Insufficient Balance.');
        }
        $bankHistory = BankAccount::where('id',$request->account)->first();
        $salaryProcess = new SalaryProcess();
        $salaryProcess->date = $request->date;
        $salaryProcess->month = $request->month;
        $salaryProcess->year = $request->year;
        $salaryProcess->bank_id = $request->bank;
        $salaryProcess->branch_id = $request->branch;
        $salaryProcess->bank_account_id = $request->account;
        $salaryProcess->previous_amount = $bankHistory->balance;
        $salaryProcess->total = $totalSalary;
        $salaryProcess->save();

        $employees = Employee::where('salary_in_jolshiri', 1)->get();

        foreach ($employees as $employee) {
            $salary = new Salary();
            $salary->salary_process_id = $salaryProcess->id;
            $salary->employee_id = $employee->id;
            $salary->date = $request->date;
            $salary->month = $request->month;
            $salary->year = $request->year;
            $salary->basic_salary = $employee->basic_salary;
            $salary->house_rent = $employee->house_rent;
            $salary->travel = $employee->travel;
            $salary->medical = $employee->medical;
            $salary->tax = $employee->tax;
            $salary->others_deduct = $employee->others_deduct;
            $salary->gross_salary = $employee->gross_salary;
            $salary->payable = $employee->payable;
            $salary->save();
        }


        $log = new TransactionLog();
        $log->date = $request->date;
        $log->particular = 'Salary';
        $log->transaction_type = 2;
        $log->transaction_method = 2;
        $log->account_head_type_id = 5;
        $log->account_head_sub_type_id = 65;
        $log->bank_id = $request->bank;
        $log->branch_id = $request->branch;
        $log->bank_account_id = $request->account;
        $log->previous_amount = $bankHistory->balance;
        $log->amount = $totalSalary;
        $log->salary_process_id = $salaryProcess->id;
        $log->save();

        BankAccount::find($request->account)->decrement('balance', $totalSalary);

        return redirect()->route('payroll.salary_process.index')->with('message', 'Salary process successful.');
    }

    public function leaveIndex() {
        $employees = Employee::orderBy('employee_id')->get();

        return view('payroll.leave.index', compact('employees'));
    }

    public function leavePost(Request $request) {
        $request->validate([
            'employee' => 'required',
            'from' => 'required|date',
            'to' => 'required|date',
            'note' => 'nullable|max:255',
            'type' => 'required'
        ]);

        $fromObj = new Carbon($request->from);
        $toObj = new Carbon($request->to);
        $totalDays = $fromObj->diffInDays($toObj) + 1;

        $leave = new Leave();
        $leave->employee_id = $request->employee;
        $leave->type = $request->type;
        $leave->year = $toObj->format('Y');
        $leave->from = $request->from;
        $leave->to = $request->to;
        $leave->total_days = $totalDays;
        $leave->note = $request->note;
        $leave->save();

        return redirect()->route('payroll.leave.index')->with('message', 'Leave add successful.');
    }

    public function salaryUpdateDatatable() {
        $query = Employee::with('department', 'designation')->where('salary_in_jolshiri', 1);

        return DataTables::eloquent($query)
            ->addColumn('department', function(Employee $employee) {
                return $employee->department->name;
            })
            ->addColumn('designation', function(Employee $employee) {
                return $employee->designation->name;
            })
            ->addColumn('action', function(Employee $employee) {
                return '<a class="btn btn-info btn-sm btn-update" role="button" data-id="'.$employee->id.'">Update</a>';
            })
            ->editColumn('photo', function(Employee $employee) {
                return '<img src="'.asset($employee->photo).'" height="50px">';
            })
            ->editColumn('employee_type', function(Employee $employee) {
                if ($employee->employee_type == 1)
                    return '<span class="label label-success">Permanent</span>';
                else
                    return '<span class="label label-warning">Temporary</span>';
            })
            ->editColumn('employee_category', function(Employee $employee) {
                if ($employee->employee_category == 1)
                    return '<span class="label label-success">Army</span>';
                else
                    return '<span class="label label-info">Civilian</span>';
            })
            ->editColumn('gross_salary', function(Employee $employee) {
                return '৳'.number_format($employee->gross_salary, 2);
            })
            ->rawColumns(['action', 'photo', 'employee_type', 'employee_category'])
            ->toJson();
    }
}
