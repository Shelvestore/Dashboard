<?php

namespace Database\Seeders;

use App\Models\Admin\PaymentMethodSetting;
use Illuminate\Database\Seeder;

class PaymentMethodSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentMethodSetting::where('id', '>', '0')->delete();
        PaymentMethodSetting::insertOrIgnore([
            [
                'payment_method_id' => 1,
                'key' => 'merchant_id',
                'value' => '---',
            ],
            [
                'payment_method_id' => 1,
                'key' => 'private_key',
                'value' => '--',
            ],
            [
                'payment_method_id' => 1,
                'key' => 'public_key',
                'value' => '--',
            ],
        ]);
    }
}
