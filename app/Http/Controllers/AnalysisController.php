<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    public function index()
    {
        $startDate = '2022-08-20';
        $endDate = '2022-08-21';

        // $period = Order::betweenDate($startDate, $endDate)
        // ->groupBy('id')
        // ->selectRaw('id, sum(subtotal) as total, 
        // customer_name, status, created_at')
        // ->orderBy('created_at')
        // ->paginate(50);

        // dd($period);

        // $subQuery は特定の期間内での各購入について、購入ID、合計金額、購入日付などの情報を持つ集計結果を表します。
        // $subQuery = Order::betweenDate($startDate, $endDate)
        // キャンセルしていないもの限る
        // ->where('status', true)
        // 購買idごとにグルーピング
        // ->groupBy('id')
        // 同じ購買IDに属する購入データの subtotal カラム（小計）を合計した値（つまり、1つの購入での合計金額）をtotalPerPurchaseとして宣言
        // ->selectRaw('id, sum(subtotal) as totalPerPurchase, 
        // created_at カラムの値を YYYYMMDD の形式（年4桁＋月2桁＋日2桁）に変換
        // DATE_FORMAT(created_at, "%Y%m%d") as date');

        // $data = DB::table($subQuery)
        // 日付ごとにグループ化
        // ->groupBy('date')
        // 日付ごとの購入の合計金額（小計）を計算
        // ->selectRaw('date, sum(totalPerPurchase) as total' )
        // ->get();

        // dd($data);

        // RFM分析
        // 1. 購買ID毎にまとめる 
        $subQuery = Order::betweenDate($startDate, $endDate)
        ->groupBy('id')
        ->selectRaw('id, customer_id, customer_name, 
        SUM(subtotal) as totalPerPurchase, created_at');

        // 2. 会員毎にまとめて最終購入日、回数、合計金額を取得
        $subQuery = DB::table($subQuery)
        ->groupBy('customer_id')
        // max(created_at) as recentDate は created_at 列の最大値を取得して recentDate という名前のカラムとして追加しています。
        // これにより、各顧客ごとに最終購入日が求められます。また、datediff(now(), max(created_at)) as recency は、現在日時から
        // 最終購入日（created_at 列の最大値）を差し引いた日数を recency という名前のカラムとして追加しています。これにより、各顧客ごとの直近の購入からの経過日数が求められます。
        // count(customer_id) as frequency は、各顧客ごとの購入回数を frequency という名前のカラムとして追加しています。
        // sum(totalPerPurchase) as monetary は、各顧客ごとの合計金額を monetary という名前のカラムとして追加しています。
        ->selectRaw('customer_id, customer_name, 
        max(created_at) as recentDate, 
        datediff(now(), max(created_at)) as recency,
        count(customer_id) as frequency, 
        sum(totalPerPurchase) as monetary');

        // dd($subQuery);

        // 4. 会員毎のRFMランクを計算
        // ? <= recency の部分は、最近度（Recency）を計算しているため、$rfmPrms[0] から $rfmPrms[3] の値が使われます。
        // ? <= frequency の部分は、頻度（Frequency）を計算しているため、$rfmPrms[4] から $rfmPrms[7] の値が使われます。
        // ? <= monetary の部分は、金額（Monetary）を計算しているため、$rfmPrms[8] から $rfmPrms[11] の値が使われます。
        $rfmPrms = [
            14, 28, 60, 90, 7, 5, 3, 2, 300000, 200000, 100000, 30000 ];

        $subQuery = DB::table($subQuery)
        // 先ほど計算したrecency（直近の購入日）、frequency（購入回数）、monetary（合計金額）をもとに、各顧客のRFMランクを計算。
        // case ... end as r の部分は、直近の購入日（recency）に対するRFMランク（r）を計算しています。when recency < ? then 5 は、
        // recencyが指定された境界値よりも小さい場合にランク5を割り当てることを意味します。同様に、when recency < ? then 4 はランク4を、
        // when recency < ? then 3 はランク3を、when recency < ? then 2 はランク2を、それ以外の場合（つまり、指定された境界値以上の場合）はランク1を割り当てます。
        // case ... end as f の部分は、購入回数（frequency）に対するRFMランク（f）を計算しています。同様に、指定された境界値に基づいてランクを割り当てています。
        // case ... end as m の部分は、合計金額（monetary）に対するRFMランク（m）を計算しています。同様に、指定された境界値に基づいてランクを割り当てています。
        ->selectRaw('customer_id, customer_name,
        recentDate, recency, frequency, monetary,
        case
            when recency < ? then 5
            when recency < ? then 4
            when recency < ? then 3
            when recency < ? then 2
            else 1 end as r,
        case
            when ? <= frequency then 5
            when ? <= frequency then 4
            when ? <= frequency then 3
            when ? <= frequency then 2
            else 1 end as f,
        case
            when ? <= monetary then 5
            when ? <= monetary then 4
            when ? <= monetary then 3
            when ? <= monetary then 2
            else 1 end as m', $rfmPrms);

        // dd($subQuery->get());

        // 5.ランク毎の数を計算する
        // RFM分析の5番目のステップであり、RFMランクごとの顧客数を計算しています。具体的には、各ランク（r、f、m）に対して顧客数を計算
        $total = DB::table($subQuery)->count();
        // $subQuery をサブクエリとして使い、その結果のレコード数（顧客数）を計算しています。これにより、RFM分析を行う対象となる顧客の総数が求められる。 
        $rCount = DB::table($subQuery)
        ->rightJoin('ranks', 'ranks.rank', '=', 'r')
        ->groupBy('rank')
        ->selectRaw('rank as r, count(r)')
        ->orderBy('r', 'desc')
        ->pluck('count(r)');
        // dd($rCount->get());

        $fCount = DB::table($subQuery)
        ->rightJoin('ranks', 'ranks.rank', '=', 'f')
        ->groupBy('rank')
        ->selectRaw('rank as f, count(f)')
        ->orderBy('f', 'desc')
        ->pluck('count(f)');

        $mCount = DB::table($subQuery)
        ->rightJoin('ranks', 'ranks.rank', '=', 'm')
        ->groupBy('rank')
        ->selectRaw('rank as m, count(m)')
        ->orderBy('m', 'desc')
        ->pluck('count(m)');



        $eachCount = []; // Vue側に渡すようの空の配列
        $rank = 5; // 初期値5
        
        for($i = 0; $i < 5; $i++)
        {
          array_push($eachCount, [
             'rank' => $rank, 
             'r' => $rCount[$i],  
             'f' => $fCount[$i],  
             'm' => $mCount[$i], 
            ]);  
            $rank--; // rankを1ずつ減らす 
        }
    
        // dd($total, $eachCount, $rCount, $fCount, $mCount);
        
        // 6. RとFで2次元で表示してみる
        $data = DB::table($subQuery)
        // $subQuery と "ranks" テーブルを右外部結合して、結果を取得します。
        ->rightJoin('ranks', 'ranks.rank', '=', 'r')
        // "ranks" テーブルを "rank" カラムでグループ化します。 
        ->groupBy('rank')

        // グループごとに以下の情報を取得します：
        // concat("r_", rank) as rRank："r_" とランクの値を連結した新しいカラムを作成します。例えば、ランクが1の場合は "r_1" となります。
        // count(case when f = 5 then 1 end ) as f_5：fが5の場合の数をカウントします。
        // count(case when f = 4 then 1 end ) as f_4：fが4の場合の数をカウントします。
        // count(case when f = 3 then 1 end ) as f_3：fが3の場合の数を、、、、
        ->selectRaw('concat("r_", rank) as rRank,
        count(case when f = 5 then 1 end ) as f_5,
        count(case when f = 4 then 1 end ) as f_4,
        count(case when f = 3 then 1 end ) as f_3,
        count(case when f = 2 then 1 end ) as f_2,
        count(case when f = 1 then 1 end ) as f_1')
        ->orderBy('rRank', 'desc')
        ->get();

        // dd($data);


        

    return Inertia::render('Analysis');
    }
}
