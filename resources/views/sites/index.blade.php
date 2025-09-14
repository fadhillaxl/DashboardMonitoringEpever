@extends('layouts.app')

@section('title', 'Sites')

@section('content')
<div class="container">
    {{-- Welcome message --}}
    @if (session('welcome'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Selamat datang, <strong>{{ session('welcome') }}</strong>!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Sites</h3>
        <a href="{{ route('sites.create') }}" class="btn btn-sm btn-dark">
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
    <div class="p-4 bg-white shadow-sm rounded-3">
        <div class="table-responsive rounded">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama Site</th>
                        <th>Alamat Lengkap</th>
                        <th>Lokasi</th>
                        <th>PIC</th>
                        <th>MAC Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sites as $site)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $site->nama_site }}</td>
                            <td>{{ $site->alamat_lengkap }}</td>
                            <td>{{ $site->lokasi }}</td>
                            <td>{{ $site->pic }}</td>
                            <td>{{ $site->mac_address }}</td>
                            <td class="d-flex justify-content-center gap-1">
                                {{-- View --}}
                                <a href="{{ route('sites.show', $site->mac_address) }}" class="btn btn-sm btn-secondary" title="View">
                                    <i class="bi bi-eye"> Show</i>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('sites.edit', $site->mac_address) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil-square"> Edit</i>
                                </a>

                                {{-- Delete Button (Trigger Modal) --}}
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $site->mac_address }}">
                                    <i class="bi bi-trash"> Delete</i>
                                </button>

                                {{-- Dashboard --}}
                                <a href="{{ route('epever.show', $site->mac_address) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-graph-up"> Dashboard</i>
                                </a>

                                {{-- Delete Modal --}}
                                <div class="modal fade" id="deleteModal{{ $site->mac_address }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $site->mac_address }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $site->mac_address }}">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete <strong>{{ $site->nama_site }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('sites.destroy', $site->mac_address) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-secondary">Yes, Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- End Delete Modal --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No sites available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
