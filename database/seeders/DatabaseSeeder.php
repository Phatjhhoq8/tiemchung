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
        // Seed vaccines from the registration module
        $this->call(\Modules\VaccineRegistration\Database\Seeders\VaccineSeeder::class);
    }
}
