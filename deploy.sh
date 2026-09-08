#!/bin/bash
# ==============================================================================
# Script tự động cập nhật và triển khai SchoolMap trên VPS
# Sử dụng: bash deploy.sh
# ==============================================================================

set -e

echo "🚀 [1/5] Kéo mã nguồn mới nhất từ Git..."
git pull origin main

echo "📦 [2/5] Build Frontend Vue 3..."
npm install
npm run build

echo "⚡ [3/5] Cập nhật dependencies Backend Laravel..."
cd backend
composer install --no-dev --optimize-autoloader

echo "🔄 [4/5] Chạy migration cơ sở dữ liệu..."
php artisan migrate --force

echo "🧹 [5/5] Làm mới và tối ưu bộ nhớ đệm Laravel..."
php artisan optimize:clear
php artisan optimize
php artisan filament:cache-components
php artisan filament:assets || true
php artisan storage:link || true

# Phân quyền lại thư mục
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

cd ..

echo "🎉 Triển khai hoàn tất thành công!"
