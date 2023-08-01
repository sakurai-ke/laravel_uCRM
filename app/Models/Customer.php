<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use app\Models\Purchase;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name','kana','tel','email','postcode','address', 'birthday','gender', 'memo'];
    
    // $query: このメソッドの第1引数として受け取る、Eloquent クエリビルダーを表す変数です。Eloquent クエリビルダーは、
    // データベースのテーブルに対するクエリを組み立てるために使用される強力なツールです。
// $input = null: このメソッドの第2引数として受け取る検索キーワードを表す変数です。$input は省略可能な引数であり、値が指定されない
// 場合は null がデフォルト値として使用されます。
    public function scopeSearchCustomers($query, $input = null)
    {
        // if (!empty($input)) { ... }: この部分は、$input が空でないかどうかをチェックしています。
        // $input は検索キーワードを表す変数で、ユーザーが検索フォームに入力したキーワードが格納されています。
        // if (Customer::where('kana', 'like', $input . '%')->orWhere('tel', 'like', $input . '%')->exists()) { ... }: 
            // この部分は、$input が空でない場合に、次の処理を実行するための条件です。
        if (!empty($input)) {
            // kana カラムが検索キーワードで始まるようなデータを取得するクエリを作成しています。
            // LIKE 演算子を使って、kana カラムの値が $input で始まるような顧客データを検索しています。
            if (Customer::where('kana', 'like', $input . '%')
            // tel カラムが検索キーワードで始まるようなデータを取得するクエリを作成しています。orWhere メソッドを
            // 使って、既に作成したクエリに対して tel カラムの検索条件を追加しています。
                ->orWhere('tel', 'like', $input . '%')->exists()
            ) {
                // もし条件が true（つまり、kana カラムまたは tel カラムが $input で始まるようなデータが存在する場合）、
                // 次の行の return $query->where('kana', 'like', $input . '%')->orWhere('tel', 'like', $input . '%'); が実行されます。
                return $query->where('kana', 'like', $input . '%')
                    ->orWhere('tel', 'like', $input . '%');
                // 上記の return 文により、クエリビルダーの $query に対して、kana カラムまたは tel カラムの値が $input 
                // で始まるようなデータを検索する条件が追加されます。
            }
        }
    }

    public function purchases()
        {
            return $this->hasMany(Purchase::class);
        }
}
