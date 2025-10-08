<?php

namespace App\Http\Controllers;

use SEOMeta;
use OpenGraph;
use Twitter;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\Formation;

use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Config;

class UnsecureController extends Controller
{
    /**
     * Ensure the user is authenticated to access this controller.
     */
    public function __construct() {}

    /**
     * Display the home page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {

        SEOMeta::setTitle("Ladatema research");
        SEOMeta::setDescription("Votre cabinet d'experts comptables
Créateur, repreneur ou chef d’entreprise, nos experts comptables vous accompagnent pour gérer et développer votre entreprise. ");
        SEOMeta::addKeyword(['experts comptables', 'formation', 'cours en ligne', 'Laravel']);

        OpenGraph::setTitle("Ladatema research");
        OpenGraph::setDescription("Votre cabinet d'experts comptables
Créateur, repreneur ou chef d’entreprise, nos experts comptables vous accompagnent pour gérer et développer votre entreprise. ");
        OpenGraph::addImage(url(asset('images/ladatema_research.jpg')));

        Twitter::setTitle("Ladatema research");
        Twitter::setImage(url(asset('images/ladatema_research.jpg')));

        $data['formations'] = Formation::latest()->limit(5)->get();
        $data['courses'] = Course::latest()->limit(5)->get();
        $data['chapters'] = Chapter::orderBy('numero', 'desc')->limit(5)->get();


        return view('welcome', $data);
    }
    public function services(Request $request)
    {
        SEOMeta::setTitle("Nos services : Assistance Comptable et Fiscale , Audit et Commissariat aux Comptes , Conseil en Création et Acquisition d’Entreprise , Représentation et Assistance Commerciale, Formation Professionnelle en Comptabilité et Bureautique,Initiation et Accompagnement en Investissement Boursier  ");
        SEOMeta::setDescription("Découvrez nos services spécialisés : assistance comptable et fiscale, audit et commissariat aux comptes, conseil en création et acquisition d'entreprise, représentation et assistance commerciale, formation en comptabilité et bureautique, et initiation à l'investissement boursier. Votre cabinet d'experts comptables vous accompagne pour gérer et développer votre entreprise.");
        SEOMeta::addKeyword(['experts comptables', 'formation', 'cours en ligne', 'Laravel']);

        OpenGraph::setTitle("Ladatema research");
        OpenGraph::setDescription("Découvrez nos services spécialisés : assistance comptable et fiscale, audit et commissariat aux comptes, conseil en création et acquisition d'entreprise, représentation et assistance commerciale, formation en comptabilité et bureautique, et initiation à l'investissement boursier. Votre cabinet d'experts comptables vous accompagne pour gérer et développer votre entreprise.");
        OpenGraph::addImage(url(asset('images/ladatema_research.jpg')));

        Twitter::setTitle("Ladatema research");
        Twitter::setDescription("Découvrez nos services spécialisés : assistance comptable et fiscale, audit et commissariat aux comptes, conseil en création et acquisition d'entreprise, représentation et assistance commerciale, formation en comptabilité et bureautique, et initiation à l'investissement boursier. Votre cabinet d'experts comptables vous accompagne pour gérer et développer votre entreprise.");
        Twitter::setImage(url(asset('images/ladatema_research.jpg')));

        $data['formations'] = Formation::latest()->limit(5)->get();
        $data['courses'] = Course::latest()->limit(5)->get();
        $data['chapters'] = Chapter::latest()->limit(5)->get();

        return view('services.index', $data);
    }




    public function showContactForm()
    {
        return view('contact');
    }



    public function sendContactForm(Request $request)
    {

        SEOMeta::setTitle("Nous contacter : Assistance Comptable et Fiscale , Audit et Commissariat aux Comptes , Conseil en Création et Acquisition d’Entreprise , Représentation et Assistance Commerciale, Formation Professionnelle en Comptabilité et Bureautique,Initiation et Accompagnement en Investissement Boursier  ");
        SEOMeta::setDescription("Découvrez nos services spécialisés : assistance comptable et fiscale, audit et commissariat aux comptes, conseil en création et acquisition d'entreprise, représentation et assistance commerciale, formation en comptabilité et bureautique, et initiation à l'investissement boursier. Votre cabinet d'experts comptables vous accompagne pour gérer et développer votre entreprise.");
        SEOMeta::addKeyword(['experts comptables', 'formation', 'cours en ligne']);

        OpenGraph::setTitle("Ladatema research");
        OpenGraph::setDescription("Découvrez nos services spécialisés : assistance comptable et fiscale, audit et commissariat aux comptes, conseil en création et acquisition d'entreprise, représentation et assistance commerciale, formation en comptabilité et bureautique, et initiation à l'investissement boursier. Votre cabinet d'experts comptables vous accompagne pour gérer et développer votre entreprise.");
        OpenGraph::addImage(url(asset('images/ladatema_research.jpg')));

        Twitter::setTitle("Ladatema research");
        Twitter::setDescription("Découvrez nos services spécialisés : assistance comptable et fiscale, audit et commissariat aux comptes, conseil en création et acquisition d'entreprise, représentation et assistance commerciale, formation en comptabilité et bureautique, et initiation à l'investissement boursier. Votre cabinet d'experts comptables vous accompagne pour gérer et développer votre entreprise.");
        Twitter::setImage(url(asset('images/ladatema_research.jpg')));

        // Validation des données du formulaire
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string|min:10',
        ]);

        // Récupérer le destinataire depuis la configuration
        //$recipient = Config::get('mail.contact_dest');
        $recipient = "moonsjokcorp@gmail.com";

        // Envoi de l'e-mail
        Mail::to($recipient)->send(new ContactMail($request->all()));

        // Redirection avec message de succès
        return back()->with('success', 'Votre message a été envoyé avec succès.');
    }

    public function formations()
    {
        // Récupère uniquement les formations qui ne sont pas supprimées
        $formations = Formation::withoutTrashed()->paginate(20); // Récupère 20 formations par page sans inclure celles supprimées

        return view('guest.formations.index', compact('formations'));
    }


    public function formationsList()
    {
        return view('guest.formations.list',);
    }




    public function formationShow(Formation $formation, $slug = null)
    {
        $generatedSlug = Str::slug($formation->title ?? 'formation'); // Générer le slug

        // Si le slug est absent ou incorrect, rediriger vers l'URL correcte
        if ($slug !== $generatedSlug) {
            return redirect()->route('guest.formations.show', [
                'formation' => $formation->id,
                'slug' => $generatedSlug,
            ], 301);
        }

        // Vérifier si la formation est supprimée
        if ($formation->trashed()) {
            return redirect()->route('guest.formations.index')->with('error', 'Cette formation est supprimée.');
        }

        // Charger les cours associés
        $courses = $formation->courses()->withoutTrashed()->get();

        return view('guest.formations.show', compact('formation', 'courses'));
    }
}
