<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Companies
        $acme = Company::create([
            'name' => 'Acme Corp',
            'cnpj' => '12345678000100',
            'email' => 'contact@acme.com',
            'phone' => '11999990000',
            'address' => 'Rua das Flores, 100 - São Paulo, SP',
        ]);

        $globex = Company::create([
            'name' => 'Globex Solutions',
            'cnpj' => '98765432000100',
            'email' => 'info@globex.com',
            'phone' => '21988880000',
            'address' => 'Av. Brasil, 500 - Rio de Janeiro, RJ',
        ]);

        // Projects
        $serviceHub = Project::create([
            'company_id' => $acme->id,
            'name' => 'ServiceHub Internal',
            'description' => 'Internal service management platform',
            'email' => 'servicehub@acme.com',
            'status' => 'active',
            'start_date' => '2025-01-15',
        ]);

        $clientPortal = Project::create([
            'company_id' => $acme->id,
            'name' => 'Client Portal',
            'description' => 'Customer-facing portal for ticket submissions',
            'email' => 'portal@acme.com',
            'status' => 'active',
            'start_date' => '2025-03-01',
        ]);

        $infraMonitoring = Project::create([
            'company_id' => $globex->id,
            'name' => 'Infrastructure Monitoring',
            'description' => 'Server and network monitoring tools',
            'email' => 'infra@globex.com',
            'status' => 'active',
            'start_date' => '2025-06-01',
        ]);

        // Users
        $technician = User::create([
            'name' => 'Carlos Technician',
            'email' => 'tech@example.com',
            'password' => Hash::make('password'),
        ]);

        $techProfile = UserProfile::create([
            'user_id' => $technician->id,
            'phone' => '11977770000',
            'position' => 'technician',
            'department' => 'IT Support',
            'bio' => 'Senior technician with 5 years of experience.',
        ]);

        // Assign technician to ServiceHub Internal and Infrastructure Monitoring
        $techProfile->projects()->attach([$serviceHub->id, $infraMonitoring->id]);

        $regularUser = User::create([
            'name' => 'Maria User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        UserProfile::create([
            'user_id' => $regularUser->id,
            'phone' => '11966660000',
            'position' => 'manager',
            'department' => 'Operations',
            'bio' => 'Operations manager at Acme Corp.',
        ]);
    }
}
