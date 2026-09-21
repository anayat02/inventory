<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AssignRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:assign {tutor} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a role to a user by TutorID or Login';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tutorIdentifier = $this->argument('tutor');
        $roleName = $this->argument('role');

        $user = \App\Models\User::where('TutorID', $tutorIdentifier)
                                ->orWhere('Login', $tutorIdentifier)
                                ->first();

        if (!$user) {
            // Если пользователя нет локально, попробуем подтянуть из Platonus
            $platonusUser = \Illuminate\Support\Facades\DB::connection('mysql_platonus')
                ->table('tutors')
                ->where('TutorID', $tutorIdentifier)
                ->orWhere('Login', $tutorIdentifier)
                ->first();

            if ($platonusUser) {
                $user = \App\Models\User::updateOrCreate(
                    ['TutorID' => $platonusUser->TutorID],
                    [
                        'Login' => $platonusUser->Login,
                        'password' => $platonusUser->Password,
                        'name' => $platonusUser->lastname ?? $platonusUser->firstname ?? $platonusUser->Login,
                    ]
                );
                $this->info("Пользователь синхронизирован из базы Platonus.");
            } else {
                $this->error("Пользователь с TutorID или Login '{$tutorIdentifier}' не найден.");
                return;
            }
        }

        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName]);
        
        $user->assignRole($role);
        
        $this->info("Роль '{$roleName}' успешно назначена пользователю {$user->Login} (TutorID: {$user->TutorID}).");
    }
}
