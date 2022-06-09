<?php

namespace App\Http\Controllers;

use App\Model\Department;
use App\Model\Designation;
use App\Model\DesignationLog;
use App\Model\Employee;
use App\Model\Leave;
use App\Model\SalaryChangeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use DataTables;

class HRController extends Controller
{
    public function employeeIndex() {
        $departments = Department::where('status', 1)
            ->orderBy('name')->get();

        return view('hr.employee.all', compact('departments'));
    }

    public function employeeAdd() {
        $departments = Department::where('status', 1)
            ->orderBy('name')->get();

        $count = Employee::count();
        $lastId = Employee::orderBy('id','desc')->first();
        if ($lastId == null) {
            $lastId = 1000;
        }else{
            $lastId = $lastId->employee_id + 1;
        }

//        $employeeId = str_pad($count+1, 1, '1', STR_PAD_LEFT);
        $employeeId = $lastId;

        return view('hr.employee.add', compact('departments', 'employeeId'));
    }

    public function employeeAddPost(Request $request) {
        $messages = [
            'gross_salary.required_if' => 'The gross salary field is required.',
            'bank_account.required_if' => 'The bank account field is required.'
        ];

        $request->validate([
            'name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'joining_date' => 'nullable|date',
            'confirmation_date' => 'nullable|date',
            'department' => 'required',
            'designation' => 'required',
            'employee_type' => 'required',
            'reporting_to' => 'nullable|string|max:255',
            'gender' => 'required',
            'marital_status' => 'required',
            'mobile_no' => 'required|digits:11',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',
            'signature' => 'nullable|image',
            'photo' => 'nullable|image',
            'present_address' => 'nullable|string|max:255',
            'permanent_address' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'religion' => 'required',
            'cv' => 'nullable|mimes:doc,pdf,docx',
            'foot' => 'nullable|numeric|min:0',
            'inch' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'gross_salary' => 'required|numeric',
            'bank_account' => 'nullable',
            'tax' => 'nullable|numeric',
            'comments' => 'nullable|max:255',
        ], $messages);

        $signature = null;
        if ($request->signature) {
            // Upload Signature
            $file = $request->file('signature');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'public/uploads/employee/signature';
            $file->move($destinationPath, $filename);

            $signature = 'uploads/employee/signature/'.$filename;
        }


        // Upload CV
        $photo = null;
        if ($request->photo) {
            // Upload Photo
            $file = $request->file('photo');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'public/uploads/employee/photo';
            $file->move($destinationPath, $filename);

            $photo = 'uploads/employee/photo/'.$filename;

        }

        // Upload CV
        $cv = null;
        if ($request->cv) {
            $file = $request->file('cv');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'public/uploads/employee/cv';
            $file->move($destinationPath, $filename);

            $cv = 'uploads/employee/cv/'.$filename;
        }

        $employee = new Employee();
        $employee->name = $request->name;
        $employee->employee_id = $request->employee_id;
        $employee->dob = $request->date_of_birth;
        $employee->joining_date = $request->joining_date;
        $employee->confirmation_date = $request->confirmation_date;
        $employee->department_id = $request->department;
        $employee->designation_id = $request->designation;
        $employee->employee_type = $request->employee_type;
        $employee->reporting_to = $request->reporting_to;
        $employee->gender = $request->gender;
        $employee->disease = $request->disease;
        $employee->blood_group = $request->blood_group;
        $employee->marital_status = $request->marital_status;
        $employee->mobile_no = $request->mobile_no;
        $employee->wife_name = $request->wife_name;
        $employee->number_of_children = $request->number_of_children;
        $employee->father_name = $request->father_name;
        $employee->mother_name = $request->mother_name;
        $employee->emergency_contact = $request->emergency_contact;
        $employee->signature = $signature;
        $employee->photo = $photo;
        $employee->present_address = $request->present_address;
        $employee->permanent_address = $request->permanent_address;
        $employee->email = $request->email;
        $employee->religion = $request->religion;
        $employee->cv = $cv;
        $employee->height_foot = $request->foot;
        $employee->height_inch = $request->inch;
        $employee->height_total_inch = $request->foot * 12 + $request->inch;
        $employee->weight = $request->weight;
        $employee->tax = $request->tax ? $request->tax : 0;
        $employee->comments = $request->comments;

        $employee->medical = round($request->gross_salary * .06);
        $employee->travel = round($request->gross_salary * .04);
        $employee->house_rent = round($request->gross_salary * .30);
        $employee->basic_salary = round($request->gross_salary * .60);
        $employee->gross_salary = $request->gross_salary;
        $employee->payable = $request->gross_salary - $request->tax;
        $employee->bank_account = $request->bank_account;
        $employee->save();

        $designationLog = new DesignationLog();
        $designationLog->employee_id = $employee->id;
        $designationLog->department_id = $request->department;
        $designationLog->designation_id = $request->designation;
        $designationLog->date = date('Y-m-d');
        $designationLog->save();

        $salaryChangeLog = new SalaryChangeLog();
        $salaryChangeLog->employee_id = $employee->id;
        $salaryChangeLog->date = date('Y-m-d');
        $salaryChangeLog->basic_salary = round($request->gross_salary * .60);
        $salaryChangeLog->house_rent = round($request->gross_salary * .30);
        $salaryChangeLog->travel = round($request->gross_salary * .04);
        $salaryChangeLog->medical = round($request->gross_salary * .06);
        $salaryChangeLog->tax = $request->tax ? $request->tax : 0;
        $salaryChangeLog->others_deduct = 0;
        $salaryChangeLog->gross_salary = round($request->gross_salary);
        $salaryChangeLog->payable = round($request->gross_salary - $request->tax);
        $salaryChangeLog->type = 5;
        $salaryChangeLog->save();


        return redirect()->route('employee.all')->with('message', 'Employee add successfully.');
    }

