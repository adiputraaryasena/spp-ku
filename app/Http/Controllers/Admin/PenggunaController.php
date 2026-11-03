<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** FR-02 — CRUD pengguna (siswa & petugas). */
class PenggunaController extends Controller
{
    /** Batas baris sekali impor massal, supaya satu permintaan tidak terlalu berat. */
    private const MAKS_BARIS_IMPOR = 500;

    private const ATURAN_TELEPON = ['nullable', 'string', 'max:20', 'regex:/^[0-9+\\-\\s()]{8,20}$/'];

    private const PESAN_TELEPON = 'Nomor telepon hanya boleh berisi angka, spasi, +, -, dan tanda kurung (8–20 karakter).';

    public function index(Request $request): View
    {
        $kata = $request->query('cari');

        $users = User::query()
            ->when($kata, fn ($q) => $q->where(function ($sub) use ($kata) {
                $sub->where('nama', 'like', "%{$kata}%")
                    ->orWhere('kode', 'like', "%{$kata}%")
                    ->orWhere('kelas', 'like', "%{$kata}%")
                    ->orWhere('telepon', 'like', "%{$kata}%");
            }))
            ->withCount('peminjamans')
            ->orderBy('role')
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pengguna', compact('users', 'kata'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:30', 'unique:users,kode'],
            'nama' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:120', 'unique:users,email'],
            'telepon' => self::ATURAN_TELEPON,
            'kelas' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(['admin', 'siswa'])],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'kode.unique' => 'NIS / kode ini sudah dipakai akun lain.',
            'telepon.regex' => self::PESAN_TELEPON,
        ], [
            'kode' => 'NIS / kode petugas',
            'telepon' => 'nomor telepon',
        ]);

        // Kata sandi dari petugas selalu bersifat sementara: pengguna wajib
        // menggantinya sendiri saat pertama kali masuk.
        $data['harus_ganti_password'] = true;

        User::create($data);

        return back()->with('sukses', 'Akun '.$data['nama'].' dibuat. '
            .'Sampaikan kata sandi sementaranya — sistem akan meminta yang bersangkutan menggantinya saat login.');
    }

    public function update(Request $request, User $pengguna): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:30', Rule::unique('users', 'kode')->ignore($pengguna->id)],
            'nama' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:120', Rule::unique('users', 'email')->ignore($pengguna->id)],
            'telepon' => self::ATURAN_TELEPON,
            'kelas' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(['admin', 'siswa'])],
            'aktif' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'telepon.regex' => self::PESAN_TELEPON,
        ], [
            'telepon' => 'nomor telepon',
        ]);

        $data['aktif'] = $request->boolean('aktif');

        if (blank($data['password'])) {
            unset($data['password']);
        } else {
            // Reset oleh petugas hanya menghasilkan kata sandi sementara.
            $data['harus_ganti_password'] = true;
            $data['password_diganti_pada'] = null;
        }

        $pengguna->update($data);

        $pesan = isset($data['password'])
            ? 'Kata sandi sementara untuk '.$pengguna->nama.' disetel. Ia wajib menggantinya saat login berikutnya.'
            : 'Data pengguna diperbarui.';

        return back()->with('sukses', $pesan);
    }

    public function destroy(Request $request, User $pengguna): RedirectResponse
    {
        if ($pengguna->id === $request->user()->id) {
            return back()->with('gagal', 'Anda tidak bisa menghapus akun yang sedang dipakai.');
        }

        if ($pengguna->peminjamans()->exists()) {
            return back()->with('gagal', 'Pengguna ini punya riwayat transaksi. Nonaktifkan akunnya saja agar riwayat tetap utuh.');
        }

        $pengguna->delete();

        return back()->with('sukses', 'Pengguna dihapus.');
    }

    /* ------------------------------------------------------------ Impor massal */

    /** Unduh contoh berkas CSV (dibuka di Excel, pemisah titik koma). */
    public function template(): StreamedResponse
    {
        $isi = "\xEF\xBB\xBF"
            ."nis;nama;kelas;email;telepon\r\n"
            ."0012345678;Contoh Siswa Satu;X RPL A;;081234567890\r\n"
            ."0012345679;Contoh Siswa Dua;X RPL A;;\r\n";

        return response()->streamDownload(function () use ($isi) {
            echo $isi;
        }, 'template-impor-siswa.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Membuat banyak akun siswa sekaligus dari berkas CSV atau teks tempelan (copy-paste dari Excel).
     * Semua akun memakai kata sandi sementara yang sama dan wajib menggantinya saat pertama masuk.
     */
    public function impor(Request $request): RedirectResponse
    {
        $request->validate([
            'berkas' => ['nullable', 'file', 'max:2048'],
            'daftar' => ['nullable', 'string', 'max:300000'],
            'kelas' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6', 'max:100'],
        ], [], [
            'berkas' => 'berkas',
            'daftar' => 'daftar akun',
            'kelas' => 'kelas bawaan',
            'password' => 'kata sandi sementara',
        ]);

        $isi = '';

        if ($request->hasFile('berkas')) {
            $berkas = $request->file('berkas');
            $ekstensi = strtolower((string) $berkas->getClientOriginalExtension());

            if (! in_array($ekstensi, ['csv', 'txt', 'tsv'], true)) {
                return back()->withInput()->with('buka_impor', true)
                    ->withErrors(['berkas' => 'Berkas harus berformat .csv (di Excel: Save As → CSV). Atau tempel daftar langsung di kotak teks.']);
            }

            $isi = (string) file_get_contents($berkas->getRealPath());
        } elseif (filled($request->input('daftar'))) {
            $isi = (string) $request->input('daftar');
        }

        if (trim($isi) === '') {
            return back()->withInput()->with('buka_impor', true)
                ->withErrors(['daftar' => 'Unggah berkas CSV atau tempel daftar siswa terlebih dahulu.']);
        }

        $baris = $this->urai($isi);

        if (count($baris) === 0) {
            return back()->withInput()->with('buka_impor', true)
                ->withErrors(['daftar' => 'Tidak ada baris data yang terbaca. Pastikan tiap baris berisi minimal NIS dan nama.']);
        }

        if (count($baris) > self::MAKS_BARIS_IMPOR) {
            return back()->withInput()->with('buka_impor', true)
                ->withErrors(['daftar' => 'Maksimal '.self::MAKS_BARIS_IMPOR.' akun sekali impor. Bagi daftar menjadi beberapa bagian.']);
        }

        $kelasBawaan = trim((string) $request->input('kelas'));

        // Cek bentrok dengan akun yang sudah ada (satu kueri per jenis, bukan per baris).
        $kodeAda = User::whereIn('kode', array_column($baris, 'kode'))->pluck('kode')->map(fn ($k) => mb_strtolower($k))->all();
        $emailList = array_values(array_filter(array_column($baris, 'email')));
        $emailAda = $emailList
            ? User::whereIn('email', $emailList)->pluck('email')->map(fn ($e) => mb_strtolower($e))->all()
            : [];

        $sudahDipakaiKode = array_flip($kodeAda);
        $sudahDipakaiEmail = array_flip($emailAda);

        $siapSimpan = [];
        $dilewati = [];
        $hash = Hash::make($request->input('password')); // satu hash dipakai semua: cepat untuk ratusan akun
        $sekarang = now();

        foreach ($baris as $b) {
            $alasan = $this->periksaBaris($b, $sudahDipakaiKode, $sudahDipakaiEmail);

            if ($alasan !== null) {
                $dilewati[] = ['baris' => $b['no'], 'kode' => $b['kode'], 'nama' => $b['nama'], 'alasan' => $alasan];
                continue;
            }

            $sudahDipakaiKode[mb_strtolower($b['kode'])] = true;
            if ($b['email'] !== '') {
                $sudahDipakaiEmail[mb_strtolower($b['email'])] = true;
            }

            $siapSimpan[] = [
                'kode' => $b['kode'],
                'nama' => $b['nama'],
                'email' => $b['email'] !== '' ? $b['email'] : null,
                'telepon' => $b['telepon'] !== '' ? $b['telepon'] : null,
                'kelas' => $b['kelas'] !== '' ? $b['kelas'] : ($kelasBawaan !== '' ? $kelasBawaan : null),
                'role' => 'siswa',
                'password' => $hash,
                'aktif' => 1,
                'harus_ganti_password' => 1,
                'password_diganti_pada' => null,
                'created_at' => $sekarang,
                'updated_at' => $sekarang,
            ];
        }

        if ($siapSimpan) {
            DB::transaction(function () use ($siapSimpan) {
                foreach (array_chunk($siapSimpan, 100) as $potong) {
                    User::insert($potong);
                }
            });
        }

        $hasil = [
            'dibuat' => count($siapSimpan),
            'dilewati' => $dilewati,
            'total' => count($baris),
        ];

        $pesan = count($siapSimpan).' akun siswa berhasil dibuat'
            .(count($dilewati) ? ', '.count($dilewati).' baris dilewati (lihat rincian di bawah).' : '.');

        $balasan = back()->with('impor_hasil', $hasil);

        return count($siapSimpan) > 0
            ? $balasan->with('sukses', $pesan)
            : $balasan->with('gagal', 'Tidak ada akun yang dibuat. Periksa rincian baris yang dilewati.');
    }

    /** Validasi satu baris impor; mengembalikan alasan penolakan atau null bila lolos. */
    private function periksaBaris(array $b, array $kodeDipakai, array $emailDipakai): ?string
    {
        if ($b['kode'] === '' || $b['nama'] === '') {
            return 'NIS dan nama wajib diisi.';
        }

        if (mb_strlen($b['kode']) > 30 || ! preg_match('/^[A-Za-z0-9._-]+$/', $b['kode'])) {
            return 'NIS hanya boleh huruf/angka (tanpa spasi), maksimal 30 karakter.';
        }

        if (mb_strlen($b['nama']) > 120) {
            return 'Nama lebih dari 120 karakter.';
        }

        if (mb_strlen($b['kelas']) > 30) {
            return 'Kelas lebih dari 30 karakter.';
        }

        if (isset($kodeDipakai[mb_strtolower($b['kode'])])) {
            return 'NIS sudah terdaftar (atau dobel di daftar ini).';
        }

        if ($b['email'] !== '') {
            if (mb_strlen($b['email']) > 120 || ! filter_var($b['email'], FILTER_VALIDATE_EMAIL)) {
                return 'Format email tidak valid.';
            }

            if (isset($emailDipakai[mb_strtolower($b['email'])])) {
                return 'Email sudah dipakai akun lain.';
            }
        }

        if ($b['telepon'] !== '' && ! preg_match('/^[0-9+\-\s()]{8,20}$/', $b['telepon'])) {
            return 'Format nomor telepon tidak valid.';
        }

        return null;
    }

    /**
     * Mengurai teks CSV/tempelan menjadi baris terstruktur.
     * Pemisah (koma, titik koma, atau tab) terdeteksi otomatis; baris judul (nis;nama;...) dikenali.
     * Tanpa baris judul, urutan kolom: NIS, nama, kelas, email, telepon.
     *
     * @return array<int, array{no:int,kode:string,nama:string,kelas:string,email:string,telepon:string}>
     */
    private function urai(string $isi): array
    {
        $isi = preg_replace('/^\xEF\xBB\xBF/', '', $isi);

        if (! mb_check_encoding($isi, 'UTF-8')) {
            $isi = mb_convert_encoding($isi, 'UTF-8', 'Windows-1252');
        }

        $semuaBaris = preg_split('/\r\n|\r|\n/', $isi);

        $pertama = '';
        foreach ($semuaBaris as $b) {
            if (trim($b) !== '') {
                $pertama = $b;
                break;
            }
        }

        $pemisah = ',';
        $terbanyak = 0;
        foreach (["\t", ';', ','] as $kandidat) {
            $jumlah = substr_count($pertama, $kandidat);
            if ($jumlah > $terbanyak) {
                $terbanyak = $jumlah;
                $pemisah = $kandidat;
            }
        }

        $kolom = ['kode', 'nama', 'kelas', 'email', 'telepon'];
        $alias = [
            'nis' => 'kode', 'nisn' => 'kode', 'kode' => 'kode', 'no induk' => 'kode',
            'nama' => 'nama', 'nama lengkap' => 'nama', 'nama siswa' => 'nama',
            'kelas' => 'kelas', 'rombel' => 'kelas',
            'email' => 'email', 'e-mail' => 'email',
            'telepon' => 'telepon', 'telp' => 'telepon', 'hp' => 'telepon', 'no hp' => 'telepon',
            'no. hp' => 'telepon', 'nomor telepon' => 'telepon', 'no telepon' => 'telepon',
        ];

        $hasil = [];
        $peta = null;
        $nomor = 0;
        $sudahPeriksaJudul = false;

        foreach ($semuaBaris as $mentah) {
            $nomor++;

            if (trim($mentah) === '') {
                continue;
            }

            $sel = array_map(fn ($v) => trim((string) $v), str_getcsv($mentah, $pemisah));

            if (! $sudahPeriksaJudul) {
                $sudahPeriksaJudul = true;
                $cocok = [];

                foreach ($sel as $i => $judul) {
                    $kunci = mb_strtolower($judul);
                    if (isset($alias[$kunci])) {
                        $cocok[$i] = $alias[$kunci];
                    }
                }

                // Dianggap baris judul bila minimal kolom NIS dan nama dikenali.
                if (in_array('kode', $cocok, true) && in_array('nama', $cocok, true)) {
                    $peta = $cocok;
                    continue;
                }
            }

            $baris = ['no' => $nomor, 'kode' => '', 'nama' => '', 'kelas' => '', 'email' => '', 'telepon' => ''];

            foreach ($sel as $i => $nilai) {
                $nama = $peta !== null ? ($peta[$i] ?? null) : ($kolom[$i] ?? null);
                if ($nama !== null) {
                    $baris[$nama] = $nilai;
                }
            }

            $hasil[] = $baris;
        }

        return $hasil;
    }
}
