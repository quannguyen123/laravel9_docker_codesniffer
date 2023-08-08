<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'partner']);
        Role::create(['name' => 'user']);
        Role::create(['name' => 'partner_admin']);
        Role::create(['name' => 'partner_hr']);
        Role::create(['name' => 'partner_accountant']);
        // Permission::create(['name' => 'edit articles2']);
    }
}
