<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        $this->call([
            RolesPermissionsSeeder::class,
            StatusSeeder::class,
            DepartmentSeeder::class,
            AgreementSeeder::class,
            CampaignSeeder::class,
            MediaSeeder::class,
            CampaignAgreementSeeder::class,
            TimeLineItemSeeder::class,
            TokenSeeder::class,
            StorePlaceHolderSeeder::class,
        ]);
        $password = Str::password(8, true, true, true, false);
        $mail = config('mail.admin_email');
        
        User::firstOrCreate(
            ['email' => $mail],
            [
                'name' => 'supervisor',
                'password' => $password,
                'email_verified_at' => now(),
            ]
        )->assignRole('admin');

        Mail::raw(
            "Usuario: programadorweb@locatelve.com\nContraseña: $password",
            function (Message $message) use ($mail) {
                $message->to($mail)->subject('Credenciales de acceso al CMS de Locatel');
            }
        );
    }
}
