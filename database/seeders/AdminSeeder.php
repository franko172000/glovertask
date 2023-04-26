<?php

namespace Database\Seeders;

use App\Persistence\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin1@test.com',
                'password' => Hash::make('test'),
                'first_name' => 'John',
                'last_name' => 'Admin1',
                'user_type' => 'admin'
            ],
            [
                'email' => 'admin2@test.com',
                'password' => Hash::make('test'),
                'first_name' => 'Fred',
                'last_name' => 'Admin2',
                'user_type' => 'admin'
            ],
            [
                'email' => 'admin3@test.com',
                'password' => Hash::make('test'),
                'first_name' => 'Kate',
                'last_name' => 'Admin3',
                'user_type' => 'admin'
            ]
        ];
        collect($users)->each(fn($user)=>User::create($user));
    }
}
