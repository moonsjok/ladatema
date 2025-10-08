@extends('layouts.guest.index')
@section('page-title', 'Contactez-nous')
@section('content')

    <div class="container mt-5">
        <h2 class="mb-4">Contactez-nous</h2>

        <div class="row">
            <div class="col-md-4">

                <p><i class="bi bi-telephone-fill"></i> +228 99 18 72 96 ( Réclamations)</p>
                <p><i class="bi bi-telephone-fill"></i> +228 92 98 08 42 ( Standard )</p>
                <p><i class="bi bi-envelope-fill"></i>
                    <a href="mailto:ladatema@gmail.com">ladatema@gmail.com</a>
                </p>
                <p><i class="bi bi-envelope-fill"></i>
                    <a href="mailto:info@ladatemaresearch.com">info@ladatemaresearch.com</a>
                </p>
                <p class=""><i class="bi bi-geo-alt-fill"></i> Agbalepedo Groupe C derrière l'immeuble UNIR, Lomé-TOGO
                </p>
            </div>


            <div class="col-md-8">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3966.5607437027074!2d1.1958772749900863!3d6.1894846937981!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zNsKwMTEnMjIuMSJOIDHCsDExJzU0LjQiRQ!5e0!3m2!1sfr!2sae!4v1739351068855!5m2!1sfr!2sae"
                    width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>

            </div>
        </div>

        <div class="row mt-3">
            <div class="row">

                <h4 class="mt-4"><i class="bi bi-envelope-fill"></i> Contactez-nous</h4>
                <p class="mb-3">Nous sommes à votre disposition pour toute question ou demande. </p>

                <form action="{{ route('contact.send') }}" method="POST" novalidate>
                    @csrf

                    <div class="mb-3 form-floating">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name') }}" required placeholder="Nom">
                        <label for="name"><i class="bi bi-person-fill"></i> Nom complet</label>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-floating">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email') }}" required placeholder="Email">
                        <label for="email"><i class="bi bi-envelope-fill"></i> Email</label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-floating">
                        <textarea style="min-height:300px;" class="form-control @error('message') is-invalid @enderror" id="message"
                            name="message" rows="5" required placeholder="Message">{{ old('message') }}</textarea>
                        <label for="message"><i class="bi bi-chat-dots-fill"></i> Message</label>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send-fill"></i> Envoyer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
