<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       Schema::disableForeignKeyConstraints();
       User::truncate();
       Schema::enableForeignKeyConstraints();

       //atur sesuai model yang sudah dibuat
       User::insert([
        'name' => 'admin',
        'email' => 'admin@admin.com',
        'password' => Hash::make('123'), 
        'role_id' => 1,
       ]);
    }
}
