<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;

class DecileService
{
  public static function decile($subQuery)
  {
    // 1. 購買ID毎にまとめる 
    $subQuery = $subQuery->groupBy('id')
    ->selectRaw('id, customer_id, customer_name, 
    SUM(subtotal) as totalPerPurchase');

    // 2. 会員毎にまとめて購入金額順にソートする
    $subQuery = DB::table($subQuery)
    ->groupBy('customer_id')
    ->selectRaw('customer_id, customer_name, 
    sum(totalPerPurchase) as total')
    ->orderBy('total', 'desc');

    // dd($subQuery);

    // 3. 購入順に連番を振る
    // データベースのセッション変数 @row_num を0で初期化しています。この変数は後で使われ、購入額のランキング（順位）を振るために使用されます。
    DB::statement('set @row_num = 0;');
    // $subQuery は引数として受け取った $subQuery クエリビルダを基に新しいクエリを構築しています。
    // @row_num:= @row_num+1 as row_num は、@row_num の値を1ずつ増やして新しいカラム row_num に代入することを意味しています。
    // これにより、データが並び替えられた後に購入額に応じた順位が振られることになります。
    // customer_id, customer_name, total の３つのカラムを取得しています。customer_id と customer_name は顧客情報であり、total
    //  は顧客の購入金額の合計です。このクエリを実行することで、購入金額の降順に並べ替えられた顧客のデータが取得されます。
    // この処理の後、デシル分析のための準備が整いました。各顧客の購入金額が順位付けされており、次にデシルグループごとに集計していきます。
    // これにより、デシル分析の結果として、各グループごとの合計金額や平均金額、全体に対する構成比を算出することができます。
    $subQuery = DB::table($subQuery)
    ->selectRaw('
    
    @row_num:= @row_num+1 as row_num,
    customer_id,
    customer_name,
    total');

    // dd($subQuery);

    // 4. 全体の件数を数え、1/10の値や合計金額を取得
    // 格納された一時テーブルの行数（件数）を取得します。つまり、このデシル分析で使用される顧客の総数を取得しています？
    $count = DB::table($subQuery)->count();
    // selectRaw() メソッドを使って、sum(total) を選択します。これにより、売上データの合計の総和が取得されます。
    // ここでは、まだ実際のデータベースクエリは実行されていません。get() メソッドはクエリを実行し、結果を取得します。
    $total = DB::table($subQuery)->selectRaw('sum(total) as total')->get();
    // 上記のget()では配列でのデータの取得となるため、配列のインデックス0番目を指定して取得。
    // （$totalの配列の中にさらにtotalプロパティが入っており、インデックス0番目を指定することにより、
    // totalプロパティを取得している、らしい？$totalの配列は一つのtotalプロパティのみ入っているらしいので
    // total[0]と指定してtotalプロパティを取得しているらしい。
    $total = $total[0]->total; // 構成比用

    $decile = ceil($count / 10); // 10分の1の件数を変数に入れる

    // 配列 $bindValues という空の配列を用意しています。これは後でSQLのクエリを作成する際に使用されます。
    // また、$tempValue という変数を0で初期化しています。
    $bindValues = [];
    $tempValue = 0;
    // データの総数を10で割り、端数がある場合には切り上げて10分の1の件数を求めています。
    for($i = 1; $i <= 10; $i++)
    {
      // for ループを10回繰り返します（$i は1から10までの値を取ります）。

// デシル分析の対象となるデータの総数が1000件であるとした場合の計算手順（この計算方法はデシル分析の一般的な手法、らしい）
// 初回（$i = 1）: $bindValues 配列に1 + 0 = 1 を追加します（10分の1のグループの開始位置）。
// $tempValue に $decile を加算します。 $tempValue += $decile という処理により、$tempValue = 0 + 100 = 100 となります。
// $bindValues 配列に 1 + 100 = 101 を追加します（10分の1のグループの終了位置）。
// 2回目（$i = 2）: $bindValues 配列に 1 + 100 = 101 を追加します（次の10分の1のグループの開始位置）。
// $tempValue に $decile を加算します。 $tempValue += $decile という処理により、$tempValue = 100 + 100 = 200 となります。
// $bindValues 配列に 1 + 200 = 201 を追加します（次の10分の1のグループの終了位置）。
// これを繰り返して、10回目（$i = 10）まで繰り返します。
    array_push($bindValues, 1 + $tempValue);
    $tempValue += $decile; 
    array_push($bindValues, 1 + $tempValue);
    }

    // dd($count, $decile, $bindValues);

    // 5 10分割しグループ毎に数字を振る
    // DB::statement('set @row_num = 0;');という行では、変数@row_numを初期化しています。この変数は後でデシル値を割り当てる際に使用されます。
    DB::statement('set @row_num = 0;');
    // DB::table($subQuery)を使って、一時テーブル $subQuery を元に新しいクエリを作成しています。このクエリでは、row_num（連番）、
    // customer_id（顧客ID）、customer_name（顧客名）、total（顧客ごとの売上合計）、decile（デシル値）を選択しています。
    // SQLのCASE文と呼ばれる条件式を使ってデシル値（1から10までの値）を計算しています。CASE文は条件に応じて異なる値を返す制御構造です。
    // ?はプレースホルダーで、後で具体的な値をバインドするためのものです。この?には配列$bindValuesの値が順番に代入されます。
    // 具体的には、以下のように動作します：
    // when ? <= row_num and row_num < ? then 1: ?の値が$bindValuesの1番目と2番目の要素（例：1と10）であれば、row_numが1から9までの範囲に含まれる顧客に対してdecileに1が割り当てられます。つまり、row_numが1以上10未満の顧客にはデシル値1が割り当てられます。
    // when ? <= row_num and row_num < ? then 2: ?の値が$bindValuesの3番目と4番目の要素（例：11と20）であれば、row_numが11から19までの範囲に含まれる顧客に対してdecileに2が割り当てられます。つまり、row_numが11以上20未満の顧客にはデシル値2が割り当てられます。
    // 同様に、when ? <= row_num and row_num < ? then 3からwhen ? <= row_num and row_num < ? then 10まで、それぞれの?の値に応じてデシル値が割り当てられます。
    // このようにして、row_numの値に応じてデシル値が割り当てられ、decileカラムに1から10までの値が格納された結果が得られます。これにより、顧客データが10分割され、各顧客に適切なデシル値が割り当てられることになります。
    $subQuery = DB::table($subQuery)
    ->selectRaw("
        row_num,
        customer_id,
        customer_name, 
        total,
        case 
            when ? <= row_num and row_num < ? then 1
            when ? <= row_num and row_num < ? then 2
            when ? <= row_num and row_num < ? then 3
            when ? <= row_num and row_num < ? then 4
            when ? <= row_num and row_num < ? then 5
            when ? <= row_num and row_num < ? then 6
            when ? <= row_num and row_num < ? then 7
            when ? <= row_num and row_num < ? then 8
            when ? <= row_num and row_num < ? then 9
            when ? <= row_num and row_num < ? then 10
        end as decile
        ", $bindValues); 
    
    // dd($subQuery);

    // 6. グループ毎の合計・平均
    // DB::table($subQuery) は、$subQuery に格納された一時テーブル（もしくはクエリ）を基にして新しいクエリビルダを作成し、
    // それを$subQuery という変数に再代入しているという意味
    $subQuery = DB::table($subQuery)
    // groupBy('decile')：デシルグループ（decileカラムの値）ごとにグループ化します。
    ->groupBy('decile')
    // selectRaw()メソッド内で、decileカラムの値（デシルグループ番号）を選択します。
    // sum(total) as totalPerGroup：totalカラム（売上データの合計）の総合計値を計算し、totalPerGroupという名前で結果を返します。
    ->selectRaw('decile, 
    round(avg(total)) as average, 
    sum(total) as totalPerGroup');

    // dd($subQuery);

    
    // 7 構成比
    // DB::statement("set @total = ${total} ;"); は、Laravelのクエリビルダを使って、直接SQL文を実行するためのメソッドです。
    // ここでは、MySQLのSETステートメントを使って、@total という名前のユーザー変数に $total の値をセットしています。
    // MySQLのユーザー変数は、セッション内で一時的に値を保存するための変数であり、セッションが終了すると破棄されます。変数名の前に @ を
    // つけることでユーザー変数を表します。SETステートメントは、SET @変数名 = 値 の形式で使われ、指定した変数に値をセットします。
    // 上記のコードでは、@total というユーザー変数に、変数 $total の値をセットしています。なぜこのコードが使われているかというと、
    // その後のクエリで@totalの値を利用して、構成比（各グループの売上合計が全体の売上に占める割合）を計算するためです。デシル分析では、
    // 各グループの売上を全体の売上で割って構成比を求める必要がありますが、それを SELECT 文の中で直接計算することは難しいため、事前に
    // ユーザー変数に全体の売上をセットしておき、その値を使って構成比を計算しています。
    DB::statement("set @total = ${total} ;");
    $data = DB::table($subQuery)
    // グループごとにデシル、平均、グループ内売上の合計、および構成比（totalRatio）を計算
    // round(100 * totalPerGroup / @total, 1) は、totalPerGroup（グループ内売上の合計）を 
    // @total（全体の売上の合計）で割り、100倍して構成比を計算しています。最後の , 1 は小数点以下を1桁に丸めることを示しています。
    ->selectRaw('decile,
        average,
        totalPerGroup,
        round(100 * totalPerGroup / @total, 1) as totalRatio
    ')
    ->get();

    // それぞれデシルの値とグループ内売上の合計値を抽出し、$labels と $totals という変数に格納
    $labels = $data->pluck('decile');
    $totals = $data->pluck('totalPerGroup');

    // return [$data, $labels, $totals]; で、デシル分析の結果として得られたデータ（$data）、デシルの値（$labels）、およびグループ内売上の合計値（$totals）を配列として返す
    return [$data, $labels, $totals];
  }
}