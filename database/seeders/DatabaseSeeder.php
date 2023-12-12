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
                ['name' => 'Aditya Birla Mutual Fund', 'email' => 'darpan.oberoi@axismf.com, geeta.parulekar@axismf.com, transactmumbai@axismf.com, etf@kcpl.ind.in, instst@kcpl.ind.in', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'AXIS Mutual Fund', 'email' => 'abslamc.west@adityabirlacapital.com, abslamc_settlement@adityabirlacapital.com, haresh.mehta@adityabirlacapital.com, lovelish.solanki@adityabirlacapital.com, pranav.gupta1@adityabirlacapital.com, priya.palan@adityabirlacapital.com, reena.singh3@adityabirlacapital.com, rupesh.gurav@adityabirlacapital.com, samyak.jain1@adityabirlacapital.com, satilekha.dey@adityabirlacapital.com, shashank.bidye@adityabirlacapital.com, shashank.bidye@adityabirlacapital.com, etf@kcpl.ind.in, instst@kcpl.ind.in', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'DSP Mutual Fund', 'email' => 'etf.trade@dspim.com, etf@dspim.com, etf@kcpl.ind.in, instst@kcpl.ind.in', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'HDFC Mutual Fund', 'email' => 'abhishekm@hdfcfund.com, amor11@bloomberg.net, rajeevr@hdfcfund.com, aruna@hdfcfund.com, corptransact@hdfcfund.com, gloriad@hdfcfund.com, goldetf@hdfcfund.com, kashok@hdfcfund.com, nirmanm@hdfcfund.com, pareenazm@hdfcfund.com, ramkumarkm@hdfcfund.com, satkumart@hdfcfund.com, servicesandheri@hdfcfund.com, servicespanvel@hdfcfund.com, servicespowai@hdfc.com,servicesvashi@hdfcfund.com, shyamalib@hdfcfund.com, snehald@hdfcfund.com, etf@kcpl.ind.in, instst@kcpl.ind.in', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'ICICI Prudential Mutual Fund', 'email' => 'etftxn@icicipruamc.com, etf@kcpl.ind.in, instst@kcpl.ind.in', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'Invesco Mutual Fund', 'email' => 'Haresh.Sadani@invesco.com, etf@kcpl.ind.in, instst@kcpl.ind.in', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'Kotak Mutual Fund', 'email' => 'equity.ops@kotak.com, salgaonkar.mukul@kotak.com, satish.dondapati@kotak.com, etf@kcpl.ind.in, instst@kcpl.ind.in', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'LIC Mutual Fund', 'email' => 'a.khot@licmf.com, transaction@licmf.com, rta@licmf.com, h.shivalkar@licmf.com, etf@kcpl.ind.in, instst@kcpl.ind.in', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'Mirae Mutual Fund', 'email' => 'fas@miraeasset.com, insti.care@miraeasset.com, operations@miraeassetmf.co.in, etf@kcpl.ind.in, instst@kcpl.ind.in', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'Motilal Mutual Fund', 'email' => 'ajaykumar.saroj@motilaloswal.com, dishant.mehta@motilaloswal.com, opsamc@motilaloswal.com, swapnil.mayekar@motilaloswal.com, fundsinvestment@motilaloswal, passive.fundsinvestment@motilaloswal.com, motmf@motilaloswal.com, etf@kcpl.ind.in, instst@kcpl.ind.in', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'Navi Mutual Fund', 'email' => 'contact.mf@navi.com; mfops@navi.com; mf.investment@navi.com, etf@kcpl.ind.in, instst@kcpl.ind.in', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'Nippon Mutual Fund', 'email' => 'aditi.kundu@nipponindiaim.com, dinesh.r.kotian@nipponindiaim.com,etfops@nipponindiaim.com, rajdeep.basu@nipponindiaim.com, tridib.das@nipponindiaim.com, viraj.raje@nipponindiaim.com, etf@kcpl.ind.in, instst@kcpl.ind.in', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'Quantum Nifty', 'email' => 'hitendra@quantumamc.com, operations@quantumamc.com, prasadm@quantumamc.com, qamc@bloomberg.net, transact@quantumamc.com, etf@kcpl.ind.in, instst@kcpl.ind.in', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'SBI Mutual Fund', 'email' => 'aditya.gangal@sbimf.com, bidesh.biswas@sbimf.com, cs.instimumbai@sbimf.com, etf@kcpl.ind.in, instst@kcpl.ind.in', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'TATA Mutual Fund', 'email' => 'cshetty@tataamc.com, agulati@tataamc.com, avardarajan@tataamc.com, jshetty@tataamc.com, kmenon@tataamc.com, rahulsingh@tataamc.com, saileshjain@tataamc.com, sgang@tataamc.com, sparekh@tataamc.com, etf@kcpl.ind.in, instst@kcpl.ind.in', 'pdf_id' => 1, 'status' => 1],
                ['name' => 'UTI Mutual Fund', 'email' => 'corporate@uti.co.in, etfgroup@uti.co.in, etf@kcpl.ind.in, instst@kcpl.ind.in', 'pdf_id' => 1, 'status' => 1],
            ];

            DB::table('amcs')->insert($amcs);
            $this->command->info('AMC table seeded successfully!');
        } else {
            $this->command->info('AMC table already has data. No need to seed.');
        }
    }
}
