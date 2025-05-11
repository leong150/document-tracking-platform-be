<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'contact_no' => '012-3456789',
            'code' => fake()->unique()->uuid(),
            'type' => 'STAFF',
            'status' => 'ACTIVE',
            'address' => 'TRX Tower - Gallery Lobby | Menara 106 Exchange, Imbi, Kuala Lumpur, Federal Territory of Kuala Lumpur, Malaysia',
            'password' => bcrypt('123456'),
        ]);
    }
}