    public function employeeEdit(Employee $employee) {
        $departments = Department::where('status', 1)
            ->orderBy('name')->get();

        return view('hr.employee.edit', compact('departments', 'employee'));
    }

    public function employeeEditPost(Employee $employee, Request $request) {

        $request->validate([
            'name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'joining_date' => 'nullable|date',
            'confirmation_date' => 'nullable|date',
            'department' => 'required',
            'designation' => 'required',
            'employee_type' => 'required',
            'reporting_to' => 'nullable|string|max:255',
            'gender' => 'required',
            'marital_status' => 'required',
            'mobile_no' => 'nullable|digits:11',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',
            'signature' => 'nullable|image',
            'photo' => 'nullable|image',
            'present_address' => 'nullable|string|max:255',
            'permanent_address' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'religion' => 'required',
            'cv' => 'nullable|mimes:doc,pdf,docx',
            'foot' => 'nullable|numeric|min:0',
            'inch' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'bank_account' => 'nullable',
            'tax' => 'nullable|numeric',
            'comments' => 'nullable|max:255',
            'status' => 'required|min:0',
            'transfer' => 'nullable',
        ]);

        $signature = $employee->signature;
        if ($request->signature) {
            // Previous Photo
            $previousPhoto = public_path($employee->signature);
            unlink($previousPhoto);

            $file = $request->file('signature');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'public/uploads/employee/signature';
            $file->move($destinationPath, $filename);

            $signature = 'uploads/employee/signature/'.$filename;
        }

        $photo = $employee->photo;
        if ($request->photo) {
            // Previous Photo
            $previousPhoto = public_path($employee->photo);
            unlink($previousPhoto);

            $file = $request->file('photo');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'public/uploads/employee/photo';
            $file->move($destinationPath, $filename);

            $photo = 'uploads/employee/photo/'.$filename;
        }

        // Upload CV
        $cv = $employee->cv;
        if ($request->cv) {
            // Previous CV
            if ($employee->cv) {
                $previousCV = public_path($employee->cv);
                unlink($previousCV);
            }

            $file = $request->file('cv');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'public/uploads/employee/cv';
            $file->move($destinationPath, $filename);

            $cv = 'uploads/employee/cv/'.$filename;
        }

        $employee->name = $request->name;
        $employee->dob = $request->date_of_birth;
        $employee->joining_date = $request->joining_date;
        $employee->confirmation_date = $request->confirmation_date;
        $employee->department_id = $request->department;
        $employee->designation_id = $request->designation;
        $employee->employee_type = $request->employee_type;
        $employee->reporting_to = $request->reporting_to;
        $employee->gender = $request->gender;
        $employee->marital_status = $request->marital_status;
        $employee->mobile_no = $request->mobile_no;
        $employee->father_name = $request->father_name;
        $employee->mother_name = $request->mother_name;
        $employee->emergency_contact = $request->emergency_contact;
        $employee->signature = $signature;
        $employee->photo = $photo;
        $employee->present_address = $request->present_address;
        $employee->permanent_address = $request->permanent_address;
        $employee->email = $request->email;
        $employee->religion = $request->religion;
        $employee->cv = $cv;
        $employee->height_foot = $request->foot;
        $employee->height_inch = $request->inch;
        $employee->height_total_inch = $request->foot * 12 + $request->inch;
        $employee->weight = $request->weight;
        $employee->comments = $request->comments;
        $employee->status = $request->status ? 1 : 0;
        $employee->transfer = $request->transfer ? 1 : 0;
        $employee->retirement = $request->retirement ? 1 : 0;
        $employee->bank_account = $request->bank_account;
        $employee->tax = $request->tax;
        $employee->payable = round($request->payable - $request->tax);
        $employee->save();


        $designationLog = DesignationLog::where('employee_id',$employee->id)->orderBy('id','desc')->first();
        $designationLog->department_id = $request->department;
        $designationLog->designation_id = $request->designation;
        $designationLog->save();

        return redirect()->route('employee.all')->with('message', 'Employee edit successfully.');
    }

