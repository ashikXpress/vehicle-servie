<?php

namespace App\Http\Controllers;

use App\Model\Bank;
use App\Model\BankAccount;
use App\Model\Cash;
use App\Model\Customer;
use App\Model\MobileBanking;
use App\Model\PurchaseInventory;
use App\Model\PurchaseInventoryLog;
use App\Model\PurchaseOrder;
use App\Model\SalePayment;
use App\Model\SalesOrder;
use App\Model\Service;
use App\Model\TransactionLog;
use App\Model\Warehouse;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use SakibRahaman\DecimalToWords\DecimalToWords;

class SaleController extends Controller
{
    public function salesOrder() {
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('sale.sales_order.create', compact('warehouses', 'banks',
        'customers'));
    }

    public function salesOrderPost(Request $request) {
        $total = $request->total;
        $due = $request->due_total;

        $rules = [
            'customer' => 'required',
            'date' => 'required|date',
            'received_by' => 'nullable|string|max:255',
            'warehouse' => 'required',
            'vat' => 'required|numeric|min:0',
            'service_vat' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'service_discount' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0|max:'.$total,
            'payment_type' => 'required',
        ];

        if ($request->serial) {
            $rules['serial.*'] = 'required';
            $rules['product_name.*'] = 'required';
            $rules['quantity.*'] = 'required|numeric|min:.01';
            $rules['unit_price.*'] = 'required|numeric|min:0';
        }

        if ($request->service_name) {
            $rules['service_name.*'] = 'required';
            $rules['service_quantity.*'] = 'required|numeric|min:.01';
            $rules['service_unit_price.*'] = 'required|numeric|min:0';
        }

        if ($due > 0)
            $rules['next_payment'] = 'required|date';

        if ($request->payment_type == '2') {
            $rules['bank'] = 'required';
            $rules['branch'] = 'required';
            $rules['account'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }

        $request->validate($rules);

        $available = true;
        $message = '';
        $counter = 0;

        if ($request->serial) {
            foreach ($request->serial as $serial) {
                $inventory = PurchaseInventory::where('serial_no', $request->serial[$counter])
                    ->where('warehouse_id', $request->warehouse)
                    ->first();

                if ($request->quantity[$counter] > $inventory->quantity) {
                    $available = false;
                    $message = 'Insufficient ' . $inventory->product->name;
                    break;
                }
                $counter++;
            }
        }

        if (!$available) {
            return redirect()->back()->withInput()->with('message', $message);
        }

        $order = new SalesOrder();
        $order->order_no = rand(10000000, 99999999);
        $order->customer_id = $request->customer;
        $order->warehouse_id = $request->warehouse;
        $order->received_by = $request->received_by;
        $order->date = $request->date;
        $order->sub_total = 0;
        $order->vat_status = $request->vat_status;
        $order->vat_percentage = $request->vat;
        $order->vat = 0;
        $order->discount = $request->discount;
        $order->total = 0;
        $order->paid = $request->paid;
        $order->due = 0;
        $order->service_sub_total = 0;
        $order->service_vat_percentage = $request->service_vat;
        $order->service_vat = 0;
        $order->service_discount = $request->service_discount;
        $order->created_by = Auth::user()->id;
        $order->save();

        $counter = 0;
        $subTotal = 0;
        $buyingPrice = 0;

        if  ($request->serial) {
            foreach ($request->serial as $serial) {
                $inventory = PurchaseInventory::where('serial_no', $request->serial[$counter])
                    ->where('warehouse_id', $request->warehouse)
                    ->with('product')
                    ->first();

                $buyingPrice += $inventory->including_price * $request->quantity[$counter];

                $order->products()->attach($inventory->product->id, [
                    'name' => $inventory->product->name,
                    'serial' => $request->serial[$counter],
                    'warranty' => $inventory->warranty,
                    'quantity' => $request->quantity[$counter],
                    'unit_price' => $request->unit_price[$counter],
                    'total' => $request->quantity[$counter] * $request->unit_price[$counter],
                ]);

                $inventory->decrement('quantity', $request->quantity[$counter]);

                $inventoryLog = new PurchaseInventoryLog();
                $inventoryLog->purchase_product_id = $inventory->product->id;
                $inventoryLog->type = 2;
                $inventoryLog->date = $request->date;
                $inventoryLog->warehouse_id = $request->warehouse;
                $inventoryLog->quantity = $request->quantity[$counter];
                $inventoryLog->unit_price = $request->unit_price[$counter];
                $inventoryLog->sales_order_id = $order->id;
                $inventoryLog->save();

                $subTotal += $request->quantity[$counter] * $request->unit_price[$counter];
                $counter++;
            }
        }

        $counter = 0;
        $serviceSubTotal = 0;
        if ($request->service_name) {
            foreach ($request->service_name as $service) {
                $service = new Service();
                $service->sales_order_id = $order->id;
                $service->name = $request->service_name[$counter];
                $service->quantity = $request->service_quantity[$counter];
                $service->unit_price = $request->service_unit_price[$counter];
                $service->total = $request->service_quantity[$counter] * $request->service_unit_price[$counter];
                $service->save();

                $serviceSubTotal += $request->service_quantity[$counter] * $request->service_unit_price[$counter];
                $counter++;
            }
        }

        $order->sub_total = $subTotal;
        $order->service_sub_total = $serviceSubTotal;
        $product_vat = ($subTotal * $request->vat) / 100;
        $service_vat = ($serviceSubTotal * $request->service_vat) / 100;
        $vat = ($subTotal * $request->vat) / 100;
        $order->vat = $vat;
        $serviceVat = ($serviceSubTotal * $request->service_vat) / 100;
        $order->service_vat = $serviceVat;

        if ($request->vat_status == 1) {
            $total = $subTotal + $serviceSubTotal + $product_vat + $service_vat - $request->discount - $request->service_discount;
        } else {
            $total = $subTotal + $serviceSubTotal - $request->discount - $request->service_discount;
        }


        $order->total = $total;
        $due = $total - $request->paid;
        $order->due = $due;
        $order->next_payment = $due > 0 ? $request->next_payment : null;
        $order->save();

        // Sales Payment
        if ($request->paid > 0) {
            if ($request->payment_type == 1 || $request->payment_type == 3) {
                $payment = new SalePayment();
                $payment->sales_order_id = $order->id;
                $payment->transaction_method = $request->payment_type;
                $payment->received_type = 1;
                $payment->amount = $request->paid;
                $payment->date = $request->date;
                $payment->save();

                if ($request->payment_type == 1)
                    Cash::first()->increment('amount', $request->paid);
                else
                    MobileBanking::first()->increment('amount', $request->paid);

                $log = new TransactionLog();
                $log->date = $request->date;
                $log->particular = 'Payment for '.$order->order_no;
                $log->transaction_type = 1;
                $log->transaction_method = $request->payment_type;
                $log->account_head_type_id = 2;
                $log->account_head_sub_type_id = 2;
                $log->amount = $request->paid;
                $log->sale_payment_id = $payment->id;
                $log->save();
            } else {
                $image = 'img/no_image.png';

                if ($request->cheque_image) {
                    // Upload Image
                    $file = $request->file('cheque_image');
                    $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                    $destinationPath = 'public/uploads/sales_payment_cheque';
                    $file->move($destinationPath, $filename);

                    $image = 'uploads/sales_payment_cheque/'.$filename;
                }

                $payment = new SalePayment();
                $payment->sales_order_id = $order->id;
                $payment->transaction_method = 2;
                $payment->received_type = 1;
                $payment->bank_id = $request->bank;
                $payment->branch_id = $request->branch;
                $payment->bank_account_id = $request->account;
                $payment->cheque_no = $request->cheque_no;
                $payment->cheque_image = $image;
                $payment->amount = $request->paid;
                $payment->date = $request->date;
                $payment->save();

                BankAccount::find($request->account)->increment('balance', $request->paid);

                $log = new TransactionLog();
                $log->date = $request->date;
                $log->particular = 'Payment for '.$order->order_no;
                $log->transaction_type = 1;
                $log->transaction_method = 2;
                $log->account_head_type_id = 2;
                $log->account_head_sub_type_id = 2;
                $log->bank_id = $request->bank;
                $log->branch_id = $request->branch;
                $log->bank_account_id = $request->account;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_image = $image;
                $log->amount = $request->paid;
                $log->sale_payment_id = $payment->id;
                $log->save();
            }
        }

        // Buying Price log
        $log = new TransactionLog();
        $log->date = $request->date;
        $log->particular = 'Buying price for '.$order->order_no;
        $log->transaction_type = 4;
        $log->transaction_method = 0;
        $log->account_head_type_id = 0;
        $log->account_head_sub_type_id = 0;
        $log->amount = $buyingPrice;
        $log->sales_order_id = $order->id;
        $log->save();

        return redirect()->route('sale_receipt.details', ['order' => $order->id]);
    }

    public function saleReceipt() {
        $banks = Bank::where('status', 1)->orderBy('name')->get();

        return view('sale.receipt.all', compact('banks'));
    }

    public function makePayment(Request $request) {
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
            $order = SalesOrder::find($request->order);
            $rules['amount'] = 'required|numeric|min:0|max:'.$order->due;
        }

        if ($request->amount < $order->due)
            $rules['next_payment_date'] = 'required|date';

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $order = SalesOrder::find($request->order);

        if ($request->payment_type == 1 || $request->payment_type == 3) {
            $payment = new SalePayment();
            $payment->sales_order_id = $order->id;
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
            $log->particular = 'Payment from '.$order->customer->name.' for '.$order->order_no;
            $log->transaction_type = 1;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 2;
            $log->account_head_sub_type_id = 2;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->sale_payment_id = $payment->id;
            $log->save();
        } else {
            $image = 'img/no_image.png';

            if ($request->cheque_image) {
                // Upload Image
                $file = $request->file('cheque_image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'public/uploads/sales_payment_cheque';
                $file->move($destinationPath, $filename);

                $image = 'uploads/sales_payment_cheque/'.$filename;
            }

            $payment = new SalePayment();
            $payment->sales_order_id = $order->id;
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
            $log->particular = 'Payment from '.$order->customer->name.' for '.$order->order_no;
            $log->transaction_type = 1;
            $log->transaction_method = 2;
            $log->account_head_type_id = 2;
            $log->account_head_sub_type_id = 2;
            $log->bank_id = $request->bank;
            $log->branch_id = $request->branch;
            $log->bank_account_id = $request->account;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_image = $image;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->sale_payment_id = $payment->id;
            $log->save();
        }

        $order->increment('paid', $request->amount);
        $order->decrement('due', $request->amount);

        if ($order->due > 0) {
            $order->next_payment = $request->next_payment_date;
        } else {
            $order->next_payment = null;
        }

        $order->save();

        return response()->json(['success' => true, 'message' => 'Payment has been completed.', 'redirect_url' => route('sale_receipt.payment_details', ['payment' => $payment->id])]);
    }

    public function saleReceiptDetails(SalesOrder $order) {
        return view('sale.receipt.details', compact('order'));
    }

    public function saleReceiptPrint(SalesOrder $order) {
        $order->amount_in_word = DecimalToWords::convert($order->total,'Taka',
            'Poisa');

        return view('sale.receipt.print', compact('order'));
    }

    public function salePaymentDetails(SalePayment $payment) {
        $payment->amount_in_word = DecimalToWords::convert($payment->amount,'Taka',
            'Poisa');
        return view('sale.receipt.payment_details', compact('payment'));
    }

    public function salePaymentPrint(SalePayment $payment) {
        $payment->amount_in_word = DecimalToWords::convert($payment->amount,'Taka',
            'Poisa');
        return view('sale.receipt.payment_print', compact('payment'));
    }

    public function customerPayment() {
        $banks = Bank::where('status', 1)->orderBy('name')->get();

        return view('sale.customer_payment.all', compact('banks'));
    }

    public function customerPaymentGetOrders(Request $request) {
        $orders = SalesOrder::where('customer_id', $request->customerId)
            ->where('due', '>', 0)
            ->orderBy('order_no')
            ->get()->toArray();

        return response()->json($orders);
    }

    public function customerPaymentGetRefundOrders(Request $request) {
        $orders = SalesOrder::where('customer_id', $request->customerId)
            ->where('refund', '>', 0)
            ->orderBy('order_no')
            ->get()->toArray();

        return response()->json($orders);
    }

    public function customerMakePayment(Request $request) {
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
            $order = SalesOrder::find($request->order);
            $rules['amount'] = 'required|numeric|min:0|max:'.$order->due;
        }

        if ($request->amount < $order->due)
            $rules['next_payment_date'] = 'required|date';

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $order = SalesOrder::find($request->order);

        if ($request->payment_type == 1 || $request->payment_type == 3) {
            $payment = new SalePayment();
            $payment->sales_order_id = $order->id;
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
            $log->particular = 'Payment from '.$order->customer->name.' for '.$order->order_no;
            $log->transaction_type = 1;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 2;
            $log->account_head_sub_type_id = 2;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->sale_payment_id = $payment->id;
            $log->save();
        } else {
            $image = 'img/no_image.png';

            if ($request->cheque_image) {
                // Upload Image
                $file = $request->file('cheque_image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'public/uploads/sales_payment_cheque';
                $file->move($destinationPath, $filename);

                $image = 'uploads/sales_payment_cheque/'.$filename;
            }

            $payment = new SalePayment();
            $payment->sales_order_id = $order->id;
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
            $log->particular = 'Payment from '.$order->customer->name.' for '.$order->order_no;
            $log->transaction_type = 1;
            $log->transaction_method = 2;
            $log->account_head_type_id = 2;
            $log->account_head_sub_type_id = 2;
            $log->bank_id = $request->bank;
            $log->branch_id = $request->branch;
            $log->bank_account_id = $request->account;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_image = $image;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->sale_payment_id = $payment->id;
            $log->save();
        }

        $order->increment('paid', $request->amount);
        $order->decrement('due', $request->amount);

        if ($order->due > 0) {
            $order->next_payment = $request->next_payment_date;
        } else {
            $order->next_payment = null;
        }

        $order->save();

        return response()->json(['success' => true, 'message' => 'Payment has been completed.', 'redirect_url' => route('sale_receipt.payment_details', ['payment' => $payment->id])]);
    }

    public function customerMakeRefund(Request $request) {
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
            $order = SalesOrder::find($request->order);
            $rules['amount'] = 'required|numeric|min:0|max:' . $order->refund;
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

        $order = SalesOrder::find($request->order);

        if ($request->payment_type == 1 || $request->payment_type == 3) {
            $payment = new SalePayment();
            $payment->sales_order_id = $order->id;
            $payment->type = 2;
            $payment->transaction_method = $request->payment_type;
            $payment->received_type = 1;
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
            $log->particular = 'Refund to '.$order->customer->name.' for '.$order->order_no;
            $log->transaction_type = 6;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 21;
            $log->account_head_sub_type_id = 43;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->sale_payment_id = $payment->id;
            $log->save();
        } else {
            $image = 'img/no_image.png';

            if ($request->cheque_image) {
                // Upload Image
                $file = $request->file('cheque_image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'public/uploads/sales_payment_cheque';
                $file->move($destinationPath, $filename);

                $image = 'uploads/sales_payment_cheque/'.$filename;
            }

            $payment = new SalePayment();
            $payment->sales_order_id = $order->id;
            $payment->type = 2;
            $payment->transaction_method = 2;
            $payment->received_type = 1;
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
            $log->particular = 'Refund to '.$order->customer->name.' for '.$order->order_no;
            $log->transaction_type = 6;
            $log->transaction_method = 2;
            $log->account_head_type_id = 21;
            $log->account_head_sub_type_id = 43;
            $log->bank_id = $request->bank;
            $log->branch_id = $request->branch;
            $log->bank_account_id = $request->account;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_image = $image;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->sale_payment_id = $payment->id;
            $log->save();
        }

        $order->decrement('refund', $request->amount);

        return response()->json(['success' => true, 'message' => 'Refund has been completed.', 'redirect_url' => route('sale_receipt.payment_details', ['payment' => $payment->id])]);
    }

    public function saleInformation() {
        return view('sale.product_sale_information.index');
    }

    public function saleInformationPost(Request $request) {
        $product = DB::table('purchase_order_purchase_product')
            ->where('serial_no', $request->serial)
            ->first();

        $sale = DB::table('purchase_product_sales_order')
            ->where('serial', $request->serial)->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Invalid serial.']);
        } elseif ($product->quantity > 1) {
            return response()->json(['success' => false, 'message' => 'This serial has many products.']);
        } elseif (!$sale) {
            return response()->json(['success' => false, 'message' => 'This serial not sell yet.']);
        } else {
            $order = SalesOrder::find($sale->sales_order_id);
            $purchaseOrder = PurchaseOrder::find($product->purchase_order_id);

            return response()->json(['success' => true, 'message' => 'This serial is sold.', 'redirect_url' => route('sale_information.print', ['purchaseOrder' => $purchaseOrder->id, 'saleOrder' => $order->id, 'serial' => $request->serial])]);
        }
    }

    public function saleInformationPrint(PurchaseOrder $purchaseOrder, SalesOrder $saleOrder) {
        $saleOrder->amount_in_word = DecimalToWords::convert($saleOrder->total,'Taka',
            'Poisa');

        return view('sale.product_sale_information.print', compact('purchaseOrder',
            'saleOrder'));
    }

    public function saleReceiptEdit(SalesOrder $order) {
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('sale.receipt.edit', compact('order', 'warehouses',
            'banks', 'customers'));
    }

    public function saleReceiptEditPost(SalesOrder $order, Request $request) {
        $total = $request->total;
        $due = $request->due_total;
        $refund = $request->refund;

        $rules = [
            'customer' => 'required',
            'date' => 'required|date',
            'received_by' => 'nullable|string|max:255',
            'warehouse' => 'required',
            'vat' => 'required|numeric|min:0',
            'service_vat' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'service_discount' => 'required|numeric|min:0',
        ];

        if ($request->serial) {
            $rules['serial.*'] = 'required';
            $rules['product_name.*'] = 'required';
            $rules['quantity.*'] = 'required|numeric|min:.01';
            $rules['unit_price.*'] = 'required|numeric|min:0';
        }

        if ($request->service_name) {
            $rules['service_name.*'] = 'required';
            $rules['service_quantity.*'] = 'required|numeric|min:.01';
            $rules['service_unit_price.*'] = 'required|numeric|min:0';
        }

        if ($due > 0)
            $rules['next_payment'] = 'required|date';

        if ($request->payment_type == '2') {
            $rules['bank'] = 'required';
            $rules['branch'] = 'required';
            $rules['account'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }

        $request->validate($rules);

        $available = true;
        $message = '';
        $counter = 0;

        $previousSerials = [];

        foreach ($order->products as $product)
            $previousSerials[] = $product->pivot->serial;

        if ($request->serial) {
            foreach ($request->serial as $serial) {
                if (in_array($serial, $previousSerials)) {
                    $inventory = PurchaseInventory::where('serial_no', $request->serial[$counter])
                        ->where('warehouse_id', $request->warehouse)
                        ->first();

                    $orderProduct = DB::table('purchase_product_sales_order')
                        ->where('sales_order_id', $order->id)
                        ->where('serial', $serial)
                        ->first();


                    if ($request->quantity[$counter] > $inventory->quantity+$orderProduct->quantity) {
                        $available = false;
                        $message = 'Insufficient ' . $inventory->product->name;
                        break;
                    }
                } else {
                    $inventory = PurchaseInventory::where('serial_no', $request->serial[$counter])
                        ->where('warehouse_id', $request->warehouse)
                        ->first();

                    if ($request->quantity[$counter] > $inventory->quantity) {
                        $available = false;
                        $message = 'Insufficient ' . $inventory->product->name;
                        break;
                    }
                }
                $counter++;
            }
        }

        if (!$available) {
            return redirect()->back()->withInput()->with('message', $message);
        }

        $counter = 0;
        $subTotal = 0;

        if  ($request->serial) {
            foreach ($request->serial as $serial) {
                if (in_array($serial, $previousSerials)) {
                    // Old Item

                    $purchaseProduct = DB::table('purchase_product_sales_order')
                        ->where('serial', $serial)
                        ->where('sales_order_id', $order->id)
                        ->first();

                    DB::table('purchase_product_sales_order')
                        ->where('serial', $serial)
                        ->where('sales_order_id', $order->id)
                        ->update([
                            'quantity' => $request->quantity[$counter],
                            'unit_price' => $request->unit_price[$counter],
                            'total' => $request->quantity[$counter] * $request->unit_price[$counter],
                        ]);

                    $subTotal += $request->quantity[$counter] * $request->unit_price[$counter];

                    // Inventory
                    $inventory = PurchaseInventory::where('serial_no', $serial)->first();

                    if ($request->quantity[$counter] > $purchaseProduct->quantity)
                        $inventory->quantity = ($inventory->quantity + $purchaseProduct->quantity) - $request->quantity[$counter];
                    elseif ($request->quantity[$counter] < $purchaseProduct->quantity) {
                        $diff = $purchaseProduct->quantity - $request->quantity[$counter];
                        $inventory->quantity = $inventory->quantity + $diff;
                    }
                    $inventory->save();

                    if ($request->quantity[$counter] != $purchaseProduct->quantity) {
                        $inventoryLog = new PurchaseInventoryLog();
                        $inventoryLog->purchase_product_id = $purchaseProduct->purchase_product_id;

                        if ($request->quantity[$counter] > $purchaseProduct->quantity) {
                            $inventoryLog->type = 3;
                            $inventoryLog->quantity = $request->quantity[$counter] - $purchaseProduct->quantity;
                        } else {
                            $inventoryLog->type = 4;
                            $inventoryLog->quantity = $purchaseProduct->quantity - $request->quantity[$counter];
                        }

                        $inventoryLog->date = date('Y-m-d');
                        $inventoryLog->warehouse_id = $inventory->warehouse_id;
                        $inventoryLog->unit_price = $inventory->unit_price;
                        $inventoryLog->sales_order_id = $order->id;
                        $inventoryLog->save();
                    }

                    if (($key = array_search($serial, $previousSerials)) !== false) {
                        unset($previousSerials[$key]);
                    }
                } else {
                    // New Item
                    $inventory = PurchaseInventory::where('serial_no', $serial)
                        ->where('warehouse_id', $request->warehouse)
                        ->with('product')
                        ->first();

                    $order->products()->attach($inventory->product->id, [
                        'name' => $inventory->product->name,
                        'serial' => $request->serial[$counter],
                        'warranty' => $inventory->warranty,
                        'quantity' => $request->quantity[$counter],
                        'unit_price' => $request->unit_price[$counter],
                        'total' => $request->quantity[$counter] * $request->unit_price[$counter],
                    ]);

                    $inventory->decrement('quantity', $request->quantity[$counter]);

                    $inventoryLog = new PurchaseInventoryLog();
                    $inventoryLog->purchase_product_id = $inventory->product->id;
                    $inventoryLog->type = 3;
                    $inventoryLog->date = date('Y-m-d');
                    $inventoryLog->warehouse_id = $request->warehouse;
                    $inventoryLog->quantity = $request->quantity[$counter];
                    $inventoryLog->unit_price = $request->unit_price[$counter];
                    $inventoryLog->sales_order_id = $order->id;
                    $inventoryLog->save();

                    $subTotal += $request->quantity[$counter] * $request->unit_price[$counter];
                }

                $counter++;
            }
        }

        // Delete items
        foreach ($previousSerials as $serial) {
            $purchaseProduct = DB::table('purchase_product_sales_order')->where('sales_order_id', $order->id)
                ->where('serial', $serial)->first();

            PurchaseInventory::where('serial_no', $serial)->increment('quantity', $purchaseProduct->quantity);

            $inventoryLog = new PurchaseInventoryLog();
            $inventoryLog->purchase_product_id = $purchaseProduct->purchase_product_id;
            $inventoryLog->type = 4;
            $inventoryLog->quantity = $purchaseProduct->quantity;
            $inventoryLog->date = date('Y-m-d');
            $inventoryLog->warehouse_id = $request->warehouse;
            $inventoryLog->unit_price = $purchaseProduct->unit_price;
            $inventoryLog->sales_order_id = $order->id;
            $inventoryLog->save();

            DB::table('purchase_product_sales_order')->where('sales_order_id', $order->id)
                ->where('serial', $serial)->delete();
        }

        // Services
        $order->services()->delete();

        $counter = 0;
        $serviceSubTotal = 0;
        if ($request->service_name) {
            foreach ($request->service_name as $service) {
                $service = new Service();
                $service->sales_order_id = $order->id;
                $service->name = $request->service_name[$counter];
                $service->quantity = $request->service_quantity[$counter];
                $service->unit_price = $request->service_unit_price[$counter];
                $service->total = $request->service_quantity[$counter] * $request->service_unit_price[$counter];
                $service->save();

                $serviceSubTotal += $request->service_quantity[$counter] * $request->service_unit_price[$counter];
                $counter++;
            }
        }

        // Update Order
        $order->customer_id = $request->customer;
        $order->warehouse_id = $request->warehouse;
        $order->received_by = $request->received_by;
        $order->date = $request->date;
        $order->sub_total = $subTotal;
        $order->vat_status = $request->vat_status;
        $order->vat_percentage = $request->vat;
        $vat = ($subTotal * $request->vat) / 100;
        $order->vat = $vat;
        $order->discount = $request->discount;
        $order->service_sub_total = $serviceSubTotal;
        $order->service_vat_percentage = $request->service_vat;
        $serviceVat = ($serviceSubTotal * $request->service_vat) / 100;
        $order->service_vat = $serviceVat;
        $order->service_discount = $request->service_discount;
        $order->created_by = Auth::user()->id;
        $order->total = $total;
        $order->due = $due;
        $order->refund = $refund;
        $order->next_payment = $due > 0 ? $request->next_payment : null;
        $order->save();

        return redirect()->route('sale_receipt.details', ['order' => $order->id]);
    }

    public function saleProductDetails(Request $request) {
        $product = PurchaseInventory::where('serial_no', $request->serial)
            ->where('warehouse_id', $request->warehouseId)
            ->where('quantity', '>', 0)
            ->with('product')
            ->first();

        if ($product) {
            $product = $product->toArray();
            return response()->json(['success' => true, 'data' => $product, 'count' => $product['quantity']]);
        } else {
            return response()->json(['success' => false, 'message' => 'Not found.']);
        }
    }

    public function saleReceiptDatatable() {
        $query = SalesOrder::with('customer');

        return DataTables::eloquent($query)
            ->addColumn('customer_name', function(SalesOrder $order) {
                return $order->customer->name;
            })
            ->addColumn('customer_address', function(SalesOrder $order) {
                return $order->customer->address;
            })
            ->addColumn('action', function(SalesOrder $order) {
                if ($order->due > 0) {
                    return '<a href="' . route('sale_receipt.details', ['order' => $order->id]) . '" class="btn btn-warning btn-sm">View</a> <a role="button" class="btn btn-warning btn-sm btn-payment" data-id="' . $order->id . '">Payment</a> <a class="btn btn-warning btn-sm" href="'.route('sale_receipt.edit', ['order' => $order->id]).'">Edit</a>';
                    //return '<a href="'.route('sale_receipt.details', ['order' => $order->id]).'" class="btn btn-warning btn-sm">View</a>';
                } else
                    return '<a href="'.route('sale_receipt.details', ['order' => $order->id]).'" class="btn btn-warning btn-sm">View</a> <a class="btn btn-warning btn-sm" href="'.route('sale_receipt.edit', ['order' => $order->id]).'">Edit</a>';
            })
            ->editColumn('date', function(SalesOrder $order) {
                return $order->date->format('j F, Y');
            })
            ->editColumn('next_payment', function(SalesOrder $order) {
                if ($order->next_payment)
                    return $order->next_payment->format('j F, Y');
                else
                    return '';
            })
            ->editColumn('total', function(SalesOrder $order) {
                return '৳'.number_format($order->total, 2);
            })
            ->editColumn('paid', function(SalesOrder $order) {
                return '৳'.number_format($order->paid, 2);
            })
            ->editColumn('due', function(SalesOrder $order) {
                return '৳'.number_format($order->due, 2);
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function customerPaymentDatatable() {
        $query = Customer::query();

        return DataTables::eloquent($query)
            ->addColumn('action', function(Customer $customer) {
                $btns = '<a class="btn btn-warning btn-sm btn-pay" role="button" data-id="'.$customer->id.'" data-name="'.$customer->name.'">Payment</a>';

                if ($customer->refund > 0)
                    $btns .= ' <a class="btn btn-danger btn-sm btn-refund" role="button" data-id="'.$customer->id.'" data-name="'.$customer->name.'">Refund</a>';

                return $btns;
            })
            ->addColumn('paid', function(Customer $customer) {
                return number_format($customer->paid, 2);
            })
            ->addColumn('due', function(Customer $customer) {
                return number_format($customer->due, 2);
            })
            ->addColumn('total', function(Customer $customer) {
                return number_format($customer->total, 2);
            })
            ->addColumn('refund', function(Customer $customer) {
                return number_format($customer->refund, 2);
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
