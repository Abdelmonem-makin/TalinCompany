@if (session("success"))
    <div class="position-fixed end-0 top-1 p-3" style="z-index: 1100">
        <div id='alertBox' class="alert d-flex alert-success alert-dismissible fade show" role="alert">
            {{ session("success") }}
            <button type="button" class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast"
                aria-label="إغلاق"></button>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="position-fixed end-0 top-1 p-3" style="z-index: 1100">
        <div id='alertBox' class="alert d-flex alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>

                        {{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

<script>
        function showAlert() {
            const alertBox = document.getElementById('alertBox');
            if (!alertBox) return; // nothing to do
            alertBox.classList.remove('d-none'); // Show the alert
            setTimeout(() => {
                alertBox.classList.add('d-none'); // Hide the alert after 3 seconds
            }, 3000);

        }
        if (document.getElementById('alertBox')) {
            showAlert();
        }
    var alertList = document.querySelectorAll(".alert");
    alertList.forEach(function(alert) {
        new bootstrap.Alert(alert);
    });
</script>
