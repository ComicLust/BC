#!/bin/sh

# Veritabanı klasörünün izinlerini ayarla (dosya oluşturmadan önce)
mkdir -p /var/www/database
chown -R www-data:www-data /var/www/database
chmod -R 775 /var/www/database

# SQLite veritabanı dosyasını kontrol et ve yoksa oluştur
if [ ! -f /var/www/database/database.sqlite ]; then
    echo "Creating database.sqlite..."
    touch /var/www/database/database.sqlite
    chown www-data:www-data /var/www/database/database.sqlite
    chmod 664 /var/www/database/database.sqlite
fi

# İzinleri ayarla (Tekrar ve garanti olsun diye)
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Laravel cache temizle
php artisan optimize:clear

# Migrationları çalıştır
php artisan migrate --force

# Seeders'ı çalıştır (Admin kullanıcısını oluşturmak için)
php artisan db:seed --force

# Storage link oluştur (varsa silip tekrar oluşturur)
php artisan storage:link

# Supervisor'ı başlat
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
