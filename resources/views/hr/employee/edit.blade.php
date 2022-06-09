@extends('layouts.app')

@section('style')
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('title')
    Employee Edit
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Employee Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('employee.edit', ['employee' => $employee->id]) }}">
                    @csrf

                    <div class="box-body">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Name *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Name"
                                       name="name" value="{{ empty(old('name')) ? ($errors->has('name') ? '' : $employee->name) : old('name') }}">

                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('employee_id') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Employee ID *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Employee ID"
                                       name="employee_id" value="{{ $employee->employee_id }}" readonly>

                                @error('employee_id')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('date_of_birth') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Date of Birth </label>

                            <div class="col-sm-10">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="dob" name="date_of_birth"
                                           value="{{ empty(old('date_of_birth')) ? ($errors->has('date_of_birth') ? '' : $employee->dob) : old('date_of_birth') }}" autocomplete="off">
                                </div>
                                <!-- /.input group -->

                                @error('date_of_birth')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('joining_date') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Joining Date *</label>

                            <div class="col-sm-10">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right date-picker" name="joining_date"
                                           value="{{ empty(old('joining_date')) ? ($errors->has('joining_date') ? '' : date('Y-m-d',strtotime($employee->joining_date))) : old('joining_date') }}" autocomplete="off">
                                </div>
                                <!-- /.input group -->

                                @error('joining_date')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('confirmation_date') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Confirmation Date </label>

                            <div class="col-sm-10">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right date-picker" name="confirmation_date"
                                           value="{{ empty(old('confirmation_date')) ? ($errors->has('confirmation_date') ? '' : $employee->confirmation_date) : old('confirmation_date') }}" autocomplete="off">
                                </div>
                                <!-- /.input group -->

                                @error('confirmation_date')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group {{ $errors->has('department') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Department *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="department" id="department">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ empty(old('department')) ? ($errors->has('department') ? '' : ($employee->department_id == $department->id ? 'selected' : '')) :
                                            (old('department') == $department->id ? 'selected' : '') }}>{{ $department->name }}</option>
                                    @endforeach
                                </select>

                                @error('department')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('designation') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Designation *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="designation" id="designation">
                                    <option value="">Select Designation</option>
                                </select>

                                @error('designation')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('employee_type') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Employee Type *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="employee_type" >
                                    <option value="">Select Employee Type</option>
                                    <option value="1" {{ empty(old('employee_type')) ? ($errors->has('employee_type') ? '' : ($employee->employee_type == '1' ? 'selected' : '')) :
                                            (old('employee_type') == '1' ? 'selected' : '') }}>Permanent</option>
                                    <option value="2" {{ empty(old('employee_type')) ? ($errors->has('employee_type') ? '' : ($employee->employee_type == '2' ? 'selected' : '')) :
                                            (old('employee_type') == '2' ? 'selected' : '') }}>Temporary</option>
                                </select>

                                @error('employee_type')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('reporting_to') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Reporting To</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Reporting To"
                                       name="reporting_to" value="{{ empty(old('reporting_to')) ? ($errors->has('reporting_to') ? '' : $employee->reporting_to) : old('reporting_to') }}">

                                @error('reporting_to')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('gender') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Gender *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="gender" >
                                    <option value="">Select Gender</option>
                                    <option value="1" {{ empty(old('gender')) ? ($errors->has('gender') ? '' : ($employee->gender == '1' ? 'selected' : '')) :
                                            (old('gender') == '1' ? 'selected' : '') }}>Male</option>
                                    <option value="2" {{ empty(old('gender')) ? ($errors->has('gender') ? '' : ($employee->gender == '2' ? 'selected' : '')) :
                                            (old('gender') == '2' ? 'selected' : '') }}>Female</option>
                                </select>

                                @error('gender')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('blood_group') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Blood Group</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="blood_group" >
                                    <option value="">Select Blood Group</option>
                                    <option value="A+" {{ empty(old('blood_group')) ? ($errors->has('blood_group') ? '' : ($employee->blood_group == 'A+' ? 'selected' : '')) :
                                            (old('blood_group') == 'A+' ? 'selected' : '') }}>A+</option>
                                    <option value="A-" {{ empty(old('blood_group')) ? ($errors->has('blood_group') ? '' : ($employee->blood_group == 'A-' ? 'selected' : '')) :
                                            (old('blood_group') == 'A-' ? 'selected' : '') }}>A-</option>
                                    <option value="B+" {{ empty(old('blood_group')) ? ($errors->has('blood_group') ? '' : ($employee->blood_group == 'B+' ? 'selected' : '')) :
                                            (old('blood_group') == 'B+' ? 'selected' : '') }}>B+</option>
                                    <option value="B-" {{ empty(old('blood_group')) ? ($errors->has('blood_group') ? '' : ($employee->blood_group == 'B-' ? 'selected' : '')) :
                                            (old('blood_group') == 'B-' ? 'selected' : '') }}>B-</option>
                                    <option value="AB+" {{ empty(old('blood_group')) ? ($errors->has('blood_group') ? '' : ($employee->blood_group == 'AB+' ? 'selected' : '')) :
                                            (old('blood_group') == 'AB+' ? 'selected' : '') }}>AB+</option>
                                    <option value="AB-" {{ empty(old('blood_group')) ? ($errors->has('blood_group') ? '' : ($employee->blood_group == 'AB-' ? 'selected' : '')) :
                                            (old('blood_group') == 'AB-' ? 'selected' : '') }}>AB-</option>
                                    <option value="O+" {{ empty(old('blood_group')) ? ($errors->has('blood_group') ? '' : ($employee->blood_group == 'O+' ? 'selected' : '')) :
                                            (old('blood_group') == 'O+' ? 'selected' : '') }}>O+</option>
                                    <option value="O-" {{ empty(old('blood_group')) ? ($errors->has('blood_group') ? '' : ($employee->blood_group == 'O-' ? 'selected' : '')) :
                                            (old('blood_group') == 'O-' ? 'selected' : '') }}>O-</option>
                                </select>

                                @error('blood_group')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('disease') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Disease</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Disease"
                                       name="disease" value="{{ empty(old('disease')) ? ($errors->has('disease') ? '' : $employee->disease) : old('disease') }}">

                                @error('disease')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('marital_status') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Marital Status *</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="marital_status" >
                                    <option value="">Select Marital Status</option>
                                    <option value="1" {{ empty(old('marital_status')) ? ($errors->has('marital_status') ? '' : ($employee->marital_status == '1' ? 'selected' : '')) :
                                            (old('marital_status') == '1' ? 'selected' : '') }}>Single</option>
                                    <option value="2" {{ empty(old('marital_status')) ? ($errors->has('marital_status') ? '' : ($employee->marital_status == '2' ? 'selected' : '')) :
                                            (old('marital_status') == '2' ? 'selected' : '') }}>Married</option>
                                </select>

                                @error('marital_status')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('mobile_no') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Mobile No.</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Mobile No."
                                       name="mobile_no" value="{{ empty(old('mobile_no')) ? ($errors->has('mobile_no') ? '' : $employee->mobile_no) : old('mobile_no') }}">

                                @error('mobile_no')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('wife_name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Wife Name</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Wife Name"
                                       name="wife_name" value="{{ empty(old('wife')) ? ($errors->has('wife') ? '' : $employee->wife) : old('wife') }}">

                                @error('wife_name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('number_of_children') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Number of Children</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Number of Children"
                                       name="number_of_children" value="{{ empty(old('number_of_children')) ? ($errors->has('number_of_children') ? '' : $employee->number_of_children) : old('number_of_children') }}">

                                @error('number_of_children')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('father_name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Father Name </label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Father Name"
                                       name="father_name" value="{{ empty(old('father_name')) ? ($errors->has('father_name') ? '' : $employee->father_name) : old('father_name') }}">

                                @error('father_name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('mother_name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Mother Name</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Mother Name"
                                       name="mother_name" value="{{ empty(old('mother_name')) ? ($errors->has('mother_name') ? '' : $employee->mother_name) : old('mother_name') }}">

                                @error('mother_name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('emergency_contact') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Emergency Contact</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Emergency Contact"
                                       name="emergency_contact" value="{{ empty(old('emergency_contact')) ? ($errors->has('emergency_contact') ? '' : $employee->emergency_contact) : old('emergency_contact') }}">

                                @error('emergency_contact')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('signature') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Signature</label>

                            <div class="col-sm-10">
                                <input type="file" class="form-control" name="signature">

                                @error('signature')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('photo') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Photo</label>

                            <div class="col-sm-10">
                                <input type="file" class="form-control" name="photo">

                                @error('photo')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('present_address') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Present Address</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Present Address"
                                       name="present_address" value="{{ empty(old('present_address')) ? ($errors->has('present_address') ? '' : $employee->present_address) : old('present_address') }}">

                                @error('present_address')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('permanent_address') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Permanent Address</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Permanent Address"
                                       name="permanent_address" value="{{ empty(old('permanent_address')) ? ($errors->has('permanent_address') ? '' : $employee->permanent_address) : old('permanent_address') }}">

                                @error('permanent_address')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('email') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Email</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Email"
                                       name="email" value="{{ empty(old('email')) ? ($errors->has('email') ? '' : $employee->email) : old('email') }}">

                                @error('email')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('religion') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Religion</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="religion" >
                                    <option value="">Select Religion</option>
                                    <option value="1" {{ empty(old('religion')) ? ($errors->has('religion') ? '' : ($employee->religion == '1' ? 'selected' : '')) :
                                            (old('religion') == '1' ? 'selected' : '') }}>Muslim</option>
                                    <option value="2" {{ empty(old('religion')) ? ($errors->has('religion') ? '' : ($employee->religion == '2' ? 'selected' : '')) :
                                            (old('religion') == '2' ? 'selected' : '') }}>Hindu</option>
                                    <option value="3" {{ empty(old('religion')) ? ($errors->has('religion') ? '' : ($employee->religion == '3' ? 'selected' : '')) :
                                            (old('religion') == '3' ? 'selected' : '') }}>Christian</option>
                                    <option value="4" {{ empty(old('religion')) ? ($errors->has('religion') ? '' : ($employee->religion == '4' ? 'selected' : '')) :
                                            (old('religion') == '4' ? 'selected' : '') }}>Other</option>
                                </select>

                                @error('religion')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('cv') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">CV</label>

                            <div class="col-sm-10">
                                <input type="file" class="form-control" name="cv">

                                @error('cv')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Height</label>

                            <div class="col-sm-5">
                                <input type="number" min="0" class="form-control" placeholder="Foot"
                                       name="foot" value="{{ empty(old('foot')) ? ($errors->has('foot') ? '' : $employee->height_foot) : old('foot') }}">

                                @error('foot')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-sm-5">
                                <input type="number" min="0" class="form-control" placeholder="Inch"
                                       name="inch" value="{{ empty(old('inch')) ? ($errors->has('inch') ? '' : $employee->height_inch) : old('inch') }}">

                                @error('inch')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('weight') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Weight (kg)</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Weight"
                                       name="weight" value="{{ empty(old('weight')) ? ($errors->has('weight') ? '' : $employee->weight) : old('weight') }}">

                                @error('weight')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('salary_in_jolshiri') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Salary In Jolshiri</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="salary_in_jolshiri" id="salary_in_jolshiri">
                                    <option value="1" {{ empty(old('salary_in_jolshiri')) ? ($errors->has('salary_in_jolshiri') ? '' : ($employee->salary_in_jolshiri == '1' ? 'selected' : '')) :
                                            (old('salary_in_jolshiri') == '1' ? 'selected' : '') }}>Yes</option>
                                    <option value="0" {{ empty(old('salary_in_jolshiri')) ? ($errors->has('salary_in_jolshiri') ? '' : ($employee->salary_in_jolshiri == '0' ? 'selected' : '')) :
                                            (old('salary_in_jolshiri') == '0' ? 'selected' : '') }}>No</option>
                                </select>

                                @error('salary_in_jolshiri')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <div id="salary">
                            <div class="form-group {{ $errors->has('bank_account') ? 'has-error' :'' }}">
                                <label class="col-sm-2 control-label">Bank Account</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="Enter Bank Account"
                                           name="bank_account" value="{{ empty(old('bank_account')) ? ($errors->has('bank_account') ? '' : $employee->bank_account) : old('bank_account') }}">

                                    @error('bank_account')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('tax') ? 'has-error' :'' }}">
                                <label class="col-sm-2 control-label">Tax </label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="Enter Tax Amount"
                                           name="tax" value="{{ empty(old('tax')) ? ($errors->has('tax') ? '' : $employee->tax) : old('tax') }}">

                                    @error('tax')
                                    <span class="help-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="form-group {{ $errors->has('comments') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Comments</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Comments"
                                       name="comments" value="{{ empty(old('comments')) ? ($errors->has('comments') ? '' : $employee->comments) : old('comments') }}">

                                @error('comments')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('status') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Status *</label>

                            <div class="col-sm-10">

                                <div class="radio" style="display: inline">
                                    <label>
                                        <input type="radio" name="status" value="1" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($employee->status == '1' ? 'checked' : '')) :
                                            (old('status') == '1' ? 'checked' : '') }}>
                                        Active
                                    </label>
                                </div>

                                <div class="radio" style="display: inline">
                                    <label>
                                        <input type="radio" name="status" value="0" {{ empty(old('status')) ? ($errors->has('status') ? '' : ($employee->status == '0' ? 'checked' : '')) :
                                            (old('status') == '0' ? 'checked' : '') }}>
                                        Inactive
                                    </label>
                                </div>

                                @error('status')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- bootstrap datepicker -->
    <script src="{{ asset('themes/backend/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $(function () {
            //Date picker
            $('#dob').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                orientation: 'bottom'
            });

            $('.date-picker').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
            });

            var designationSelected = '{{ empty(old('designation')) ? ($errors->has('designation') ? '' : $employee->designation_id) : old('designation') }}';

            $('#department').change(function () {
                var departmentId = $(this).val();
                $('#designation').html('<option value="">Select Designation</option>');

                if (departmentId != '') {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_designation') }}",
                        data: { departmentId: departmentId }
                    }).done(function( response ) {
                        $.each(response, function( index, item ) {
                            if (designationSelected == item.id)
                                $('#designation').append('<option value="'+item.id+'" selected>'+item.name+'</option>');
                            else
                                $('#designation').append('<option value="'+item.id+'">'+item.name+'</option>');
                        });
                    });
                }
            });

            $('#department').trigger('change');

        });
    </script>
@endsection
