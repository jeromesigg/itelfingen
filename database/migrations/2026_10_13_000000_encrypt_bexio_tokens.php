<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * Bestehende Bexio-Tokens verschlüsseln (BexioToken castet access_token und refresh_token jetzt als "encrypted").
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->encryptExisting();
    }

    public function down(): void
    {
        foreach (DB::table('bexio_tokens')->get() as $row) {
            DB::table('bexio_tokens')->where('id', $row->id)->update([
                'access_token' => $this->decryptOrKeep($row->access_token),
                'refresh_token' => $this->decryptOrKeep($row->refresh_token),
            ]);
        }
    }

    /**
     * Verschlüsselt nur Werte, die noch im Klartext sind; mehrfaches Ausführen ist unschädlich.
     */
    public function encryptExisting(): void
    {
        foreach (DB::table('bexio_tokens')->get() as $row) {
            DB::table('bexio_tokens')->where('id', $row->id)->update([
                'access_token' => $this->encryptOnce($row->access_token),
                'refresh_token' => $this->encryptOnce($row->refresh_token),
            ]);
        }
    }

    private function encryptOnce(string $value): string
    {
        try {
            Crypt::decryptString($value);

            return $value;
        } catch (DecryptException) {
            return Crypt::encryptString($value);
        }
    }

    private function decryptOrKeep(string $value): string
    {
        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            return $value;
        }
    }
};
