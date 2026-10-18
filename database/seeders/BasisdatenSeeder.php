<?php

namespace Database\Seeders;

use App\Enums\ArchiveState;
use App\Enums\BookingState;
use App\Enums\ContractState;
use App\Enums\UserRole;
use App\Models\ArchiveStatus;
use App\Models\ContractStatus;
use App\Models\EventStatus;
use App\Models\Homepage;
use App\Models\Role;
use App\Models\Salutation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BasisdatenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Role::create([
            'id' => UserRole::Administrator->value,
            'name' => 'Administrator',
            'is_admin' => true,
            'is_team' => false, ]);
        Role::create([
            'id' => UserRole::Team->value,
            'name' => 'Team',
            'is_admin' => false,
            'is_team' => true, ]);
        Role::create([
            'id' => UserRole::Gast->value,
            'name' => 'Gast',
            'is_admin' => false,
            'is_team' => false, ]);
        Role::create([
            'id' => UserRole::Verwalter->value,
            'name' => 'Verwalter',
            'is_admin' => false,
            'is_team' => true, ]);

        EventStatus::create([
            'id' => BookingState::Neu->value,
            'name' => 'Neu',
            'color' => 'P', ]);
        EventStatus::create([
            'id' => BookingState::Bestaetigt->value,
            'name' => 'Bestätigt',
            'color' => 'B', ]);
        EventStatus::create([
            'id' => BookingState::Storniert->value,
            'name' => 'Storniert', ]);
        EventStatus::create([
            'id' => BookingState::Eigene->value,
            'name' => 'Eigene Termine',
            'color' => 'B', ]);

        ContractStatus::create([
            'id' => ContractState::Offen->value,
            'name' => 'Offen', ]);
        ContractStatus::create([
            'id' => ContractState::AngebotErstellt->value,
            'name' => 'Angebot erstellt', ]);
        ContractStatus::create([
            'id' => ContractState::AngebotVersendet->value,
            'name' => 'Angebot versendet', ]);
        ContractStatus::create([
            'id' => ContractState::RechnungErstellt->value,
            'name' => 'Rechnung erstellt', ]);
        ContractStatus::create([
            'id' => ContractState::RechnungVersendet->value,
            'name' => 'Rechnung versendet', ]);
        ContractStatus::create([
            'id' => ContractState::Storniert->value,
            'name' => 'Storniert', ]);

        ArchiveStatus::create([
            'id' => ArchiveState::Aktiv->value,
            'name' => 'Aktiv', ]);
        ArchiveStatus::create([
            'id' => ArchiveState::Archiviert->value,
            'name' => 'Archiviert', ]);

        Homepage::create([
            'title' => env('APP_NAME'), ]);

        $user = User::create([
            'id' => 1,
            'username' => 'Administrator',
            'password' => Hash::make(env('ADMIN_PASSWORD')),
            'role_id' => UserRole::Administrator,
            'is_active' => true,
        ]);

        Salutation::create([
            'name' => 'Herr', ]);
        Salutation::create([
            'name' => 'Frau', ]);
    }
}
