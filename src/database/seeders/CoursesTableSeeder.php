<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use DateTime;   // phpのDateTime関数を使うときのおまじない

class CoursesTableSeeder extends Seeder
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
                'shop_id' => 1,
                'name' => '江戸前寿司ディナーコース',
                'price' => 11000,
                'detail' => 'テスト', 
                'enable' => true,
            ],
            [
                'shop_id' => 1,
                'name' => '厳選ネタおまかせコース',
                'price' => 13000,
                'detail' => 'テスト', 
                'enable' => true,
            ],
            [
                'shop_id' => 1,
                'name' => '寿司と天ぷらスペシャルコース',
                'price' => 9000,
                'detail' => 'テスト', 
                'enable' => true,
            ],
            [
                'shop_id' => 1,
                'name' => '旬の握りおまかせコース',
                'price' => 8000,
                'detail' => 'テスト', 
                'enable' => true,
            ],
            [
                'shop_id' => 2,
                'name' => '特選黒毛和牛コース',
                'price' => 12000,
                'detail' => 'テスト', 
                'enable' => true,
            ],
            [
                'shop_id' => 2,
                'name' => '贅沢タン＆カルビコース',
                'price' => 8500,
                'detail' => 'テスト', 
                'enable' => true,
            ],
            [
                'shop_id' => 2,
                'name' => '炭火焼肉スペシャルコース',
                'price' => 9000,
                'detail' => 'テスト', 
                'enable' => true,
            ],
            [
                'shop_id' => 3,
                'name' => '定番居酒屋おつまみコース',
                'price' => 3500,
                'detail' => 'テスト', 
                'enable' => true,
            ],
            [
                'shop_id' => 3,
                'name' => '串焼き盛り合わせコース',
                'price' => 4000,
                'detail' => 'テスト', 
                'enable' => true,
            ],
            
            
        ];

        $data = [];
        foreach($params as $param) {
            $exists = (
                Course::where('shop_id', $param['shop_id'])
                    -> where('name', $param['name'])
                    -> exists()
            );

            if (!$exists){
                $param['created_at'] =  new DateTime();
                $param['updated_at'] =  new DateTime();
                $data[] = $param;
            }
        }
        DB::table('courses')->insert($data);
    }
}
