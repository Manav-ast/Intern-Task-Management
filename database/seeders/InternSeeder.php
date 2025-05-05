<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Intern;

class InternSeeder extends Seeder
{
    public function run(): void
    {
        Intern::create([
            'name' => 'John Intern',
            'email' => 'intern@test.com',
            'password' => Hash::make('password'),
        ]);
    }
}

