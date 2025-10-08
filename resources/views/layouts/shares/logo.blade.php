<style>
    .logo {
        display: inline-flex;
        align-items: center;
        /* Aligne verticalement les éléments */
        gap: 0;
        /* Supprime les espaces entre les caractères */
    }

    .logo>img {
        width: 130px;
        height: auto;
    }
</style>

<a href="{{ route('welcome') }}" class="logo text-decoration-none">
    <img class="img-fluid" src="{{ asset('images/LOGO_LADATEMA_SARL.png') }}" load="lazy">
</a>
