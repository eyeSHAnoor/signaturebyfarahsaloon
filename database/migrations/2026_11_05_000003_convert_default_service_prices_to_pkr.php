<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $prices = [
        'signature-cut-finish' => [55, 5500],
        'blowout-style' => [38, 3800],
        'balayage-gloss' => [195, 19500],
        'root-refresh' => [85, 8500],
        'restorative-hair-ritual' => [72, 7200],
        'signature-glow-facial' => [110, 11000],
        'soft-glam-makeup' => [95, 9500],
        'manicure-pedicure' => [68, 6800],
        'bridal-beauty-preview' => [165, 16500],
        'brow-shape-tint' => [35, 3500],
    ];

    public function up(): void
    {
        foreach ($this->prices as $slug => [$oldPrice, $newPrice]) {
            $service = DB::table('services')
                ->where('slug', $slug)
                ->where('price', $oldPrice)
                ->first(['id']);

            if (! $service) {
                continue;
            }

            DB::table('services')->where('id', $service->id)->update(['price' => $newPrice]);
            DB::table('appointments')
                ->where('service_id', $service->id)
                ->where('price', $oldPrice)
                ->update(['price' => $newPrice]);
        }
    }

    public function down(): void
    {
        foreach ($this->prices as $slug => [$oldPrice, $newPrice]) {
            $service = DB::table('services')
                ->where('slug', $slug)
                ->where('price', $newPrice)
                ->first(['id']);

            if (! $service) {
                continue;
            }

            DB::table('services')->where('id', $service->id)->update(['price' => $oldPrice]);
            DB::table('appointments')
                ->where('service_id', $service->id)
                ->where('price', $newPrice)
                ->update(['price' => $oldPrice]);
        }
    }
};
