<?php

namespace App\Http\Controllers;

use App\Model\Customer;
use Illuminate\Http\Request;
use DataTables;

class CustomerController extends Controller
{
    public function index() {
        return view('sale.customer.all');
    }

    public function add() {
        return view('sale.customer.add');
    }

    public function addPost(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_no' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $customer = new Customer();
        $customer->name = $request->name;
        $customer->mobile_no = $request->mobile_no;
        $customer->address = $request->address;
        $customer->save();

        return redirect()->route('customer')->with('message', 'Customer add successfully.');
    }

    public function edit(Customer $customer) {
        return view('sale.customer.edit', compact('customer'));
    }

    public function editPost(Customer $customer, Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_no' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $customer->name = $request->name;
        $customer->mobile_no = $request->mobile_no;
        $customer->address = $request->address;
        $customer->save();;

        return redirect()->route('customer')->with('message', 'Customer edit successfully.');
    }

    public function datatable() {
        $query = Customer::query();

        return DataTables::eloquent($query)
            ->addColumn('action', function(Customer $customer) {
                return '<a class="btn btn-warning btn-sm" href="'.route('customer.edit', ['customer' => $customer->id]).'">Edit</a>';
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
