<?php

namespace App\Http\Controllers\Web\Report;

use App\Exports\OrderExport;
use App\Exports\StoreExport;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Repositories\StoreRepository;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportController extends Controller
{
    public function index()
    {
        $shops = (new StoreRepository())->getAll();

        return view('report.shoplist', compact('shops'));
    }

    public function generateReport(Store $store)
    {
        $pdf = PDF::loadView('pdf.generate-report', [
            'store' => $store,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream($store->name.now()->format('H-i-s').'.pdf');
    }

    public function exportOrder(Store $store)
    {
        $orders = collect([]);
        foreach ($store->orders as $order) {
            $collection = [
                'id' => $order->prefix.$order->order_code,
                'customer' => $order->customer->user->name,
                'order_date' => $order->created_at->format('M d, Y'),
                'total' => currencyPosition($order->total_amount),
                'discount' => currencyPosition($order->discount ?? 0),
                'delivery_charge' => currencyPosition($order->delivery_charge ?? 0),
                'quantity' => $order->products->sum('pivot.quantity').'Pieces',
                'delivery_date' => Carbon::parse($order->delivery_date)->format('M d, Y'),
                'order_status' => $order->order_status->value,
                'payment' => $order->payment_type,
            ];
            $orders[] = $collection;
        }

        return Excel::download(new OrderExport($orders), 'shop-orders.xlsx');
    }

    public function exportStore($id = null)
    {
        $user = auth()->user();
        $stores = (new StoreRepository())->getAll();
        $fileName = 'all-shop-list.xlsx';
        if ($id) {
            $stores = (new StoreRepository())->query()->where('id', $id)->get();
            $fileName = $stores[0]->name.'.xlsx';
        }
        $storesCollection = collect([]);
        $storesCollection[] = [
            'sl' => 'SL',
            'name' => 'Name',
            'commission' => 'Commission',
            'total_selling' => 'Total Selling',
            'total_revenue' => 'Total Revenue',
            'commission_cost' => 'Commission Cost',
            'total_order' => 'Total Order',
            'total_cancle' => 'Total Cancel Order',
            'create_at' => 'Created At',
        ];
        foreach ($stores as $store) {
            $productIds = $store->products()->pluck('id')->toArray();
            $total = \App\Models\OrderProduct::whereIn('id', $productIds)->get();
            $amount = $store->orders->sum('total_amount');
            $cancle = $store->orders->where('order_status', 'Cancelled')->count();

            $collection = [
                'id'=>$store->id,
                'name' => $store->name,
                'commission' => $store->commission.'%',
                'total_selling' => (string) $total->sum('quantity'),
                'total_revenue' => (string) currencyPosition($store->orders->sum('total_amount')),
                'commission_cost' => (string) currencyPosition(round(($amount / 100) * $store->commission, 2)),
                'total_order' => (string) $store->orders->count(),
                'total_cancle' => (string) $cancle,
                'create_at' => $store->created_at->format('M d, Y'),
            ];
            $storesCollection[] = $collection;
        }

        return Excel::download(new StoreExport($storesCollection), $fileName);
    }

    public function details(Store $store)
    {
        return view('report.shop_wise_report', compact('store'));
    }
}
