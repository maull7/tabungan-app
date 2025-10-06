<?php

namespace Database\Seeders;

use App\Actions\CreateSavingsAccountForUser;
use App\Actions\PerformDeposit;
use App\Actions\PerformWithdrawal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $createAccount = App::make(CreateSavingsAccountForUser::class);
        $performDeposit = App::make(PerformDeposit::class);
        $performWithdrawal = App::make(PerformWithdrawal::class);

        $users = [
            [
                'name' => 'Demo Nasabah',
                'email' => 'demo@tabungan.test',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Demo Kedua',
                'email' => 'demo2@tabungan.test',
                'password' => bcrypt('password'),
            ],
        ];

        foreach ($users as $userData) {
            /** @var User $user */
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData,
            );

            $createAccount->handle($user);

            for ($i = 0; $i < 5; $i++) {
                $amount = rand(100_000, 500_000);
                $performDeposit->handle($user, $amount, 'Setoran demo #'.($i + 1));

                if ($i % 2 === 0) {
                    $withdrawAmount = rand(50_000, $amount - 10_000);
                    try {
                        $performWithdrawal->handle($user, $withdrawAmount, 'Penarikan demo #'.($i + 1));
                    } catch (\InvalidArgumentException $e) {
                        // abaikan bila saldo tidak cukup saat seeding
                    }
                }
            }
        }
    }
}
