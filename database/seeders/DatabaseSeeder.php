<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Purchase;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            ItemSeeder::class
        ]);
        
        \App\Models\Customer::factory(1000)->create();

        $items = \App\Models\Item::all();

        Purchase::factory(100)->create()
        // eachでPurchaseを1件ずつ処理する
        // コールバック関数で指定。$itemsは上記の$items = \App\Models\Item::all();で使用できるようにしている。
        ->each(function(Purchase $purchase) use ($items){
            // attachを記述することにより中間テーブルにも同時に登録が可能
        $purchase->items()->attach(
            // purchace_id 1つにつき、item_idが1〜3のランダムの数づつ割り振られる
            // （1回の購入でアイテムを3つ購入している、ということ）
        $items->random(rand(1,3))->pluck('id')->toArray(),
        // それぞれのアイテムについて、そのアイテムを何個購入したかを1〜5個でランダムに割り振る
        ['quantity' => rand(1, 5) ] ); });
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
