<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /** Verify the user's email. */
    public function verify(Request $request, $id, $hash)
    {
        $user = $request->user();

        // Vérification de l'identité de l'utilisateur
        if (! $user || (int) $id !== $user->getKey()) {
            return Redirect::route('login')->with('error', 'Utilisateur non identifié.');
        }

        // Vérification du hash
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return Redirect::route('login')->with('error', 'Lien de vérification invalide.');
        }

        // Rediriger si déjà vérifié
        if ($user->hasVerifiedEmail()) {
            return redirect('/dashboard');
        }

        // Si profil incomplet, on NE valide PAS l’email
        if (
            empty($user->nom) ||
            empty($user->prenoms) ||
            empty($user->phone_call) ||
            empty($user->phone_whatsapp)
        ) {
            return redirect()->route('profile.complete')->with('info', 'Veuillez compléter votre profil avant de vérifier votre adresse e-mail.');
        }

        // ✅ Si tout est OK, on valide l’e-mail
        $user->markEmailAsVerified();
        event(new Verified($user));

        return redirect('/dashboard')->with('success', 'Adresse e-mail vérifiée avec succès.');
    }

    /*
     * Soumet les données du formulaire et met à jour le profil utilisateur.
     */
    public function completeprofile(Request $request)
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

        // Vérifier si l'e-mail a changé
        $emailChanged = $user->email !== $validatedData['email'];

        // 💾 Mise à jour du profil utilisateur
        $user->name = $displayName;
        $user->nom = $validatedData['nom'];
        $user->prenoms = $validatedData['prenoms'];
        $user->phone_call = $validatedData['phone_call'];
        $user->phone_whatsapp = $validatedData['phone_whatsapp'];

        if ($emailChanged) {
            $user->email = $validatedData['email'];
            $user->email_verified_at = null; // Invalider l'ancien e-mail
        }

        $user->save();

        // 📧 Envoyer le mail de vérification
        $user->sendEmailVerificationNotification();

        return redirect()
            ->route('verification.notice')
            ->with('success', 'Profil complété avec succès. Un e-mail de vérification a été envoyé à ' . $user->email . '.');
    }




    /** Resend the email verification notification. */
    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect('/dashboard');
        }

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $emailChanged = false;

        // Si l'email a changé, on le met à jour
        if ($user->email !== $request->email) {
            $user->email = $request->email;
            $user->email_verified_at = null; // Invalider l'ancien email
            $user->save();
            $emailChanged = true;
        }

        // Envoyer le mail de vérification
        $user->sendEmailVerificationNotification();

        // Message adapté selon le cas
        if ($emailChanged) {
            return back()->with('success', 'Votre adresse email a été mise à jour. Un nouvel email de vérification a été envoyé à ' . $user->email . '.');
        }

        return back()->with('success', 'Un email de vérification a été renvoyé à votre adresse actuelle : ' . $user->email . '.');
    }
}
