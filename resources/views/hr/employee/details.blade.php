@extends('layouts.app')

@section('style')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('title')
    Employee Details
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#profile" data-toggle="tab">Profile</a></li>
                    <li><a href="#salary" data-toggle="tab">Salary</a></li>
                    <li><a href="#designation_log" data-toggle="tab">Designation Log</a></li>
                    <li><a href="#leave" data-toggle="tab">Leave</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="profile">
                        <div class="row">
                            <div class="col-md-8">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Employee ID</th>
                                        <td>{{ $employee->employee_id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ $employee->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date of Birth</th>
                                        <td>{{ $employee->dob ? $employee->dob->format('j F, Y') : '' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Joining Date</th>
                                        <td>{{ $employee->joining_date ? $employee->joining_date->format('j F, Y') : '' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Confirmation Date</th>
                                        <td>{{ $employee->confirmation_date ? $employee->confirmation_date->format('j F, Y') : '' }}</td>
                                    </tr>


                                    <tr>
                                        <th>Department</th>
                                        <td>{{ $employee->department->name }}</td>
                                    </tr>

                                    <tr>
                                        <th>Designation</th>
                                        <td>{{ $employee->designation->name }}</td>
                                    </tr>

                                    <tr>
                                        <th>Employee Type</th>
                                        <td>
                                            @if($employee->employee_type == 1)
                                                Permanent
                                            @else
                                                Temporary
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Reporting To</th>
                                        <td>{{ $employee->reporting_to }}</td>
                                    </tr>

                                    <tr>
                                        <th>Gender</th>
                                        <td>
                                            @if($employee->gender == 1)
                                                Male
                                            @else
                                                Female
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Marital Status</th>
                                        <td>
                                            @if($employee->marital_status == 1)
                                                Single
                                            @else
                                                Married
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Mobile No.</th>
                                        <td>{{ $employee->mobile_no }}</td>
                                    </tr>

                                    <tr>
                                        <th>Father Name</th>
                                        <td>{{ $employee->father_name }}</td>
                                    </tr>

                                    <tr>
                                        <th>Mother Name</th>
                                        <td>{{ $employee->mother_name }}</td>
                                    </tr>

                                    <tr>
                                        <th>Emergency Contact</th>
                                        <td>{{ $employee->emergency_contact }}</td>
                                    </tr>

                                    <tr>
                                        <th>Present Address</th>
                                        <td>{{ $employee->present_address }}</td>
                                    </tr>

                                    <tr>
                                        <th>Permanent Address</th>
                                        <td>{{ $employee->permanent_address }}</td>
                                    </tr>

                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $employee->email }}</td>
                                    </tr>

                                    <tr>
                                        <th>Religion</th>
                                        <td>
                                            @if($employee->religion == 1)
                                                Muslim
                                            @elseif($employee->religion == 2)
                                                Hindu
                                            @elseif($employee->religion == 3)
                                                Christian
                                            @elseif($employee->religion == 4)
                                                Other
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Bank Account</th>
                                        <td>{{ $employee->bank_account }}</td>
                                    </tr>

                                    <tr>
                                        <th>Height</th>
                                        <td>
                                            {{ $employee->height_foot ? $employee->height_foot.'\'' : '' }}
                                            {{ $employee->height_inch ? $employee->height_inch.'"' : '' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Weight (kg)</th>
                                        <td>{{ $employee->weight }}</td>
                                    </tr>

                                </table>
                            </div>

                            <div class="col-md-4 text-center">
                                <img class="img-thumbnail" src="{{ asset($employee->photo) }}" width="150px"> <br><br>
                                <img class="img-thumbnail" src="{{ asset($employee->signature) }}" width="150px"> <br><br>

                                @if($employee->cv)
                                    <a href="{{ asset($employee->cv) }}" class="btn btn-primary btn-sm" download>Download CV</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- /.tab-pane -->

                    <div class="tab-pane" id="salary">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Basic Salary</th>
                                    <td>{{ number_format($employee->basic_salary,2) }}</td>
                                </tr>
                                <tr>
                                    <th>House Rent</th>
                                    <td>{{ number_format($employee->house_rent,2) }}</td>
                                </tr>
                                <tr>
                                    <th>Travel</th>
                                    <td>{{ number_format($employee->travel,2) }}</td>
                                </tr>
                                <tr>
                                    <th>Medical</th>
                                    <td>{{ number_format($employee->medical,2) }}</td>
                                </tr>
                                <tr>
                                    <th>Tax</th>
                                    <td>{{ number_format($employee->tax ,2)}}</td>
                                </tr>
                                <tr>
                                    <th>Others Deduct</th>
                                    <td>{{ number_format($employee->others_deduct,2) }}</td>
                                </tr>
                                <tr>
                                    <th>Gross Salary</th>
                                    <th>{{ number_format($employee->gross_salary,2) }}</th>
                                </tr>
                                <tr>
                                    <th>Payable Salary</th>
                                    <th>{{ number_format($employee->payable,2) }}</th>
                                </tr>
                            </table>
                        </div>

                        <div class="table-responsive">
                            <table class="table no-margin">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Basic Salary</th>
                                    <th>House Rent</th>
                                    <th>Travel</th>
                                    <th>Medical</th>
                                    <th>Tax</th>
                                    <th>Others Deduct</th>
                                    <th>Gross Salary</th>
                                    <th>Payable Salary</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($employee->salaryChangeLog as $log)
                                    <tr>
                                        <td>{{ $log->date->format('j F, Y') }}</td>
                                        <td>
                                            @if($log->type == 1)
                                                Confirmation
                                            @elseif($log->type == 2)
                                                Yearly Increment
                                            @elseif($log->type == 3)
                                                Promotion
                                            @elseif($log->type == 4)
                                                Other
                                            @elseif($log->type == 5)
                                                Initial
                                            @endif
                                        </td>
                                        <td>{{ number_format($log->basic_salary,2) }}</td>
                                        <td>{{ number_format($log->house_rent,2) }}</td>
                                        <td>{{ number_format($log->travel,2) }}</td>
                                        <td>{{ number_format($log->medical,2)}}</td>
                                        <td>{{ number_format($log->tax,2) }}</td>
                                        <td>{{ number_format($log->others_deduct,2) }}</td>
                                        <td>{{ number_format($log->gross_salary,2) }}</td>
                                        <td>{{ number_format($log->payable,2) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.tab-pane -->

                    <div class="tab-pane" id="designation_log">
                        <div class="table-responsive">
                            <table class="table no-margin">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($employee->designationLogs as $log)
                                    <tr>
                                        <td>{{ $log->date->format('j F, Y') }}</td>
                                        <td>{{ $log->department->name }}</td>
                                        <td>{{ $log->designation->name }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.tab-pane -->

                    <div class="tab-pane" id="leave">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Filter</h3>
                                    </div>
                                    <!-- /.box-header -->

                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Year</label>

                                                    <select class="form-control" id="leave-year">
                                                        @for($i=2020; $i <= date('Y'); $i++)
                                                            <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive" id="leave-table">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Days</th>
                                    <th>Note</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($leaves as $leave)
                                    <tr>
                                        <td>
                                            @if($leave->type == 1)
                                                Annual
                                            @elseif($leave->type == 2)
                                                Casual
                                            @elseif($leave->type == 3)
                                                Sick
                                            @elseif($leave->type == 4)
                                                Others
                                            @endif
                                        </td>
                                        <td>{{ $leave->from->format('j F, Y') }}</td>
                                        <td>{{ $leave->to->format('j F, Y') }}</td>
                                        <td>{{ $leave->total_days }}</td>
                                        <td>{{ $leave->note }}</td>
                                    </tr>
                                @endforeach
                                </tbody>

                                <tfoot>
                                <tr>
                                    <th colspan="3">Total Days</th>
                                    <th>{{ $leaves->sum('total_days') }}</th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
            <!-- nav-tabs-custom -->
        </div>
        <!-- /.col -->
    </div>
@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('themes/backend/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('themes/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

    <script>
        $(function () {
            $('#leave-year').change(function () {
                var year = $(this).val();
                var employeeId = '{{ $employee->id }}';

                $.ajax({
                    method: "POST",
                    url: "{{ route('employee.get_leaves') }}",
                    data: { year: year, employeeId: employeeId }
                }).done(function( response ) {
                    $('#leave-table').html(response.html);
                });
            });
        })
    </script>
@endsection
