# Hướng Dẫn Từng Bước Triển Khai Hệ Thống SchoolMap Lên VPS Ubuntu (LEMP)

Tài liệu này cung cấp các câu lệnh chuẩn xác và đầy đủ nhất để bạn có thể copy-paste trực tiếp vào Terminal SSH của VPS Ubuntu và đưa hệ thống vào hoạt động với tên miền của bạn.

---

## BƯỚC 1: KẾT NỐI SSH VÀO VPS & CÀI ĐẶT MÔI TRƯỜNG

Mở Terminal trên máy tính và SSH vào VPS:
```bash
ssh root@<IP_CỦA_VPS>
```

Chạy toàn bộ các khối lệnh sau:

### 1.1 Cập nhật hệ thống & Cài đặt công cụ nền tảng
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl wget git unzip zip software-properties-common ufw
```

### 1.2 Mở tường lửa (Firewall)
```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw --force enable
```

### 1.3 Cài đặt Nginx
```bash
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

### 1.4 Cài đặt PHP 8.2 & các Extension cần thiết cho Laravel & Filament
```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-curl php8.2-gd \
                    php8.2-mbstring php8.2-xml php8.2-zip php8.2-bcmath php8.2-intl
sudo systemctl enable php8.2-fpm
sudo systemctl start php8.2-fpm
```

### 1.5 Cài đặt Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 1.6 Cài đặt Node.js 20 LTS & npm
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### 1.7 Cài đặt MySQL Server 8.0
```bash
sudo apt install -y mysql-server
sudo systemctl enable mysql
sudo systemctl start mysql
```

---

## BƯỚC 2: TẠO DATABASE MYSQL

Đăng nhập vào MySQL:
```bash
sudo mysql
```

Chạy các lệnh SQL sau *(hãy thay `MatKhauCuaBan@2026` bằng mật khẩu bảo mật của bạn)*:
```sql
CREATE DATABASE schoolmap_ninhbinh CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'schoolmap_user'@'localhost' IDENTIFIED BY 'MatKhauCuaBan@2026';
GRANT ALL PRIVILEGES ON schoolmap_ninhbinh.* TO 'schoolmap_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## BƯỚC 3: ĐƯA MÃ NGUỒN LÊN VPS & THIẾT LẬP BACKEND

### 3.1 Đưa mã nguồn vào thư mục `/var/www/schoolmap`
```bash
sudo mkdir -p /var/www/schoolmap
sudo chown -R $USER:$USER /var/www/schoolmap

# Nếu bạn đẩy qua Git:
cd /var/www/schoolmap
git clone <URL_REPO_GIT_CỦA_BẠN> .
```

*(Hoặc nếu bạn copy từ máy cá nhân lên VPS qua SCP/Rsync):*
```bash
# Lệnh chạy từ máy cá nhân:
scp -r d:/Code/SchoolMap/* root@<IP_VPS>:/var/www/schoolmap/
```

### 3.2 Cài đặt & Cấu hình Laravel Backend
```bash
cd /var/www/schoolmap/backend

# Cài đặt PHP dependencies
composer install --no-dev --optimize-autoloader

# Tạo file cấu hình .env
cp .env.production.example .env
nano .env
```

Trong file `.env`, cập nhật các dòng:
```env
APP_NAME="Bản đồ số Giáo dục Ninh Bình"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com   # Thay bằng tên miền thực tế của bạn

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=schoolmap_ninhbinh
DB_USERNAME=schoolmap_user
DB_PASSWORD=MatKhauCuaBan@2026   # Mật khẩu bạn đã tạo ở Bước 2
```
*(Nhấn `Ctrl + O` rồi `Enter` để lưu, `Ctrl + X` để thoát nano).*

### 3.3 Khởi tạo dữ liệu & Phân quyền Laravel
```bash
# Tạo APP_KEY bảo mật
php artisan key:generate

# Nạp cấu trúc bảng không gian GIS & danh mục 129 Xã/Phường ban đầu
mysql -u schoolmap_user -p schoolmap_ninhbinh < /var/www/schoolmap/database/schema.sql

# Chạy migration các bảng mở rộng và nạp 19 trường học chuẩn 2025
php artisan migrate --force
php artisan db:seed --class=HaNamSchoolsSeeder --force

# Tạo tài khoản Quản trị viên đăng nhập Filament Admin
php artisan make:filament-user

# Tạo liên kết thư mục ảnh/logo
php artisan storage:link

# Tối ưu hóa Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:cache-components

# Phân quyền cho Nginx đọc/ghi thư mục storage và cache
sudo chown -R www-data:www-data /var/www/schoolmap/backend/storage /var/www/schoolmap/backend/bootstrap/cache
sudo chmod -R 775 /var/www/schoolmap/backend/storage /var/www/schoolmap/backend/bootstrap/cache
```

---

## BƯỚC 4: BUILD ỨNG DỤNG FRONTEND VUE 3

```bash
cd /var/www/schoolmap
npm ci
npm run build

# Kiểm tra thư mục dist đã được tạo đầy đủ
ls -la /var/www/schoolmap/dist
```

---

## BƯỚC 5: CẤU HÌNH VIRTUAL HOST NGINX

Copy file cấu hình đã chuẩn bị sẵn vào Nginx:
```bash
sudo cp /var/www/schoolmap/nginx-schoolmap.conf /etc/nginx/sites-available/schoolmap
sudo nano /etc/nginx/sites-available/schoolmap
```
*(Tại dòng `server_name your-domain.com;`, đổi `your-domain.com` thành tên miền của bạn hoặc địa chỉ IP VPS nếu chưa gắn domain).*

Kích hoạt trang web và khởi động lại Nginx:
```bash
# Tạo symlink kích hoạt
sudo ln -s /etc/nginx/sites-available/schoolmap /etc/nginx/sites-enabled/

# Xóa trang mặc định của Nginx
sudo rm -f /etc/nginx/sites-enabled/default

# Kiểm tra cú pháp Nginx
sudo nginx -t

# Nạp lại cấu hình
sudo systemctl reload nginx
```

---

## BƯỚC 6: CÀI ĐẶT SSL MIỄN PHÍ (HTTPS) VỚI LET'S ENCRYPT

*(Bước này thực hiện khi tên miền của bạn đã trỏ bản ghi DNS A về IP VPS)*:
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```
*Chọn `Yes` khi được hỏi tự động chuyển hướng HTTP sang HTTPS.*

---

## BƯỚC 7: KIỂM TRA HỆ THỐNG HOÀN THÀNH

1. Mở trình duyệt truy cập: `https://your-domain.com`
   - Kiểm tra bản đồ số hiển thị đầy đủ ranh giới, danh sách cơ sở giáo dục.
   - Kiểm tra bấm vào Phường Phủ Lý hiển thị chuẩn xác 4 trường học.
2. Truy cập trang Quản trị: `https://your-domain.com/admin`
   - Đăng nhập bằng tài khoản Admin đã tạo ở Bước 3.3.
   - Thử thêm mới hoặc chỉnh sửa trường học, ghim vị trí và upload ảnh đại diện.
