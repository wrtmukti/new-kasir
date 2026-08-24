<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistoryBundleSeeder extends Seeder
{
    public function run(): void
    {
        $bundles = DB::table('bundles')->where('delete_status', 0)->get();

        $now = now();
        $count = 0;

        foreach ($bundles as $bundle) {
            $existing = DB::table('bundle_histories')
                ->where('bundle_id', $bundle->bundle_id)
                ->where('action_type', 'create')
                ->exists();

            if ($existing) continue;

            DB::table('bundle_histories')->insert([
                'bundle_id' => $bundle->bundle_id,
                'outlet_id' => $bundle->outlet_id,
                'bundle_code' => $bundle->bundle_code,
                'bundle_name' => $bundle->bundle_name,
                'bundle_slug' => $bundle->bundle_slug,
                'bundle_description' => $bundle->bundle_description,
                'bundle_price' => $bundle->bundle_price,
                'bundle_status' => $bundle->bundle_status,
                'bundle_image' => $bundle->bundle_image,
                'effective_date' => $bundle->created_at ?? $now,
                'action_type' => 'create',
                'changed_by' => $bundle->created_by ?? 'seeder',
                'created_by' => $bundle->created_by,
                'delete_status' => 0,
                'created_at' => $bundle->created_at ?? $now,
                'updated_at' => $now,
            ]);
            $count++;
        }

        $this->command->info("HistoryBundleSeeder: {$count} records created.");
    }
}
