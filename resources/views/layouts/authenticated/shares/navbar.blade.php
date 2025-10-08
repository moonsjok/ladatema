<!-- Sidebar responsive -->
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse vh-100">
    <div class="position-sticky">
        <div class="dashboard-page-header mb-5">
            <h5 class="p-3 border-bottom">
                {{-- {{ env('APP_NAME') }} --}}
                @include('layouts.shares.logo')
            </h5>
        </div>
        @include('layouts.authenticated.students.partials.sidebar')
    </div>
</nav>

<!-- Offcanvas Sidebar pour mobile -->
<div class="offcanvas offcanvas-start" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
    <div class="offcanvas-header ">
        <h1 class="offcanvas-title" id="offcanvasSidebarLabel">
            {{-- {{ env('APP_NAME') }} --}}
            @include('layouts.shares.logo')

        </h1>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body sidebar border-top">
        {{-- <h5 class="sidebar-heading text-muted  pb-3 text-center ">
            <span>{{ str_replace('_', ' ', ucfirst(Auth()->user()->name)) }}</span>
        </h5> --}}
        <!-- Contenu du menu identique à la sidebar -->
        @include('layouts.authenticated.students.partials.sidebar')
        <!-- Inclure le même contenu que la sidebar ici -->
    </div>
</div>

<!-- JavaScript pour gérer les sous-menus et le bouton -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".toggle-menu").forEach(function(element) {
            element.addEventListener("click", function() {
                const submenu = this.nextElementSibling;
                submenu.classList.toggle("open");
            });
        });
    });
</script>
