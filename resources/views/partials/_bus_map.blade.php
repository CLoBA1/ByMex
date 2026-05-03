{{--
    Bus Map Partial — Croquis Real (49 asientos)
    Variables: $mode ('public'|'admin'), $seatData (admin only), $tour
--}}
@php
$busMap = [
    ['operator', '_', 'bus_title', 'stairs', '_'],
    ['empty', 'empty', 'aisle', 'extra_label', '_'],
    [1, 2, 'aisle', 3, 4],
    [5, 6, 'aisle', 7, 8],
    [9, 10, 'aisle', 11, 12],
    [13, 14, 'aisle', 15, 16],
    [17, 18, 'aisle', 'wc', '_'],
    [20, 19, 'aisle', 'door', '_'],
    [21, 22, 'aisle', 23, 24],
    [25, 26, 'aisle', 27, 28],
    [29, 30, 'aisle', 31, 32],
    [33, 34, 'aisle', 35, 36],
    [37, 38, 'aisle', 39, 40],
    [41, 42, 'aisle', 43, 44],
    [45, 46, 49, 47, 48],
];
$isAdmin = ($mode ?? 'public') === 'admin';
@endphp

<div class="rbus-container">
<div class="rbus-grid">
@foreach($busMap as $row)
    @foreach($row as $cell)
        @if($cell === '_')
            {{-- span continuation, skip --}}
        @elseif(is_int($cell))
            @php $sn = str_pad($cell, 2, '0', STR_PAD_LEFT); @endphp
            @if($isAdmin)
                @php $data = ($seatData ?? [])[$cell] ?? null; @endphp
                @if($data)
                    <div class="admin-seat admin-seat-occupied" style="background: {{ $data['bg'] }}; border: {{ $data['borderWidth'] }} solid {{ $data['border'] }};">
                        {{ $sn }}
                        <div class="tooltip-content">
                            <div class="tooltip-row"><strong>Asiento:</strong> {{ $sn }}</div>
                            <div class="tooltip-row"><strong>Pasajero:</strong> {{ $data['name'] }}</div>
                            @if($data['type'])<div class="tooltip-row"><strong>Categoría:</strong> {{ $data['type'] }}</div>@endif
                            <div class="tooltip-row"><strong>Abordaje:</strong> {{ $data['bp'] }}</div>
                            <div class="tooltip-row"><strong>Estado:</strong>
                                <span style="color: {{ $data['status'] == 'Pagada' ? '#4ade80' : '#fbbf24' }};">{{ mb_strtoupper($data['status']) }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="admin-seat admin-seat-free">{{ $sn }}</div>
                @endif
            @else
                <div class="seat" data-seat="{{ $cell }}">{{ $sn }}</div>
            @endif
        @elseif($cell === 'aisle')
            <div class="rbus-aisle"></div>
        @elseif($cell === 'operator')
            <div class="rbus-special rbus-operator"><i class="fa-solid fa-ban"></i><span>OPERADOR</span></div>
        @elseif($cell === 'bus_title')
            <div class="rbus-title">{{ $tour->title ?? 'TOUR' }}</div>
        @elseif($cell === 'stairs')
            <div class="rbus-special rbus-stairs"><i class="fa-solid fa-stairs"></i><span>ESCALERAS</span></div>
        @elseif($cell === 'extra_label')
            <div class="rbus-special rbus-extra-label"><small>Asiento Adicional</small></div>
        @elseif($cell === 'wc')
            <div class="rbus-special rbus-wc"><i class="fa-solid fa-restroom"></i><span>WC</span></div>
        @elseif($cell === 'door')
            <div class="rbus-special rbus-door"><i class="fa-solid fa-door-open"></i><span>PUERTA</span></div>
        @elseif($cell === 'empty')
            <div class="rbus-empty"></div>
        @endif
    @endforeach
@endforeach
</div>
</div>
