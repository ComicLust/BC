#!/bin/sh

# Laravel cache temizle
php artisan optimize:clear

# Migrationları çalıştır (Production'da dikkatli olunmalı, force ile çalıştırıyoruz)
php artisan migrate --force

# Storage link oluştur (varsa silip tekrar oluşturur)
php artisan storage:link

# Supervisor'ı başlat
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
