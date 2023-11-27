<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

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
    }
}
