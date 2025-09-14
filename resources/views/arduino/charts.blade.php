@extends('layouts.app')

@section('title', 'Charts - ' . $mac_address)

@section('content')
    <h2 class="mb-4 text-start fw-bold">Arduino Sensors Charts - {{ $mac_address }}</h2>

    <div class="row">
        <div class="col col-12 col-md-6">
            <div class="mb-3 text-start">
                <span class="badge bg-info">
                    <i class="bi bi-clock-history me-1"></i>
                    Last Update: {{ $lastRow['time'] ?? '-' }}
                </span>
            </div>
        </div>
        <div class="col col-12 col-md-6">
            <div class="mb-3 text-end">
                @foreach (['15m', '1h', '1d', '1w', '1m', '1y'] as $r)
                    <a href="?range={{ $r }}"
                        class="btn btn-sm btn-outline-primary {{ $range == $r ? 'active' : '' }}">
                        {{ strtoupper($r) }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @if (count($rows) > 0)
        <div class="row g-4">
            @foreach ($chartColumns as $col)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card bg-white text-dark p-3 shadow-sm h-100">
                        <h5 class="card-title text-start fw-bold border-bottom pb-2 mb-3">
                            {{ $labels[$col] ?? ucwords(str_replace('_', ' ', $col)) }}
                        </h5>
                        <canvas id="chart-{{ $col }}"></canvas>
                        @if (isset($lastRow[$col]))
                            <div class="text-center mt-1 small text-muted">
                                Last Value: {{ $lastRow[$col] }}
                            </div>
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

        Object.keys(rows[0] || {}).forEach(col => {
            if (['time', 'mac_address'].includes(col)) return;

            const ctx = document.getElementById('chart-' + col).getContext('2d');
            const data = rows.map(r => r[col]);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: rows.map(r => r.time), // timestamp utk tooltip
                    datasets: [{
                        label: col.replaceAll('_', ' '),
                        data: data,
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
                            display: false
                        },
                        zoom: {
                            zoom: {
                                wheel: { enabled: true },
                                pinch: { enabled: true },
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
                                text: col.replaceAll('_', ' ')
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
