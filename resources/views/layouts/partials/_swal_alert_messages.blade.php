<script>
    function showSweetAlert(type, message) {
        Swal.fire({
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 5000
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        @if (session('success'))
            showSweetAlert('success', '{{ session('success') }}');
        @elseif (session('status'))
            showSweetAlert('success', '{{ session('status') }}');
        @elseif (session('error'))
            showSweetAlert('error', '{{ session('error') }}');
        @endif
    });
</script>
<script>
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            Swal.fire({
                icon: 'error',
                title: 'Erreur de validation',
                text: '{{ $error }}'
            });
        @endforeach
    @endif

    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Succès',
            text: '{{ session('success') }}'
        });
    @endif

    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: '{{ session('error') }}'
        });
    @endif

    @if (session('info'))
        Swal.fire({
            icon: 'info',
            title: 'Information',
            text: '{{ session('info') }}'
        });
    @endif

    @if (session('warning'))
        Swal.fire({
            icon: 'warning',
            title: 'Attention',
            text: '{{ session('warning') }}'
        });
    @endif
</script>
