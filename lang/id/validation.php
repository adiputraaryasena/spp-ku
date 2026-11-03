<?php

/*
| Pesan validasi Bahasa Indonesia untuk aturan yang dipakai SPP-Ku.
| Aturan lain otomatis memakai pesan bawaan Laravel (Bahasa Inggris).
*/

return [
    'required' => 'Kolom :attribute wajib diisi.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'email' => 'Format :attribute tidak valid.',
    'date' => 'Kolom :attribute bukan tanggal yang valid.',
    'after_or_equal' => 'Kolom :attribute harus tanggal hari ini atau setelahnya.',
    'integer' => 'Kolom :attribute harus berupa angka.',
    'boolean' => 'Kolom :attribute hanya menerima ya atau tidak.',
    'image' => 'Berkas :attribute harus berupa gambar.',
    'exists' => 'Pilihan :attribute tidak tersedia.',
    'unique' => 'Nilai :attribute sudah digunakan.',
    'in' => 'Pilihan :attribute tidak valid.',

    'min' => [
        'string' => 'Kolom :attribute minimal :min karakter.',
        'numeric' => 'Kolom :attribute minimal :min.',
        'file' => 'Berkas :attribute minimal :min kilobita.',
        'array' => 'Kolom :attribute minimal berisi :min item.',
    ],
    'max' => [
        'string' => 'Kolom :attribute maksimal :max karakter.',
        'numeric' => 'Kolom :attribute maksimal :max.',
        'file' => 'Ukuran berkas :attribute maksimal :max kilobita.',
        'array' => 'Kolom :attribute maksimal berisi :max item.',
    ],
    'mimes' => 'Berkas :attribute harus bertipe: :values.',

    // Dipakai oleh aturan Illuminate\Validation\Rules\Password
    'password' => [
        'letters' => 'Kolom :attribute harus memuat sedikitnya satu huruf.',
        'mixed' => 'Kolom :attribute harus memuat huruf besar dan huruf kecil.',
        'numbers' => 'Kolom :attribute harus memuat sedikitnya satu angka.',
        'symbols' => 'Kolom :attribute harus memuat sedikitnya satu simbol.',
        'uncompromised' => 'Kolom :attribute pernah bocor pada kebocoran data. Pilih yang lain.',
    ],

    'attributes' => [
        'kode' => 'NIS / kode petugas',
        'nama' => 'nama',
        'email' => 'email',
        'kelas' => 'kelas',
        'role' => 'peran',
        'password' => 'kata sandi',
        'password_lama' => 'kata sandi lama',
        'nomor_buku' => 'nomor buku',
        'judul' => 'judul buku',
        'pengarang' => 'pengarang',
        'penerbit' => 'penerbit',
        'tahun_terbit' => 'tahun terbit',
        'kategori_id' => 'kategori',
        'jumlah_total' => 'jumlah eksemplar',
        'rak' => 'lokasi rak',
        'deskripsi' => 'deskripsi',
        'cover' => 'sampul buku',
        'keterangan' => 'keterangan',
        'tanggal_pengajuan' => 'tanggal pengajuan',
        'tanggal_kembali' => 'tanggal pengembalian',
        'catatan_siswa' => 'catatan',
        'catatan_admin' => 'catatan petugas',
        'dari' => 'tanggal awal',
        'sampai' => 'tanggal akhir',
    ],

    'custom' => [],
];
