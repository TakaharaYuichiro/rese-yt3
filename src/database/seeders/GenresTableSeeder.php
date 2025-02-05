<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Genre;
use DateTime;

class GenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $params = [
            "イタリアン",   // id=1
            "ラーメン",     // 2
            "居酒屋",       // 3
            "寿司",         // 4
            "焼肉",         // 5
        ];

        foreach($params as $param) {
            if (!(Genre::where('genre', $param)->exists())){
                DB::table('genres')->insert([
                    'genre' => $param,
                    'created_at' => new DateTime(),
                    'updated_at' => new DateTime(),
                ]);
            }
        }

    }
}
