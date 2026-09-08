# KẾ HOẠCH XÂY DỰNG BẢN ĐỒ SỐ GIÁO DỤC TỈNH NINH BÌNH

## 1. Tên dự án

**BẢN ĐỒ SỐ GIÁO DỤC TỈNH NINH BÌNH**

---

## 2. Mục tiêu dự án

Xây dựng một nền tảng bản đồ số tập trung dữ liệu các cơ sở giáo dục trên toàn địa bàn tỉnh Ninh Bình, phục vụ:

- Tra cứu thông tin trường học.
- Hiển thị vị trí các cơ sở giáo dục trực quan trên bản đồ.
- Tìm kiếm, lọc và thống kê dữ liệu theo nhiều tiêu chí.
- Hỗ trợ cán bộ quản lý dữ liệu trường học.
- Hỗ trợ lãnh đạo theo dõi, thống kê và phân tích sự phân bố mạng lưới giáo dục.
- Tạo nền tảng để mở rộng các chức năng GIS nâng cao trong tương lai.

Hệ thống được định hướng theo mô hình của trang GIS giáo dục TP.HCM nhưng tối ưu theo hướng:

- Mã nguồn mở.
- Không sử dụng API bản đồ trả phí.
- Không phụ thuộc Google Maps API hoặc Mapbox.
- Phát triển trên máy local.
- Triển khai trực tiếp lên VPS của đơn vị.
- Dữ liệu được quản lý tập trung trên hệ thống riêng.

---

## 3. Đối tượng sử dụng

### 3.1. Người dân

Có thể:

- Tra cứu trường học.
- Tìm kiếm theo tên trường.
- Xem vị trí trường trên bản đồ.
- Xem thông tin cơ bản của trường.
- Lọc trường theo cấp học, loại hình, xã/phường.
- Xem chỉ đường đến trường.

### 3.2. Cán bộ quản lý

Có thể:

- Quản lý danh sách trường học.
- Thêm, sửa, xóa thông tin.
- Import dữ liệu từ Excel.
- Kiểm tra và xác minh dữ liệu.
- Quản lý tọa độ trường.
- Quản lý số liệu thống kê.

### 3.3. Lãnh đạo

Có thể:

- Xem dashboard tổng quan.
- Xem thống kê theo khu vực.
- Xem phân bố các cấp học.
- Xem mật độ trường.
- Phân tích dữ liệu phục vụ quản lý và quy hoạch.

---

# 4. Phạm vi dữ liệu

Hệ thống hướng tới quản lý toàn bộ cơ sở giáo dục trên địa bàn tỉnh Ninh Bình hiện tại.

Dữ liệu nên bao gồm:

- Mầm non.
- Tiểu học.
- THCS.
- THPT.
- Trung tâm giáo dục thường xuyên.
- Cơ sở giáo dục nghề nghiệp.
- Cao đẳng.
- Đại học.
- Các cơ sở giáo dục khác nếu cần.

Do địa giới hành chính đã thay đổi sau sắp xếp, dữ liệu nên lưu đồng thời:

- Đơn vị hành chính hiện tại.
- Xã/phường hiện tại.
- Khu vực trước sáp nhập.
- Huyện/thành phố cũ nếu cần phục vụ tra cứu dữ liệu lịch sử.

Ví dụ:

```text
Tỉnh hiện tại: Ninh Bình
Xã/phường hiện tại: Phường Phủ Lý
Khu vực trước sáp nhập: Hà Nam
```

---

# 5. Kiến trúc hệ thống

```text
                         NGƯỜI DÙNG
                             │
                             ▼
                    ┌─────────────────┐
                    │     Vue 3       │
                    │     Vite        │
                    │     MapLibre    │
                    └────────┬────────┘
                             │
                         REST API
                             │
                             ▼
                    ┌─────────────────┐
                    │     Laravel     │
                    │     Backend     │
                    └────────┬────────┘
                             │
                ┌────────────┴────────────┐
                │                         │
                ▼                         ▼
              MySQL                 GeoJSON / GIS
             Database                    Data
                │
                └────────────┬────────────┘
                             ▼
                         Ubuntu VPS
                   Nginx + PHP-FPM + MySQL
```

Bản đồ:

