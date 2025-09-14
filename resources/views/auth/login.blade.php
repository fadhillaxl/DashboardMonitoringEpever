@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="card shadow-sm rounded p-4">
    {{-- Judul dengan border-bottom menyatu --}}
    <div class="pb-2 mb-4 border-bottom text-center">
        <h3 class="fw-bold mb-0">Login</h3>
    </div>

    {{-- Error --}}
    @if ($errors->any())
        <div class="alert alert-danger small">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Login --}}
    <form method="POST" action="{{ url('/login') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus autocomplete="email">
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-dark w-100">Login</button>
    </form>
</div>
@endsection
