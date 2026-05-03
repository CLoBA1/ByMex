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

<style>
/* ==========================================================================
   Real Bus Layout (rbus-) — Croquis fiel al autobús real
   ========================================================================== */
.rbus-container {
    width: fit-content;
    margin: 0 auto;
    padding: 30px 20px;
    border-radius: 40px 40px 10px 10px;
    border: 3px solid rgba(148,163,184,0.3);
    background: rgba(255,255,255,0.02);
    box-shadow: inset 0 0 20px rgba(0,0,0,0.1), 0 10px 30px rgba(0,0,0,0.2);
    position: relative;
}

.admin-bus-map .rbus-container {
    background: #f8fafc;
    border-color: #cbd5e1;
    box-shadow: inset 0 0 20px rgba(0,0,0,0.02);
}

.rbus-grid {
    display: grid;
    grid-template-columns: repeat(5, 42px);
    gap: 12px 10px;
    justify-content: center;
}
.rbus-aisle { min-height: 1px; }
.rbus-empty { min-height: 0; }

.rbus-special {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    grid-column: span 2;
    padding: 0;
    border-radius: 8px;
    font-size: 0.55rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    gap: 2px;
    height: 100%;
    min-height: 42px;
}
.rbus-special i { font-size: 0.8rem; }

.rbus-operator {
    background: rgba(245,158,11,0.1);
    border: 1px dashed rgba(245,158,11,0.4);
    color: #fbbf24;
}
.rbus-stairs {
    background: rgba(234,179,8,0.1);
    border: 1px solid rgba(234,179,8,0.3);
    color: #facc15;
}
.rbus-wc {
    background: rgba(34,197,94,0.08);
    border: 1px solid rgba(34,197,94,0.25);
    color: #4ade80;
}
.rbus-door {
    background: rgba(148,163,184,0.08);
    border: 1px dashed rgba(148,163,184,0.3);
    color: #94a3b8;
}
.rbus-extra-label {
    font-size: 0.5rem;
    color: rgba(250,204,21,0.7);
    border: 1px dashed rgba(250,204,21,0.3);
    background: rgba(250,204,21,0.05);
}
.rbus-title {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.55rem;
    font-weight: 800;
    color: var(--primary, #d62828);
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Admin overrides */
.admin-bus-map .rbus-operator {
    background: #fef3c7; border-color: #f59e0b; color: #92400e;
}
.admin-bus-map .rbus-stairs {
    background: #fef08a; border-color: #eab308; color: #854d0e;
}
.admin-bus-map .rbus-wc {
    background: #dcfce7; border-color: #86efac; color: #166534;
}
.admin-bus-map .rbus-door {
    background: #f1f5f9; border-color: #94a3b8; color: #475569;
}
.admin-bus-map .rbus-extra-label {
    color: #854d0e; border-color: #eab308; background: #fef9c3;
}
.admin-bus-map .rbus-title {
    color: var(--navy, #0d1b2a);
}
</style>

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
