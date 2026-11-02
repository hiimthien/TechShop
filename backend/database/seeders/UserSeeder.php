<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrNew(['email' => 'admin@techshop.com']);
        $admin->name = 'Admin';
        $admin->password = 'password';
        $admin->role = 'admin';
        $admin->save();

        $customer = User::firstOrNew(['email' => 'customer@techshop.com']);
        $customer->name = 'Nguyễn Văn Khách';
        $customer->password = 'password';
        $customer->role = 'customer';
        $customer->save();
    }
}
