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
            'customer_id' => rand(1, Customer::count()),
            'status' => $this->faker->boolean,
        ];
    }
}

// 'customer_id' => rand(1, Customer::count()),
// customer_id属性には、ランダムな顧客IDが設定されます。rand(1, Customer::count())は、1からCustomerモデルのレコード数までの範囲からランダムな整数値を生成します。このようにして、ランダムに選択された顧客IDがcustomer_id属性に設定されます。