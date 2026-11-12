<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $contactDetails = [
        'phone' => '03336601912',
        'email' => 'Signaturebyfarah@gmail.com',
        'address' => 'Signature by Farah, 887/B, Peoples Colony No. 1, near Al-Fateh Sports Complex, Chen One Road, Faisalabad, Pakistan',
    ];

    private array $previousContactDetails = [
        'phone' => '+92 300 1234567',
        'email' => 'hello@farahsalon.pk',
        'address' => 'Signature by farah 887 /B, peoples  colony #1, near alfateh sport camplex chenone road, Faisalabad, Pakistan',
    ];

    public function up(): void
    {
        $this->saveSettings($this->contactDetails);
    }

    public function down(): void
    {
        $this->saveSettings($this->previousContactDetails);
    }

    private function saveSettings(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $setting = DB::table('salon_settings')->where('key', $key);
            $timestamp = now();

            if ($setting->exists()) {
                $setting->update(['value' => $value, 'updated_at' => $timestamp]);

                continue;
            }

            DB::table('salon_settings')->insert([
                'key' => $key,
                'value' => $value,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }
};
