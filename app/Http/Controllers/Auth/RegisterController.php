<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'prenoms' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_call' => 'nullable|string|max:30',
            'phone_whatsapp' => 'nullable|string|max:30',
        ]);

        // Use the first token of prenoms as the short 'name' field (if available)
        $firstPrenom = null;
        if (!empty($validatedData['prenoms'])) {
            $parts = preg_split('/\s+/', trim($validatedData['prenoms']));
            $firstPrenom = $parts[0] ?? trim($validatedData['prenoms']);
        }
        $displayName = $firstPrenom ? $firstPrenom : trim(($validatedData['prenoms'] ?? '') . ' ' . ($validatedData['nom'] ?? ''));

        $user = User::create([
            'name' => $displayName,
            'prenoms' => $validatedData['prenoms'],
            'nom' => $validatedData['nom'],
            'email' => $validatedData['email'],
            'phone_call' => $validatedData['phone_call'] ?? null,
            'phone_whatsapp' => $validatedData['phone_whatsapp'] ?? null,
            'password' => Hash::make($validatedData['password']),
        ]);

        Profile::create([
            'user_id' => $user->id,
            'first_name' => $validatedData['prenoms'] ?? null,
            'last_name' => $validatedData['nom'] ?? null,
            'phone' => $validatedData['phone_call'] ?? null,
            'address' => null,
            'photo' => null,
        ]);

        // Fire Registered event to send email verification notification
        event(new Registered($user));

        // Redirect to email verification notice page
        return redirect()->route('verification.notice')->with('success', 'Inscription réussie. Veuillez vérifier votre email pour activer votre compte.');
    }
}
