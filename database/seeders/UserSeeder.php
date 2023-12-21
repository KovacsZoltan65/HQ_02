<?php

namespace Database\Seeders;

//use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('users')->truncate();
        
        $password = 'password';
        $arr_users = [
            ['id' => 0, 'name' => 'Super Admin', 'email' => 'superadmin@sample.com', 'password' => Hash::make($password), 'role' => 1, 'language' => 'hu'],
            ['id' => 0, 'name' => 'Admin',       'email' => 'admin@sample.com',      'password' => Hash::make($password), 'role' => 1, 'language' => 'hu'],
            ['id' => 0, 'name' => 'Manager',     'email' => 'managerr@sample.com',   'password' => Hash::make($password), 'role' => 2, 'language' => 'hu'],
            ['id' => 0, 'name' => 'User',        'email' => 'user@sample.com',       'password' => Hash::make($password), 'role' => 2, 'language' => 'hu'],
        ];
        $count = count($arr_users);
        
        $this->command->warn(PHP_EOL . 'Creating users...');
        $this->command->getOutput()->progressStart($count);
        
        foreach($arr_users as $user){
            \App\Models\User::factory()->create($user);
            //User::factory()->create($user);
            $this->command->getOutput()->progressAdvance();
        }
        $this->command->getOutput()->progressFinish();
        $this->command->info(PHP_EOL . 'Users created');
    }
}
