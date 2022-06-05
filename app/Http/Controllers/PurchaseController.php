<?php

namespace App\Http\Controllers;

use App\Model\Bank;
use App\Model\BankAccount;
use App\Model\Cash;
use App\Model\MobileBanking;
use App\Model\PurchaseInventory;
use App\Model\PurchaseInventoryLog;
use App\Model\PurchaseOrder;
use App\Model\PurchasePayment;
use App\Model\PurchaseProduct;
use App\Model\Supplier;
use App\Model\TransactionLog;
use App\Model\Warehouse;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use SakibRahaman\DecimalToWords\DecimalToWords;
use DB;

class PurchaseController extends Controller
{
    public function purchaseOrder() {
        $suppliers = Supplier::where('status', 1)->orderBy('name')->get();
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $products = PurchaseProduct::where('status', 1)->orderBy('name')->get();
        $banks = Bank::where('status', 1)->orderBy('name')->get();

        return view('purchase.purchase_order.create', compact('suppliers',
            'warehouses', 'products','banks'));
    }

    public function purchaseOrderPost(Request $request)
    {
        $rules = [
            'supplier' => 'required',
            'warehouse' => 'required',
            'date' => 'required|date',
            'product.*' => 'required',
            'serial.*' => 'required|unique:purchase_order_purchase_product,serial_no',
            'warranty.*' => 'required',
            'quantity.*' => 'required|numeric|min:.01',
            'unit_price.*' => 'required|numeric|min:0',
            'including_price.*' => 'required|numeric|min:0',
            'selling_price.*' => 'required|numeric|min:0',
            'bank' => 'required_if:payment_type,==,2',
            'branch' => 'required_if:payment_type,==,2',
            'account' => 'required_if:payment_type,==,2',
            'cheque_image' => 'nullable|image',
        ];
        if ($request->total-$request->paid > 0) {
            $rules['next_payment'] = 'required|date';
        }
        $request->validate($rules);


        if ($request->paid > 0) {
            if ($request->payment_type == 1 || $request->payment_type == 4) {
                $cash = Cash::first();
                // dd($request->paid);
                if ($request->paid > $cash->amount){
                    return redirect()->back()->withInput()->with('error', 'Insufficient Balance');
                }
            } elseif ($request->payment_type == 3) {
                $mobileBanking = MobileBanking::first();
                if ($request->paid > $mobileBanking->amount){
                    return redirect()->back()->withInput()->with('error', 'Insufficient Balance');
                }
            }else {
                $account = BankAccount::find($request->account);
                if ($request->paid > $account->balance){
                    return redirect()->back()->withInput()->with('error', 'Insufficient Balance');
                }
            }
        }

        // dd($request->all());

        $order = new PurchaseOrder();
        $order->order_no = rand(10000000, 99999999);
        $order->supplier_id = $request->supplier;
        $order->warehouse_id = $request->warehouse;
        $order->date = $request->date;
        $order->next_payment = $request->next_payment;
        $order->transport_cost = $request->transport_cost;
        $order->total = 0;
        $order->paid = 0;
        $order->due = 0;
        $order->save();

        $counter = 0;
        $total = 0;
        foreach ($request->product as $reqProduct) {
            $product = PurchaseProduct::find($reqProduct);

            $order->products()->attach($reqProduct, [
                'name' => $product->name,
                'type' => $request->type[$counter],
                'serial_no' => $request->serial[$counter],
                'warranty' => $request->warranty[$counter],
                'quantity' => $request->quantity[$counter],
                'unit_price' => $request->unit_price[$counter],
                'including_price' => $request->including_price[$counter],
                'selling_price' => $request->selling_price[$counter],
                'total' => $request->quantity[$counter] * $request->unit_price[$counter],
            ]);

            $total += $request->quantity[$counter] * $request->unit_price[$counter];

            // Inventory
            $inventory = new PurchaseInventory();
            $inventory->purchase_product_id = $product->id;
            $inventory->quantity = $request->quantity[$counter];
            $inventory->serial_no = $request->serial[$counter];
            $inventory->warranty = $request->warranty[$counter];
            $inventory->unit_price = $request->unit_price[$counter];
            $inventory->including_price = $request->including_price[$counter];
            $inventory->selling_price = $request->selling_price[$counter];
            $inventory->warehouse_id = $request->warehouse;
            $inventory->save();

            $inventoryLog = new PurchaseInventoryLog();
            $inventoryLog->purchase_product_id = $product->id;
            $inventoryLog->type = 1;
            $inventoryLog->date = $request->date;
            $inventoryLog->warehouse_id = $request->warehouse;
            $inventoryLog->quantity = $request->quantity[$counter];
            $inventoryLog->unit_price = $request->unit_price[$counter];
            $inventoryLog->supplier_id = $request->supplier;
            $inventoryLog->save();

            $counter++;
        }

        $order->total = $total;
        $order->due = $total;
        $order->save();

        if ($request->paid > 0) {
            if ($request->payment_type == 1 || $request->payment_type == 3 || $request->payment_type == 4) {
                $payment = new PurchasePayment();
                $payment->purchase_order_id = $order->id;
                $payment->transaction_method = $request->payment_type;
                $payment->amount = $request->paid;
                $payment->date = $request->date;
                $payment->note = $request->note;
                $payment->save();

                if ($request->payment_type == 1 || $request->payment_type == 4)
                    Cash::first()->decrement('amount', $request->paid);
                else
                    MobileBanking::first()->decrement('amount', $request->paid);

                $log = new TransactionLog();
                $log->date = $request->date;
                $log->particular = 'Paid to ' . $order->supplier->name . ' for ' . $order->order_no;
                $log->transaction_type = 3;
                $log->transaction_method = $request->payment_type;
                $log->account_head_type_id = 1;
                $log->account_head_sub_type_id = 1;
                $log->amount = $request->paid;
                $log->note = $request->note;
                $log->purchase_payment_id = $payment->id;
                $log->save();
            } else {
                $image = 'img/no_image.png';

                if ($request->cheque_image) {
                    // Upload Image
                    $file = $request->file('cheque_image');
                    $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                    $destinationPath = 'public/uploads/purchase_payment_cheque';
                    $file->move($destinationPath, $filename);

                    $image = 'uploads/purchase_payment_cheque/' . $filename;
                }

                $payment = new PurchasePayment();
                $payment->purchase_order_id = $order->id;
                $payment->transaction_method = 2;
                $payment->bank_id = $request->bank;
                $payment->branch_id = $request->branch;
                $payment->bank_account_id = $request->account;
                $payment->cheque_no = $request->cheque_no;
                $payment->cheque_image = $image;
                $payment->amount = $request->paid;
                $payment->date = $request->date;
                $payment->note = $request->note;
                $payment->save();

                BankAccount::find($request->account)->decrement('balance', $request->paid);

                $log = new TransactionLog();
                $log->date = $request->date;
                $log->particular = 'Paid to ' . $order->supplier->name . ' for ' . $order->order_no;
                $log->transaction_type = 3;
                $log->transaction_method = 2;
                $log->account_head_type_id = 1;
                $log->account_head_sub_type_id = 1;
                $log->bank_id = $request->bank;
                $log->branch_id = $request->branch;
                $log->bank_account_id = $request->account;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_image = $image;
                $log->amount = $request->paid;
                $log->note = $request->note;
                $log->purchase_payment_id = $payment->id;
                $log->save();
            }

            $order->increment('paid', $request->paid);
            $order->decrement('due', $request->paid);
        }



        return redirect()->route('purchase_receipt.qr_code', ['order' => $order->id]);
    }

