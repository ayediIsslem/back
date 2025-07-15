<?php

namespace App\Http\Controllers;

use App\Models\Pointeuse;
use App\Models\Adherent;
use Illuminate\Http\Request;

class PointeuseController extends Controller
{
    // Lister toutes les pointeuses avec leurs adhérents
    public function index()
    {
        $pointeuses = Pointeuse::with('adherents')->get();
        return response()->json($pointeuses);
    }

    // Afficher une pointeuse par son ID avec ses adhérents
    public function show($id)
    {
        $pointeuse = Pointeuse::with('adherents')->find($id);
        if (!$pointeuse) {
            return response()->json(['message' => 'Pointeuse non trouvée'], 404);
        }
        return response()->json($pointeuse);
    }

    // Créer une nouvelle pointeuse
    public function store(Request $request)
    {
        $request->validate([
            'ip' => 'required|string|max:45', // IPv4/IPv6 max length
            'port' => 'required|string|max:10',
            'designation' => 'required|string|max:255',
        ]);

        $pointeuse = Pointeuse::create($request->all());

        return response()->json($pointeuse, 201);
    }

    // Mettre à jour une pointeuse existante
    public function update(Request $request, $id)
    {
        $pointeuse = Pointeuse::find($id);
        if (!$pointeuse) {
            return response()->json(['message' => 'Pointeuse non trouvée'], 404);
        }

        $request->validate([
            'ip' => 'sometimes|required|string|max:45',
            'port' => 'sometimes|required|string|max:10',
            'designation' => 'sometimes|required|string|max:255',
        ]);

        $pointeuse->update($request->all());

        return response()->json($pointeuse);
    }

    // Supprimer une pointeuse
    public function destroy($id)
    {
        $pointeuse = Pointeuse::find($id);
        if (!$pointeuse) {
            return response()->json(['message' => 'Pointeuse non trouvée'], 404);
        }

        $pointeuse->delete();

        return response()->json(['message' => 'Pointeuse supprimée avec succès']);
    }

    // Assigner un adhérent à une pointeuse
    public function assignerAdherent(Request $request, $pointeuseId)
    {
        $request->validate([
            'adherent_id' => 'required|exists:adherents,id',
        ]);

        $pointeuse = Pointeuse::find($pointeuseId);
        if (!$pointeuse) {
            return response()->json(['message' => 'Pointeuse non trouvée'], 404);
        }

        if (!$pointeuse->adherents()->where('adherent_id', $request->adherent_id)->exists()) {
            $pointeuse->adherents()->attach($request->adherent_id);
        }

        return response()->json(['message' => 'Adhérent assigné à la pointeuse avec succès']);
    }

    // Désassigner un adhérent d'une pointeuse
    public function desassignerAdherent(Request $request, $pointeuseId)
    {
        $request->validate([
            'adherent_id' => 'required|exists:adherents,id',
        ]);

        $pointeuse = Pointeuse::find($pointeuseId);
        if (!$pointeuse) {
            return response()->json(['message' => 'Pointeuse non trouvée'], 404);
        }

        $pointeuse->adherents()->detach($request->adherent_id);

        return response()->json(['message' => 'Adhérent désassigné de la pointeuse avec succès']);
    }
}
