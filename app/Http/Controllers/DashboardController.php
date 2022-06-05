<?php

namespace App\Http\Controllers;

use App\Model\PurchaseOrder;
use App\Model\PurchaseProduct;
use App\Model\SalePayment;
use App\Model\SalesOrder;
use App\Model\TransactionLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

class DashboardController extends Controller
{
    public function index() {
        $todaySale = SalesOrder::whereDate('date', date('Y-m-d'))->sum('total');
        $todayDue = SalesOrder::whereDate('date', date('Y-m-d'))->sum('due');
        $todayDueCollection = SalePayment::whereDate('date', date('Y-m-d'))
            ->where('type', 1)
            ->where('received_type', 2)->sum('amount');
        $todayCashSale = SalePayment::whereDate('date', date('Y-m-d'))
            ->where('type', 1)
            ->where('received_type', 1)->sum('amount');
        $todayExpense = TransactionLog::whereDate('date', date('Y-m-d'))
            ->whereIn('transaction_type', [3, 2, 6])->sum('amount');

        $todaySaleReceipt = SalesOrder::whereDate('date', date('Y-m-d'))
            ->with('customer')
            ->orderBy('created_at', 'desc')->paginate(10);
        $todaySaleReceipt->setPageName('sale_receipt');
        $todayPurchaseReceipt = PurchaseOrder::whereDate('date', date('Y-m-d'))
            ->with('supplier')
            ->orderBy('created_at', 'desc')->paginate(10);
        $todayPurchaseReceipt->setPageName('purchase_receipt');

        // Order Count By Month
        $startDate = [];
        $endDate = [];
        $saleAmountLabel = [];
        $saleAmount = [];

        for($i=11; $i >= 0; $i--) {
            $date = Carbon::now();
            $saleAmountLabel[] = $date->startOfMonth()->subMonths($i)->format('M, Y');
            $startDate[] = $date->format('Y-m-d');
            $endDate[] = $date->endOfMonth()->format('Y-m-d');
        }

        for($i=0; $i < 12; $i++) {
            $saleAmount[] = SalesOrder::where('date', '>=', $startDate[$i])
                ->where('date', '<=', $endDate[$i])
                ->sum('total');
        }

        // Product Upload chart
        $orderCount = [];

        for($i=0; $i < 12; $i++) {
            $orderCount[] = SalesOrder::where('date', '>=', $startDate[$i])
                ->where('date', '<=', $endDate[$i])
                ->count();
        }

        // Best Seller Products
        $bestSellingItemsSql = "SELECT purchase_products.id, count
                FROM purchase_products
                LEFT JOIN (SELECT purchase_product_id, SUM(quantity) count FROM purchase_product_sales_order GROUP BY purchase_product_id) t ON purchase_products.id = t.purchase_product_id
                WHERE purchase_products.status = 1
                ORDER BY count DESC
                LIMIT 10";

        $bestSellingItemsResult = DB::select($bestSellingItemsSql);
        $bestSellingItemsIds = [];

        foreach ($bestSellingItemsResult as $item)
            $bestSellingItemsIds[] = $item->id;

        $bestSellingItemsIdsString = implode(",", $bestSellingItemsIds);
        $bestSellingProductsQuery = PurchaseProduct::query();
        $bestSellingProductsQuery->whereIn('id', $bestSellingItemsIds);

        if (count($bestSellingItemsIds) > 0)
            $bestSellingProductsQuery->orderByRaw('FIELD(id,'.$bestSellingItemsIdsString.')');
        $bestSellingProducts = $bestSellingProductsQuery->get();

        foreach ($bestSellingProducts as $product) {
            $product->count = DB::table('purchase_product_sales_order')
                ->where('purchase_product_id', $product->id)
                ->sum('quantity');
        }

        // Recently Added Product
        $recentlyProducts = PurchaseProduct::take(10)->latest()->get();

        $data = [
            'todaySale' => $todaySale,
            'todayDue' => $todayDue,
            'todayDueCollection' => $todayDueCollection,
            'todayExpense' => $todayExpense,
            'todayCashSale' => $todayCashSale,
            'todaySaleReceipt' => $todaySaleReceipt,
            'todayPurchaseReceipt' => $todayPurchaseReceipt,
            'saleAmountLabel' => json_encode($saleAmountLabel),
            'saleAmount' => json_encode($saleAmount),
            'orderCount' => json_encode($orderCount),
            'bestSellingProducts' => $bestSellingProducts,
            'recentlyProducts' => $recentlyProducts
        ];

        return view('dashboard', $data);
    }
}
