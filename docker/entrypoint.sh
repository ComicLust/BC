#!/bin/sh

# SQLite veritabanı dosyasını kontrol et ve yoksa oluştur
if [ ! -f /var/www/database/database.sqlite ]; then
    echo "Creating database.sqlite..."
    touch /var/www/database/database.sqlite
fi

# İzinleri ayarla (Tekrar ve garanti olsun diye)
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/database
chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/database

# Laravel cache temizle
php artisan optimize:clear

# Migrationları çalıştır (Production'da dikkatli olunmalı, force ile çalıştırıyoruz)
php artisan migrate --force

# Storage link oluştur (varsa silip tekrar oluşturur)
php artisan storage:link

# Supervisor'ı başlat
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
