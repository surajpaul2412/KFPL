<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Pdf;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {		
		// ROLE SEEDER
		if (Role::count() === 0) {
            // ROLE SEEDER
            $arr = [
                ['name' => 'Admin', 'description' => 'Administrator'],
                ['name' => 'Trader', 'description' => 'Trader Users'],
                ['name' => 'Ops', 'description' => 'Users in Operations'],
                ['name' => 'Backoffice', 'description' => 'Back Office Users'],
                ['name' => 'Dealer', 'description' => 'Dealers'],
                ['name' => 'Accounts', 'description' => 'Account Users']
            ];

            foreach ($arr as $row) {
                Role::create($row);
            }
            $this->command->info('Roles table seeded successfully!');
        } else {
            $this->command->info('Roles table already has data. No need to seed.');
        }
		
		// CREATE ADMIN User 
        if (User::count() === 0) {
    		$user = User::create([
    		   'name'     => 'admin',
    		   'email'    => 'admin@kfpl.com',
    		   'password' => Hash::make('admin123'),
    		   'phone'    => null
    		]);
    		
    		$user->roles()->sync([1]);
        }

        // PDF
        if (Pdf::count() === 0) {
            $arr = [
                ['name' => 'Sample PDF 1', 'status' => 1],
                ['name' => 'Sample PDF 2', 'status' => 1],
                ['name' => 'Inactive PDF', 'status' => 0],
            ];

            foreach ($arr as $row) {
                Pdf::create($row);
            }
            $this->command->info('Pdf table seeded successfully!');
        } else {
            $this->command->info('Pdf table already has data. No need to seed.');
        }
    }
}
