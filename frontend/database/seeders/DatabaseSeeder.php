<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $driver = DB::getDriverName();

        if($driver == 'pgsql') {
            // $user_id = Str::ulid();
            $user_id = Str::uuid();
        } else {
            $user_id = Str::uuid();

        }
        DB::table('users')->insert([
            'id' => $user_id, // asumsi ULID
            'username' => 'testuser',
            'email' => 'testuser@gmail.com',
            'password' => Hash::make('12345678' . env('APP_SECRET_KEY_HASH')),
            'status' => 'active',
        ]);

        $app_id = DB::table('app')->insertGetId([
            'app_name' => 'Starter App K4',
            'desc' => 'Laravel ',
            'status' => true,
        ]);

        $biodata_ref_id = DB::table('biodata_ref')->insertGetId([
            'biodata_ref_name' => 'user_biodata',
            'status' => true,
        ]);

        if($driver == 'pgsql') {
            // $user_level_id = Str::ulid();
            $user_level_id = Str::uuid();
        } else {
            $user_level_id = Str::uuid();
        }
        DB::table('user_level')->insert([
            'id' => $user_level_id,
            'biodata_ref_id' => $biodata_ref_id,
            'app_id' => $app_id,
            'user_level_name' => 'Super Administrator',
            'status' => true,
        ]);

        if($driver == 'pgsql') {
            // $user_role_id = Str::ulid();
            $user_role_id = Str::uuid();
        } else {
            $user_role_id = Str::uuid();

        }
        DB::table('user_role')->insert([
            'id' => $user_role_id,
            'user_id' => $user_id,
            'user_level_id' => $user_level_id,
            'status' => true,
        ]);

        DB::table('user_biodata')->insert([
            'user_id' => $user_id,
            'user_code' => 'SA01',
            'name' => 'Test Super Admin',
            'sex' => 'L',
            'date_of_birth' => '2025-06-23',
            'status' => true,
        ]);

        $dashboard_id = DB::table('menu')->insertGetId([
            'menu_code' => 'DSB',
            'menu_name' => 'Dashboard',
            'menu_type' => 'main',
            'status' => true,
        ]);

        $datamaster_id = DB::table('menu')->insertGetId([
            'menu_code' => 'DM',
            'menu_name' => 'Data Master',
            'menu_type' => 'sub',
            'status' => true,
        ]);

        $app_id = DB::table('menu')->insertGetId([
            'menu_code' => 'APP',
            'menu_name' => 'Aplikasi',
            'menu_type' => 'main',
            'status' => true,
        ]);

        $biodataref_id = DB::table('menu')->insertGetId([
            'menu_code' => 'BIOREF',
            'menu_name' => 'Biodata Ref',
            'menu_type' => 'main',
            'status' => true,
        ]);

        $userlevel_id = DB::table('menu')->insertGetId([
            'menu_code' => 'UL',
            'menu_name' => 'User Level',
            'menu_type' => 'main',
            'status' => true,
        ]);

        $menu_id = DB::table('menu')->insertGetId([
            'menu_code' => 'MENU',
            'menu_name' => 'Menu',
            'menu_type' => 'sub',
            'status' => true,
        ]);

        $mainmenu_id = DB::table('menu')->insertGetId([
            'menu_code' => 'MMENU',
            'menu_name' => 'Main Menu',
            'menu_type' => 'main',
            'status' => true,
        ]);

        $menu_role_id = DB::table('menu')->insertGetId([
            'menu_code' => 'MENUROLE',
            'menu_name' => 'Role Akses Menu',
            'menu_type' => 'main',
            'status' => true,
        ]);

        $muser_id = DB::table('menu')->insertGetId([
            'menu_code' => 'USR',
            'menu_name' => 'Users',
            'menu_type' => 'sub',
            'status' => true,
        ]);

        $akunUser_id = DB::table('menu')->insertGetId([
            'menu_code' => 'AU',
            'menu_name' => 'Akun User',
            'menu_type' => 'sub',
            'status' => true,
        ]);

        $userbiodata_id = DB::table('menu')->insertGetId([
            'menu_code' => 'USRB',
            'menu_name' => 'User Biodata',
            'menu_type' => 'main',
            'status' => true,
        ]);

        DB::table('menu_role')->insert([
            'menu_id' => $dashboard_id,
            'user_level_id' => $user_level_id,
            'menu_url' => 'dashboard',
            'role_order' => 1,
            'status' => true,
        ]);

        DB::table('menu_role')->insert([
            'menu_id' => $datamaster_id,
            'user_level_id' => $user_level_id,
            'menu_url' => 'data_master',
            'role_order' => 2,
            'status' => true,
        ]);

        DB::table('menu_role')->insert([
            'menu_id' => $app_id,
            'user_level_id' => $user_level_id,
            'parent_menu_code' => 'DM',
            'menu_url' => 'data_master/aplikasi',
            'role_view' => true,
            'role_create' => true,
            'role_update' => true,
            'role_delete' => true,
            'role_order' => 1,
            'status' => true,
        ]);

        DB::table('menu_role')->insert([
            'menu_id' => $biodataref_id,
            'user_level_id' => $user_level_id,
            'parent_menu_code' => 'DM',
            'menu_url' => 'data_master/biodata_ref',
            'role_view' => true,
            'role_create' => true,
            'role_update' => true,
            'role_delete' => true,
            'role_order' => 2,
            'status' => true,
        ]);

        DB::table('menu_role')->insert([
            'menu_id' => $userlevel_id,
            'user_level_id' => $user_level_id,
            'parent_menu_code' => 'DM',
            'menu_url' => 'data_master/user_level',
            'role_view' => true,
            'role_create' => true,
            'role_update' => true,
            'role_delete' => true,
            'role_order' => 3,
            'status' => true,
        ]);

        DB::table('menu_role')->insert([
            'menu_id' => $menu_id,
            'user_level_id' => $user_level_id,
            'parent_menu_code' => 'DM',
            'menu_url' => 'data_master/menu',
            'role_order' => 4,
            'status' => true,
        ]);

        DB::table('menu_role')->insert([
            'menu_id' => $mainmenu_id,
            'user_level_id' => $user_level_id,
            'parent_menu_code' => 'MENU',
            'menu_url' => 'data_master/menu/main_menu',
            'role_view' => true,
            'role_create' => true,
            'role_update' => true,
            'role_delete' => true,
            'role_order' => 1,
            'status' => true,
        ]);

        DB::table('menu_role')->insert([
            'menu_id' => $menu_role_id,
            'user_level_id' => $user_level_id,
            'parent_menu_code' => 'MENU',
            'menu_url' => 'data_master/menu/role_akses_menu',
            'role_view' => true,
            'role_create' => true,
            'role_update' => true,
            'role_delete' => true,
            'role_order' => 2,
            'status' => true,
        ]);

        DB::table('menu_role')->insert([
            'menu_id' => $muser_id,
            'user_level_id' => $user_level_id,
            'parent_menu_code' => 'DM',
            'menu_url' => 'data_master/users',
            'role_order' => 5,
            'status' => true,
        ]);

        DB::table('menu_role')->insert([
            'menu_id' => $akunUser_id,
            'user_level_id' => $user_level_id,
            'parent_menu_code' => 'USR',
            'menu_url' => 'data_master/users/akun_user',
            'role_view' => true,
            'role_create' => true,
            'role_update' => true,
            'role_delete' => true,
            'role_order' => 1,
            'status' => true,
        ]);
        DB::table('menu_role')->insert([
            'menu_id' => $userbiodata_id,
            'user_level_id' => $user_level_id,
            'parent_menu_code' => 'USR',
            'menu_url' => 'data_master/users/user_biodata',
            'role_view' => true,
            'role_create' => true,
            'role_update' => true,
            'role_delete' => true,
            'role_order' => 2,
            'status' => true,
        ]);
    }
}
