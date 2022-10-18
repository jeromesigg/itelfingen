<?php

namespace Database\Seeders;

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
            'id' => config('status.role_Administrator'),
            'name' => 'Administrator',
            'is_admin' => true,
            'is_team' => false]);
        Role::create([
            'id' => config('status.role_Team'),
            'name' => 'Team',
            'is_admin' => false,
            'is_team' => true]);
        Role::create([
            'id' => config('status.role_Gast'),
            'name' => 'Gast',
            'is_admin' => false,
            'is_team' => false]);
        Role::create([
            'id' => config('status.role_Verwalter'),
            'name' => 'Verwalter',
            'is_admin' => false,
            'is_team' => true]);


        EventStatus::create([
            'id' => config('status.event_neu'),
            'name' => 'Neu',
            'color' => 'P']);
        EventStatus::create([
            'id' => config('status.event_bestaetigt'),
            'name' => 'Bestätigt',
            'color' => 'B']);
        EventStatus::create([
            'id' => config('status.event_storniert'),
            'name' => 'Storniert']);
        EventStatus::create([
            'id' => config('status.event_eigene'),
            'name' => 'Eigene Termine',
            'color' => 'B']);

        ContractStatus::create([
            'id' => config('status.contract_offen'),
            'name' => 'Offen']);
        ContractStatus::create([
            'id' => config('status.contract_angebot_erstellt'),
            'name' => 'Angebot erstellt']);
        ContractStatus::create([
            'id' => config('status.contract_angebot_versendet'),
            'name' => 'Angebot versendet']);
        ContractStatus::create([
            'id' => config('status.contract_rechnung_erstellt'),
            'name' => 'Rechnung erstellt']);
        ContractStatus::create([
            'id' => config('status.contract_rechnung_versendet'),
            'name' => 'Rechnung versendet']);
        ContractStatus::create([
            'id' => config('status.contract_storniert'),
            'name' => 'Storniert']);

        ArchiveStatus::create([
            'id' => config('status.aktiv'),
            'name' => 'Aktiv']);
        ArchiveStatus::create([
            'id' => config('status.archiviert'),
            'name' => 'Archiviert']);

        Homepage::create([
            'title' => env('APP_NAME')]);

        $user = User::create( [
            'id' => 1,
            'username' => 'Administrator',
            'password' => Hash::make(env('ADMIN_PASSWORD')),
            'role_id' => config('status.role_Administrator'),
            'is_active' => true,
        ]);

        Salutation::create([
            'name' => 'Herr']);
        Salutation::create([
            'name' => 'Frau']);
    }
}
