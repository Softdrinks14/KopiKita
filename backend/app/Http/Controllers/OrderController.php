<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\OrderDetail;
use App\Models\orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = orders::select('id', 'customer_name', 'table_no', 'order_date', 'order_time', 'status', 'total')->get();
        return response(['data' => $orders]);
    }

    public function show($id)
    {
        $order = orders::select('id', 'customer_name', 'table_no', 'order_date', 'order_time', 'status', 'total')
            ->where('id', $id)
            ->first();
        return response(['data' => $order->loadMissing(['orderDetail:order_id,price,item_id', 'orderDetail.item:name,id'])]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|max:100',
            'table_no' => 'required|max:5',
            'items' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $data =  $request->all();
            $data['order_date'] = date('Y-m-d');
            $data['order_time'] = date('H:i:s');
            $data['status'] = 'ordered';
            $data['total'] = 0;
            $data['items'] = $request->items;

            $order = orders::create($data);


            //create order detail
            collect($data['items'])->map(function ($item) use ($order) {
                $foodAndDrink = Item::where('id', $item['id'])->first();
                OrderDetail::create([
                    'order_id' => $order->id,
                    'item_id' => $item['id'],
                    'price' => $foodAndDrink->price,
                    'qty' => $item['qty'],
                ]);
            });

            //total order
            $order->total = $order->sumOrderPrice();
            $order->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }

        return response(['data' => $order]);
    }

    public function done($id)
    {
        $order = orders::find($id);
        if ($order->status != 'ordered') {
            return response(['message' => 'Order Cannot Change'], 400);
        }

        $order->status = 'done';
        $order->save();

        return response(['data' => $order]);
    }

    public function paid($id)
    {
        $order = orders::find($id);
        if ($order->status != 'done') {
            return response(['message' => 'Order Cannot Change'], 400);
        }

        $order->status = 'paid';
        $order->save();

        return response(['data' => $order]);
    }


}
