<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Создаем роль admin
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Список старых жестко закодированных ID
        $allowedUserIds = [646, 359, 521, 463, 738, 503, 817, 398, 786, 796, 782, 347, 772];
        
        $platonusUsers = \Illuminate\Support\Facades\DB::connection('mysql_platonus')->table('tutors')->whereIn('TutorID', $allowedUserIds)->get();

        foreach ($platonusUsers as $pUser) {
            $localUser = User::updateOrCreate(
                ['TutorID' => $pUser->TutorID],
                [
                    'Login' => $pUser->Login,
                    'password' => $pUser->Password,
                    'name' => $pUser->lastname ?? $pUser->firstname ?? $pUser->Login,
                ]
            );

            if (!$localUser->hasRole('admin')) {
                $localUser->assignRole($adminRole);
            }
        }
    }
}
