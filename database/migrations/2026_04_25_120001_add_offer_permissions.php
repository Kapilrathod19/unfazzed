<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

return new class extends Migration {
    public function up(): void
    {
        $timestamp = Carbon::now();

        $parentId = DB::table('permissions')->insertGetId([
            'name' => 'offer',
            'guard_name' => 'web',
            'parent_id' => null,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        $permissions = [
            [
                'name' => 'offer list',
                'guard_name' => 'web',
                'parent_id' => $parentId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'offer add',
                'guard_name' => 'web',
                'parent_id' => $parentId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'offer edit',
                'guard_name' => 'web',
                'parent_id' => $parentId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'offer delete',
                'guard_name' => 'web',
                'parent_id' => $parentId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert($permission);
        }
    }

    public function down(): void
    {
        $names = [
            'offer',
            'offer list',
            'offer add',
            'offer edit',
            'offer delete',
        ];

        DB::table('permissions')->whereIn('name', $names)->delete();
    }
};
