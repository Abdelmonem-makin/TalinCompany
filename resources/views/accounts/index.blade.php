@extends("layouts.app")
@section("content")
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>الحسابات</h1>
            {{-- <a href="{{ route("transactions.index") }}" class="btn btn-primary">المعاملات</a> --}}
            <div class="col-md-4">
                <input class="form-control" id="searchInput" placeholder="بحث بالاسم أو الرقم" oninput="liveSearch()">
            </div>
            <a href="{{ route("accounts.create") }}" class="btn btn-primary">اضافة حساب جديد</a>
        </div>
        <!-- resources/views/accounts/index.blade.php -->
        <div class="card p-3">
            <div id="tableContainer">
                @include('accounts.partials.table')
            </div>
        </div>
    </div>

    {{ $accounts->links() }}

    <script>
        let searchTimeout;

        function liveSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                const searchTerm = document.getElementById('searchInput').value;

                fetch(`{{ route('accounts.index') }}?search=${encodeURIComponent(searchTerm)}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('tableContainer').innerHTML = data.html;
                    // Update pagination links to work with AJAX
                    const paginationContainer = document.querySelector('.pagination');
                    if (paginationContainer) {
                        paginationContainer.innerHTML = data.pagination;
                        // Add event listeners to pagination links
                        paginationContainer.querySelectorAll('a').forEach(link => {
                            link.addEventListener('click', function(e) {
                                e.preventDefault();
                                fetch(this.href, {
                                    method: 'GET',
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    document.getElementById('tableContainer').innerHTML = data.html;
                                    paginationContainer.innerHTML = data.pagination;
                                })
                                .catch(error => console.error('Error:', error));
                            });
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
            }, 10); // 300ms debounce
        }
    </script>
@endsection
