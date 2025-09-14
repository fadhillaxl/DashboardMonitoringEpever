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
            <a href="{{ route('sites.create') }}" class="btn btn-primary">
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
                                <td class="d-flex justify-content-center gap-1 flex-wrap">
                                    {{-- View --}}
                                    <a href="{{ route('sites.show', $site->mac_address) }}" class="btn btn-sm btn-info"
                                        title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('sites.edit', $site->mac_address) }}" class="btn btn-sm btn-warning"
                                        title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('sites.destroy', $site->mac_address) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Yakin hapus site ini?')"
                                            class="btn btn-sm btn-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>

                                    {{-- Dashboard --}}
                                    <a href="{{ route('epever.show', $site->mac_address) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-graph-up"></i>
                                    </a>
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
