@extends('layouts.app')

@section('title', 'Water Heater Control Panel - ' . $mac_address)

@section('content')
    <div class="container">
        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12 col-md-6">
                <h2 class="text-start fw-bold">Relay Control Panel - {{ $mac_address }}</h2>
            </div>
            <div class="col-12 col-md-6 text-end">
                <a href="{{ url()->current() }}" class="btn btn-sm btn-primary">Refresh</a>
            </div>
        </div>

        {{-- Info umum --}}
        <div class="row mb-4 p-3 border rounded shadow-sm bg-white">
            <h4 class="fw-bold mb-3">
                <i class="bi bi-info-circle me-2"></i> Relay Info
            </h4>
            <ul class="list-group">
                <li class="list-group-item">
                    <strong>Relay Connection:</strong>
                    <span class="badge {{ $relay->relay_connection ? 'bg-success' : 'bg-danger' }}">
                        {{ $relay->relay_connection ? 'Connected' : 'Disconnected' }}
                    </span>
                </li>

                <li class="list-group-item">
                    <strong>Last Update:</strong>
                    <span class="badge bg-info">
                        {{ $relay?->update_from_site?->format('d M Y H:i:s') ?? 'Belum ada update' }}
                    </span>
                </li>
            </ul>
        </div>

        {{-- Relay Channels --}}
        <div class="row mb-4 p-3 border rounded shadow-sm bg-white">
            <h4 class="fw-bold mb-3">
                <i class="bi bi-lightning text-primary me-2"></i> Relay Channels
            </h4>

            @foreach ($relay_command as $channel => $cmd)
                @php $cond = $relay_condition[$channel] ?? 0; @endphp
                <div class="col-6 col-md-3 mb-3">
                    <div class="card shadow-sm text-center p-3 h-100">
                        <small class="text-muted d-block mb-1">Channel {{ $channel + 1 }}</small>

                        {{-- Kondisi realtime --}}
                        <div class="mb-2">
                            <span class="badge {{ $cond ? 'bg-success' : 'bg-danger' }}">
                                Condition: {{ $cond ? 'ON' : 'OFF' }}
                            </span>
                        </div>

                        {{-- Command dari web --}}
                        <form method="POST" action="{{ route('relay.updateCommand', $relay->id) }}">
                            @csrf
                            <input type="hidden" name="channel" value="{{ $channel }}">
                            <select name="state" class="form-select form-select-sm mb-2">
                                <option value="0" @selected($cmd == 0)>OFF</option>
                                <option value="1" @selected($cmd == 1)>ON</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Update</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
