<?php

namespace App\Http\Controllers;

use App\Models\InertiaTest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InertiaTestController extends Controller
{
    public function index()
    {
        return Inertia::render('Inertia/Index', [
            // blogsは適当に指名した
            'blogs' => InertiaTest::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('Inertia/Create');
    }

    public function show($id)
    {
        // dd($id);
        return Inertia::render('Inertia/Show', [
            // 左側の'id'をshowファイルのdefinePropsに渡している
            'id' => $id,
            // findOrFail($id)について、データベースの中の一件を取得する
            // findOrFail()は、一致するidが見つからなかった場合は、エラーを返します。
            'bloggg' => InertiaTest::findOrFail($id)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'max:20'],
            'content' => ['required'],
            ]);
// $inertiaTest = new InertiaTest; の後ろでは「()」は不要？？？
        $inertiaTest = new InertiaTest;
        $inertiaTest->title = $request->title;
        $inertiaTest->content = $request->content;
        $inertiaTest->save();
        return to_route('inertia.index')->with(['message' => '登録しました。']);
    }

// 削除処理
// findOrFail メソッドはデータベースからレコードを取得する際に、レコードが存在しない場合に例外をスローすることで、データの存在を保証するためメソッド
    public function delete($id)
{
        $book = InertiaTest::findOrFail($id);
        $book->delete();
        return to_route('inertia.index')
        ->with([ 'message' => '削除しました。' ]);
}
}
