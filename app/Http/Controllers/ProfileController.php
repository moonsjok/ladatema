<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

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

        if (!$user) {
            Alert::error('Erreur', 'Vous devez être connecté.');
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }

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

        $emailChanged = ($user->email !== $validatedData['email']);

        // 💾 Mise à jour du profil utilisateur
        $user->fill([
            'name' => $displayName,
            'nom' => $validatedData['nom'],
            'prenoms' => $validatedData['prenoms'],
            'email' => $validatedData['email'],
            'phone_call' => $validatedData['phone_call'],
            'phone_whatsapp' => $validatedData['phone_whatsapp'],
        ]);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        // 📧 Si l'e-mail a été modifié, envoyer une nouvelle notification de vérification
        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
            Alert::success('E-mail modifié', 'Votre adresse e-mail a été modifiée. Un nouvel e-mail de vérification vous a été envoyé.');
            return redirect()->route('verification.notice')->with('success', 'Votre adresse e-mail a été modifiée. Un nouvel e-mail de vérification vous a été envoyé.');
        }

        // 🟢 Si l'e-mail n'est pas encore vérifié
        if (!$user->hasVerifiedEmail()) {
            Alert::info('Profil complété', 'Profil complété avec succès. Vous pouvez maintenant valider votre adresse e-mail.');
            return redirect()->route('verification.notice')->with('success', 'Profil complété avec succès. Vous pouvez maintenant valider votre adresse e-mail.');
        }

        // ✅ Si le profil et l'e-mail sont déjà vérifiés, rediriger vers le tableau de bord
        Alert::success('Profil mis à jour', 'Votre profil a été mis à jour avec succès.');
        return redirect()->route('dashboard')->with('success', 'Votre profil a été mis à jour avec succès.');
    }
}
