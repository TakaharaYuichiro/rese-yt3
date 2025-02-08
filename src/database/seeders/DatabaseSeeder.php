<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(GenresTableSeeder::class);
        $this->call(ShopsTableSeeder::class);
        $this->call(EvaluationsTableSeeder::class);
        $this->call(CoursesTableSeeder::class);
        $this->call(ManagersTableSeeder::class);
        $this->call(ReservationsTableSeeder::class);


        
    }
}
