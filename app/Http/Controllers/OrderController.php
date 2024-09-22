<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index()
    {
        return OrderDetails::all();
    }

    public function showUserOrder()
    {    
        $userId = \auth()->user()->id;        
        
        $order = Order::where('user_id', $userId)->where('status', 'Unpaid')->first();
        $orderDetail = OrderDetails::where('order_id', $order->id)->get();                        
        
        $detailedOrder = [];
        foreach ($orderDetail as $item) {
            $detailedOrder[$item->id] = [
                'id' => $item->id,
                'order_id' => $item->order_id,
                'product_id' => Product::find($item->product_id),
                'qty' => $item->qty,

            ];
        }
        $data = \collect(array_values($detailedOrder));
        return $data;

    }

    public function store(OrderRequest $request)
    {   
        // return response($request->all());
        $data = $request->validated();
        $ids = explode('_', $data['product_id']);
        $qtys = explode('_', $data['qty']);
        
        // return \response($data);
        $order = Order::where('user_id', $data['user_id'])->where('status', 'Unpaid')->first();
        if (isset($order)) {
            
            $isUnpaid = $order->status === 'Unpaid' ? true : false;
            if($isUnpaid) {
                
                $order->orderDetails()->delete();
                foreach ($ids as $key => $id) {
                    $qty = isset($qtys[$key]) ? $qtys[$key] : 1;
                    $order->orderDetails()->create([
                        'product_id' => $id,
                        'qty' => $qty
                    ]);                                
                };   
    
                return $order->id;
            }
        }

        //bikin entry baru kalau order user sebelumnya sudah masuk ke pembayaran
        $order = Order::create([
            'user_id' => $data['user_id'],
            'total_amount' => $data['total_amount'],
            'status' => 'Unpaid'
        ]);

        foreach ($ids as $key => $id) {
            $qty = isset($qtys[$key]) ? $qtys[$key] : 1;
            $order->orderDetails()->create([
                'product_id' => $id,
                'qty' => $qty
            ]);                                
        };                     
        return $order->id;
    }

    public function storeOrder(OrderDetailsRequest $request)
    {
        $data = $request->validated();
        OrderDetails::create($data);
    }

    public function orderChart(Request $request)
    {   //1 = daily, 2 = monthly, 3 = annually
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $currentWeek = Carbon::now()->week;
        $period = "";        
        // return response($request->timePeriod == 1);
        if($request->timePeriod == 1){ //daily in a month
            $period = "daily";
            $data = Order::selectRaw('DAY(created_at) as day, SUM(total_price) as total')
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->groupBy('day')
                    ->get();
            }
        if($request->timePeriod === 2){ //weekly
            $period = "weekly";
            $data = Order::selectRaw('WEEK(created_at) as week, SUM(total_price) as total')
                    ->whereYear('created_at', $currentYear)
                    ->whereRaw('WEEK(created_at) = ?', [$currentWeek])
                    ->groupBy('week')
                    ->get();
        }
        if($request->timePeriod === 3){ //monthly
            $period = "monthly";
            $data = Order::selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
                    ->whereYear('created_at', $currentYear)
                    ->groupBy('month')
                    ->get();
        }
        if($request->timePeriod === 4){ //annually for the past 1 decade
            $period = "annually";
            $data = Order::selectRaw('YEAR(created_at) as year, SUM(total_price) as total')
                    ->whereYear('created_at', '>=', $currentYear - 10)
                    ->groupBy('year')
                    ->get();
        }
        $response = [
            'type' => $period,
            'data' => $data
        ];
        return response()->json($response);
    }

       
}
