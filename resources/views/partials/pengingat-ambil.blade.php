{{-- Pengingat: pengajuan pending wajib diambil di perpustakaan dalam 24 jam, bila tidak otomatis ditolak.
     Butuh variabel $menunggu (koleksi Peminjaman berstatus pending). --}}
@if ($menunggu->isNotEmpty())
    <div class="alert alert-warning pengingat-ambil" role="status">
        @include('partials.icon', ['name' => 'clock', 'size' => 20])
        <div>
            <strong>Segera ke perpustakaan untuk mengambil buku.</strong>
            Pengajuan yang tidak diambil dalam {{ config('sppku.batas_ambil_jam') }} jam akan ditolak otomatis.
            <ul>
                @foreach ($menunggu as $m)
                    @php $batas = $m->batasAmbil(); @endphp
                    <li>
                        <span class="strong">{{ $m->buku->judul }}</span> —
                        ambil sebelum <strong>{{ $batas->translatedFormat('l, d F Y, H.i') }} WIB</strong>
                        @php $sisaJam = max(0, (int) floor(now()->diffInMinutes($batas, false) / 60)); @endphp
                        <span class="muted">(sisa kurang lebih {{ $sisaJam }} jam)</span>
                    </li>
                @endforeach
            </ul>
            <div class="small mt-1">@include('partials.jam-status')</div>
        </div>
    </div>
@endif
