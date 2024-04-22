<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Vhiepp\VNDataFaker\VNFaker;
use App\Services\UserService;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userService = new UserService();
        $users = \App\Models\User::factory(30)->create();
        foreach ($users as $user) {
            $user->emails()->create([
                'email_address' => VNFaker::email([], str($user->firstname)->slug('') . str($user->date_of_birth)->slug('')),
            ]);
            $user->accounts()->create([
                'username' => $user->emails[0]->email_address,
                'password' => '123',
                'provider' => 'email/password',
                'provider_id' => $user->emails[0]->email_address,
            ]);
            $userService->changeAvatar($user);
        }
    }
}
