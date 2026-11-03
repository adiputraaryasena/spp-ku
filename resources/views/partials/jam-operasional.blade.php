{{-- Jam buka & tutup perpustakaan + status "sedang buka/tutup" saat ini.
     Jadwal diatur di config/sppku.php (kunci jam_operasional). --}}
@php
    $statusJam = \App\Support\JamOperasional::status();
    $jadwalJam = \App\Support\JamOperasional::jadwal();
@endphp
<section class="card jam-card" aria-labelledby="judul-jam">
    <div class="card-head">
        @include('partials.icon', ['name' => 'clock', 'size' => 18])
        <h2 class="grow" id="judul-jam">Jam operasional</h2>
        <span class="badge {{ $statusJam['buka'] ? 'badge-tersedia' : 'badge-habis' }}">
            {{ $statusJam['buka'] ? 'Sedang buka' : 'Sedang tutup' }}
        </span>
    </div>
    <div class="card-body">
        <p class="small muted mb-2">{{ $statusJam['detail'] }}</p>
        <ul class="jam-list">
            @foreach ($jadwalJam as $j)
                <li @class(['is-today' => $j['hari_ini'], 'is-libur' => $j['libur']])>
                    <span class="hari">{{ $j['hari'] }}@if ($j['hari_ini']) <em>hari ini</em>@endif</span>
                    <span class="jam">
                        @if ($j['libur'])
                            Tutup
                        @else
                            {{ $j['buka'] }} – {{ $j['tutup'] }} WIB
                        @endif
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
</section>
