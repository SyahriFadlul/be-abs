<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin_user = New User();
        $admin_user -> name = 'Admin';
        $admin_user -> username = 'Admin';
        $admin_user -> email = 'admin@mail.com';
        $admin_user -> password = Hash::make('admin123');
        $admin_user -> save();

        $admin_user -> assignRole('admin');

        $admin_profile = New Profile();
        $admin_profile -> name = 'Admin';        
        $admin_profile -> gender = 'Laki-laki';

    
    }
}
