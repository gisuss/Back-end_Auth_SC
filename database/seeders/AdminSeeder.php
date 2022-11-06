<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::create([
            'identification' => 'V-12345678',
            'email' => 'admin@gmail.com',
            'active' => '1',
            'username' => 'admin',
            'password'  =>  Hash::make('12345678'),
            'email_verified_at' => Carbon::now(),
        ]);
        $user1->assignRole('coordinator');

        $user2 = User::create([
            'identification' => 'V-12344321',
            'email' => 'mirellaherrera@gmail.com',
            'active' => '1',
            'username' => 'mherrera',
            'password'  =>  Hash::make('12345678'),
            'email_verified_at' => Carbon::now(),
        ]);
        $user2->assignRole('coordinator');
    }
}
