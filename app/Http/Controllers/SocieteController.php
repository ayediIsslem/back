<?php



namespace App\Http\Controllers;

use App\Models\Societe;
use Illuminate\Http\Request;

class SocieteController extends Controller
{
    // Afficher la société (on suppose qu'il y a qu'une seule)
    public function index()
    {
        $societe = Societe::first();
        return response()->json($societe);
    }

    // Créer une nouvelle société
    public function store(Request $request)
    {
        $validated = $request->validate([
            'raison_sociale' => 'required|string|max:255',
            'code_tva' => 'required|string|max:50|unique:societes,code_tva',
            'adresses' => 'required|string',
            'telephone' => 'required|string|max:20',
        ]);

        $societe = Societe::create($validated);

        return response()->json(['message' => 'Société créée avec succès.', 'societe' => $societe], 201);
    }

    // Modifier la société
    public function update(Request $request, $id)
    {
        $societe = Societe::findOrFail($id);

        $validated = $request->validate([
            'raison_sociale' => 'required|string|max:255',
            'code_tva' => 'required|string|max:50|unique:societes,code_tva,' . $id,
            'adresses' => 'required|string',
            'telephone' => 'required|string|max:20',
        ]);

        $societe->update($validated);

        return response()->json(['message' => 'Société mise à jour avec succès.', 'societe' => $societe]);
    }

    // Supprimer (optionnel)
    public function destroy($id)
    {
        $societe = Societe::findOrFail($id);
        $societe->delete();

        return response()->json(['message' => 'Société supprimée avec succès.']);
    }
}
