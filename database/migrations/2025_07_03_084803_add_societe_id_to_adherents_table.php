
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('adherents', function (Blueprint $table) {
            $table->unsignedBigInteger('societe_id')->after('prenom');

            // Si la table "societes" existe et tu veux la relation
            $table->foreign('societe_id')->references('id')->on('societes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('adherents', function (Blueprint $table) {
            $table->dropForeign(['societe_id']);
            $table->dropColumn('societe_id');
        });
    }
};
