<?php

namespace App\Http\Controllers;

use App\Models\Adherent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdherentController extends Controller
{
    public function index()
    {
        $adherents = Adherent::with('pointeuses')->get();

        return response()->json($adherents->map(function ($a) {
            return [
                'id' => $a->id,
                'code' => $a->code,
                'nom' => $a->nom,
                'prenom' => $a->prenom,
                'societe_id' => $a->societe_id,
                'pointeuses' => $a->pointeuses,
                'photo_url' => $a->photo_path ? asset('storage/' . $a->photo_path) : null,
            ];
        }));
    }

    public function show($id)
    {
        $a = Adherent::with('pointeuses')->find($id);
        if (!$a) return response()->json(['message' => 'Adhérent non trouvé'], 404);

        return response()->json([
            'id' => $a->id,
            'code' => $a->code,
            'nom' => $a->nom,
            'prenom' => $a->prenom,
            'societe_id' => $a->societe_id,
            'pointeuses' => $a->pointeuses,
            'photo_url' => $a->photo_path ? asset('storage/' . $a->photo_path) : null,
        ]);
    }

    public function getPhoto($id)
    {
        $adherent = Adherent::find($id);
    
        if (!$adherent || !$adherent->photo_path) {
            return response()->json(['message' => 'Photo non trouvée'], 404);
        }
    
        $photoPath = 'public/' . $adherent->photo_path;
    
        if (!Storage::exists($photoPath)) {
            return response()->json(['message' => 'Fichier introuvable'], 404);
        }
    
        $url = Storage::url($adherent->photo_path);
        return response()->json(['url' => asset($url)]);
    }
    


    public function store(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|unique:adherents,code',
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'societe_id' => 'required|exists:societes,id',
                'photos' => 'nullable|image|max:2048',
            ]);

            $data = $request->only(['code', 'nom', 'prenom', 'societe_id']);
            $adherent = Adherent::create($data);

            if ($request->hasFile('photos')) {
                $file = $request->file('photos');
                $ext = $file->getClientOriginalExtension();
                $filename = "adherent_{$adherent->id}.$ext";
            
                // ✅ stockage dans storage/app/public/photos
                Storage::disk('public')->putFileAs('photos', $file, $filename);
            
                $adherent->photos = file_get_contents($file->getRealPath());
                $adherent->photo_path = "photos/$filename";
                $adherent->save();
            }
            
            return response()->json([
                'message' => 'Adhérent créé',
                'adherent' => [
                    'id' => $adherent->id,
                    'code' => $adherent->code,
                    'nom' => $adherent->nom,
                    'prenom' => $adherent->prenom,
                    'societe_id' => $adherent->societe_id,
                    'photo_url' => $adherent->photo_path ? asset('storage/' . $adherent->photo_path) : null,
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message' => 'Erreur serveur'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $adherent = Adherent::find($id);
        if (!$adherent) return response()->json(['message' => 'Non trouvé'], 404);

        $request->validate([
            'code' => 'sometimes|required|string|unique:adherents,code,' . $id,
            'nom' => 'sometimes|required|string|max:255',
            'prenom' => 'sometimes|required|string|max:255',
            'societe_id' => 'sometimes|required|exists:societes,id',
            'photos' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['code', 'nom', 'prenom', 'societe_id']);

        if ($request->hasFile('photos')) {
            $file = $request->file('photos');
            $ext = $file->getClientOriginalExtension();
            $filename = "adherent_{$adherent->id}.$ext";
        
            // ✅ stockage dans storage/app/public/photos
            Storage::disk('public')->putFileAs('photos', $file, $filename);
        
            $data['photos'] = file_get_contents($file->getRealPath());
            $data['photo_path'] = "photos/$filename";
        }
        
        $adherent->update($data);

        return response()->json([
            'message' => 'Adhérent modifié',
            'photo_url' => $adherent->photo_path ? asset('storage/' . $adherent->photo_path) : null
        ]);
    }

    public function destroy($id)
    {
        $a = Adherent::find($id);
        if (!$a) return response()->json(['message' => 'Non trouvé'], 404);

        if ($a->photo_path && Storage::disk('public')->exists($a->photo_path)) {
            Storage::disk('public')->delete($a->photo_path);
        }

        $a->delete();
        return response()->json(['message' => 'Supprimé']);
    }
    public function cropFace($id)
{
    $adherent = Adherent::find($id);
    if (!$adherent || !$adherent->photo_path) {
        return response()->json(['message' => 'Image non trouvée'], 404);
    }

    $srcPath = public_path('storage/' . $adherent->photo_path);
    $croppedName = 'cropped_' . basename($srcPath);
    $destPath = storage_path("app/public/photos/" . $croppedName);

    try {
        $response = Http::timeout(5)->get('http://localhost:25002/ZKCropFace/CropFace', [
            'SrcFileName' => $srcPath,
            'DesFileName' => $destPath
        ]);
    } catch (\Exception $e) {
        Log::error('Erreur lors de la requête à ZKCropFace : ' . $e->getMessage());
        return response()->json(['message' => 'Erreur de connexion au service de crop'], 500);
    }

    $body = trim($response->body());

    // 1️⃣ Essai normal
    $result = json_decode($body, true);

    // 2️⃣ Si échec, on pense que c'est une string JSON encodée
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($result)) {
        $body = stripslashes($body); // Enlève les \"
        $decodedOnce = json_decode($body, true); // 1er décodage

        if (is_string($decodedOnce)) {
            $result = json_decode($decodedOnce, true); // 2e décodage
        } else {
            $result = $decodedOnce;
        }
    }

    // 3️⃣ Vérification finale
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($result) || !isset($result['ret'])) {
        Log::error('Décodage JSON échoué : ' . json_last_error_msg());
        return response()->json([
            'message' => 'Réponse non décodable du service de crop',
            'raw_response' => $body
        ], 500);
    }

    if ($result['ret'] === "0" && strpos($result['error'], 'Succ') !== false) {
        return response()->json([
            'message' => 'Image croppée avec succès',
            'cropped_path' => asset('storage/photos/' . $croppedName),
        ]);
    }

    return response()->json([
        'message' => 'Erreur lors du crop',
        'retour_service' => $result,
    ], 500);
}


}
