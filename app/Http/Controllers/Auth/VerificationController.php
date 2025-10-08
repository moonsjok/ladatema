<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Redirect;

class VerificationController extends Controller
{
    /** Verify the user's email. */
    public function verify(Request $request, $id, $hash)
    {
        $user = $request->user();

        if (! $user || (int) $id !== $user->getKey()) {
            return Redirect::route('login')->with('error', 'Utilisateur non identifié.');
        }

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return Redirect::route('login')->with('error', 'Lien de vérification invalide.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('/dashboard');
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return redirect('/dashboard')->with('success', 'Adresse e-mail vérifiée.');
    }

    /** Resend the email verification notification. */
    public function resend(Request $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return redirect('/dashboard');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Email de vérification renvoyé.');
    }
}
