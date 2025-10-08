@if (auth()->user()->role === 'dev' || auth()->user()->role === 'owner')
    @include('layouts.authenticated.partials.admin_sidebar')
@else
    @include('layouts.authenticated.partials.user_sidebar')
@endif
