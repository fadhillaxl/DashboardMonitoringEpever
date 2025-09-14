@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 text-start fw-bold">Arduino Sensors - {{ $mac_address }}</h2>

    <div class="row">
        <div class="col col-12 col-md-6">
            <div class="mb-3 text-start">
                <span class="badge bg-info fs-6">
                    <i class="bi bi-clock-history me-1"></i>
                    Last Update: {{ $time ?? '-' }}
                </span>
            </div>
        </div>
        <div class="col col-12 col-md-6 text-end">
            <a href="" class="btn btn-sm btn-primary mb-3">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </a>
        </div>
    </div>

    {{-- Light Sensor --}}
    <div class="row mb-4">
        @foreach ($lightSection as $section => $cards)
            <div class="col-12">
                <div class="p-3 border rounded shadow-sm bg-white h-100">
                    <h5 class="fw-bold mb-3">{{ $section }}</h5>
                    <div class="row">
                        @foreach ($cards as $title => [$field, $icon])
                            <div class="col-6">
                                <div class="card shadow-sm text-center p-3">
                                    <i class="bi {{ $icon }} fs-3 mb-1 text-primary"></i>
                                    <small class="d-block text-muted">{{ $title }}</small>
                                    <h6 class="fw-bold mb-0">
                                        @if (in_array($field, $booleanFields))
                                            @if (!empty($data[$field]))
                                                <span class="text-success">ON</span>
                                            @else
                                                <span class="text-secondary">OFF</span>
                                            @endif
                                        @else
                                            {{ $data[$field] ?? 'N/A' }}
                                        @endif
                                    </h6>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Anemometer Sensors --}}
    <div class="row mb-4">
        @foreach ($anemometerSections as $section => $cards)
            <div class="col-12 col-md-6 mb-3">
                <div class="p-3 border rounded shadow-sm bg-white h-100">
                    <h5 class="fw-bold mb-3">{{ $section }}</h5>
                    <div class="row">
                        @foreach ($cards as $title => [$field, $icon])
                            <div class="col-6">
                                <div class="card shadow-sm text-center p-3">
                                    <i class="bi {{ $icon }} fs-3 mb-1 text-primary"></i>
                                    <small class="d-block text-muted">{{ $title }}</small>
                                    <h6 class="fw-bold mb-0">{{ $data[$field] ?? 'N/A' }}</h6>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pressure Sensors --}}
    <div class="row mb-4">
        @foreach ($pressureSections as $section => $cards)
            <div class="col-12 col-md-6 mb-3">
                <div class="p-3 border rounded shadow-sm bg-white h-100">
                    <h5 class="fw-bold mb-3">{{ $section }}</h5>
                    <div class="row">
                        @foreach ($cards as $title => [$field, $icon])
                            <div class="col-6">
                                <div class="card shadow-sm text-center p-3">
                                    <i class="bi {{ $icon }} fs-3 mb-1 text-primary"></i>
                                    <small class="d-block text-muted">{{ $title }}</small>
                                    <h6 class="fw-bold mb-0">{{ $data[$field] ?? 'N/A' }}</h6>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
