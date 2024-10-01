<?php

namespace App\Http\Controllers;

use App\Events\InvoiceCreated;
use App\Events\UserCheckingOut;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {           
        // event(new UserCheckingOut($request)); //make new invoice entry in db

        // $invoiceId = Invoice::where('status', 'Unpaid')->latest()->first()->id;
        
        $userData = User::find($request->input('customer_id'));
        $userProfileData = $userData->profile()->first();
        $userAddressData = $userData->address()->find($request->input('address_id'));
        $userMainAddress = $userData->address()->where('is_main_address', 1)->first();
        $orderId = Order::find($request->input('orderId'))->id;        

        return $request;
        $client = new Client([
            'verify' => false,
        ]);
        
        $authorization = base64_encode(config('midtrans.server_key') . ':' . config('midtrans.merchang_pw'));
        try {
            $response = $client->request('POST', 'https://app.sandbox.midtrans.com/snap/v1/transactions', [
                'json' => [
                    'transaction_details' => [
                        'order_id' => $orders->id,
                        'gross_amount' => $orders->total_price,
                    ],
                    'customer_details' => [
                        'first_name' => $request->name,                
                        // 'last_name' => '',                
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'address' => $request->address,
                    ],
                    'shipping_address' => [
                        'first_name' => $request->name,                
                        // 'last_name' => '',                
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'address' => $request->address,
                    ],
                    'credit_card' => [
                        'secure' => true,
                    ],
                    "item_details"=> [
                        "id"=> $request->id,
                        "price"=> $request->price,
                        "quantity"=> $request->qty,
                        "name"=> Product::find($request->id)->name,
                      ]
                ],
                'headers' => [
                    'accept' => 'application/json',                
                    // 'authorization' => 'Basic U0ItTWlkLXNlcnZlci11NkxOWWNWOEl5ODlnTkdiTUtIcmNUSHQ6TXVsdGltYXgxMjM=',                
                    'authorization' => 'Basic U0ItTWlkLXNlcnZlci1ERGFPeE5ZMkhaRms2eS1mdktPR1E5bVk6VmVvc2hpQDQ=',
                    'content-type' => 'application/json',                
                ],
            ]);
    
            // Get the response body
            $body = \json_decode($response->getBody());
            $paymentUrl = $body->redirect_url;

            return $body;
        } catch (Exception $e) {
            Log::error('error : ' . $e);
            return $e;
        }
    } 

    public function checkStatus($invoiceId)
    {
        try {
            $merchantOrderId = $invoiceId;
            $transactionList = \Duitku\Api::transactionStatus($merchantOrderId, $this->duitkuConfig());
        
            header('Content-Type: application/json');
            $transaction = json_decode($transactionList);
        
        
            if ($transaction->statusCode == "00") {
                // Action Success
                $status = 'delivered';
                $invoice = Invoice::where('id', $invoiceId)->first();
                $invoice->status = $status;
                $invoice->save();

                return $status;
            } else if ($transaction->statusCode == "01") {
                // Action Unpaid
                $status = 'Unpaid';
                $invoice = Invoice::where('id', $invoiceId)->first();
                $invoice->status = $status;
                $invoice->save();

                return $status;
            } else {
                // Action Failed Or Expired
                $status = 'cancelled';
                $invoice = Invoice::where('id', $invoiceId)->first();
                $invoice->status = $status;
                $invoice->save();

                return $status;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function callback()
    {
        // try {
        //     $callback = \Duitku\Pop::callback($this->duitkuConfig());
        
        //     header('Content-Type: application/json');
        //     $notif = json_decode($callback);
        
        //     var_dump($callback);
        
        //     if ($notif->resultCode == "00") {
        //         // Action Success
        //     } else if ($notif->resultCode == "01") {
        //         // Action Failed
        //     }
        // } catch (Exception $e) {
        //     http_response_code(400);
        //     echo $e->getMessage();
        // }
        $apiKey = '1debd660005cc17500511e8925cc07f4'; // API key anda
        $merchantCode = isset($_POST['merchantCode']) ? $_POST['merchantCode'] : null; 
        $amount = isset($_POST['amount']) ? $_POST['amount'] : null; 
        $merchantOrderId = isset($_POST['merchantOrderId']) ? $_POST['merchantOrderId'] : null; 
        $productDetail = isset($_POST['productDetail']) ? $_POST['productDetail'] : null; 
        $additionalParam = isset($_POST['additionalParam']) ? $_POST['additionalParam'] : null; 
        $paymentCode = isset($_POST['paymentCode']) ? $_POST['paymentCode'] : null; 
        $resultCode = isset($_POST['resultCode']) ? $_POST['resultCode'] : null; 
        $merchantUserId = isset($_POST['merchantUserId']) ? $_POST['merchantUserId'] : null; 
        $reference = isset($_POST['reference']) ? $_POST['reference'] : null; 
        $signature = isset($_POST['signature']) ? $_POST['signature'] : null; 
        $publisherOrderId = isset($_POST['publisherOrderId']) ? $_POST['publisherOrderId'] : null; 
        $spUserHash = isset($_POST['spUserHash']) ? $_POST['spUserHash'] : null; 
        $settlementDate = isset($_POST['settlementDate']) ? $_POST['settlementDate'] : null; 
        $issuerCode = isset($_POST['issuerCode']) ? $_POST['issuerCode'] : null; 

        //log callback untuk debug 
        // file_put_contents('callback.txt', "* Callback *\r\n", FILE_APPEND | LOCK_EX);

        if(!empty($merchantCode) && !empty($amount) && !empty($merchantOrderId) && !empty($signature))
        {
            $params = $merchantCode . $amount . $merchantOrderId . $apiKey;
            $calcSignature = md5($params);

            if($signature == $calcSignature)
            {
                //Callback tervalidasi
                //Silahkan rubah status transaksi anda disini
                file_put_contents('callback.txt', "* Berhasil *\r\n\r\n", FILE_APPEND | LOCK_EX);

            }
            else
            {
                file_put_contents('callback.txt', "* Bad Signature *\r\n\r\n", FILE_APPEND | LOCK_EX);
                throw new Exception('Bad Signature');
            }
        }
        else
        {
            file_put_contents('callback.txt', "* Bad Parameter *\r\n\r\n", FILE_APPEND | LOCK_EX);
            throw new Exception('Bad Parameter');;
        }

    }

    public function getPaymentMethod()
    {        
        try {
            $paymentAmount = "10000"; //"YOUR_AMOUNT";
            $paymentMethodList = \Duitku\Pop::getPaymentMethod($paymentAmount, $this->duitkuConfig());
        
            header('Content-Type: application/json');
            $json = \json_encode($paymentMethodList);
            return $paymentMethodList;
        } catch (Exception $e) {
            return \response($e->getMessage());
        }
    }

    private function duitkuConfig()
    {
        $duitkuConfig = new \Duitku\Config(\config('duitku.merchant_key'), \config('duitku.merchant_code'));
        
        // false for production mode
        // true for sandbox mode
        $duitkuConfig->setSandboxMode(true);
        // set sanitizer (default : true)
        $duitkuConfig->setSanitizedMode(false);
        // set log parameter (default : true)
        $duitkuConfig->setDuitkuLogs(false);

        return $duitkuConfig;
    }

    public function test()
    {   
        $client = new Client([
            'verify' => false,
        ]);
        try {
            //code...
            $response = $client->request('POST', 'https://app.sandbox.midtrans.com/snap/v1/transactions',[
                'json'=> [

                ],
                'headers' => [
                    'accept' => 'application/json',
                    'authorization' => 'Basic U0ItTWlkLXNlcnZlci1ERGFPeE5ZMkhaRms2eS1mdktPR1E5bVk6VmVvc2hpQDQ=',
                    'content-type' => 'application/json',
                ]
            ]);
            return $response;
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }   
        $id = 'INV-240300002';

        try {
            $merchantOrderId = $id;
            $transactionList = \Duitku\Api::transactionStatus($merchantOrderId, $this->duitkuConfig());
        
            header('Content-Type: application/json');
            $transaction = json_decode($transactionList);
        
        
            if ($transaction->statusCode == "00") {
                // Action Success
                return 'delivered';
            } else if ($transaction->statusCode == "01") {
                // Action Unpaid
                
                return 'Unpaid';
            } else {
                // Action Failed Or Expired
                return 'cancelled';
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
}

