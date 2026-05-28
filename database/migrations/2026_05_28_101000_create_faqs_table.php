<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->nullable()->default('1')->comment('1- Active , 0- InActive');
            $table->timestamps();
        });

        $timestamp = Carbon::now();

        $parentId = DB::table('permissions')->insertGetId([
            'name' => 'faq',
            'guard_name' => 'web',
            'parent_id' => null,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        $permissions = [
            [
                'name' => 'faq list',
                'guard_name' => 'web',
                'parent_id' => $parentId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'faq add',
                'guard_name' => 'web',
                'parent_id' => $parentId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'faq edit',
                'guard_name' => 'web',
                'parent_id' => $parentId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'faq delete',
                'guard_name' => 'web',
                'parent_id' => $parentId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert($permission);
        }

        // Get admin and demo_admin roles and map permissions
        $roles = Role::whereIn('name', ['admin', 'demo_admin'])->get();
        $faqPermissions = Permission::where(function($query) use ($parentId) {
            $query->where('id', $parentId)->orWhere('parent_id', $parentId);
        })->get();

        foreach ($roles as $role) {
            foreach ($faqPermissions as $permission) {
                $exists = DB::table('role_has_permissions')
                    ->where('permission_id', $permission->id)
                    ->where('role_id', $role->id)
                    ->exists();

                if (!$exists) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permission->id,
                        'role_id' => $role->id,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faqs');

        $names = [
            'faq',
            'faq list',
            'faq add',
            'faq edit',
            'faq delete',
        ];

        DB::table('permissions')->whereIn('name', $names)->delete();
    }
};