    public function purchaseReceipt() {
        return view('purchase.receipt.all');
    }

    public function purchaseReceiptDetails(PurchaseOrder $order) {
        return view('purchase.receipt.details', compact('order'));
    }

    public function purchaseReceiptPrint(PurchaseOrder $order) {
        return view('purchase.receipt.print', compact('order'));
    }

    public function qrCode(PurchaseOrder $order) {
        $qrCodes = collect();

        foreach($order->products as $product) {
            if ($product->pivot->type == 2) {
                $qrCodes->push(['name' => $product->pivot->name, 'serial' => $product->pivot->serial_no]);
            } else {
                $qrCodes->push(['name' => $product->pivot->name, 'serial' => $product->pivot->serial_no]);
            }
        }

        return view('purchase.receipt.qr_code', compact('order', 'qrCodes'));
    }

    public function qrCodePrint(PurchaseOrder $order) {
        $qrCodes = collect();

        foreach($order->products as $product) {
            if ($product->pivot->type == 2) {
                $qrCodes->push(['name' => $product->pivot->name, 'serial' => $product->pivot->serial_no]);
            } else {
                $qrCodes->push(['name' => $product->pivot->name, 'serial' => $product->pivot->serial_no]);
            }
        }
        return view('purchase.receipt.qr_code_print', compact('order', 'qrCodes'));
    }

