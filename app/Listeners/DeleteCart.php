<?php

namespace App\Listeners;

use App\Events\InvoiceCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Cart;

class DeleteCart
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
    public function handle(InvoiceCreated $event): void
    {   
        $userId = \auth()->user()->id;
        $userCart = Cart::where('user_id', $userId)->get();
        Cart::destroy($userCart);
    }
}
