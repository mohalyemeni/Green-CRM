<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EntrustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        $adminRole = Role::create([
            'name' =>'Admin',
            'display_name' =>'Administration',
            'description' =>'Administrator',
            'allowed_route' =>'admin',
        ]);

        $supervisorRole = Role::create([
            'name' =>'Supervisor',
            'display_name' =>'Supervisor',
            'description' =>'Supervisor',
            'allowed_route' =>'admin',
        ]);

        $customerRole = Role::create([
            'name'          =>'customer',
            'display_name'  =>'Customer',
            'description'   =>'Customer',
            'allowed_route' =>null,
        ]);

        $Admin = User::create([
            'first_name'        => 'Adminstrator',
            'last_name'         => 'System',
            'username'          => 'administrator',
            'email'             => 'admin@ecommercear.com',
            'email_verified_at' => now(),
            'mobile'            => '967700000000',
            'password'          => bcrypt(123123123),
            'user_image'        => 'avatar.svg',
            'status'            => 1,
            'remember_token'    => Str::random(10),
        ]);
        $Admin->attachRole($adminRole);

        $Supervisor = User::create([
            'first_name'        => 'Supervisor',
            'last_name'         => 'System',
            'username'          => 'supervisor',
            'email'             => 'supervisor@ecommercear.com',
            'email_verified_at' => now(),
            'mobile'            => '967700000001',
            'password'          => bcrypt(123123123),
            'user_image'        => 'avatar.svg',
            'status'            => 1,
            'remember_token'    => Str::random(10),
        ]);
        $Supervisor->attachRole($supervisorRole);

        $Customer = User::create([
            'first_name'        => 'Mohammed',
            'last_name'         => 'Al-Yemeni',
            'username'          => 'mohd',
            'email'             => 'mohd@ecommercear.com',
            'email_verified_at' => now(),
            'mobile'            => '967700000002',
            'password'          => bcrypt(123123123),
            'user_image'        => 'avatar.svg',
            'status'            => 1,
            'remember_token'    => Str::random(10),
        ]);
        $Customer->attachRole($customerRole);

        for($i =1; $i<=20 ; $i++){
            $randomCustomer = User::create([
                'first_name'        => $faker->firstName(),
                'last_name'         => $faker->lastName(),
                'username'          => $faker->username(),
                'email'             => $faker->unique()->safeEmail(),
                'email_verified_at' => now(),
                'mobile'            => '96770000' . $faker->numberBetween(000000, 999999),
                'password'          => bcrypt(123123123),
                'user_image'        => 'avatar.svg',
                'status'            => 1,
                'remember_token'    => Str::random(10),
            ]);
            $randomCustomer->attachRole($customerRole);
        }

    }
}
