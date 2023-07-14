<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onUpdate('cascade');
            $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
};

//             $table->foreignId('customer_id')->constrained()->onUpdate('cascade');について
// foreignId('customer_id')：customer_idという外部キーカラムを作成します。外部キーカラムは他のテーブルのレコードを参照するために使用されます。foreignId()メソッドは、外部キーカラムを作成するための便利なメソッドです。customer_idは外部キーのカラム名です。
// constrained()：constrained()メソッドは外部キー制約を設定します。つまり、customer_idカラムが他のテーブルの特定のカラムを参照することを指定します。デフォルトでは、constrained()メソッドはカラム名としてidカラムを参照しますが、必要に応じて明示的に指定することもできます。
// onUpdate('cascade')：onUpdate()メソッドは、参照されているレコードが更新された場合にどのように振る舞うかを指定します。cascadeオプションは、参照されているレコードが更新された場合に、それに関連するレコードも自動的に更新されることを意味します。つまり、customersテーブルのidカラムが更新された場合、purchasesテーブルのcustomer_idカラムに対応するレコードも自動的に更新されます。