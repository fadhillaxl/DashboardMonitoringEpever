@if (!empty($macs_menu_map) && $currentSite && in_array($currentSite, $macs_menu_map))
    <div class="container">
        <div class="d-flex flex-wrap justify-content-start gap-2 p-3 px-0 rounded-3">
            @php
                $menuMap = [
                    'epever' => ['label' => 'Epever', 'icon' => 'bi-lightning-charge'],
                    'epeverCharts' => ['label' => 'Epever Charts', 'icon' => 'bi-graph-up'],
                    'arduino' => ['label' => 'Arduino Sensors', 'icon' => 'bi-cpu'],
                    'arduinoCharts' => ['label' => 'Arduino Charts', 'icon' => 'bi-graph-up'],
                    'sensors' => ['label' => 'Sensors', 'icon' => 'bi-speedometer2'],
                    'sensorsCharts' => ['label' => 'Sensors Charts', 'icon' => 'bi-graph-up'],
                    'relayControl' => ['label' => 'Relay Control', 'icon' => 'bi-toggle-on'],
                ];

                $currentPath = request()->path();
                $activeKey = last(explode('/', $currentPath));
            @endphp

            @foreach ($menuMap as $key => $item)
                <a href="{{ url("/dashboard/$currentSite/$key") }}"
                    class="btn btn-sm d-flex align-items-center gap-1 {{ $key === $activeKey ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>
@endif
