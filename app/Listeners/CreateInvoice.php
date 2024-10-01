<?php

namespace App\Listeners;

use App\Events\UserCheckingOut;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateInvoice
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserCheckingOut $event): void
    {
        $requestData = $event->request->all();

        $orderId = Order::find($requestData['orderId']);

        $orderDetail = $orderId->orderDetails()->get();

        $invoice = Invoice::create([
            'customer_id' =>  $requestData['customer_id'],
            'invoice_date' => now(),
            'total_amount' => $requestData['paymentAmount'],
            'status' => 'Unpaid',
            'payment_url' => '',
        ]);
        foreach ($orderDetail as $item) {
            $price = Product::find($item->product_id)->price;
            $invoice->invoiceDetails()->create([
                'product_id' => $item->product_id,
                'qty' => $item->qty,
                'unit_price' => $price,
                'sub_total' => $price * $item->qty,
            ]);
        }

    }
}
