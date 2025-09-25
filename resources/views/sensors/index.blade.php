@extends('layouts.app')

@section('title', 'Modbus Sensor - ' . $mac_address)

@section('content')
    <h2 class="mb-4 text-start fw-bold">Data Sensor - {{ $mac_address }}</h2>

    @if ($time)
        <div class="row mb-2">
            <div class="col col-12 col-md-6">
                <div class="mb-3 text-start">
                    <span class="badge bg-dark fs-6">Last Update: {{ $time }}</span>
                </div>
            </div>
            <div class="col col-12 col-md-6 text-end">
                <a href="" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-clockwise"> Refresh</i></a>
            </div>
        </div>

        {{-- Temperature --}}
        <div class="row g-3 mb-5">
            <h5 class="mt-4 mb-2 fw-bold">
                <i class="bi bi-thermometer-half me-2 text-danger"></i> Temperature (PT-100)
            </h5>
            @foreach ($temperatures as $sensor)
                <div class="col-12 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm bg-white text-dark text-center">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <p class="mb-1 small text-uppercase">
                                <i class="bi bi-thermometer text-danger"></i> {{ $sensor['label'] }}
                            </p>
                            <p class="fs-5 fw-bold mb-2">{{ $sensor['value'] ?? 0.0 }} °C</p>
                            @if (isset($sensor['status']))
                                <button class="btn btn-sm {{ $sensor['status'] ? 'btn-success' : 'btn-danger' }}" disabled>
                                    <i class="bi {{ $sensor['status'] ? 'bi-check-circle' : 'bi-x-circle' }}"> {{ $sensor['status'] ? 'ON' : 'OFF' }}</i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Humidity --}}
        <div class="row g-3">
            <h5 class="mt-4 mb-2 fw-bold">
                <i class="bi bi-droplet-half me-2 text-primary"></i> Humidity (THM-30MD)
            </h5>
            @foreach ($humidities as $sensor)
                <div class="col-12 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm bg-white text-dark text-center">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <p class="mb-1 small text-uppercase">
                                <i class="bi bi-droplet text-primary"></i> {{ $sensor['label'] }}
                            </p>
                            <p class="fs-5 fw-bold mb-2">{{ $sensor['value'] ?? 0.0 }} %RH</p>
                            @if (isset($sensor['status']))
                                <button class="btn btn-sm {{ $sensor['status'] ? 'btn-success' : 'btn-danger' }}" disabled>
                                    <i class="bi {{ $sensor['status'] ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                                    {{ $sensor['status'] ? 'ON' : 'OFF' }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning text-center">No data available</div>
    @endif
@endsection
