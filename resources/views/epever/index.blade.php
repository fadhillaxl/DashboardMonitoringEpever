@extends('layouts.app')

@section('title', 'EPEVER - ' . $mac_address)

@section('content')
    <div class="container">
        {{-- Header --}}
        <h2 class="mb-4 text-start fw-bold">EPEVER Solar System - {{ $mac_address }}</h2>

        <div class="row mb-2">
            <div class="col col-12 col-md-6">
                <div class="mb-3 text-start">
                    <span class="badge bg-dark fs-6">Last Update: {{ $time ?? '-' }}</span>
                </div>
            </div>
            <div class="col col-12 col-md-6 text-end">
                <a href="" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-clockwise"> Refresh</i></a>
            </div>
        </div>

        @foreach ($sections as $section => $info)
            <div class="row mb-4 p-3 border rounded shadow-sm bg-white">
                <h4 class="fw-bold mb-3">
                    <i class="bi {{ $info['icon'] }} me-2"></i> {{ $section }}
                </h4>

                @foreach ($info['fields'] as $title => $field)
                    @php
                        $value = $data[$field] ?? 'N/A';
                        $unit = collect($unitMap)->filter(fn($u, $k) => str_contains($title, $k))->first() ?? '';
                        $badgeClass = $statusColors[$value] ?? 'bg-secondary';
                    @endphp

                    <div class="col-6 col-md-3 mb-3">
                        <div class="card shadow-sm text-center p-2 h-100">
                            <small class="text-muted">{{ $title }}</small>
                            <h5 class="fw-bold mt-2">
                                @if (in_array($field, $booleanFields))
                                    <span class="badge {{ $value ? 'bg-success' : 'bg-danger' }}">
                                        {{ $value ? 'TRUE' : 'FALSE' }}
                                    </span>
                                @elseif (in_array($field, $stringStatusFields))
                                    <span class="badge {{ $badgeClass }}">{{ $value }}</span>
                                @else
                                    {{ $value }}{{ $unit }}
                                @endif
                            </h5>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endsection
