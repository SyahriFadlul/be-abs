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

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {           
        event(new UserCheckingOut($request)); //make new invoice entry in db

        $invoiceId = Invoice::where('status', 'Unpaid')->latest()->first()->id;
        
        $userData = User::find($request->input('customer_id'));
        $userProfileData = $userData->profile()->first();
        $userAddressData = $userData->address()->find($request->input('address_id'));
        $orderId = Order::find($request->input('orderId'))->id;        

        $frontendUrl = config('sanctum.stateful');
        
        $merchantCode = config('duitku.merchant_code');
        $merchantKey  = config('duitku.merchant_key');

        $paymentMethod      = strtoupper($request->input('paymentMethod')); // PaymentMethod list => https://docs.duitku.com/pop/id/#payment-method
        $paymentAmount      = $request->input('paymentAmount'); // Amount
        $email              = $userData->email; // your customer email
        $phoneNumber        = $userProfileData->phone_number; // your customer phone number (optional)
        $productDetails     = "Pembayaran untuk Toko Rendstore";
        $merchantOrderId    = $invoiceId; // from merchant, unique   
        $additionalParam    = ''; // optional
        $merchantUserInfo   = ''; // optional
        $customerVaName     = $request->input('customerVaName'); // display name on bank confirmation display
        $callbackUrl        = ''; // url  for callback
        $returnUrl          =  'http://' . strval($frontendUrl[0]);// url for redirect
        $expiryPeriod       = 60; // set the expired time in minutes
        $timestamp = round(microtime(true) * 1000);
        $signature = md5($merchantCode . $merchantOrderId . $paymentAmount . $merchantKey);
        

        // Customer Detail
        $firstName          = $userProfileData->firstName;
        $lastName           = $userProfileData->lastName;

        // Address
        $address            =$userAddressData->streetbuilding;
        $city               =$userAddressData->city;
        $postalCode         =$userAddressData->postalCode;
        $province           =$userAddressData->province;
        $countryCode        = "ID";

        $userAddress = array(
            'firstName'     => $firstName,
            'lastName'      => $lastName,
            'address'       => $address,
            'city'          => $city,
            'postalCode'    => $postalCode,
            'province'      => $province,
            'phone'         => $phoneNumber,
            'countryCode'   => $countryCode
        );

        $customerDetail = array(
            'firstName'         => $firstName,
            'lastName'          => $lastName,
            'email'             => $email,
            'phoneNumber'       => $phoneNumber,
            'billingAddress'    => $userAddress,
            'shippingAddress'   => $userAddress
        );

        // Item Details
        $item1 = array(
            'name'      => $productDetails,
            'price'     => $paymentAmount,
            'quantity'  => 1
        );

        $itemDetails = array(
            $item1
        );

        $params = array(
            'paymentAmount' => $paymentAmount,
        'merchantOrderId' => $merchantOrderId,
        'productDetails' => $productDetails,
        'additionalParam' => $additionalParam,
        'merchantUserInfo' => $merchantUserInfo,
        'customerVaName' => $customerVaName,
        'email' => $email,
        'phoneNumber' => $phoneNumber,
        'itemDetails' => $itemDetails,
        'customerDetail' => $customerDetail,
        //'creditCardDetail' => $creditCardDetail,
        'callbackUrl' => $callbackUrl,
        'returnUrl' => $returnUrl,
        'expiryPeriod' => $expiryPeriod,
        'paymentMethod' => $paymentMethod,
        );

        // $params_string = json_encode($params);
        // //echo $params_string;
        // $url = 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry'; // Sandbox
        // // $url = 'https://passport.duitku.com/webapi/api/merchant/v2/inquiry'; // Production

        // //log transaksi untuk debug 
        // // file_put_contents('log_createInvoice.txt', "* log *\r\n", FILE_APPEND | LOCK_EX);
        // // file_put_contents('log_createInvoice.txt', $params_string . "\r\n\r\n", FILE_APPEND | LOCK_EX);
        // // file_put_contents('log_createInvoice.txt', 'x-duitku-signature:' . $signature . "\r\n\r\n", FILE_APPEND | LOCK_EX);
        // // file_put_contents('log_createInvoice.txt', 'x-duitku-timestamp:' . $timestamp . "\r\n\r\n", FILE_APPEND | LOCK_EX);
        // // file_put_contents('log_createInvoice.txt', 'x-duitku-merchantcode:' . $merchantCode . "\r\n\r\n", FILE_APPEND | LOCK_EX);
        // $ch = curl_init();

        // curl_setopt($ch, CURLOPT_URL, $url); 
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);                                                                  
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        //     'Content-Type: application/json',                                                                                
        //     'Content-Length: ' . strlen($params_string),
        //     'x-duitku-signature:' . $signature ,
        //     'x-duitku-timestamp:' . $timestamp ,
        //     'x-duitku-merchantcode:' . $merchantCode    
        //     )                                                                       
        // );   
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        // $request = curl_exec($ch);
        // $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // if($httpCode == 200)
        // {   
        //     $currentOrder = Order::find($orderId)->first();            
        //     $currentOrder->status = 'completed';
        //     $currentOrder->save();

        //     \event(new InvoiceCreated());
        //     $result = json_decode($request, true);
        //     //header('location: '. $result['paymentUrl']);
        //     return $result;
        //     // echo "paymentUrl :". $result['paymentUrl'] . "<br />";
        //     // echo "reference :". $result['reference'] . "<br />";
        //     // echo "statusCode :". $result['statusCode'] . "<br />";
        //     // echo "statusMessage :". $result['statusMessage'] . "<br />";
        // }
        // else
        // {
        //     $request = json_decode($request);
            
        //     $error_message = "Server Error " . $httpCode ." ". $request->Message;
        //     return $error_message;
        // }
    
        try {
            // createInvoice Request
            $responseDuitkuPop = \Duitku\Api::createInvoice($params, $this->duitkuConfig());

            header('Content-Type: application/json');

            // change order status
            $currentOrder = Order::find($request->input('orderId'));
            $currentOrder->status = 'Paid';
            $currentOrder->save();

            \event(new InvoiceCreated());
            
            return $responseDuitkuPop;
        } catch (Exception $e) {
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

