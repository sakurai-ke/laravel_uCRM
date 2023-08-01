<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB ファサードを使用して、users テーブルにデータを挿入
        DB::table('users')->insert([
            'name' => 'test',
            'email' => 'test@test.com',
            // 与えられた文字列（この場合は 'password123'）をハッシュ化してセキュアな形式に変換するLaravelのヘルパー関数
            'password' => Hash::make('password123')
        ]);
    }
}
