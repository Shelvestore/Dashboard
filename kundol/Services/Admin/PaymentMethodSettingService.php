<?php
namespace App\Services\Admin;

use App\Models\Admin\PaymentMethodSetting;
use App\Traits\ApiResponser;
use DB;

class PaymentMethodSettingService
{
    use ApiResponser;
    public function Update($parms, $paymentMethodId)
    {
        // update payment method setting
        try {
            $setting = PaymentMethodSetting::where('payment_method_id', $paymentMethodId)->pluck('key')->toArray();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => 'payment method setting not found!'], 401);
        }

        foreach ($parms['key'] as $index => $key) {
            if (isset($parms['value'][$index]) && in_array($key, $setting)) {
                try {
                    PaymentMethodSetting::set($key, $parms['value'][$index], $paymentMethodId);
                } catch (Exception $e) {
                    DB::rollBack();
                    return $this->errorResponse('payment method description can not updated due to internal server error!', 401);
                }
            }

        }
        return 1;
        // end update payment method setting
    }
}