```text
MapLibre GL JS
      │
      ▼
OpenFreeMap
      │
      ▼
OpenStreetMap Data
```

---

# 6. Công nghệ đề xuất

| Thành phần | Công nghệ |
|---|---|
| Backend | Laravel 12/13 |
| Frontend | Vue 3 |
| Build Tool | Vite |
| CSS | Tailwind CSS |
| Map Engine | MapLibre GL JS |
| Map Tile | OpenFreeMap |
| Map Data | OpenStreetMap |
| Database | MySQL |
| GIS Data | GeoJSON / PMTiles |
| Biểu đồ | Apache ECharts hoặc Chart.js |
| Authentication | Laravel Authentication |
| Phân quyền | Spatie Laravel Permission |
| Import Excel | maatwebsite/excel |
| Web Server | Nginx |
| Server OS | Ubuntu |
| SSL | Let's Encrypt |
| Version Control | Git |
| Development | Local |
| Production | VPS |

---

# 7. Nguyên tắc kỹ thuật

## 7.1. Không sử dụng API bản đồ trả phí

Không sử dụng:

- Google Maps API.
- Mapbox.
- ArcGIS Online trả phí.
- Firebase nếu không cần thiết.

Ưu tiên:

- MapLibre GL JS.
- OpenFreeMap.
- OpenStreetMap.
- GeoJSON.
- PMTiles nếu dữ liệu GIS lớn.

## 7.2. Chủ động dữ liệu

Toàn bộ dữ liệu trường học được lưu trong MySQL và VPS của đơn vị.

Không phụ thuộc vào:

- Google Sheets.
- Google Apps Script.
- Cloudflare Pages.
- SaaS bên ngoài.

## 7.3. Dữ liệu quan trọng hơn giao diện

Không triển khai các tính năng GIS nâng cao trước khi:

- Danh sách trường được chuẩn hóa.
- Mã trường được xác định.
- Địa chỉ được kiểm tra.
- Tọa độ được xác minh.
- Đơn vị hành chính được chuẩn hóa.

---

# 8. PHÂN HỆ 1 — BẢN ĐỒ SỐ

Trang chủ hiển thị bản đồ toàn tỉnh.

Ví dụ giao diện:

```text
┌───────────────────────────────────────────────────────┐
│ BẢN ĐỒ SỐ GIÁO DỤC TỈNH NINH BÌNH          🔎      │
├──────────────────┬────────────────────────────────────┤
│                  │                                    │
│ Bộ lọc           │                                    │
│                  │                                    │
│ Cấp học          │              BẢN ĐỒ               │
│ Loại hình        │                                    │
│ Xã/phường        │        🏫      🏫                 │
│                  │              🏫                    │
│                  │                     🏫             │
│                  │                                    │
└──────────────────┴────────────────────────────────────┘
```

Marker phân biệt theo cấp học:

- Mầm non.
- Tiểu học.
- THCS.
- THPT.
- GDTX.
- GDNN.
- Cao đẳng.
- Đại học.

---

# 9. Marker Clustering

Không hiển thị hàng nghìn HTML marker độc lập.

Sử dụng clustering của MapLibre.

Ví dụ:

```text
Zoom xa

        125
         ●

Zoom gần

🏫      🏫      🏫
```

Lợi ích:

- Tăng hiệu năng.
- Giảm tải trình duyệt.
- Bản đồ dễ quan sát.
- Phù hợp khi số lượng trường lớn.

---

# 10. PHÂN HỆ 2 — TÌM KIẾM

Cho phép tìm kiếm theo:

- Tên trường.
- Mã trường.
- Địa chỉ.
- Xã/phường.

Ví dụ:

```text
🔎 THPT chuyên...
```

Sau khi tìm thấy:

1. Bản đồ tự động zoom đến trường.
2. Marker được highlight.
3. Popup thông tin được mở.

---

# 11. PHÂN HỆ 3 — BỘ LỌC

## 11.1. Lọc theo cấp học

```text
☑ Mầm non
☑ Tiểu học
☑ THCS
☑ THPT
☐ GDTX
☐ GDNN
☐ Cao đẳng
☐ Đại học
```

## 11.2. Lọc theo loại hình

```text
☑ Công lập
☐ Tư thục
☐ Khác
```

## 11.3. Lọc theo địa bàn

