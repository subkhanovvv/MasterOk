<style>
    .alert-position {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1060;
        min-width: 350px;
        max-width: 500px;
    }
</style>
<div class="alert-container">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show alert-position">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-alert-box me-2"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show alert-position">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-check-circle me-2"></i>
                <div>{{ Session::get('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 150);
            }, 5000);
        });
    });
</script>
