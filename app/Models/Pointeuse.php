<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pointeuse extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip',
        'port',
        'designation',
    ];

    public function adherents()
    {
        return $this->belongsToMany(Adherent::class, 'adherent_pointeuse');
    }

}
