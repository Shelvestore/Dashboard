<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Traits\ApiResponser;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Resources\Web\Order as OrderResource;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\OrderProcessed;
use App\Models\Admin\Account;
use App\Models\Admin\Currency;
use App\Models\Admin\DefaultAccount;
use App\Models\Admin\ShippingMethod;
use App\Models\Admin\TaxRate;
use App\Models\Admin\Transaction;
use App\Models\Admin\TransactionDetail;
use App\Services\Admin\OrderService;
use App\Models\Web\Order;

class OrderProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ApiResponser;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $parms;
    public function __construct($parms)
    {
        $this->parms = $parms;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            \DB::beginTransaction();
            $orderService = new OrderService;
                
            $amount = 0;
            $customer_id = \Auth::id();
            $currency = Currency::defaultCurrency()->select('exchange_rate', 'symbol_position', 'code')->first();

            if($customer_id == '' || $customer_id == null){
                $customer_id = $this->parms['customer_id'];
            }

            $stockValidate = $orderService->CheckStock($customer_id);
            if ($stockValidate['status'] == 'Error')
                return $stockValidate;

            if (isset($this->parms['coupon_code'])) {
                $couponValidate = $orderService->CouponCodeValidation($this->parms['coupon_code'], $customer_id, $customer_id);
                if ($couponValidate['status'] == 'Error')
                    return $couponValidate;
                $sql = $couponValidate['data'];
                if ($sql->type == 'percentage') {
                    $amount = ($stockValidate['data'] / 100) * $sql->amount;
                } else {
                    $amount = $sql->amount;
                }
                $this->parms['coupon_amount'] = $amount * $currency->exchange_rate;
            }
            $this->parms['order_price'] = $stockValidate['data'];
            $this->parms['order_price'] = $this->parms['order_price'] * $currency->exchange_rate;
            $this->parms['order_price'] = $this->parms['order_price'] - $amount;
            if($this->parms['payment_method'] == 'Stripe'){
                $paymentMethod = $orderService->paymentMethod($this->parms['payment_method'], $this->parms['cc_number'], $this->parms['cc_expiry_month'], $this->parms['cc_expiry_year'], $this->parms['cc_cvc'], $this->parms['order_price']);

                if ($paymentMethod['status'] == 'Error')
                    return $paymentMethod;
                
                if($paymentMethod['message'] == 'Success'){
                    $this->parms['order_status'] = 'Complete';
                }
            }
            else if($this->parms['payment_method'] == 'PayPal' && isset($this->parms['transaction_id'])){
                $this->parms['order_status'] = 'Complete';
                $this->parms['transaction_id'] = $this->parms['transaction_id'];
                $paymentMethod['message'] = 'Success';
            }
            $tax_rate = TaxRate::findByState($this->parms['delivery_state'])->get();
            $total = 0;
            foreach($tax_rate as $tax_rates){
                $total = $total + $tax_rates->tax_rate;
            }
            $total = $total * $currency->exchange_rate;
            $shippingMethodPrice = ShippingMethod::where('is_default', '1')->first();
            $shipping_method_price = 0;
            if($shippingMethodPrice){
                $shipping_method_price = $shippingMethodPrice->amount * $currency->exchange_rate;
                $this->parms['shipping_method'] = $shippingMethodPrice->methods_type_link;
            }

            $this->parms['total_tax'] = $total;
            $this->parms['shipping_cost'] = $shipping_method_price;
            $this->parms['order_price'] = $this->parms['order_price'] + $total + $shipping_method_price;

            if(isset($this->parms['action']) && $this->parms['action'] == 'PayPal'){
                return $this->successResponseArray($this->parms['order_price'], 'PayPal');
            }

            $this->parms['customer_id'] = $customer_id;
            $this->parms['currency_id'] = $currency->id;
            $this->parms['currency_value'] = $currency->exchange_rate;
            $sql = Order::create($this->parms);

            if($this->parms['payment_method'] != 'COD'){
                if($paymentMethod['message'] == 'Success'){
                    $orderService->CompleteTransaction($sql, $this->parms['customer_id']);
                }
            }


            // $default_account = DefaultAccount::pluck('account_id', 'type')->toArray();
            // $account_id = Account::where('type', 'customer')->where('reference_id', $this->parms['customer_id'])->value('id');
            // if (!$account_id) {
            //     $account_id = $default_account['customer'];
            // }
            //     $inc = Transaction::latest()->value('transaction_number');
            //     $inc = intVal($inc);
            //     $inc++;
            //     $trans_id = Transaction::create([
            //         'transaction_number' => $inc,
            //         'transaction_date' => date('Y-m-d'),
            //         'description' => 'order sale item'
            //     ]);
            //     TransactionDetail::create([
            //         'transaction_id' => $trans_id->id,
            //         'account_id' => $default_account['cash'],
            //         'reference_id' => $sql->id,
            //         'type' => 'cash',
            //         'dr_amount' => $sql->order_price,
            //         'cr_amount' => '0'
            //     ]);

            //     TransactionDetail::create([
            //         'transaction_id' => $trans_id->id,
            //         'account_id' => $account_id,
            //         'reference_id' => $sql->id,
            //         'type' => 'sale',
            //         'dr_amount' => '0',
            //         'cr_amount' => $sql->total_tax
            //     ]);
            //     TransactionDetail::create([
            //         'transaction_id' => $trans_id->id,
            //         'account_id' => $account_id,
            //         'reference_id' => $sql->id,
            //         'type' => 'sale',
            //         'dr_amount' => '0',
            //         'cr_amount' => $sql->coupon_amount
            //     ]);
            //     TransactionDetail::create([
            //         'transaction_id' => $trans_id->id,
            //         'account_id' => $account_id,
            //         'reference_id' => $sql->id,
            //         'type' => 'sale',
            //         'dr_amount' => '0',
            //         'cr_amount' => $sql->shipping_cost
            //     ]);

            //     $remaining = intVal($sql->order_price) - intVal($sql->total_tax) - intVal($sql->coupon_amount) - intVal($sql->shipping_cost);

            //     TransactionDetail::create([
            //         'transaction_id' => $trans_id->id,
            //         'account_id' => $account_id,
            //         'reference_id' => $sql->id,
            //         'type' => 'sale',
            //         'dr_amount' => '0',
            //         'cr_amount' => $remaining
            //     ]);

        } catch (Exception $e) {
            return $this->errorResponse();
        }
        if ($sql) {
            OrderProcessed::dispatch($sql->id);
            \DB::commit();
            return $this->successResponse(new OrderResource($sql), 'Order Save Successfully!');
        } else {
            \DB::rollback();
            return $this->errorResponse();
        }
    }
}
