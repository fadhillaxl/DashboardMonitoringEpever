@extends('layouts.app')

@section('title', 'Add Site')

@section('content')
<div class="container">
    <h3 class="mb-4 fw-bold">Add Site</h3>

    <div class="p-4 bg-white shadow-sm rounded-3">
        <form method="POST" action="{{ route('sites.store') }}">
            @csrf

            {{-- Include form fields --}}
            @include('sites.partials.form')

            {{-- Buttons --}}
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Save
                </button>
                <a href="{{ route('sites.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
