@extends('layouts.app')

@section('title', 'Charts - ' . $mac_address)

@section('content')
    <h2 class="mb-4 text-start fw-bold">EPEVER Charts - {{ $mac_address }}</h2>

    <div class="row">
        <div class="col col-12 col-md-6">
            <div class="mb-3 text-start">
                <span class="badge bg-dark fs-6">Last Update: {{ $lastRow['time'] ?? '-' }}</span>
            </div>
        </div>
        <div class="col col-12 col-md-6">
            <div class="mb-3 text-end d-flex flex-wrap justify-content-end align-items-center gap-2">

                {{-- Quick Range Buttons --}}
                <div class="btn-group" role="group">
                    @foreach ($availableRanges as $r)
                        <a href="?range={{ $r }}"
                            class="btn btn-sm btn-outline-dark {{ empty($startDate) && $range == $r ? 'active' : '' }}">
                            {{ strtoupper($r) }}
                        </a>
                    @endforeach
                </div>

                {{-- Custom Range Form --}}
                <form method="GET" class="row g-2 align-items-center">
                    <input type="hidden" name="range" value="">

                    <div class="col-12 col-md-auto">
                        <input type="datetime-local" name="start_date" value="{{ $startDate ?? '' }}"
                            class="form-control form-control-sm">
                    </div>

                    <div class="col-12 col-md-auto text-center">
                        <span>to</span>
                    </div>

                    <div class="col-12 col-md-auto">
                        <input type="datetime-local" name="end_date" value="{{ $endDate ?? '' }}"
                            class="form-control form-control-sm">
                    </div>

                    <div class="col-12 col-md-auto">
                        <button type="submit" class="btn btn-dark btn-sm w-100">Apply</button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    @if (count($rows) > 0)
        <div class="row g-4">
            @foreach ($chartColumns as $col)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card bg-white text-dark p-3 shadow-sm">
                        <h5 class="card-title text-uppercase text-start fw-bold border-bottom pb-2 mb-3">
                            {{ ucwords(str_replace('_', ' ', $col)) }}
                        </h5>
                        <canvas id="chart-{{ $col }}"></canvas>
                        @if (isset($lastRow[$col]))
                            <div class="text-center mt-1 small text-muted">Last Value: {{ $lastRow[$col] }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning text-center">No data available for this range</div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
    <script>
        const rows = @json($rows);

        @foreach ($chartColumns as $col)
            const ctx{{ $col }} = document.getElementById('chart-{{ $col }}').getContext('2d');
            const data{{ $col }} = rows.map(r => r['{{ $col }}']);

            new Chart(ctx{{ $col }}, {
                type: 'line',
                data: {
                    labels: rows.map(() => ''),
                    datasets: [{
                        label: '{{ $col }}',
                        data: data{{ $col }},
                        borderColor: '#2b7fff',
                        backgroundColor: 'rgba(219, 234, 254, 1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 3,
                        pointBackgroundColor: '#2b7fff',
                        pointBorderColor: '#2b7fff'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                title: (tooltipItems) => rows[tooltipItems[0].dataIndex].time
                            }
                        },
                        legend: {
                            display: false,
                            position: 'top'
                        },
                        zoom: {
                            zoom: {
                                wheel: {
                                    enabled: true
                                },
                                pinch: {
                                    enabled: true
                                },
                                mode: 'x'
                            },
                            pan: {
                                enabled: true,
                                mode: 'x'
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            title: {
                                display: true,
                                text: '{{ $col }}'
                            }
                        }
                    }
                }
            });
        @endforeach
    </script>
@endpush
