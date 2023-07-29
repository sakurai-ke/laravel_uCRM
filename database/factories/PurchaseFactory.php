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
        // 過去10年分のデータを取得
        $decade = $this->faker->dateTimeThisDecade;
        // '+2 years'の記述で過去8年、未来2年のデータを取得
        $created_at = $decade->modify('+2 years');

        return [
            // 今回はPurchase 100件のデータに対して登録されているcustomer_idのうちのランダムなidを割り振る。
            // （今回はDatabaseSeeer.phpでPurchaseを100件追加している。）
            'customer_id' => rand(1, Customer::count()),
            // 登録とキャンセルの情報
            'status' => $this->faker->boolean,
            // 先ほど生成した2年後の日時を 'created_at' フィールドの値として設定しています。
            'created_at' => $created_at
        ];
    }
}

// 'customer_id' => rand(1, Customer::count()),
// customer_id属性には、ランダムな顧客IDが設定されます。rand(1, Customer::count())は、1からCustomerモデルのレコード数までの範囲からランダムな整数値を生成します。
// このようにして、ランダムに選択された顧客IDがcustomer_id属性に設定されます。