```text
Tỉnh Ninh Bình

Xã/phường
    ↓
Phường...
Xã...
```

## 11.4. Lọc theo khu vực trước sáp nhập

```text
Tất cả
Hà Nam
Nam Định
Ninh Bình
```

---

# 12. PHÂN HỆ 4 — POPUP THÔNG TIN TRƯỜNG

Khi click marker:

```text
┌───────────────────────────────┐
│ 🏫 THPT ABC                   │
│                               │
│ THPT • Công lập               │
│                               │
│ 📍 Phường ABC                 │
│ ☎ 0226 xxx xxx                │
│                               │
│ 👨‍🎓 1.245 học sinh            │
│ 👨‍🏫 82 giáo viên              │
│                               │
│ [Chi tiết]                    │
│ [Chỉ đường]                   │
└───────────────────────────────┘
```

Popup chỉ hiển thị thông tin quan trọng.

Thông tin chi tiết chuyển sang trang hồ sơ trường.

---

# 13. PHÂN HỆ 5 — HỒ SƠ TRƯỜNG

URL ví dụ:

```text
/truong/thpt-abc
```

## 13.1. Thông tin chung

- Tên trường.
- Mã trường.
- Cấp học.
- Loại hình.
- Địa chỉ.
- Xã/phường.
- Tọa độ.

## 13.2. Thông tin liên hệ

- Website.
- Email.
- Điện thoại.

## 13.3. Quy mô

- Số học sinh.
- Số giáo viên.
- Số lớp.

## 13.4. Cơ sở vật chất

Có thể quản lý:

- Số phòng học.
- Phòng máy tính.
- Thư viện.
- Phòng chức năng.
- Sân thể thao.
- Các hạng mục khác.

## 13.5. Thông tin dữ liệu

Hiển thị:

```text
Dữ liệu được cập nhật lần cuối: dd/mm/yyyy
```

---

# 14. PHÂN HỆ 6 — GIS LAYER

Các lớp dữ liệu:

```text
BẢN ĐỒ
│
├── Hành chính
│    ├── Ranh giới tỉnh
│    └── Ranh giới xã/phường
│
├── Giáo dục
│    ├── Mầm non
│    ├── Tiểu học
│    ├── THCS
│    ├── THPT
│    ├── GDTX
│    ├── GDNN
│    ├── Cao đẳng
│    └── Đại học
│
└── Phân tích
     ├── Heatmap
     ├── Buffer
     └── Coverage
```

Cho phép người dùng bật/tắt từng layer.

---

# 15. PHÂN HỆ 7 — CÔNG CỤ BẢN ĐỒ

Các chức năng nên có:

- Zoom.
- Zoom về toàn tỉnh.
- Fullscreen.
- Định vị người dùng.
- Bật/tắt layer.
- Lấy tọa độ.
- Đo khoảng cách.
- Đo diện tích.
- Đổi nền bản đồ nếu cần.

Ví dụ đo khoảng cách:

```text
A ●────────────────● B

Khoảng cách: 3.24 km
```

---

# 16. PHÂN HỆ 8 — CHỈ ĐƯỜNG

Ở phiên bản đầu không xây routing engine riêng.

Button:

```text
🧭 Chỉ đường
```

Có thể mở:

- Google Maps.
- OpenStreetMap.

Việc mở liên kết Google Maps không yêu cầu sử dụng Google Maps API.

---

# 17. PHÂN HỆ 9 — DASHBOARD THỐNG KÊ

Trang:

```text
/thong-ke
```

Hiển thị tổng quan:

```text
TỔNG CƠ SỞ GIÁO DỤC

1.382

MẦM NON
421

TIỂU HỌC
356

THCS
281

THPT
124
```

---

# 18. Thống kê theo địa phương

Cho phép chọn một xã/phường.

Ví dụ:

```text
Tổng trường:     32

Mầm non:         10
Tiểu học:         8
THCS:             7
THPT:             5
Khác:             2

Học sinh:    18.250
Giáo viên:    1.250
```

Có thể bổ sung:

- Số lớp.
- Tỷ lệ giáo viên/học sinh.
- Công lập/tư thục.
- Số trường theo cấp học.

---

# 19. Biểu đồ

Sử dụng:

- Apache ECharts.
- Hoặc Chart.js.

