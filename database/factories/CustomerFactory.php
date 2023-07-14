<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $tel = str_replace('-', '', $this->faker->phoneNumber);
        // 第一引数は取得対象の文字列であり、ここでは $this->faker->address の結果が指定されています。
        // 第二引数は取得する部分文字列の開始位置であり、ここでは 9 が指定されています。
        // この場合、元の住所の文字列の先頭から 9 文字目以降の部分文字列が取得されます。
        $address = mb_substr($this->faker->address, 9);
        
        return [
            'name' => $this->faker->name,
            'kana' => $this->faker->kanaName,
            'email' => $this->faker->email,
            'postcode' => $this->faker->postcode,
            'birthday' => $this->faker->dateTime,
            'gender' => $this->faker->numberBetween(0, 2),
            'memo' => $this->faker->realText(50),
            'tel' => $tel, // 変更
            'address' => $address, // 変更
        ];
    }
}
