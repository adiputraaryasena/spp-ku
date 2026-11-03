{{-- Lencana kecil: Buka / Tutup + keterangan singkat. --}}
@php $statusJam = \App\Support\JamOperasional::status(); @endphp
<span class="jam-status {{ $statusJam['buka'] ? 'is-buka' : 'is-tutup' }}">
    <span class="titik" aria-hidden="true"></span>
    <strong>{{ $statusJam['buka'] ? 'Perpustakaan buka' : 'Perpustakaan tutup' }}</strong>
    <span class="rinci">· {{ $statusJam['detail'] }}</span>
</span>
