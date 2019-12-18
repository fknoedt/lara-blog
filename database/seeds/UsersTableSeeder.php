<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * 50 fake users
     */
    public function run()
    {
        factory(App\User::class, 50)->create();
    }
}
