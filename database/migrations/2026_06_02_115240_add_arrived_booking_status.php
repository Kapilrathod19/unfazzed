<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds 'arrived' status to booking_statuses table if not already present.
     */
    public function up(): void
    {
        // Only insert if 'arrived' status doesn't already exist
        $exists = DB::table('booking_statuses')->where('value', 'arrived')->exists();

        if (!$exists) {
            DB::table('booking_statuses')->insert([
                'label' => 'Arrived',
                'value' => 'arrived',
                'sequence' => 3,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('booking_statuses')->where('value', 'arrived')->delete();
    }
};
