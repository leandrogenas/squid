<?php

    use Illuminate\Database\Seeder;
    use Spatie\Permission\Models\Role;
    use Spatie\Permission\Models\Permission;

    class RolesAndPermissionsSeeder extends Seeder
    {
        public function run()
        {
            // Reset cached roles and permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            // create permissions
            foreach (\App\Enums\PermissoesTipo::getValues() as $permissao){
                Permission::create(['name' => $permissao]);
            }

            $role = Role::create(['name' => 'super-admin']);
            $role->givePermissionTo(Permission::all());
        }
    }
