<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Manager;
use DateTime;

class ManagersTableSeeder extends Seeder
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
                'user_id' => 2,
                'shop_id' => 1,
            ],
            [
                'user_id' => 2,
                'shop_id' => 2,
            ],
            [
                'user_id' => 2,
                'shop_id' => 3,
            ],
            [
                'user_id' => 3,
                'shop_id' => 4,
            ],
        ];

        $data = [];
        foreach($params as $param) {
            $exists = (
                Manager::where('user_id', $param['user_id'])
                         -> where('shop_id', $param['shop_id'])
                         -> exists()
            );

            if (!$exists){
                $param['created_at'] =  new DateTime();
                $param['updated_at'] =  new DateTime();
                $data[] = $param;
            }
        }
        DB::table('managers')->insert($data);
    }
}
