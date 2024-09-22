<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Events\InvoiceCreated;
use App\Models\InvoiceDetails;
use App\Models\Product;
use App\Http\Controllers\PaymentController;

class InvoicesController extends Controller
{
    //

    public function index()
    {
        $invoices = Invoice::all();

        return $invoices;
    }

    public function showAllUserInvoices($id)
    {        
        $invoices = Invoice::where('customer_id', $id)->with('invoiceDetails')->get();
        $data = [];
        foreach ($invoices as $invoice) {
            $invoiceDetails = $invoice->invoiceDetails()->get();
            $paymencController = new PaymentController();
            $invoiceKey = $invoice['id'];

            $checkStatus = $paymencController->checkStatus($invoiceKey);
                        
            if (!isset($invoice[$invoiceKey])) {
                $data[$invoiceKey] = [
                    'id' => $invoice['id'],
                    'customer_id' => $invoice['customer_id'],                    
                    'invoice_date' => $invoice['invoice_date'],
                    'total_amount' => $invoice['total_amount'],
                    'status' => $checkStatus,
                    'details' => []
                ];
            }

            foreach ($invoiceDetails as $detail) {    
                $detailKey = $detail['id'];
                if (!isset($data[$invoiceKey]['details'][$detailKey])) {
                    $data[$invoiceKey]['details'][$detailKey] = [
                        'id' => $detail['id'],
                        'invoice_id' => $detail['invoice_id'],
                        'product_id' => Product::find($detail['product_id']),
                        'qty' => $detail['qty'],
                        'unit_price' => $detail['unit_price'],
                        'sub_total' => $detail['sub_total']
                    ];
                }
            }
            $data[$invoiceKey]['details'] = array_values($data[$invoiceKey]['details']);
        }
        $collectData = \array_values($data);        

        return response()->json($collectData, 200);
    }

    public function store()
    {   
        $invoices = Invoice::create([
            'customer_id' => User::find(1)->id,
            'total_amount' => 2000,
            'invoice_date' => \now(),
        ]);
        $invoices->invoiceDetails()->create([
            'product_id' => Product::find(1)->id,
            'qty' => 1,
            'unit_price' => 2000,
            'sub_total' => 2000,
        ]);
        \event(new InvoiceCreated());
        return $invoices;
    }    

}
