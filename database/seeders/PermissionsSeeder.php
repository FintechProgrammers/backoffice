<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions  = [
            'manage finance report',

            'create user',
            'edit user',
            'delete user',
            'set user as ambassador',

            'manage admin',
            'create admin',
            'delete admin',
            'banned admin',
            'edit admin',

            'manage product',
            'create product',
            'delete product',
            'edit product',

            'manage package',
            'create package',
            'delete package',
            'edit package',
            'publish package',

            'manage rank',
            'create rank',
            'edit rank',
            'delete rank',

            'manage commission plan',
            'create commission plan',
            'edit commission plan',
            'delete commission plan',

            'manage subscription',

            'manege sales',

            'manage support subject',
            'create support subject',
            'edit support subject',
            'delete support subject',

            'manage kyc',
            'approve kyc',

            'manage settings',
            'manage banner',
            'create banner',
            'delete banner',
            'manage provider',
            'manage roles'
        ];

        foreach ($permissions as $permission) {
            if (!Permission::whereName($permission)) {
                Permission::create(['name' => $permission, 'guard_name' => 'admin']);
            }
        }
    }
}
