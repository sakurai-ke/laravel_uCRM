<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;

class AnalysisService
{
  public static function perDay($subQuery)
  {
    // $subQuery は、日付の範囲が指定された売上データを含むクエリです。
    // これは AnalysisController.php の index メソッドで作成され、AnalysisService.php の各メソッドに渡されます。
    // $subQuery に対して where('status', true) を適用して、売上データのステータスが 
    // true（つまり、有効な売上データ）のもののみを対象としています。
    $query = $subQuery->where('status', true)
    // 売上データを個別の顧客ごとにグループ化します。これにより、各顧客ごとに売上データを集計することができます。
      ->groupBy('id')
      // id（顧客のID）、sum(subtotal)（売上データの合計金額）、および 
      // DATE_FORMAT(created_at, "%Y%m%d")（売上データの作成日を指定されたフォーマットで表示）を選択
      ->selectRaw('id, sum(subtotal) as totalPerPurchase, 
      DATE_FORMAT(created_at, "%Y%m%d") as date');

      // 上記のクエリを元に、DB::table($query) を実行してデータを取得
    $data = DB::table($query)
    // 取得したデータに対して、groupBy('date') を適用して日付ごとにグループ化します。これにより、日別の売上データを集計します。
    ->groupBy('date')
    // 日付とその日付に対応する売上データの合計を選択します。
    ->selectRaw('date, sum(totalPerPurchase) as total' )
    ->get();

    // $data 変数から日付データの列（date 列）を取り出し、$labels 変数に代入しています。これにより、日別の売上データのラベルが取得されます。
    // $data は日別の売上データが格納されたデータオブジェクトです。このデータオブジェクトには、各日付の売上データが含まれています。
    // $labels は日別の売上データの日付を格納した配列です。これはチャートのX軸に表示されるラベルとして利用されます。
    // pluck メソッドは、コレクション内の要素から特定のカラムの値を取り出すために使われる。
    $labels = $data->pluck('date');
    // $data 変数から売上データの合計の列（total 列）を取り出し、$totals 変数に代入しています。これにより、日別の売上データの合計が取得されます。
    // $totals は日別の売上データの合計金額を格納した配列です。これはチャートのY軸に表示されるデータとして利用されます。
    $totals = $data->pluck('total');

    // $data、$labels、および $totals を配列としてまとめて、このメソッドの呼び出し元に返します。
    // 具体的には、このメソッドは AnalysisController.php の index メソッドで呼び出されています。
    return [$data, $labels, $totals];

    // これらのデータは、AnalysisController.php の index メソッドで受け取られ、<Chart> コンポーネントに渡されます？
  }

  public static function perMonth($subQuery)
  {
    $query = $subQuery->where('status', true)
      ->groupBy('id')
      ->selectRaw('id, sum(subtotal) as totalPerPurchase, 
      DATE_FORMAT(created_at, "%Y%m") as date');

    $data = DB::table($query)
    ->groupBy('date')
    ->selectRaw('date, sum(totalPerPurchase) as total' )
    ->get();

    $labels = $data->pluck('date');
    $totals = $data->pluck('total');

    return [$data, $labels, $totals];
  }

  public static function perYear($subQuery)
  {
    $query = $subQuery->where('status', true)
      ->groupBy('id')
      ->selectRaw('id, sum(subtotal) as totalPerPurchase, 
      DATE_FORMAT(created_at, "%Y") as date');

    $data = DB::table($query)
    ->groupBy('date')
    ->selectRaw('date, sum(totalPerPurchase) as total' )
    ->get();

    $labels = $data->pluck('date');
    $totals = $data->pluck('total');

    return [$data, $labels, $totals];
  }
}