<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Profile;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Contrainte de clé étrangère
            $table->string('photo')->nullable(); // Chemin de la photo de profil
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('bio')->nullable(); // Informations supplémentaires
            $table->timestamps();
            $table->softDeletes();
        });

        // Création automatique de profils pour les utilisateurs existants
        User::all()->each(function ($user) {
            Profile::create([
                'user_id' => $user->id,
                'photo' => null,
                'phone' => null,
                'address' => null,
                'birth_date' => null,
                'bio' => null,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
