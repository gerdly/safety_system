<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Create the primary company record
        Company::create([
            'name' => 'Aviation Company',
        ]);
    }
}