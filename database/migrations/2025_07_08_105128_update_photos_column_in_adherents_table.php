<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modifier le champ 'photos' en LONGBLOB
        DB::statement('ALTER TABLE adherents MODIFY photos LONGBLOB');
    }

    public function down(): void
    {
        // Revenir à BLOB si besoin
        DB::statement('ALTER TABLE adherents MODIFY photos BLOB');
    }
};