Các biểu đồ có thể có:

- Trường theo cấp học.
- Trường theo xã/phường.
- Công lập và ngoài công lập.
- Học sinh theo cấp học.
- Giáo viên theo cấp học.
- Quy mô học sinh qua các năm.

Ví dụ:

```text
Mầm non    ███████████████ 421
Tiểu học   ████████████    356
THCS       █████████        281
THPT       ████             124
```

---

# 20. PHÂN HỆ 10 — HEATMAP

Hiển thị mật độ cơ sở giáo dục.

Mục đích:

- Xác định khu vực tập trung nhiều trường.
- Xác định khu vực có mật độ trường thấp.
- Hỗ trợ quan sát sự phân bố mạng lưới giáo dục.

---

# 21. PHÂN HỆ 11 — BUFFER ANALYSIS

Cho phép chọn một trường và phân tích bán kính:

```text
1 km
3 km
5 km
10 km
```

Ví dụ kết quả:

```text
Trong bán kính 3 km:

12 trường Mầm non
7 trường Tiểu học
4 trường THCS
2 trường THPT
```

Ứng dụng:

- Đánh giá mức độ tập trung trường học.
- Phân tích khu vực phục vụ.
- Hỗ trợ quy hoạch mạng lưới trường.

---

# 22. PHÂN HỆ 12 — BACKEND QUẢN TRỊ

URL:

```text
/admin
```

Dashboard quản trị:

```text
1.382 trường

12 trường thiếu tọa độ

4 dữ liệu cần kiểm tra

15 trường vừa cập nhật
```

Các chức năng:

- Đăng nhập.
- Quản lý trường.
- Quản lý cấp học.
- Quản lý loại hình trường.
- Quản lý xã/phường.
- Quản lý số liệu thống kê.
- Quản lý dữ liệu cơ sở vật chất.
- Quản lý người dùng.
- Quản lý phân quyền.

---

# 23. Quản lý trường

Các chức năng CRUD:

- Thêm trường.
- Sửa trường.
- Xem trường.
- Xóa/khóa trường.
- Cập nhật tọa độ.
- Cập nhật trạng thái.
- Cập nhật thông tin liên hệ.

Ví dụ:

```text
Tên trường | Cấp học | Xã/phường | Loại | Trạng thái | Thao tác
```

---

# 24. IMPORT EXCEL

Đây là chức năng bắt buộc.

Quy trình:

```text
Excel từ Sở GD&ĐT
        ↓
Upload Excel
        ↓
Validate dữ liệu
        ↓
Preview
        ↓
Xác nhận
        ↓
Import Database
```

Laravel package:

```text
maatwebsite/excel
```

---

# 25. KIỂM TRA DỮ LIỆU

Sau import, hệ thống cần kiểm tra:

```text
❌ Thiếu mã trường
❌ Trùng mã trường
❌ Thiếu tọa độ
❌ Tọa độ không hợp lệ
❌ Tọa độ ngoài phạm vi tỉnh
❌ Xã/phường không tồn tại

⚠ Trùng tên trường
⚠ Thiếu số điện thoại
⚠ Thiếu website
⚠ Dữ liệu lâu chưa xác minh
```

Nên có trạng thái:

```text
VERIFIED
UNVERIFIED
NEED_REVIEW
INACTIVE
```

---

# 26. CẤU TRÚC DATABASE ĐỀ XUẤT

Không nên lưu toàn bộ dữ liệu vào một bảng `schools`.

Các bảng chính:

```text
users

roles
permissions

schools

education_levels

school_types

wards

legacy_areas

school_statistics

school_facilities

school_images

data_imports

data_verifications
```

---

# 27. BẢNG SCHOOLS

Ví dụ:

```text
schools
----------------------------

id
code
name

education_level_id
school_type_id

ward_id

legacy_province
legacy_district
legacy_ward

address

latitude
longitude

phone
email
website

description

status
last_verified_at

created_at
updated_at
```

---

# 28. BẢNG SCHOOL_STATISTICS

Không nên lưu trực tiếp:

```text
schools.student_count
schools.teacher_count
```

Nếu hệ thống cần theo dõi lịch sử.

Nên tách:

```text
school_statistics
----------------------------

id
school_id
academic_year

students
teachers
classes

created_at
updated_at
```

