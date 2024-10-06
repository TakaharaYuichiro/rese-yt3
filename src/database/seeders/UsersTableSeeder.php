<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $params = [
            [
                'name' => '近藤太郎',
                'email' => 'taro@gmail.com',
                'password' => bcrypt('test_pw1234'),
            ],
            [
                'name' => '太田 明日香',
                'email' => 'test@ex.com',
                'password' => bcrypt('test_pw1234'),
            ],
            [
                'name' => '岡本 玲央奈',
                'email' => 'okamoto_reona@example.com',
                'password' => bcrypt('test_pw1234'),
            ],
            [
                'name' => '鈴木 絵里子',
                'email' => 'suzuki818@example.org',
                'password' => bcrypt('test_pw1234'),
            ],
            [
                'name' => '高畑 慎吾',
                'email' => 'shingotakahata@example.net',
                'password' => bcrypt('test_pw1234'),
            ],
            [
                'name' => '寺島 葉子',
                'email' => 'youkoterashima@example.ne.jp',
                'password' => bcrypt('test_pw1234'),
            ],
            [
                'name' => '服部 篤史',
                'email' => 'hattori_913@example.org',
                'password' => bcrypt('test_pw1234'),
            ],
            [
                'name' => '田中 貴明',
                'email' => 'tanaka_116@example.ne.jp',
                'password' => bcrypt('test_pw1234'),
            ],
            [
                'name' => '橘 秀敏',
                'email' => 'tachibana527@example.co.jp',
                'password' => bcrypt('test_pw1234'),
            ],
            [
                'name' => '大島 真理',
                'email' => 'oshimamari@example.com',
                'password' => bcrypt('test_pw1234'),
            ],
            [
                'name' => '栗原 将人',
                'email' => 'kurihara_masato@example.co.jp',
                'password' => bcrypt('test_pw1234'),
            ],
        ];

        foreach($params as $param) {
            if (!(User::where('email', $param['email'])->exists())){
                DB::table('users')->insert($param);
            }
        }
    }
}
