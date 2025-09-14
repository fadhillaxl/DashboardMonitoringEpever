<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">UHAMKA</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            {{-- Menu kiri --}}
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard/sites*') ? 'active' : '' }}"
                        href="{{ url('/dashboard/sites') }}">
                        Sites
                    </a>
                </li>
            </ul>

            {{-- Tombol Logout --}}
            <form action="{{ route('logout') }}" method="POST" class="d-flex align-items-center ms-auto">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    Logout
                </button>
            </form>
        </div>

    </div>
</nav>
