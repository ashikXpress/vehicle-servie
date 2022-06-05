<?php

namespace App\Http\View\Composers;

use App\Model\PurchaseInventory;
use App\Model\SalesOrder;
use Illuminate\View\View;
use DB;

class LayoutComposer
{
    public function __construct()
    {

    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $nextPayments = SalesOrder::where('next_payment', date('Y-m-d'))->get();
        $stocks = PurchaseInventory::with('product', 'warehouse')
            ->groupBy('purchase_product_id', 'warehouse_id')
            ->select(DB::raw('sum(`quantity`) as quantity, purchase_product_id, warehouse_id'))
            ->inRandomOrder()->limit(3)->get();

        $data = [
            'nextPayments' => $nextPayments,
            'stocks' => $stocks,
        ];

        $view->with('layoutData', $data);
    }
}
