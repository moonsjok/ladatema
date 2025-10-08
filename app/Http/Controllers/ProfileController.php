<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display a listing of the profiles.
     */
    public function index()
    {
        $profiles = Profile::withTrashed()->paginate(10);
        return view('profiles.index', compact('profiles'));
    }

    /**
     * Show the form for creating a new profile.
     */
    public function create()
    {
        return view('profiles.create');
    }

    /**
     * Store a newly created profile in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            // Accept either first_name/last_name or prenoms/nom for compatibility
            'first_name' => 'required_without:prenoms|string|max:255',
            'last_name' => 'required_without:nom|string|max:255',
            'prenoms' => 'required_without:first_name|string|max:255',
            'nom' => 'required_without:last_name|string|max:255',
            'phone' => 'nullable|string|max:20',
            'phone_call' => 'nullable|string|max:30',
            'phone_whatsapp' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        // Map prenoms/nom to first_name/last_name if provided
        if (isset($data['prenoms']) && !isset($data['first_name'])) {
            $data['first_name'] = $data['prenoms'];
        }
        if (isset($data['nom']) && !isset($data['last_name'])) {
            $data['last_name'] = $data['nom'];
        }
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('profiles', 'public');
        }

        Profile::create($data);

        // If the form provided phone_call or phone_whatsapp and a user_id, update the related user
        if (!empty($data['user_id'])) {
            $user = \App\Models\User::find($data['user_id']);
            if ($user) {
                $update = [];
                if ($request->filled('phone_call')) {
                    $update['phone_call'] = $request->input('phone_call');
                }
                if ($request->filled('phone_whatsapp')) {
                    $update['phone_whatsapp'] = $request->input('phone_whatsapp');
                }
                if (!empty($update)) {
                    $user->update($update);
                }
            }
        }
        return redirect()->route('profiles.index')->with('success', 'Profile created successfully.');
    }

    /**
     * Show the form for editing the specified profile.
     */
    public function edit(Profile $profile)
    {
        return view('profiles.edit', compact('profile'));
    }

    /**
     * Update the specified profile in storage.
     */
    public function update(Request $request, Profile $profile)
    {
        $request->validate([
            'first_name' => 'required_without:prenoms|string|max:255',
            'last_name' => 'required_without:nom|string|max:255',
            'prenoms' => 'required_without:first_name|string|max:255',
            'nom' => 'required_without:last_name|string|max:255',
            'phone' => 'nullable|string|max:20',
            'phone_call' => 'nullable|string|max:30',
            'phone_whatsapp' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        if (isset($data['prenoms']) && !isset($data['first_name'])) {
            $data['first_name'] = $data['prenoms'];
        }
        if (isset($data['nom']) && !isset($data['last_name'])) {
            $data['last_name'] = $data['nom'];
        }
        if ($request->hasFile('photo')) {
            if ($profile->photo) {
                Storage::disk('public')->delete($profile->photo);
            }
            $data['photo'] = $request->file('photo')->store('profiles', 'public');
        }

        $profile->update($data);

        // If phone_call or phone_whatsapp provided, sync to related user
        if ($profile->user_id) {
            $user = $profile->user;
            if ($user) {
                $update = [];
                if ($request->filled('phone_call')) {
                    $update['phone_call'] = $request->input('phone_call');
                }
                if ($request->filled('phone_whatsapp')) {
                    $update['phone_whatsapp'] = $request->input('phone_whatsapp');
                }
                if (!empty($update)) {
                    $user->update($update);
                }
            }
        }
        return redirect()->route('profiles.index')->with('success', 'Profile updated successfully.');
    }

    /**
     * Remove the specified profile from storage.
     */
    // public function destroy(Profile $profile)
    // {
    //     $profile->delete();
    //     return redirect()->route('profiles.index')->with('success', 'Profile deleted successfully.');
    // }

    /**
     * Restore a soft-deleted profile.
     */
    // public function restore($id)
    // {
    //     Profile::withTrashed()->findOrFail($id)->restore();
    //     return redirect()->route('profiles.index')->with('success', 'Profile restored successfully.');
    // }

    /**
     * Permanently delete a profile.
     */
    // public function forceDelete($id)
    // {
    //     $profile = Profile::withTrashed()->findOrFail($id);
    //     if ($profile->photo) {
    //         Storage::disk('public')->delete($profile->photo);
    //     }
    //     $profile->forceDelete();
    //     return redirect()->route('profiles.index')->with('success', 'Profile permanently deleted.');
    // }
}
