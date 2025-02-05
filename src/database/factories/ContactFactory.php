<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [


            'first_name'=> $this->faker->firstName(),
            'last_name'=> $this->faker->lastName(),
            'gender'=> $this->faker->numberBetween(1,3),
            'email'=> $this->faker->safeEmail(),
            'tel'=> $this->faker->numberBetween(100,9999),
            'tel_middle'=> $this->faker->numberBetween(100,9999),
            'tel_bottom'=> $this->faker->numberBetween(1000,9999),
            'address'=> $this->faker->address(),
            'building'=> $this->faker->secondaryAddress(),
            'category_id'=> $this->faker->numberBetween(1,5),
            'detail'=> $this->faker->sentence(rand(10,100)),
        ];
    }
}
