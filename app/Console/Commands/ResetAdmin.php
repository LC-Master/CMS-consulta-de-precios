<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ResetAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:reset-admin
                        {--M|email= : Buscar específicamente por email} 
                        {--R|role= : Filtrar o asignar un rol} 
                        {--N|new_name= : Cambiar el nombre del usuario}
                        {--W|new_mail= : Cambiar el email del usuario}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the admin user credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email') ?? config('mail.admin_email');
        $role = $this->option('role') ?? 'admin';
        $newName = $this->option('new_name') ?? 'admin';
        $newEmail = $this->option('new_mail') ?? $email;

        $user = User::role($role)->orWhere('email', $email)->first();
        
        if (!$user) {
            $this->error("No se encontró ningún usuario con el email '{$email}' o rol '{$role}'.");
            return 1;
        }
        $password = Str::password(8, true, true, true, false);
        $user->update([
            'name' => $newName,
            'email' => $newEmail,
            'password' => $password,
        ]);
        $user->syncRoles($role);

        \DB::table('sessions')->where('user_id', $user->id)->delete();

        try {
            Mail::raw(
                "Usuario: {$email}\nContraseña: {$password}",
                function (Message $message) use ($email) {
                    $message->to($email)->subject('Credenciales de acceso al CMS de Locatel');
                }
            );
        } catch (\Exception $e) {
            $this->error("Error al enviar el correo: " . $e->getMessage());
        }

        $this->info("Usuario '{$user->name}' actualizado exitosamente.");
        $this->info("-----------------------------------------");
        $this->info(" SEGURIDAD RESTAURADA ");
        $this->info(" - Clave actualizada para: {$email}");
        $this->info(" - Todas las sesiones activas han sido cerradas.");
        $this->info(" - Se ha enviado un correo al {$email} con la nueva contraseña.");
        $this->info(" - Su rol actual es: {$role}");
        $this->info(" - Se recomienda cambiar la contraseña después de iniciar sesión.");
        $this->info("La contraseña temporal es: {$password}");
        $this->info("-----------------------------------------");

    }
}
