<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Pdf;
use App\Models\Amc;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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

            $user = User::create([
               'name'     => 'trader',
               'email'    => 'trader@kfpl.com',
               'password' => Hash::make('trader123'),
               'phone'    => null
            ]);
            $user->roles()->sync([2]);

            $user = User::create([
               'name'     => 'ops',
               'email'    => 'ops@kfpl.com',
               'password' => Hash::make('ops123'),
               'phone'    => null
            ]);
            $user->roles()->sync([3]);

            $user = User::create([
               'name'     => 'backoffice',
               'email'    => 'backoffice@kfpl.com',
               'password' => Hash::make('backoffice123'),
               'phone'    => null
            ]);
            $user->roles()->sync([4]);

            $user = User::create([
               'name'     => 'dealer',
               'email'    => 'dealer@kfpl.com',
               'password' => Hash::make('dealer123'),
               'phone'    => null
            ]);
            $user->roles()->sync([5]);

            $user = User::create([
               'name'     => 'accounts',
               'email'    => 'accounts@kfpl.com',
               'password' => Hash::make('accounts123'),
               'phone'    => null
            ]);
            $user->roles()->sync([6]);
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

        // AMCs
        if (Amc::count() === 0) {
            $amcs = [
                ['name' => 'Aditya Birla Mutual Fund', 'email' => 'aditya@example.com', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'AXIS Mutual Fund', 'email' => 'axis@example.com', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'DSP Mutual Fund', 'email' => 'dsp@example.com', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'HDFC Mutual Fund', 'email' => 'hdfc@example.com', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'ICICI Prudential Mutual Fund', 'email' => 'icici@example.com', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'Invesco Mutual Fund', 'email' => 'invesco@example.com', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'Kotak Mutual Fund', 'email' => 'kotak@example.com', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'LIC Mutual Fund', 'email' => 'lic@example.com', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'Mirae Mutual Fund', 'email' => 'mirae@example.com', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'Motilal Mutual Fund', 'email' => 'motilal@example.com', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'Navi Mutual Fund', 'email' => 'navi@example.com', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'Nippon Mutual Fund', 'email' => 'nippon@example.com', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'Quantum Nifty', 'email' => 'quantum@example.com', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'SBI Mutual Fund', 'email' => 'sbi@example.com', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'TATA Mutual Fund', 'email' => 'tata@example.com', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'UTI Mutual Fund', 'email' => 'uti@example.com', 'pdf_id' => 1, 'status' => 1],
            ];

            DB::table('amcs')->insert($amcs);
            $this->command->info('AMC table seeded successfully!');
        } else {
            $this->command->info('AMC table already has data. No need to seed.');
        }
    }
}
