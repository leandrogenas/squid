<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\User::create([
            "name"=>"Jean Lindo",
            "email"=>"jeananimax@gmail.com",
            "password"=> Hash::make("jean1023x")
        ]);
    }
}
