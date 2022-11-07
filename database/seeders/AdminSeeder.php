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
        $user = User::create([
            'identification' => 'V-8044677',
            'email' => 'mirella.herrera@gmail.com ',
            'active' => '1',
            'username' => 'mherrera',
            'password'  =>  Hash::make('8044677'),
            'email_verified_at' => Carbon::now(),
        ]);
        $user->assignRole('coordinator');
    }
}
