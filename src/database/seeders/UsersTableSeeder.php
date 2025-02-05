<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use DateTime;

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
                'name' => 'テスト管理者',
                'email' => 'admin@ex.com',
                'password' => bcrypt('test_pw1234'),
                'role' => 1,
                'email_verified_at' => new DateTime(),
            ],
            [
                'name' => 'テスト店舗代表者',
                'email' => 'manager@ex.com',
                'password' => bcrypt('test_pw1234'),
                'role' => 11,
                'email_verified_at' => new DateTime(),
            ],
            [
                'name' => '岡本 玲央奈',
                'email' => 'okamoto_reona@example.com',
                'password' => bcrypt('test_pw1234'),
                'role' => 11,
                'email_verified_at' => null,
            ],
            [
                'name' => '鈴木 絵里子',
                'email' => 'suzuki818@example.org',
                'password' => bcrypt('test_pw1234'),
                'role' => 11,
                'email_verified_at' => null,
            ],
            [
                'name' => 'テスト利用者',
                'email' => 'test@ex.com',
                'password' => bcrypt('test_pw1234'),
                'role' => 21,
                'email_verified_at' => new DateTime(),
            ],
            [
                'name' => 'テスト利用者2',
                'email' => 'test2@ex.com',
                'password' => bcrypt('test_pw1234'),
                'role' => 21,
                'email_verified_at' => new DateTime(),
            ],
            [
                'name' => '高畑 慎吾',
                'email' => 'shingotakahata@example.net',
                'password' => bcrypt('test_pw1234'),
                'role' => 21,
                'email_verified_at' => null,
            ],
            [
                'name' => '寺島 葉子',
                'email' => 'youkoterashima@example.ne.jp',
                'password' => bcrypt('test_pw1234'),
                'role' => 21,
                'email_verified_at' => null,
            ],
            [
                'name' => '服部 篤史',
                'email' => 'hattori_913@example.org',
                'password' => bcrypt('test_pw1234'),
                'role' => 21,
                'email_verified_at' => null,
            ],
            [
                'name' => '田中 貴明',
                'email' => 'tanaka_116@example.ne.jp',
                'password' => bcrypt('test_pw1234'),
                'role' => 21,
                'email_verified_at' => null,
            ],
            [
                'name' => '橘 秀敏',
                'email' => 'tachibana527@example.co.jp',
                'password' => bcrypt('test_pw1234'),
                'role' => 21,
                'email_verified_at' => null,
            ],
            [
                'name' => '大島 真理',
                'email' => 'oshimamari@example.com',
                'password' => bcrypt('test_pw1234'),
                'role' => 21,
                'email_verified_at' => null,
            ],
            [
                'name' => '栗原 将人',
                'email' => 'kurihara_masato@example.co.jp',
                'password' => bcrypt('test_pw1234'),
                'role' => 21,
                'email_verified_at' => null,
            ],
        ];

        for($i=0; $i<100; $i++) {
            $params[] = [
                'name' => 'テスト利用者'.strval($i+1),
                'email' => 'test_user'.strval($i+1) .'@ex.com',
                'password' => bcrypt('test_pw1234'),
                'role' => 21,
                'email_verified_at' => null,
            ];
        }

        $existsData = User::all()->pluck('email')->toArray();
        $data = [];
        foreach($params as $param) {
            if (!in_array($param['email'], $existsData)) {
                $param['created_at'] =  new DateTime();
                $param['updated_at'] =  new DateTime();
                $data[] = $param;
            }
        }

        DB::table('users')->insert($data);
    }
}
