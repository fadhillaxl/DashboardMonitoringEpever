@extends('layouts.app')

@section('title', 'Edit Site')

@section('content')
<div class="container">
    <h3 class="mb-4 fw-bold">Edit Site</h3>

    <div class="p-4 bg-white shadow-sm rounded-3">
        <form method="POST" action="{{ route('sites.update', $site->mac_address) }}">
            @csrf
            @method('PUT')

            {{-- Include form fields --}}
            @include('sites.partials.form', ['site' => $site])

            {{-- Buttons --}}
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Update
                </button>
                <a href="{{ route('sites.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