    public function supplierPayment() {
        $suppliers = Supplier::all();
        $banks = Bank::where('status', 1)->orderBy('name')->get();

        return view('purchase.supplier_payment.all', compact('suppliers', 'banks'));
    }

    public function supplierPaymentGetOrders(Request $request) {
        $orders = PurchaseOrder::where('supplier_id', $request->supplierId)
            ->where('due', '>', 0)
            ->orderBy('order_no')
            ->get()->toArray();

        return response()->json($orders);
    }

    public function supplierPaymentGetRefundOrders(Request $request) {
        $orders = PurchaseOrder::where('supplier_id', $request->supplierId)
            ->where('refund', '>', 0)
            ->orderBy('order_no')
            ->get()->toArray();

        return response()->json($orders);
    }

    public function supplierPaymentOrderDetails(Request $request) {
        $order = PurchaseOrder::where('id', $request->orderId)
            ->first()->toArray();

        return response()->json($order);
    }

    public function makePayment(Request $request) {
        $rules = [
            'order' => 'required',
            'payment_type' => 'required',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'next_payment' => 'nullable|date',
            'note' => 'nullable|string|max:255',
        ];

        if ($request->payment_type == '2') {
            $rules['bank'] = 'required';
            $rules['branch'] = 'required';
            $rules['account'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }

        if ($request->order != '') {
            $order = PurchaseOrder::find($request->order);
            $rules['amount'] = 'required|numeric|min:0|max:'.$order->due;
        }

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            if ($request->payment_type == 1) {
                $cash = Cash::first();

                if ($request->amount > $cash->amount)
                    $validator->errors()->add('amount', 'Insufficient balance.');
            } else {
                if ($request->account != '') {
                    $account = BankAccount::find($request->account);

                    if ($request->amount > $account->balance)
                        $validator->errors()->add('amount', 'Insufficient balance.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $order = PurchaseOrder::find($request->order);
        $order->next_payment = $request->next_payment?? $order->next_payment;
        $order->save();

        if ($request->payment_type == 1 || $request->payment_type == 3) {
            $payment = new PurchasePayment();
            $payment->purchase_order_id = $order->id;
            $payment->transaction_method = $request->payment_type;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->note = $request->note;
            $payment->save();

            if ($request->payment_type == 1)
                Cash::first()->decrement('amount', $request->amount);
            else
                MobileBanking::first()->decrement('amount', $request->amount);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Paid to '.$order->supplier->name.' for '.$order->order_no;
            $log->transaction_type = 3;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 1;
            $log->account_head_sub_type_id = 1;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->purchase_payment_id = $payment->id;
            $log->save();

        } else {
            $image = 'img/no_image.png';

            if ($request->cheque_image) {
                // Upload Image
                $file = $request->file('cheque_image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'public/uploads/purchase_payment_cheque';
                $file->move($destinationPath, $filename);

                $image = 'uploads/purchase_payment_cheque/'.$filename;
            }

            $payment = new PurchasePayment();
            $payment->purchase_order_id = $order->id;
            $payment->transaction_method = 2;
            $payment->bank_id = $request->bank;
            $payment->branch_id = $request->branch;
            $payment->bank_account_id = $request->account;
            $payment->cheque_no = $request->cheque_no;
            $payment->cheque_image = $image;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->note = $request->note;
            $payment->save();

            BankAccount::find($request->account)->decrement('balance', $request->amount);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Paid to '.$order->supplier->name.' for '.$order->order_no;
            $log->transaction_type = 3;
            $log->transaction_method = 2;
            $log->account_head_type_id = 1;
            $log->account_head_sub_type_id = 1;
            $log->bank_id = $request->bank;
            $log->branch_id = $request->branch;
            $log->bank_account_id = $request->account;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_image = $image;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->purchase_payment_id = $payment->id;
            $log->save();
        }

        $order->increment('paid', $request->amount);
        $order->decrement('due', $request->amount);

        return response()->json(['success' => true, 'message' => 'Payment has been completed.', 'redirect_url' => route('purchase_receipt.payment_details', ['payment' => $payment->id])]);
    }

    public function makeRefund(Request $request) {
        $rules = [
            'order' => 'required',
            'payment_type' => 'required',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
        ];

        if ($request->payment_type == '2') {
            $rules['bank'] = 'required';
            $rules['branch'] = 'required';
            $rules['account'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }

        if ($request->order != '') {
            $order = PurchaseOrder::find($request->order);
            $rules['amount'] = 'required|numeric|min:0|max:'.$order->refund;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $order = PurchaseOrder::find($request->order);

        if ($request->payment_type == 1 || $request->payment_type == 3) {
            $payment = new PurchasePayment();
            $payment->purchase_order_id = $order->id;
            $payment->type = 2;
            $payment->transaction_method = $request->payment_type;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->note = $request->note;
            $payment->save();

            if ($request->payment_type == 1)
                Cash::first()->increment('amount', $request->amount);
            else
                MobileBanking::first()->increment('amount', $request->amount);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Refund from '.$order->supplier->name.' for '.$order->order_no;
            $log->transaction_type = 5;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 20;
            $log->account_head_sub_type_id = 42;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->purchase_payment_id = $payment->id;
            $log->save();

        } else {
            $image = 'img/no_image.png';

            if ($request->cheque_image) {
                // Upload Image
                $file = $request->file('cheque_image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'public/uploads/purchase_payment_cheque';
                $file->move($destinationPath, $filename);

                $image = 'uploads/purchase_payment_cheque/'.$filename;
            }

            $payment = new PurchasePayment();
            $payment->purchase_order_id = $order->id;
            $payment->type = 2;
            $payment->transaction_method = 2;
            $payment->bank_id = $request->bank;
            $payment->branch_id = $request->branch;
            $payment->bank_account_id = $request->account;
            $payment->cheque_no = $request->cheque_no;
            $payment->cheque_image = $image;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->note = $request->note;
            $payment->save();

            BankAccount::find($request->account)->increment('balance', $request->amount);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Refund from '.$order->supplier->name.' for '.$order->order_no;
            $log->transaction_type = 5;
            $log->transaction_method = 2;
            $log->account_head_type_id = 20;
            $log->account_head_sub_type_id = 42;
            $log->bank_id = $request->bank;
            $log->branch_id = $request->branch;
            $log->bank_account_id = $request->account;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_image = $image;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->purchase_payment_id = $payment->id;
            $log->save();
        }

        $order->decrement('refund', $request->amount);

        return response()->json(['success' => true, 'message' => 'Refund has been completed.', 'redirect_url' => route('purchase_receipt.payment_details', ['payment' => $payment->id])]);
    }

    public function purchasePaymentDetails(PurchasePayment $payment) {
        $payment->amount_in_word = DecimalToWords::convert($payment->amount,'Taka',
            'Poisa');
        return view('purchase.receipt.payment_details', compact('payment'));
    }

    public function purchasePaymentPrint(PurchasePayment $payment) {
        $payment->amount_in_word = DecimalToWords::convert($payment->amount,'Taka',
            'Poisa');
        return view('purchase.receipt.payment_print', compact('payment'));
    }

    public function purchaseInventory() {
        return view('purchase.inventory.all');
    }

    public function purchaseInventoryDetails(PurchaseProduct $product, Warehouse $warehouse) {
        return view('purchase.inventory.details', compact('product', 'warehouse'));
    }

    public function purchaseInventoryQrCode(PurchaseProduct $product, Warehouse $warehouse) {
        $rows = PurchaseInventory::where('purchase_product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->where('quantity', '>', 0)->get();

        return view('purchase.inventory.qr_code', compact('rows', 'product', 'warehouse'));
    }

    public function purchaseReceiptEdit(PurchaseOrder $order) {
        $suppliers = Supplier::where('status', 1)->orderBy('name')->get();
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $products = PurchaseProduct::where('status', 1)->orderBy('name')->get();

        return view('purchase.receipt.edit', compact('order', 'suppliers',
            'warehouses', 'products'));
    }

    public function purchaseReceiptEditPost(PurchaseOrder $order, Request $request) {
        $validator = Validator::make($request->all(), [
            'supplier' => ['required'],
            'warehouse' => ['required'],
            'date' => ['required', 'date'],
            'product' => ['required'],
            'serial.*' => [
                'required',
                Rule::unique('purchase_order_purchase_product', 'serial_no')->ignore($order->id, 'purchase_order_id'),
            ],
            'warranty.*' => ['required'],
            'quantity.*' => ['required', 'numeric', 'min:.01'],
            'unit_price.*' => ['required', 'numeric', 'min:0'],
            'including_price.*' => ['required', 'numeric', 'min:0'],
            'selling_price.*' => ['required', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $previousSerials = [];

        foreach ($order->products as $product)
            $previousSerials[] = $product->pivot->serial_no;

        $counter = 0;
        $total = 0;
        foreach ($request->serial as $serial) {
            if (in_array($serial, $previousSerials)) {
                // Old Item
                $product = PurchaseProduct::find($request->product[$counter]);

                $purchaseProduct = DB::table('purchase_order_purchase_product')
                    ->where('serial_no', $serial)->first();

                DB::table('purchase_order_purchase_product')
                    ->where('serial_no', $serial)
                    ->update([
                        'purchase_product_id' => $request->product[$counter],
                        'name' => $product->name,
                        'type' => $request->type[$counter],
                        'serial_no' => $request->serial[$counter],
                        'warranty' => $request->warranty[$counter],
                        'quantity' => $request->quantity[$counter],
                        'unit_price' => $request->unit_price[$counter],
                        'including_price' => $request->including_price[$counter],
                        'selling_price' => $request->selling_price[$counter],
                        'total' => $request->quantity[$counter] * $request->unit_price[$counter],
                    ]);

                $total += $request->quantity[$counter] * $request->unit_price[$counter];

                // Inventory
                $inventory = PurchaseInventory::where('serial_no', $serial)->first();
                $inventory->purchase_product_id = $product->id;
                $inventory->quantity = $request->quantity[$counter];
                $inventory->warranty = $request->warranty[$counter];
                $inventory->unit_price = $request->unit_price[$counter];
                $inventory->including_price = $request->including_price[$counter];
                $inventory->selling_price = $request->selling_price[$counter];
                $inventory->warehouse_id = $request->warehouse;
                $inventory->save();

                if ($request->quantity[$counter] != $purchaseProduct->quantity) {
                    $inventoryLog = new PurchaseInventoryLog();
                    $inventoryLog->purchase_product_id = $product->id;

                    if ($request->quantity[$counter] > $purchaseProduct->quantity) {
                        $inventoryLog->type = 3;
                        $inventoryLog->quantity = $request->quantity[$counter] - $purchaseProduct->quantity;
                    } else {
                        $inventoryLog->type = 4;
                        $inventoryLog->quantity = $purchaseProduct->quantity - $request->quantity[$counter];
                    }

                    $inventoryLog->date = date('Y-m-d');
                    $inventoryLog->warehouse_id = $request->warehouse;
                    $inventoryLog->unit_price = $request->unit_price[$counter];
                    $inventoryLog->supplier_id = $request->supplier;
                    $inventoryLog->sales_order_id = $order->id;
                    $inventoryLog->save();
                }

                if (($key = array_search($serial, $previousSerials)) !== false) {
                    unset($previousSerials[$key]);
                }
            } else {
                // New Item
                $product = PurchaseProduct::find($request->product[$counter]);

                $order->products()->attach($product->id, [
                    'name' => $product->name,
                    'type' => $request->type[$counter],
                    'serial_no' => $request->serial[$counter],
                    'warranty' => $request->warranty[$counter],
                    'quantity' => $request->quantity[$counter],
                    'unit_price' => $request->unit_price[$counter],
                    'including_price' => $request->including_price[$counter],
                    'selling_price' => $request->selling_price[$counter],
                    'total' => $request->quantity[$counter] * $request->unit_price[$counter],
                ]);

                $total += $request->quantity[$counter] * $request->unit_price[$counter];

                // Inventory
                $inventory = new PurchaseInventory();
                $inventory->purchase_product_id = $product->id;
                $inventory->quantity = $request->quantity[$counter];
                $inventory->serial_no = $request->serial[$counter];
                $inventory->warranty = $request->warranty[$counter];
                $inventory->unit_price = $request->unit_price[$counter];
                $inventory->including_price = $request->including_price[$counter];
                $inventory->selling_price = $request->selling_price[$counter];
                $inventory->warehouse_id = $request->warehouse;
                $inventory->save();

                $inventoryLog = new PurchaseInventoryLog();
                $inventoryLog->purchase_product_id = $product->id;
                $inventoryLog->type = 3;
                $inventoryLog->date = date('Y-m-d');
                $inventoryLog->warehouse_id = $request->warehouse;
                $inventoryLog->quantity = $request->quantity[$counter];
                $inventoryLog->unit_price = $request->unit_price[$counter];
                $inventoryLog->supplier_id = $request->supplier;
                $inventoryLog->save();
            }

            $counter++;
        }

        // Delete items
        foreach ($previousSerials as $serial) {
            $purchaseProduct = DB::table('purchase_order_purchase_product')->where('purchase_order_id', $order->id)
                ->where('serial_no', $serial)->first();

            $inventory = PurchaseInventory::where('serial_no', $serial)
                ->where('purchase_product_id', $purchaseProduct->purchase_product_id)->first();

            $inventoryLog = new PurchaseInventoryLog();
            $inventoryLog->purchase_product_id = $purchaseProduct->purchase_product_id;
            $inventoryLog->type = 4;
            $inventoryLog->quantity = $purchaseProduct->quantity;
            $inventoryLog->date = date('Y-m-d');
            $inventoryLog->warehouse_id = $request->warehouse;
            $inventoryLog->unit_price = $purchaseProduct->unit_price;
            $inventoryLog->supplier_id = $request->supplier;
            $inventoryLog->sales_order_id = $order->id;
            $inventoryLog->save();

            $inventory->delete();
            DB::table('purchase_order_purchase_product')->where('purchase_order_id', $order->id)
                ->where('serial_no', $serial)->delete();
        }

        // Update Order
        $order->supplier_id = $request->supplier;
        $order->warehouse_id = $request->warehouse;
        $order->date = $request->date;
        $order->next_payment = $request->next_payment??$order->next_payment;
        $order->transport_cost = $request->transport_cost;

        if ($total > $order->total) {
            if ($order->refund > 0) {
                if ($order->refund > $total - $order->total) {
                    $order->decrement('refund', $total - $order->total);
                } else  {
                    $previousRefund = $order->refund;
                    $order->decrement('refund', $order->refund);
                    $order->increment('due', $total - $order->total- $previousRefund);
                }
            } else {
                $order->increment('due', $total - $order->total);
            }

        } elseif($order->total > $total) {
            if ($order->due >= 0) {
                if ($order->due > $order->total - $total) {
                    $order->decrement('due', $order->total - $total);
                } else {
                    $previousDue = $order->due;
                    $order->decrement('due', $order->due);
                    $order->increment('refund', $order->total - $total - $previousDue);
                }
            } else {
                $order->increment('refund', $order->total - $total);
            }
        }

        $order->total = $total;
        $order->save();

        return redirect()->route('purchase_receipt.details', ['order' => $order->id]);
    }

    public function checkDeleteStatus(Request $request) {
        $inventory = PurchaseInventory::where('serial_no', $request->serial)->first();

        if ($inventory) {
            if ($inventory->quantity > 0) {
                return response()->json(['success' => true, 'message' => 'This product not sold.']);
            } else {
                return response()->json(['success' => false, 'message' => 'This product already sold.']);
            }
        }

        return response()->json(['success' => true, 'message' => 'Serial not found.']);
    }

    public function purchaseProductJson(Request $request) {
        if (!$request->searchTerm) {
            $products = PurchaseProduct::where('status', 1)->orderBy('name')->limit(10)->get();
        } else {
            $products = PurchaseProduct::where('status', 1)->where('name', 'like', '%'.$request->searchTerm.'%')->orderBy('name')->limit(10)->get();
        }

        $data = array();

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->id,
                'text' => $product->name
            ];
        }

        echo json_encode($data);
    }

    public function purchaseReceiptDatatable() {
        $query = PurchaseOrder::with('supplier');

        return DataTables::eloquent($query)
            ->addColumn('supplier', function(PurchaseOrder $order) {
                return $order->supplier->name;
            })
            ->addColumn('action', function(PurchaseOrder $order) {
                return '<a href="'.route('purchase_receipt.details', ['order' => $order->id]).'" class="btn btn-warning btn-sm">View</a> <a href="'.route('purchase_receipt.qr_code', ['order' => $order->id]).'" class="btn btn-warning btn-sm">QR Code</a> <a href="'.route('purchase_receipt.edit', ['order' => $order->id]).'" class="btn btn-warning btn-sm">Edit</a>';
            })
            ->editColumn('date', function(PurchaseOrder $order) {
                return $order->date->format('j F, Y');
            })
            ->editColumn('next_payment', function(PurchaseOrder $order) {
                return $order->next_payment? $order->next_payment->format('j F, Y'):'';
            })
            ->editColumn('total', function(PurchaseOrder $order) {
                return '৳'.number_format($order->total, 2);
            })
            ->editColumn('paid', function(PurchaseOrder $order) {
                return '৳'.number_format($order->paid, 2);
            })
            ->editColumn('due', function(PurchaseOrder $order) {
                return '৳'.number_format($order->due, 2);
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function purchaseInventoryDatatable() {
        $query = PurchaseInventory::with('product', 'warehouse')
            ->groupBy('purchase_product_id', 'warehouse_id')
            ->select(DB::raw('sum(`quantity`) as quantity, sum(`quantity` * `selling_price`) as total_selling_price, purchase_product_id, warehouse_id'));

        return DataTables::eloquent($query)
            ->addColumn('product', function(PurchaseInventory $inventory) {
                return $inventory->product->name;
            })
            ->addColumn('warehouse', function(PurchaseInventory $inventory) {
                return $inventory->warehouse->name;
            })
            ->addColumn('action', function(PurchaseInventory $inventory) {
                return '<a href="'.route('purchase_inventory.details', ['product' => $inventory->purchase_product_id, 'warehouse' => $inventory->warehouse->id]).'" class="btn btn-warning btn-sm">Details</a> <a href="'.route('purchase_inventory.qr_code', ['product' => $inventory->purchase_product_id, 'warehouse' => $inventory->warehouse->id]).'" class="btn btn-warning btn-sm">QR Code</a>';
                //return '<a href="'.route('purchase_inventory.details', ['product' => $inventory->purchase_product_id, 'warehouse' => $inventory->warehouse->id]).'" class="btn btn-warning btn-sm">Details</a>';

            })
            ->editColumn('quantity', function(PurchaseInventory $inventory) {
                return number_format($inventory->quantity, 2);
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function purchaseInventoryDetailsDatatable() {
        $query = PurchaseInventoryLog::where('purchase_product_id', request('product_id'))
            ->where('warehouse_id', request('warehouse_id'))
            ->with('product', 'supplier', 'order');

        return DataTables::eloquent($query)
            ->editColumn('date', function(PurchaseInventoryLog $log) {
                return $log->date->format('j F, Y');
            })
            ->editColumn('type', function(PurchaseInventoryLog $log) {
                if ($log->type == 1)
                    return '<span class="label label-success">In</span>';
                elseif ($log->type == 2)
                    return '<span class="label label-danger">Out</span>';
                elseif ($log->type == 3)
                    return '<span class="label label-success">Add</span>';
                else
                    return '<span class="label label-danger">Return</span>';
            })
            ->editColumn('quantity', function(PurchaseInventoryLog $log) {
                return number_format($log->quantity, 2);
            })
            ->editColumn('unit_price', function(PurchaseInventoryLog $log) {
                if ($log->unit_price)
                    return '৳'.number_format($log->unit_price, 2);
                else
                    return '';
            })
            ->editColumn('supplier', function(PurchaseInventoryLog $log) {
                if ($log->supplier)
                    return $log->supplier->name;
                else
                    return '';
            })
            ->editColumn('order', function(PurchaseInventoryLog $log) {
                if ($log->order)
                    return '<a href="'.route('sale_receipt.details', ['order' => $log->order->id]).'">'.$log->order->order_no.'</a>';
                else
                    return '';
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['type', 'order'])
            ->filter(function ($query) {
                if (request()->has('date') && request('date') != '') {
                    $dates = explode(' - ', request('date'));
                    if (count($dates) == 2) {
                        $query->where('date', '>=', $dates[0]);
                        $query->where('date', '<=', $dates[1]);
                    }
                }

                if (request()->has('type') && request('type') != '') {
                    $query->where('type', request('type'));
                }
            })
            ->toJson();
    }
}
