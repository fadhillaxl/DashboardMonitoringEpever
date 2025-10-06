@extends('dashboard.layouts.main')

@section('title', 'Sites')

@section('content')
    <div class="container py-3">

        {{-- Welcome message --}}
        @if (session('welcome'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Selamat datang, <strong>{{ session('welcome') }}</strong>!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-2 mb-md-0">Sites</h3>
            <a href="{{ route('sites.create') }}" class="btn btn-dark btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Add Site
            </a>
        </div>

        {{-- Success message --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Sites Section --}}
        <div class="bg-white p-3 p-md-4 shadow-sm rounded-3">
            <div class="table-responsive">
                <table class="table table-striped align-middle text-nowrap">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>No</th>
                            <th>Nama Site</th>
                            <th>Alamat Lengkap</th>
                            <th>Lokasi</th>
                            <th>PIC</th>
                            <th>MAC Address</th>
                            <th>Dashboard</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sites as $site)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-break">{{ $site->nama_site }}</td>
                                <td class="text-break">{{ $site->alamat_lengkap }}</td>
                                <td>{{ $site->lokasi }}</td>
                                <td>{{ $site->pic }}</td>
                                <td><small>{{ $site->mac_address }}</small></td>
                                <td>
                                    {{-- Dashboard --}}
                                    <a href="{{ route('epever.show', $site->mac_address) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-graph-up"></i> Dashboard
                                    </a>
                                </td>
                                <td>
                                    {{-- View --}}
                                    <a href="{{ route('sites.show', $site->mac_address) }}"
                                        class="btn btn-sm btn-secondary">
                                        <i class="bi bi-eye"></i> Show
                                    </a>
                                    {{-- Edit --}}
                                    <a href="{{ route('sites.edit', $site->mac_address) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    {{-- Delete --}}
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $site->mac_address }}">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            {{-- Delete Modal --}}
                            <div class="modal fade" id="deleteModal{{ $site->mac_address }}" tabindex="-1"
                                aria-labelledby="deleteModalLabel{{ $site->mac_address }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $site->mac_address }}">
                                                Confirm Delete</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            Are you sure you want to delete
                                            <strong>{{ $site->nama_site }}</strong>?
                                        </div>
                                        <div class="modal-footer justify-content-center">
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <form action="{{ route('sites.destroy', $site->mac_address) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No sites available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
