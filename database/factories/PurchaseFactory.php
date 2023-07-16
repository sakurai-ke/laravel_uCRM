<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            // 今回はPurchase 100件のデータに対して登録されているcustomer_idのうちのランダムなidを割り振る。
            // （今回はDatabaseSeeer.phpでPurchaseを100件追加している。）
            'customer_id' => rand(1, Customer::count()),
            // 登録とキャンセルの情報
            'status' => $this->faker->boolean,
        ];
    }
}

// 'customer_id' => rand(1, Customer::count()),
// customer_id属性には、ランダムな顧客IDが設定されます。rand(1, Customer::count())は、1からCustomerモデルのレコード数までの範囲からランダムな整数値を生成します。
// このようにして、ランダムに選択された顧客IDがcustomer_id属性に設定されます。