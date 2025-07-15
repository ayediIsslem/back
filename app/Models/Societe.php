<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Societe extends Model
{
    use HasFactory;

    protected $fillable = [
        'raison_sociale',
        'code_tva',
        'adresses',
        'telephone',
    ];
    public function adherents()
    {
        return $this->hasMany(Adherent::class);
    }
}