Ví dụ:

```text
2024-2025
2025-2026
2026-2027
```

Nhờ đó có thể thống kê xu hướng qua nhiều năm.

---

# 29. CƠ SỞ VẬT CHẤT

Bảng:

```text
school_facilities
```

Có thể lưu:

- Số phòng học.
- Phòng máy.
- Số máy tính.
- Thư viện.
- Phòng thí nghiệm.
- Nhà đa năng.
- Sân thể thao.
- Các thiết bị khác.

Không bắt buộc triển khai ở MVP.

---

# 30. PHÂN QUYỀN

Tối thiểu gồm:

## Super Admin

- Toàn quyền.

## Admin

- Quản lý dữ liệu.
- Import.
- Cập nhật trường.
- Quản lý thống kê.

## Viewer / Cán bộ

- Xem dashboard.
- Xem báo cáo.
- Không chỉnh sửa dữ liệu.

Sử dụng:

```text
Spatie Laravel Permission
```

---

# 31. DỮ LIỆU GIS

Ranh giới hành chính có thể lưu tại:

```text
/public/gis/
```

Ví dụ:

```text
province.geojson
wards.geojson
```

Nếu dữ liệu lớn, cân nhắc chuyển sang:

```text
PMTiles
```

Không nên tải file GeoJSON quá lớn trực tiếp xuống trình duyệt.

---

# 32. CẤU TRÚC SOURCE CODE

Khuyến nghị dùng một repository:

```text
project/
│
├── app/
│    └── Laravel Backend
│
├── resources/
│    └── Vue 3 Frontend
│
├── public/
│    └── gis/
│
├── database/
│
├── routes/
│
└── docs/
```

Ưu tiên:

> Laravel + Vue 3 trong cùng repository.

Lý do:

- Dễ phát triển.
- Dễ deploy.
- Dễ bảo trì.
- Không cần microservice.
- Không cần tách frontend/backend thành hai dự án nếu chưa có lý do thực tế.

---

# 33. MÔI TRƯỜNG LOCAL

Có thể phát triển bằng:

```text
PHP
Composer
Laravel
Node.js
NPM
MySQL
Vite
Git
```

Có thể sử dụng Docker nếu nhóm đã quen.

Quy trình:

```text
Local Development
       ↓
Git
       ↓
VPS
       ↓
Production
```

---

# 34. TRIỂN KHAI VPS

Production:

```text
Internet
   │
   ▼
Nginx
   │
   ▼
Laravel
   │
   ├── REST API
   ├── Admin
   ├── Vue Build
   └── GIS Data
          │
          ▼
        MySQL
```

Sử dụng:

- Ubuntu.
- Nginx.
- PHP-FPM.
- MySQL.
- Let's Encrypt SSL.

---

# 35. BACKUP

Cần backup database tự động.

Ví dụ:

```text
mysqldump
    ↓
database_2026_09_02.sql
```

Có thể giữ:

```text
7 bản daily
4 bản weekly
3 bản monthly
```

Nếu có điều kiện, backup phải được đưa ra khỏi VPS chính.

Không nên chỉ lưu backup trên cùng VPS với hệ thống production.

---

# 36. QUY TRÌNH XỬ LÝ DỮ LIỆU

```text
Dữ liệu từ Sở GD&ĐT
        ↓
Chuẩn hóa cấu trúc
        ↓
Chuẩn hóa mã trường
        ↓
Chuẩn hóa địa chỉ
        ↓
Chuẩn hóa xã/phường
        ↓
Xác định tọa độ
        ↓
Kiểm tra dữ liệu
        ↓
Import database
        ↓
Xác minh
        ↓
Hiển thị trên bản đồ
```

---

# 37. DỮ LIỆU TRƯỜNG TỐI THIỂU

```text
school_id
school_code
school_name

education_level
school_type

ward
address

legacy_province
legacy_district

latitude
longitude

phone
email
website

status
last_verified_at
```

Nguyên tắc:

> Không sử dụng tên trường làm ID.

ID/mã trường phải ổn định vì trường có thể:

- Đổi tên.
- Hợp nhất.
- Chia tách.
- Chuyển địa chỉ.

---

# 38. MVP — PHIÊN BẢN 1

MVP chỉ tập trung vào các chức năng cốt lõi.

