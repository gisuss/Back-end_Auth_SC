<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'firstname' => 'Admin',
            'lastname'  =>  'Admin',
            'identification_document' => '12345678',
            'email' => 'admin@gmail.com',
            'gender' => 'M',
            'birthday' => '1992-10-29',
            'phone' => '04244600000',
            'faculty' => 'Facyt',
            'departament' => 'Computación',
            'active' => '1',
            'username' => 'admin',
            'password'  =>  Hash::make('12345678'),
        ]);
        $user->assignRole('coordinator');
    }
}