    public function employeeDetails(Employee $employee) {
        $leaves = Leave::where('employee_id', $employee->id)
            ->where('year', date('Y'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hr.employee.details', compact('employee', 'leaves'));
    }

    public function getLeave(Request $request) {
        $leaves = Leave::where('employee_id', $request->employeeId)
            ->where('year', $request->year)
            ->orderBy('created_at', 'desc')
            ->get();

        $html = view('partials.leave_table', compact('leaves'))->render();

        return response()->json(['html' => $html]);
    }

    public function employeeDesignationUpdate(Request $request) {
        $rules = [
            'department' => 'required',
            'designation' => 'required',
            'date' => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $employee = Employee::find($request->id);
        $employee->department_id = $request->department;
        $employee->designation_id = $request->designation;
        $employee->save();

        $log = new DesignationLog();
        $log->employee_id = $employee->id;
        $log->department_id = $request->department;
        $log->designation_id = $request->designation;
        $log->date = $request->date;
        $log->save();

        return response()->json(['success' => true, 'message' => 'Update has been completed.']);
    }

    public function employeeDatatable() {
        $query = Employee::with('department', 'designation');

        return DataTables::eloquent($query)
            ->addColumn('department', function(Employee $employee) {
                return $employee->department->name;
            })
            ->addColumn('designation', function(Employee $employee) {
                return $employee->designation->name;
            })
            ->addColumn('action', function(Employee $employee) {
                return '<a class="btn btn-primary btn-sm btn-change-designation" role="button" data-id="'.$employee->id.'">Change Designation</a> <a class="btn btn-primary btn-sm" href="'.route('employee.details', ['employee' => $employee->id]).'">Details</a> <a class="btn btn-info btn-sm" href="'.route('employee.edit', ['employee' => $employee->id]).'">Edit</a>';
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
            ->rawColumns(['action', 'photo', 'employee_type', 'employee_category'])
            ->toJson();
    }
}
