<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Affiche le formulaire de complétion du profil.
     */
    public function showForm()
    {
        return view('auth.complete-profile');
    }

    /**
     * Soumet les données du formulaire et met à jour le profil utilisateur.
     */
    public function submitForm(Request $request)
    {

        $user = Auth::user();

        // 🔐 Validation des champs requis
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenoms' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone_call' => 'required|string|max:20|unique:users,phone_call,' . $user->id,
            'phone_whatsapp' => 'required|string|max:20|unique:users,phone_whatsapp,' . $user->id,
        ]);

        // 👤 Génération du champ "name" à partir du premier prénom
        $firstPrenom = null;
        if (!empty($validatedData['prenoms'])) {
            $parts = preg_split('/\s+/', trim($validatedData['prenoms']));
            $firstPrenom = $parts[0] ?? $validatedData['prenoms'];
        }

        $displayName = $firstPrenom ?? trim($validatedData['prenoms'] . ' ' . $validatedData['nom']);

        // 💾 Mise à jour du profil utilisateur
        $user->update([
            'name' => $displayName,
            'nom' => $validatedData['nom'],
            'prenoms' => $validatedData['prenoms'],
            'email' => $validatedData['email'],
            'phone_call' => $validatedData['phone_call'],
            'phone_whatsapp' => $validatedData['phone_whatsapp'],
        ]);

        // ✅ Redirection après succès
        return redirect()->route('verification.notice')->with('success', 'Profil complété avec succès. Vous pouvez maintenant valider votre adresse e-mail.');
    }
}
