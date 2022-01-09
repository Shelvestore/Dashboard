<?php

namespace App\Repository\Web;

use App\Contract\Web\OrderInterface;
use App\Traits\ApiResponser;
use Illuminate\Support\Collection;
use App\Jobs\OrderProcess;
use App\Services\Admin\OrderService;
use Session;
class OrderRepository implements OrderInterface
{
    use ApiResponser;

    public function store(array $parms)
    {
        try {
            $cartItemValidation = new OrderService;
            $cartItemValidation = $cartItemValidation->CartItemValidation();
            if ($cartItemValidation == 0)
                return $this->errorResponse('Empty Cart!');
            
            $parms['action'] = '';
            if($parms['payment_method'] == 'PayPal'){
                $parms['customer_id'] = \Auth::id();
                Session::put('order_data',$parms);
                // return $parms = Session::get('order_data');
                $parms['action'] = 'PayPal';
            }
            return OrderProcess::dispatchNow($parms);
            return $this->successResponse(OrderProcess::dispatchNow($parms), 'Data Get Successfully!');
        } catch (Exception $e) {
            return $this->errorResponse();
        }
    }
}
