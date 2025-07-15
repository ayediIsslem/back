<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Ce fichier est destiné aux routes qui retournent des vues HTML.
| Par exemple : pages frontend, tableau de bord, etc.
| Toutes les routes ici utilisent le middleware "web".
|
*/

Route::get('/', function () {
    return view('welcome');
});
