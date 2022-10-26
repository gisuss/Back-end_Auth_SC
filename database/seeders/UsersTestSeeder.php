<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::create([
            'identification' => 'V-123456789',
            'email' => 'student@gmail.com',
            'active' => '1',
            'username' => 'student',
            'password'  =>  Hash::make('12345678'),
        ]);
        $user1->assignRole('student');

        $user2 = User::create([
            'identification' => 'V-123443219',
            'email' => 'tutor@gmail.com',
            'active' => '1',
            'username' => 'tutor',
            'password'  =>  Hash::make('12345678'),
        ]);
        $user2->assignRole('tutor');
    }
}
