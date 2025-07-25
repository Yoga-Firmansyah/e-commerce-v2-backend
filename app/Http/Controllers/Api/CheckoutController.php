<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        // Set midtrans configuration
        \Midtrans\Config::$serverKey    = config('services.midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('services.midtrans.isProduction');
        \Midtrans\Config::$isSanitized  = config('services.midtrans.isSanitized');
        \Midtrans\Config::$is3ds        = config('services.midtrans.is3ds');
    }

    public function store()
    {
        DB::transaction(function () {


            $length = 10;
            $random = '';
            for ($i = 0; $i < $length; $i++) {
                $random .= rand(0, 1) ? rand(0, 9) : chr(rand(ord('a'), ord('z')));
            }

            $no_invoice = 'INV-' . Str::upper($random);

            $invoice = Invoice::create([
                'invoice'       => $no_invoice,
                'customer_id'   => auth()->guard('api')->user()->id,
                'courier'       => $this->request->courier,
                'service'       => $this->request->service,
                'cost_courier'  => $this->request->cost,
                'weight'        => $this->request->weight,
                'name'          => $this->request->name,
                'phone'         => $this->request->phone,
                'province'      => $this->request->province,
                'city'          => $this->request->city,
                'address'       => $this->request->address,
                'grand_total'   => $this->request->grand_total,
                'status'        => 'pending',
            ]);

            foreach (Cart::where('customer_id', auth()->guard('api')->user()->id)->get() as $cart) {

                $invoice->orders()->create([
                    'invoice_id'    => $invoice->id,
                    'invoice'       => $no_invoice,
                    'product_id'    => $cart->product_id,
                    'product_name'  => $cart->product->title,
                    'image'         => $cart->product->image,
                    'qty'           => $cart->quantity,
                    'price'         => $cart->price,
                ]);
            }

            $payload = [
                'transaction_details' => [
                    'order_id'      => $invoice->invoice,
                    'gross_amount'  => $invoice->grand_total,
                ],
                'customer_details' => [
                    'first_name'       => $invoice->name,
                    'email'            => auth()->guard('api')->user()->email,
                    'phone'            => $invoice->phone,
                    'shipping_address' => [
                        'first_name'       => $invoice->name,
                        'email'            => auth()->guard('api')->user()->email,
                        'phone'            => $invoice->phone,
                        'address' => $invoice->address
                    ]
                ]
            ];

            //create snap
            $snapToken = Snap::getSnapToken($payload);
            $invoice->snap_token = $snapToken;
            $invoice->save();

            $this->response['snap_token'] = $snapToken;
        });

        return response()->json([
            'success' => true,
            'message' => 'Order Successfully',
            $this->response
        ]);
    }

    public function notificationHandler(Request $request)
    {
        $payload      = $request->getContent();
        $notification = json_decode($payload);

        $validSignatureKey = hash("sha512", $notification->order_id . $notification->status_code . $notification->gross_amount . config('services.midtrans.serverKey'));

        if ($notification->signature_key != $validSignatureKey) {
            return response(['message' => 'Invalid signature'], 403);
        }

        $transaction  = $notification->transaction_status;
        $type         = $notification->payment_type;
        $orderId      = $notification->order_id;
        $fraud        = $notification->fraud_status;

        $data_transaction = Invoice::where('invoice', $orderId)->first();

        if ($transaction == 'capture') {

            if ($type == 'credit_card') {

                if ($fraud == 'challenge') {

                    $data_transaction->update([
                        'status' => 'pending'
                    ]);
                } else {

                    $data_transaction->update([
                        'status' => 'success'
                    ]);
                }
            }
        } elseif ($transaction == 'settlement') {


            $data_transaction->update([
                'status' => 'success'
            ]);
        } elseif ($transaction == 'pending') {

            $data_transaction->update([
                'status' => 'pending'
            ]);
        } elseif ($transaction == 'deny') {

            $data_transaction->update([
                'status' => 'failed'
            ]);
        } elseif ($transaction == 'expire') {

            $data_transaction->update([
                'status' => 'expired'
            ]);
        } elseif ($transaction == 'cancel') {

            $data_transaction->update([
                'status' => 'failed'
            ]);
        }
    }
}