## Chức năng bắt buộc

1. Hiển thị bản đồ trường học.
2. Marker clustering.
3. Tìm kiếm trường.
4. Lọc theo cấp học.
5. Lọc theo loại hình.
6. Lọc theo xã/phường.
7. Popup thông tin.
8. Hồ sơ trường.
9. Định vị người dùng.
10. Chỉ đường.
11. Dashboard thống kê.
12. Admin CRUD.
13. Import Excel.
14. Validate dữ liệu.

Mục tiêu:

> Hoàn thiện tốt V1 trước khi bổ sung GIS nâng cao hoặc AI.

---

# 39. PHIÊN BẢN 2 — GIS

Sau khi MVP ổn định, bổ sung:

- Ranh giới tỉnh.
- Ranh giới xã/phường.
- Layer control.
- Đo khoảng cách.
- Đo diện tích.
- Lấy tọa độ.
- Heatmap.
- Buffer.
- Thống kê theo vùng.
- Cơ sở vật chất.
- Thống kê lịch sử theo năm học.

---

# 40. PHIÊN BẢN 3 — SMART GIS

Giai đoạn nghiên cứu nâng cao:

- Population Layer.
- Population Density.
- Dữ liệu dân số theo độ tuổi.
- School Coverage.
- Accessibility Analysis.
- Travel Time Analysis.
- Phân tích sức chứa trường.
- Phân tích dân số và trường học.
- Đề xuất khu vực thiếu trường.
- Hỗ trợ quy hoạch mạng lưới trường.

Ví dụ:

```text
Dân số trẻ 6-10 tuổi
        +
Sức chứa trường Tiểu học
        +
Khoảng cách đến trường
        +
Mạng lưới giao thông
        ↓
Xác định khu vực có nguy cơ thiếu trường
```

Không triển khai các phân tích này nếu chưa có nguồn dữ liệu dân cư chính xác.

---

# 41. KẾ HOẠCH TRIỂN KHAI 10 TUẦN

| Tuần | Công việc | Sản phẩm |
|---|---|---|
| 1 | Khảo sát, xác định yêu cầu | SRS, danh sách chức năng |
| 2 | Thiết kế database, chuẩn dữ liệu | ERD, Data Dictionary |
| 3 | Xây Backend Admin | Authentication, CRUD |
| 4 | Import Excel, Data Validation | Module Import |
| 5 | Xây MapLibre Core | Bản đồ, marker |
| 6 | Search, Filter, Cluster | MVP Map |
| 7 | School Detail, GIS Layer | Hồ sơ trường |
| 8 | Dashboard, Statistics | Dashboard |
| 9 | Heatmap, Buffer, GIS Tools | GIS V1 |
| 10 | Testing, Security, Deploy | Production |

---

# 42. MILESTONE 1 — DATA

Hoàn thành:

- Danh sách trường.
- Chuẩn mã trường.
- Chuẩn cấp học.
- Chuẩn loại hình.
- Danh mục xã/phường.
- Tọa độ.
- Database.
- Quy tắc validation.

---

# 43. MILESTONE 2 — ADMIN

Hoàn thành:

- Login.
- Phân quyền.
- CRUD trường.
- CRUD danh mục.
- Import Excel.
- Preview dữ liệu.
- Validate.
- Quản lý thống kê.

---

# 44. MILESTONE 3 — MAP

Hoàn thành:

- MapLibre.
- Basemap.
- Marker.
- Marker cluster.
- Search.
- Filter.
- Popup.
- School Detail.
- Locate user.
- Direction.

---

# 45. MILESTONE 4 — GIS

Hoàn thành:

- Administrative Boundary.
- Layer Control.
- Measure Distance.
- Measure Area.
- Get Coordinates.
- Heatmap.
- Buffer Analysis.

---

# 46. MILESTONE 5 — PRODUCTION

Hoàn thành:

- Testing.
- Security.
- Performance optimization.
- Database backup.
- VPS deployment.
- HTTPS.
- Documentation.
- User manual.
- Technical documentation.

---

# 47. PHÂN CÔNG NHÂN SỰ THAM KHẢO

Nếu có 5 thành viên:

