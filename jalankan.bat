@echo off
title SPP-Ku
echo =========================================================
echo   SPP-Ku - Sistem Peminjaman ^& Pengembalian Buku
echo =========================================================
echo.
echo  PASTIKAN SUDAH: MySQL di XAMPP menyala, dan berkas
echo  database\spp_ku.sql sudah diimpor lewat phpMyAdmin.
echo.
pause
if not exist vendor ( echo [1/3] Memasang pustaka Laravel... & call composer install )
if not exist .env ( echo [2/3] Menyiapkan .env... & copy .env.example .env >nul & php artisan key:generate )
echo [3/3] Server berjalan di http://localhost:8000  (Ctrl+C untuk berhenti)
php artisan serve
pause
