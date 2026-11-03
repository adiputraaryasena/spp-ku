{{-- Pengingat batas pengembalian. Butuh $pengingatTempo (koleksi Peminjaman dipinjam yang
     jatuh temponya dalam pengingat_hari ke depan atau sudah lewat). Dari view composer layouts.siswa. --}}
@if ($pengingatTempo->isNotEmpty())
    @php $adaTerlambat = $pengingatTempo->contains(fn ($m) => $m->sisaHari() < 0); @endphp
    <div class="pengingat-tempo {{ $adaTerlambat ? 'is-terlambat' : '' }}" role="alert">
        <div class="pengingat-tempo-inner">
            <div class="pengingat-tempo-judul">
                @include('partials.icon', ['name' => 'alert', 'size' => 18])
                <strong>
                    {{ $adaTerlambat ? 'Ada buku yang sudah melewati batas pengembalian' : 'Batas pengembalian buku hampir habis' }}
                </strong>
            </div>

            <ul class="pengingat-tempo-list">
                @foreach ($pengingatTempo as $m)
                    @php
                        $sisa = $m->sisaHari();
                        $tempo = $m->tanggal_jatuh_tempo;
                        $teks = match (true) {
                            $sisa < 0 => 'terlambat '.abs($sisa).' hari (denda berjalan Rp '.number_format($m->hitungDenda(), 0, ',', '.').')',
                            $sisa === 0 => 'jatuh tempo HARI INI',
                            $sisa === 1 => 'jatuh tempo BESOK ('.$tempo->translatedFormat('l, d M').')',
                            default => 'jatuh tempo '.$sisa.' hari lagi ('.$tempo->translatedFormat('l, d M').')',
                        };
                        $tutup = $sisa >= 0 && ! \App\Support\JamOperasional::bukaPada($tempo);
                    @endphp
                    <li>
                        <div class="isi">
                            <span class="strong">{{ $m->buku->judul }}</span>
                            @if ($m->eksemplar)<span class="kode-buku">{{ $m->eksemplar->kode }}</span>@endif
                            — {{ $teks }}.
                            @if ($tutup)
                                <span class="small">Perpustakaan tutup pada hari {{ $tempo->translatedFormat('l') }}; kembalikan di hari buka sebelumnya.</span>
                            @endif
                            @if ($m->bolehDiperpanjang())
                                <span class="small">Perpanjang {{ config('sppku.durasi_perpanjangan') }} hari atau kembalikan ke perpustakaan.</span>
                            @elseif ($sisa >= 0)
                                <span class="small">Perpanjangan sudah terpakai — kembalikan ke perpustakaan.</span>
                            @else
                                <span class="small">Segera kembalikan ke perpustakaan.</span>
                            @endif
                        </div>
                        @if ($m->bolehDiperpanjang())
                            <form method="POST" action="{{ route('siswa.perpanjang', $m) }}">
                                @csrf
                                <button class="btn btn-sm btn-masuk" type="submit">Perpanjang</button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
