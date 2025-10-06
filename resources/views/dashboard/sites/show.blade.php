@extends('dashboard.layouts.main')

@section('title', 'Detail Site')

@section('content')
<div class="container">
    <h3 class="mb-4 fw-bold">Detail Site</h3>

    <div class="p-4 bg-white shadow-sm rounded-3">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th scope="row" style="width: 200px;">Nama Site</th>
                    <td>{{ $site->nama_site }}</td>
                </tr>
                <tr>
                    <th scope="row">Alamat Lengkap</th>
                    <td>{{ $site->alamat_lengkap }}</td>
                </tr>
                <tr>
                    <th scope="row">Lokasi</th>
                    <td>{{ $site->lokasi }}</td>
                </tr>
                <tr>
                    <th scope="row">PIC</th>
                    <td>{{ $site->pic }}</td>
                </tr>
                <tr>
                    <th scope="row">MAC Address</th>
                    <td>{{ $site->mac_address }}</td>
                </tr>
            </tbody>
        </table>

        <a href="{{ route('sites.index') }}" class="btn btn-dark">
            <i class="bi bi-arrow-left-circle me-1"></i> Back
        </a>
    </div>
</div>
@endsection
