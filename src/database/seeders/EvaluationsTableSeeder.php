<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Evaluation;

class EvaluationsTableSeeder extends Seeder
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
                'user_id' => 1,
                'shop_id' => 1,
                'favorite' => true,
                'score' => 0,
                'comment' => 'テスト', 
            ],
            [
                'user_id' => 1,
                'shop_id' => 2,
                'favorite' => true,
                'score' => 0,
                'comment' => 'テスト', 
            ],
            [
                'user_id' => 1,
                'shop_id' => 5,
                'favorite' => true,
                'score' => 0,
                'comment' => 'テスト', 
            ],
            [
                'user_id' => 1,
                'shop_id' => 6,
                'favorite' => false,
                'score' => 0,
                'comment' => 'テスト', 
            ],

            [
                'user_id' => 2,
                'shop_id' => 1,
                'favorite' => true,
                'score' => 0,
                'comment' => 'テスト', 
            ],
            [
                'user_id' => 2,
                'shop_id' => 2,
                'favorite' => true,
                'score' => 0,
                'comment' => 'テスト', 
            ],
            
        ];

        foreach($params as $param) {
            $exists = (
                Evaluation::where('user_id', $param['user_id'])
                         -> where('shop_id', $param['shop_id'])
                         -> exists()
            );

            if (!$exists){
                DB::table('evaluations')->insert($param);
            }
        }
    }
}
