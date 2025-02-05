<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;
use DateTime;

class ReservationsTableSeeder extends Seeder
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
                'booked_datetime' => date("Y-m-d H:i:s", strtotime("+7 day 20:00")),
                'booked_minutes' => 60,
                'people_counts' => 3,
                'user_id' => 5,
                'remarks' => '',
            ],
            [
                'shop_id' => 1,
                'booked_datetime' => date("Y-m-d H:i:s", strtotime("-7 day 19:00")),
                'booked_minutes' => 60,
                'people_counts' => 2,
                'user_id' => 5,
                'remarks' => '',
            ],
            [
                'shop_id' => 1,
                'booked_datetime' => date("Y-m-d H:i:s", strtotime("19:00")),
                'booked_minutes' => 60,
                'people_counts' => 3,
                'user_id' => 6,
                'remarks' => '',
            ],
            [
                'shop_id' => 2,
                'booked_datetime' => date("Y-m-d H:i:s", strtotime("18:00")),
                'booked_minutes' => 60,
                'people_counts' => 3,
                'user_id' => 7,
                'remarks' => '',
            ],
            [
                'shop_id' => 1,
                'booked_datetime' => date("Y-m-d H:i:s", strtotime("18:00")),
                'booked_minutes' => 60,
                'people_counts' => 3,
                'user_id' => 8,
                'remarks' => '',
            ],
        ];

        $data = [];
        foreach($params as $param) {
            $exists = false;

            if (!$exists){
                $param['created_at'] =  new DateTime();
                $param['updated_at'] =  new DateTime();
                $data[] = $param;
            }
        }
        DB::table('reservations')->insert($data);

    }
}
