<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\Subtotal;
use Carbon\Carbon;


class Order extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope(new Subtotal);
    }

    public function scopeBetweenDate($query, $startDate = null, $endDate = null)
    {
        if(is_null($startDate) && is_null($endDate))
        { return $query; }

        if(!is_null($startDate) && is_null($endDate))
        {  return $query->where('created_at', ">=", $startDate); }

        if(is_null($startDate) && !is_null($endDate))
        {
            // 終了日に1日を追加した日付を$endDate1に代入します。これは、指定した終了日を含めるための処理です。
            $endDate1 = Carbon::parse($endDate)->addDays(1);
            // クエリにwhereメソッドを使用して、created_atカラムが$endDate1以下のレコードをフィルタリングします。
            // つまり、終了日までのレコードが対象になります。
            return $query->where('created_at', '<=', $endDate1);
        }

        if(!is_null($startDate) && !is_null($endDate))
        {
            // 終了日に1日を追加した日付を$endDate1に代入します。これは、指定した終了日を含めるための処理です。
            $endDate1 = Carbon::parse($endDate)->addDays(1);
            // クエリにwhereメソッドを使用して、created_atカラムが$startDate以上かつ$endDate1以下のレコードを
            // フィルタリングします。つまり、指定した開始日から終了日までのレコードが対象になります。
            return $query->where('created_at', ">=", $startDate)
            ->where('created_at', '<=', $endDate1);
        }
    }

}

// booted() メソッドは、モデルの起動時に追加の初期化や設定を行うために使用されます。
// 特に、グローバルスコープの追加やイベントリスナーの登録などの設定を行う際によく使用されます。
// booted() メソッド内で addGlobalScope() メソッドを使用して、Subtotal グローバルスコープを追加しています。
// グローバルスコープは、データベースクエリに常に適用される共通の条件や操作を定義するために使用されます。

// addGlobalScope() メソッドは、指定されたグローバルスコープをモデルに追加するためのメソッドです。
// この例では、Subtotal グローバルスコープが追加されているため、このモデルに対するクエリには常に Subtotal グローバルスコープが適用されます。

// つまり、このコードでは Order モデルが起動されるときに Subtotal グローバルスコープが自動的に追加され、
// そのグローバルスコープが定義する条件や操作がデフォルトで適用されることになります。