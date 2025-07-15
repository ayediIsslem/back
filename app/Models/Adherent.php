<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Adherent extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'nom',
        'prenom',
        'photos',
        'societe_id'
    ];

    // Cache le champ 'photos' pour éviter de le retourner dans le JSON
    protected $hidden = ['photos'];

    public function pointeuses()
    {
        return $this->belongsToMany(Pointeuse::class, 'adherent_pointeuse');
    }

    public function societe()
    {
        return $this->belongsTo(Societe::class);
    }
}
