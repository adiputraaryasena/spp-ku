<footer class="site-foot">
    <div class="site-foot-inner">
        <div>
            <div class="brand-foot">{{ config('sppku.sekolah.perpustakaan') }}</div>
            <p class="small">{{ config('sppku.sekolah.nama') }}</p>
            <p class="small mt-1">@include('partials.jam-status')</p>
        </div>
        <div>
            <div class="brand-foot">Jam operasional</div>
            <ul class="jam-list jam-list-ringkas">
                @foreach (\App\Support\JamOperasional::jadwal() as $j)
                    <li @class(['is-today' => $j['hari_ini'], 'is-libur' => $j['libur']])>
                        <span class="hari">{{ $j['hari'] }}</span>
                        <span class="jam">{{ $j['libur'] ? 'Tutup' : $j['buka'].' – '.$j['tutup'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</footer>
