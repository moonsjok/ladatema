<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::all();
        return view('authenticated.owners.partners.index', compact('partners'));
    }

    public function create()
    {
        return view('authenticated.owners.partners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $partner = Partner::create(['name' => $request->name]);
            $partner->addMediaFromRequest('logo')->toMediaCollection('logo');

            Alert::success('Succès', 'Partenaire ajouté avec succès !');
            return redirect()->route('partners.index');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l’ajout du partenaire : ' . $e->getMessage());
            Alert::error('Erreur', 'Une erreur s’est produite : ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function edit(Partner $partner)
    {
        return view('authenticated.owners.partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $partner->update(['name' => $request->name]);

            if ($request->hasFile('logo')) {
                $partner->clearMediaCollection('logo');
                $partner->addMediaFromRequest('logo')->toMediaCollection('logo');
            }

            Alert::success('Succès', 'Partenaire mis à jour avec succès !');
            return redirect()->route('partners.index');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour du partenaire : ' . $e->getMessage());
            Alert::error('Erreur', 'Une erreur s’est produite : ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy(Partner $partner)
    {
        try {
            $partner->clearMediaCollection('logo');
            $partner->delete();

            Alert::success('Succès', 'Partenaire supprimé avec succès !');
            return redirect()->route('partners.index');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du partenaire : ' . $e->getMessage());
            Alert::error('Erreur', 'Une erreur s’est produite : ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
