/* SPP-Ku — interaksi ringan: modal, toast, konfirmasi hapus. */
(function () {
  'use strict';

  /* ---------------------------------------------------------- Modal */
  function bukaModal(id, data) {
    var modal = document.getElementById(id);
    if (!modal) return;

    if (data) {
      Object.keys(data).forEach(function (key) {
        var field = modal.querySelector('[data-isi="' + key + '"]');
        if (!field) return;
        if ('value' in field && field.tagName !== 'DIV') {
          field.value = data[key] === null ? '' : data[key];
        } else {
          field.textContent = data[key];
        }
      });

      var form = modal.querySelector('form[data-action]');
      if (form && data.__action) form.setAttribute('action', data.__action);
    }

    modal.hidden = false;
    document.body.style.overflow = 'hidden';

    var fokus = modal.querySelector('input:not([type=hidden]), select, textarea, button');
    if (fokus) fokus.focus();
  }

  function tutupModal(modal) {
    if (!modal) return;
    modal.hidden = true;
    document.body.style.overflow = '';
  }

  document.addEventListener('click', function (e) {
    var pemicu = e.target.closest('[data-modal]');
    if (pemicu) {
      e.preventDefault();
      var isi = pemicu.getAttribute('data-isi-json');
      bukaModal(pemicu.getAttribute('data-modal'), isi ? JSON.parse(isi) : null);
      return;
    }

    if (e.target.closest('[data-tutup]')) {
      e.preventDefault();
      tutupModal(e.target.closest('.modal'));
      return;
    }

    if (e.target.classList.contains('modal')) tutupModal(e.target);
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal:not([hidden])').forEach(tutupModal);
    }
  });

  /* ------------------------------------------------- Konfirmasi hapus */
  document.addEventListener('submit', function (e) {
    var pesan = e.target.getAttribute('data-konfirmasi');
    if (pesan && !window.confirm(pesan)) e.preventDefault();
  });

  /* ------------------------------------------------------------ Toast */
  document.querySelectorAll('.toast').forEach(function (toast) {
    var timer = setTimeout(function () { toast.remove(); }, 6000);
    var tombol = toast.querySelector('button');
    if (tombol) {
      tombol.addEventListener('click', function () {
        clearTimeout(timer);
        toast.remove();
      });
    }
  });

  /* ------------------------------------------- Menu geser (admin, mobile) */
  var bodi = document.body;
  var tombolMenu = document.querySelector('[data-menu-toggle]');

  function aturMenu(buka) {
    bodi.classList.toggle('menu-terbuka', buka);
    if (tombolMenu) {
      tombolMenu.setAttribute('aria-expanded', buka ? 'true' : 'false');
      tombolMenu.setAttribute('aria-label', buka ? 'Tutup menu' : 'Buka menu');
    }
  }

  document.addEventListener('click', function (e) {
    if (e.target.closest('[data-menu-toggle]')) {
      aturMenu(!bodi.classList.contains('menu-terbuka'));
    } else if (e.target.closest('[data-menu-tutup]') || e.target.closest('.sidebar a.side-link')) {
      aturMenu(false);
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') aturMenu(false);
  });

  window.addEventListener('resize', function () {
    if (window.innerWidth > 900) aturMenu(false);
  });

  /* ----------------------------------- Label kolom tabel untuk tampilan ponsel
     Di layar sempit tabel berubah menjadi kartu; tiap sel memakai judul kolomnya
     sebagai label. Diisi otomatis dari <thead> sehingga tidak perlu mengubah tiap tabel. */
  document.querySelectorAll('table.data').forEach(function (tabel) {
    var judul = Array.prototype.map.call(
      tabel.querySelectorAll('thead th'),
      function (th) { return th.textContent.replace(/\s+/g, ' ').trim(); }
    );

    tabel.querySelectorAll('tbody tr').forEach(function (baris) {
      Array.prototype.forEach.call(baris.children, function (sel, i) {
        if (sel.tagName !== 'TD' || sel.colSpan > 1) return;

        if (judul[i]) sel.setAttribute('data-label', judul[i]);

        if (!sel.querySelector(':scope > .td-isi')) {
          var bungkus = document.createElement('div');
          bungkus.className = 'td-isi';
          while (sel.firstChild) bungkus.appendChild(sel.firstChild);
          sel.appendChild(bungkus);
        }
      });
    });
  });

  /* ------------------------------------- Pratinjau rentang kode eksemplar
     Menghitung jumlah kode dari "dari kode" s.d. "sampai kode" sebelum disimpan,
     dengan aturan yang sama seperti di server (awalan sama, diakhiri angka). */
  function pecahKode(k) {
    var m = /^(.*?)(\d+)$/.exec((k || '').trim());
    return m ? { awalan: m[1], angka: m[2] } : null;
  }

  function perbaruiRentang(wadah) {
    var awal = wadah.querySelector('[data-rentang-awal]');
    var akhir = wadah.querySelector('[data-rentang-akhir]');
    var info = wadah.querySelector('[data-rentang-info]');
    var hasil = document.querySelector('[data-rentang-hasil="' + wadah.getAttribute('data-rentang') + '"]');
    if (!awal || !akhir) return;

    var teks = null, jumlah = null, galat = false;
    var a = pecahKode(awal.value), b = pecahKode(akhir.value);

    if (awal.value.trim() !== '' && akhir.value.trim() !== '') {
      if (!a || !b) {
        teks = 'Kode harus diakhiri angka, contoh BK-101-001.'; galat = true;
      } else if (a.awalan !== b.awalan) {
        teks = 'Awalan kode awal dan akhir harus sama.'; galat = true;
      } else if (parseInt(b.angka, 10) < parseInt(a.angka, 10)) {
        teks = 'Kode akhir harus lebih besar dari kode awal.'; galat = true;
      } else {
        jumlah = parseInt(b.angka, 10) - parseInt(a.angka, 10) + 1;
        if (jumlah > 500) { teks = 'Maksimal 500 kode sekali input.'; galat = true; jumlah = null; }
        else teks = jumlah + ' kode akan dibuat: ' + awal.value.trim() + ' sampai ' + akhir.value.trim() + '.';
      }
    }

    if (info && teks) {
      info.textContent = teks;
      info.style.color = galat ? 'var(--danger)' : 'var(--success)';
    }
    if (hasil) hasil.textContent = jumlah ? jumlah + ' eksemplar' : 'Dihitung dari rentang kode';
  }

  document.querySelectorAll('[data-rentang]').forEach(function (wadah) {
    wadah.addEventListener('input', function () { perbaruiRentang(wadah); });
    perbaruiRentang(wadah);
  });

  /* Buka modal otomatis bila validasi server gagal */
  var gagal = document.querySelector('[data-buka-otomatis]');
  if (gagal) bukaModal(gagal.getAttribute('data-buka-otomatis'));
})();
