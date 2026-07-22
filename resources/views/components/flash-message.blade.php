@if (session()->has('flash_notification') || session()->has('error') || session()->has('success'))
    <style>
        div:where(.swal2-container) div:where(.swal2-popup) {
            border-radius: 2rem !important;
        }
        div:where(.swal2-container) button:where(.swal2-styled).swal2-confirm {
            border-radius: 1rem !important;
            padding: 0.75rem 2rem !important;
            font-weight: bold !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (session()->has('flash_notification'))
                @foreach (session('flash_notification')->all() as $notification)
                    Swal.fire({
                        icon: '{{ $notification->level == "danger" ? "error" : "success" }}',
                        title: '{{ $notification->level == "danger" ? "Oops..." : "Berhasil!" }}',
                        text: '{!! addslashes($notification->message) !!}',
                        confirmButtonColor: '{{ $notification->level == "danger" ? "#ef4444" : "#22c55e" }}',
                    });
                @endforeach
            @endif

            @if (session()->has('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{!! addslashes(session("error")) !!}',
                    confirmButtonColor: '#ef4444',
                });
            @endif

            @if (session()->has('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{!! addslashes(session("success")) !!}',
                    confirmButtonColor: '#22c55e',
                });
            @endif
        });
    </script>
@endif
