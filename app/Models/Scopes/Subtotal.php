<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class Subtotal implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $sql = 'select purchases.id as id
        , items.id as item_id
        , item_purchase.id as pivot_id
        , items.price * item_purchase.quantity as subtotal
        , customers.id as customer_id
        , customers.name as customer_name
        , items.name as item_name
        , items.price as item_price
        , item_purchase.quantity
        , purchases.status
        , purchases.created_at 
        , purchases.updated_at 
        from purchases
        left join item_purchase on purchases.id = item_purchase.purchase_id
        left join items on item_purchase.item_id = items.id 
        left join customers on purchases.customer_id = customers.id 
        ';

        $builder->fromSub($sql, 'order_subtotals');

    }

}

// SQL文が定義されており、purchases テーブル、item_purchase テーブル、items テーブル、customers テーブルを
// 結合して結果を返します。このSQL文は、購入情報に関連する商品の小計（数量 × 単価）を計算し、各項目を含む結果セットを取得するものです。

// $builder->fromSub($sql, 'order_subtotals') は、Eloquentクエリビルダーに指定したSQL文 $sql を
// 一時テーブルとして追加し、その一時テーブルを order_subtotals という名前で使用することを意味しています。