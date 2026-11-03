@php
    $isi = $isiData ?? false;
    $d = fn (string $field) => $isi ? ' data-isi="'.$field.'"' : '';
    $v = fn (string $field) => $isi ? '' : old($field, '');
@endphp

<div class="form-row">
    <div class="field">
        <label for="{{ $prefix }}-nomor">Nomor buku (unik)</label>
        <input id="{{ $prefix }}-nomor" class="input" type="text" name="nomor_buku" required maxlength="40"
               placeholder="Contoh: RPL-008" value="{{ $v('nomor_buku') }}" {!! $d('nomor_buku') !!}>
        <p class="hint">Nomor yang sudah terpakai akan ditolak sistem.</p>
    </div>
    <div class="field">
        <label for="{{ $prefix }}-kategori">Kategori</label>
        <select id="{{ $prefix }}-kategori" class="select" name="kategori_id" required {!! $d('kategori_id') !!}>
            @foreach ($kategoris as $k)
                <option value="{{ $k->id }}" @selected(! $isi && (int) old('kategori_id') === $k->id)>{{ $k->nama }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="field">
    <label for="{{ $prefix }}-judul">Judul buku</label>
    <input id="{{ $prefix }}-judul" class="input" type="text" name="judul" required maxlength="200"
           value="{{ $v('judul') }}" {!! $d('judul') !!}>
</div>

<div class="form-row">
    <div class="field">
        <label for="{{ $prefix }}-pengarang">Nama pengarang</label>
        <input id="{{ $prefix }}-pengarang" class="input" type="text" name="pengarang" maxlength="120"
               value="{{ $v('pengarang') }}" {!! $d('pengarang') !!}>
    </div>
    <div class="field">
        <label for="{{ $prefix }}-penerbit">Nama penerbit</label>
        <input id="{{ $prefix }}-penerbit" class="input" type="text" name="penerbit" maxlength="120"
               value="{{ $v('penerbit') }}" {!! $d('penerbit') !!}>
    </div>
</div>

<div class="form-row">
    <div class="field">
        <label for="{{ $prefix }}-tahun">Tahun terbit</label>
        <input id="{{ $prefix }}-tahun" class="input" type="number" name="tahun_terbit"
               min="1900" max="{{ date('Y') + 1 }}" value="{{ $v('tahun_terbit') }}" {!! $d('tahun_terbit') !!}>
    </div>
    <div class="field">
        <label>Jumlah eksemplar</label>
        <div class="input" style="display:flex;align-items:center;background:var(--canvas);color:var(--muted)" aria-live="polite"
             @if (! $isi) data-rentang-hasil="{{ $prefix }}" @endif>
            @if ($isi)
                Dikelola lewat tombol “Kode”
            @else
                Dihitung dari rentang kode
            @endif
        </div>
    </div>
</div>

@if (! $isi)
    <div class="rentang-kode" data-rentang="{{ $prefix }}">
        <div class="rentang-kode-judul">Rentang kode eksemplar</div>
        <div class="form-row" style="margin-bottom:0">
            <div class="field">
                <label for="{{ $prefix }}-kode-awal">Dari kode</label>
                <input id="{{ $prefix }}-kode-awal" class="input mono" type="text" name="kode_awal" required maxlength="60"
                       placeholder="Contoh: RPL-008-001" value="{{ old('kode_awal') }}" data-rentang-awal autocomplete="off">
            </div>
            <div class="field">
                <label for="{{ $prefix }}-kode-akhir">Sampai kode</label>
                <input id="{{ $prefix }}-kode-akhir" class="input mono" type="text" name="kode_akhir" required maxlength="60"
                       placeholder="Contoh: RPL-008-010" value="{{ old('kode_akhir') }}" data-rentang-akhir autocomplete="off">
            </div>
        </div>
        <p class="hint" data-rentang-info style="margin-top:8px">
            Satu kode untuk satu fisik buku. Isi kode pertama dan terakhir; sistem membuat semua kode di antaranya.
            Kode yang sudah dipakai buku mana pun akan ditolak, sehingga tidak ada kode ganda.
            Buku dipinjamkan mulai dari kode terkecil.
        </p>
    </div>
@else
    <p class="hint" style="margin:0 0 16px">
        Untuk menambah, menonaktifkan, atau melihat kode eksemplar, tutup jendela ini lalu klik tombol <strong>Kode</strong> pada baris buku.
    </p>
@endif

<div class="form-row">
    <div class="field">
        <label for="{{ $prefix }}-rak">Lokasi rak</label>
        <input id="{{ $prefix }}-rak" class="input" type="text" name="rak" maxlength="30"
               placeholder="Contoh: A-01" value="{{ $v('rak') }}" {!! $d('rak') !!}>
    </div>
    <div class="field">
        <label for="{{ $prefix }}-cover">Sampul buku (opsional)</label>
        <input id="{{ $prefix }}-cover" class="input" type="file" name="cover" accept="image/*"
               style="padding:10px 12px;height:auto">
        <p class="hint">JPG/PNG/WebP, maksimal 2 MB, rasio 3:4.</p>
    </div>
</div>

<div class="field" style="margin-bottom:0">
    <label for="{{ $prefix }}-deskripsi">Deskripsi</label>
    <textarea id="{{ $prefix }}-deskripsi" class="textarea" name="deskripsi" maxlength="2000"
              placeholder="Ringkasan isi buku yang akan dibaca siswa di katalog.">{{ $v('deskripsi') }}</textarea>
</div>
