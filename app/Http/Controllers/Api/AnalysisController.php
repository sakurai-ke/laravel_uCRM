<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Services\AnalysisService;
use App\Services\DecileService;
use App\Services\RFMService;

class AnalysisController extends Controller
{
    public function index(Request $request)
{
    $subQuery = Order::betweenDate($request->startDate, $request->endDate);

    // 日別の場合
        if($request->type === 'perDay'){
           list($data, $labels, $totals) = AnalysisService::perDay($subQuery);
        }

        if($request->type === 'perMonth'){
            list($data, $labels, $totals) = AnalysisService::perMonth($subQuery);
         }

         if($request->type === 'perYear'){
            list($data, $labels, $totals) = AnalysisService::perYear($subQuery);
         }

         if($request->type === 'decile'){
            list($data, $labels, $totals) = DecileService::decile($subQuery);
         }

         if($request->type === 'rfm'){
            list($data, $totals, $eachCount) = RFMService::rfm($subQuery, $request->rfmPrms);
         
            // Ajax通信なのでJsonで返却する必要がある
            // response()->json([...])を使ってJSONレスポンスを作成しています。[...]の部分には、レスポンスとして
            // 返すデータを含む連想配列を指定します。
            return response()->json([
            // $data: これはデータ分析の結果を格納する変数です。具体的には、選択された分析方法に応じて、
            // 日別、月別、年別、デシル分析、RFM分析などの結果が格納されます。
                'data' => $data,
                // クライアントから送信されたリクエストの中に含まれるtypeパラメータの値を取得します。このパラメータは、
                // どのような分析方法が選択されたかを示すものです。例えば、もしtypeパラメータが'perDay'の場合、
                // 日別の分析が選択されたことを示します。同様に、'perMonth'は月別、'perYear'は年別、'decile'はデシル分析、
                // 'rfm'はRFM分析を意味します。
                'type' => $request->type,
                'eachCount' => $eachCount,
                'totals' => $totals,
                // 第二引数として指定されているResponse::HTTP_OKは、レスポンスのHTTPステータスコードを表しています。
            // Response::HTTP_OKはHTTPステータスコード200を意味し、リクエストが成功したことを示します。
            ], Response::HTTP_OK);

        }
        return response()->json([
            'data' => $data,
            'type' => $request->type,
            'labels' => $labels,
            'totals' => $totals,
        ], Response::HTTP_OK);
}
}
