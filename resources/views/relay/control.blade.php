@extends('layouts.app')

@section('title', 'Relay Control Panel - ' . $mac_address)

@section('content')
    <div class="container">

        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12 col-md-6">
                <h2 class="text-start fw-bold">
                    Relay Control Panel - {{ $mac_address }}
                </h2>
            </div>
        </div>

        {{-- Status & Last Update --}}
        <div class="row mb-2">
            <div class="col-12 mb-2">
                <span class="badge bg-dark fs-6">
                    Last Update: {{ $relay?->update_from_site?->format('d M Y H:i:s') ?? '-' }}
                </span>
            </div>
            <div class="col-12 col-md-6 mb-2">
                <span class="badge {{ $relay->relay_connection ? 'bg-success' : 'bg-danger' }} fs-6">
                    Relay Connection: {{ $relay->relay_connection ? 'Connected' : 'Disconnected' }}
                </span>
            </div>
            <div class="col-12 col-md-6 text-end mb-3">
                <a href="" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </a>
            </div>
        </div>

        {{-- Relay Channels Container --}}
        <div class="p-3 shadow-sm border bg-white bg-opacity-75 rounded">
            <h3 class="fw-bold"><i class="bi bi-lightning text-primary"></i> Relay Channels</h3>
            <div class="row g-3 mt-2">
                @foreach ($relay_command as $channel => $cmd)
                    @php $cond = $relay_condition[$channel] ?? 0; @endphp
                    <div class="col-12 col-md-3">
                        <div class="card h-100">

                            {{-- Top: Channel Info --}}
                            <div class="p-3 border-bottom text-center">
                                <h5 class="mb-2">Channel {{ $channel + 1 }}</h5>
                                <button class="btn btn-sm {{ $cond ? 'btn-success' : 'btn-danger' }} w-100" disabled>
                                     <i class="bi {{ $cond ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                                    {{ $cond ? 'ON' : 'OFF' }}
                                </button>
                            </div>

                            {{-- Bottom: Control Form --}}
                            <div class="p-3 d-flex justify-content-center">
                                <form method="POST" action="{{ route('relay.updateCommand', $relay->id) }}"
                                    class="d-flex align-items-center">
                                    @csrf
                                    <input type="hidden" name="channel" value="{{ $channel }}">

                                    <select name="state" class="form-select form-select-sm me-2" style="max-width: 70px;">
                                        <option value="0" @selected($cmd == 0)>OFF</option>
                                        <option value="1" @selected($cmd == 1)>ON</option>
                                    </select>

                                    <button type="submit" class="btn btn-sm btn-warning d-flex align-items-center">
                                        <i class="bi bi-toggles me-1"></i> Update
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
@endsection
