@extends('layouts.app')

@section('title', 'Detail Site')

@section('content')
<div class="container">
    <h3 class="mb-4 fw-bold">Detail Site</h3>

    <div class="p-4 bg-white shadow-sm rounded-3">
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Nama Site:</strong> {{ $site->nama_site }}</li>
            <li class="list-group-item"><strong>Alamat Lengkap:</strong> {{ $site->alamat_lengkap }}</li>
            <li class="list-group-item"><strong>Lokasi:</strong> {{ $site->lokasi }}</li>
            <li class="list-group-item"><strong>PIC:</strong> {{ $site->pic }}</li>
            <li class="list-group-item"><strong>MAC Address:</strong> {{ $site->mac_address }}</li>
        </ul>

        <a href="{{ route('sites.index') }}" class="btn btn-secondary mt-3">
            <i class="bi bi-arrow-left-circle me-1"></i> Back
        </a>
    </div>
</div>
@endsection