| Thành viên | Phụ trách chính |
|---|---|
| Thành viên 1 | Team Lead, Architecture, Backend |
| Thành viên 2 | MapLibre, GIS |
| Thành viên 3 | Vue, UI/UX |
| Thành viên 4 | Database, Import Excel, Data |
| Thành viên 5 | Dashboard, Testing, Deployment |

Lưu ý:

> Chuẩn hóa và xác minh dữ liệu là trách nhiệm chung, không nên giao toàn bộ cho một thành viên.

---

# 48. TIÊU CHÍ NGHIỆM THU V1

## Dữ liệu

- Không trùng mã trường.
- Tọa độ hợp lệ.
- Trường hiển thị đúng địa điểm.
- Danh mục xã/phường chính xác.
- Có ngày xác minh dữ liệu.

## Bản đồ

- Load nhanh.
- Cluster hoạt động tốt.
- Search chính xác.
- Filter chính xác.
- Mobile responsive.

## Backend

- CRUD ổn định.
- Import Excel có validate.
- Có phân quyền.
- Có log hoặc trạng thái dữ liệu cần kiểm tra.

## Production

- HTTPS.
- Backup database.
- Không lộ thông tin nhạy cảm.
- Không sử dụng API bắt buộc trả phí.

---

# 49. CÁC CHỨC NĂNG KHÔNG NÊN LÀM NGAY

Không ưu tiên trong MVP:

- Chatbot AI.
- AI gợi ý trường.
- Routing Engine riêng.
- Microservices.
- App mobile riêng.
- Blockchain.
- Hệ thống tài khoản cho toàn bộ trường.
- Machine Learning khi chưa có dữ liệu.
- Accessibility Index khi chưa có dữ liệu dân cư đủ tin cậy.

Các tính năng này dễ làm dự án phình to nhưng không giải quyết vấn đề cốt lõi.

---

# 50. KIẾN TRÚC CUỐI CÙNG

```text
Ubuntu VPS
│
├── Nginx
│
├── Laravel
│    │
│    ├── Authentication
│    ├── Authorization
│    ├── Admin
│    ├── REST API
│    ├── Excel Import
│    ├── Data Validation
│    └── Statistics
│
├── Vue 3
│    │
│    ├── Map
│    ├── Search
│    ├── Filter
│    ├── Dashboard
│    └── School Profile
│
├── MapLibre GL JS
│
├── OpenFreeMap
│
├── GeoJSON / PMTiles
│
└── MySQL
```

---

# 51. STACK CHỐT

```text
Laravel
+
Vue 3
+
MySQL
+
MapLibre GL JS
+
OpenFreeMap
+
OpenStreetMap
+
GeoJSON / PMTiles
+
Apache ECharts
+
Nginx
+
Ubuntu VPS
```

---

# 52. NGUYÊN TẮC CHỐT DỰ ÁN

1. Không sử dụng Google Maps API.
2. Không sử dụng Mapbox trả phí.
3. Không phụ thuộc Google Sheets hoặc Apps Script.
4. Không phụ thuộc Cloudflare Pages.
5. Dữ liệu được quản lý trên VPS riêng.
6. Không xây microservice nếu chưa cần.
7. Không chạy theo AI trước khi dữ liệu chuẩn.
8. Không triển khai GIS nâng cao nếu dữ liệu nền chưa chính xác.
9. Import Excel và kiểm tra dữ liệu là chức năng cốt lõi.
10. Ưu tiên độ chính xác, tốc độ và khả năng vận hành thực tế.

---

# 53. MỤC TIÊU CUỐI CÙNG

Phiên bản đầu tiên cần đạt được:

> Xây dựng một hệ thống bản đồ số giáo dục tỉnh Ninh Bình có khả năng quản lý tập trung dữ liệu cơ sở giáo dục, hiển thị trực quan trên bản đồ, hỗ trợ tìm kiếm, lọc, thống kê, quản trị và nhập dữ liệu từ Excel; sử dụng hoàn toàn các công nghệ mã nguồn mở hoặc miễn phí, phát triển trên local và triển khai trên VPS riêng.

Sau khi dữ liệu và hệ thống V1 ổn định, tiếp tục phát triển thành:

> **Nền tảng GIS giáo dục phục vụ quản lý, phân tích và hỗ trợ quy hoạch mạng lưới cơ sở giáo dục tỉnh Ninh Bình.**
