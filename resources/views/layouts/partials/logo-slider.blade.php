<div class="container my-5">
    <h2 class="text-center mb-2">Nos Partenaires</h2>
    <h5 class="text-center text-muted">Nous avons plus de {{ App\Models\Partner::count() }} partenaires</h5>

    <div class="logo-slider">
        <div class="logo-track">
            @php
                $partners = App\Models\Partner::all();
            @endphp
            @foreach ($partners as $partner)
                @if ($partner->hasMedia('logo'))
                    <div class="logo-item">
                        <img src="{{ $partner->getFirstMediaUrl('logo', 'thumb') }}" alt="{{ $partner->name }}"
                            class="img-fluid">
                    </div>
                @endif
            @endforeach

            <!-- Duplication des logos pour un effet infini -->
            @foreach ($partners as $partner)
                @if ($partner->hasMedia('logo'))
                    <div class="logo-item">
                        <img src="{{ $partner->getFirstMediaUrl('logo', 'thumb') }}" alt="{{ $partner->name }}"
                            class="img-fluid">
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

<style>
    .logo-slider {
        overflow: hidden;
        position: relative;
        width: 100%;
        background: #f8f9fa;
        padding: 20px 0;
        white-space: nowrap;
    }

    .logo-track {
        display: flex;
        gap: 30px;
        animation: scrollLogos 15s linear infinite;
    }

    .logo-item {
        flex: 0 0 auto;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .logo-item img {
        max-height: 100%;
        max-width: 150px;
        object-fit: contain;
    }

    @keyframes scrollLogos {
        from {
            transform: translateX(0);
        }

        to {
            transform: translateX(-50%);
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const track = document.querySelector(".logo-track");
        const clone = track.innerHTML;
        track.innerHTML += clone; // Duplication pour un effet infini
    });
</script>
