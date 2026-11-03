#!/usr/bin/env bash
set -e
echo "=== SPP-Ku ==="
echo "Pastikan MySQL menyala dan database/spp_ku.sql sudah diimpor ke phpMyAdmin."
[ -d vendor ] || { echo "[1/3] Memasang pustaka Laravel..."; composer install; }
[ -f .env ]   || { echo "[2/3] Menyiapkan .env..."; cp .env.example .env; php artisan key:generate; }
echo "[3/3] Server berjalan di http://localhost:8000"
php artisan serve
