# Kết quả nhập dữ liệu sạch vào CSDL local

## Dữ liệu đã nhập

| Hạng mục | Kết quả |
| --- | ---: |
| Cơ sở có báo cáo quy mô/kế hoạch đào tạo sạch | 11 |
| Dòng trường–ngành/nghề trong các báo cáo này | 137 |
| Địa chỉ trung tâm đã đối chiếu và cập nhật | 19 |
| Ngành/nghề mới trong danh mục | 26 |
| Liên kết cơ sở–ngành/nghề–trình độ mới | 98 |
| Báo cáo tuyển sinh cũ chuyển từ JSON sang CSDL | 14 |
| Tổng bản ghi nguồn lưu trong CSDL | 44 |
| Bản ghi chưa chắc chắn bỏ qua, không lưu CSDL | 8 |

Ngay sau lần nhập: 83 cơ sở, 232 ngành/nghề, 482 liên kết đào tạo. Sau bước đối chiếu và loại 12 bản ghi seed trùng được người dùng cho phép, CSDL còn **71 cơ sở, 232 ngành/nghề, 479 liên kết đào tạo**, giữ đủ 44 hồ sơ nguồn; 3 liên kết ngành cũ đã được lưu trong bản sao, không thêm vào danh mục của bản ghi chuẩn. Xem [kết quả xử lý trùng cơ sở](kiem-tra-trung-co-so-2026-11-12.md). Danh mục doanh nghiệp và việc làm đã chuyển CSDL ở lần cập nhật trước: API hiện trả 101 doanh nghiệp đang hoạt động và 80 tin.

## Những phần đã bỏ qua

| Cơ sở | Lý do |
| --- | --- |
| Trường Cao đẳng Cơ điện Nông nghiệp và Môi trường | Cần xác minh danh tính cơ sở; không dùng mã ứng viên |
| Trường Cao đẳng Kinh tế và Công nghệ tỉnh Ninh Bình | Cần xác minh danh tính cơ sở; không tự coi là trường đổi tên |
| Trường Cao đẳng Cơ điện và Thủy lợi Bắc Bộ tại tỉnh Ninh Bình | Chưa ghép được duy nhất với CSDL |
| Trường Trung cấp nghề Quốc tế | Chưa ghép được duy nhất với CSDL |
| Trung tâm GDNN-GDTX Trực Ninh | Chưa ghép được duy nhất với CSDL |
| Trung tâm GDNN-GDTX Hải Hậu | Chưa ghép được duy nhất với CSDL |
| Trung tâm GDTX, Tin học và Ngoại ngữ Ninh Bình | Tên không khớp chắc chắn với bản ghi hiện có |
| Trung tâm GDNN-GDTX Tam Điệp | Chưa ghép được duy nhất với CSDL |

Không tạo mới trường từ các bản ghi trên, không tự gán vào cơ sở có tên gần giống. Không nhập số điện thoại lãnh đạo, kể cả trong payload nguồn lưu CSDL. Số điện thoại chung hiện có của các trường được giữ nguyên; kiểm tra đối chiếu bản sao cho kết quả 0 số bị thay đổi.

Mã cơ sở trùng trong nguồn không được dùng làm định danh và không được lưu vào báo cáo nhập. Dòng tổng nguồn bị cảnh báo không khớp bị loại khỏi payload; các tổng hiển thị được cộng từ các dòng ngành sạch đã đối chiếu. Không dùng quy mô hoặc kế hoạch nguồn để ghi đè chỉ tiêu hằng năm.

## Cách lưu và sử dụng

- `school_source_records`: báo cáo đào tạo, địa chỉ đã đối chiếu và báo cáo tuyển sinh đã chuyển đổi. Chỉ lưu bản ghi ghép chắc chắn.
- `training_majors`, `school_training_major`: bổ sung danh mục và liên kết; giữ các liên kết, chỉ tiêu đang có. Liên kết mới có chỉ tiêu 0 vì file không phải chỉ tiêu hằng năm.
- `schools.address`: cập nhật địa chỉ sạch cho 19 trung tâm; không thay đổi tọa độ, tài khoản, lãnh đạo hay số liên hệ hiện có.
- API `/api/schools/{id}` trả `training_source_report` và `admission_report` từ CSDL; dữ liệu báo cáo được tải khi mở hồ sơ chi tiết.
- Giao diện có phần quy mô hiện tại/kế hoạch 2026. Đã bỏ import `admissionResults.json` trong frontend, bỏ danh mục huyện tĩnh và bỏ dữ liệu dự phòng về kỳ/trình độ trong các portal.
- Doanh nghiệp, việc làm, trường học, ngành/nghề và báo cáo đều đọc CSDL/API. Tệp nguồn JSON/Excel vẫn được giữ để truy vết và chạy import; dữ liệu ranh giới bản đồ là tài nguyên địa lý, không phải bộ số liệu nghiệp vụ thay thế CSDL.

## Sao lưu và kiểm tra

Đã sao lưu các bảng schools, training_majors, school_training_major và school_source_records trước khi ghi tại:

`backend/storage/app/import-backups/clean-gdnn-20261112-042031-682756.json`

Cột nhị phân `schools.geom` được mã hóa hex trong bản sao; khóa `binary_encoding` ghi rõ quy ước phục hồi. Thư mục backup được loại khỏi Git.

Kiểm tra sau nhập: không có số điện thoại lãnh đạo trong các bản ghi nguồn; không thay đổi số điện thoại chung của trường. Chạy lại đối chiếu: 0 báo cáo cần ghi, 0 địa chỉ cần cập nhật, 0 ngành và liên kết cần thêm.

## Cập nhật VPS sau khi đưa code lên Git

```bash
cd /var/www/schoolmap
git pull --ff-only origin main
cd backend
php artisan migrate --force
php artisan directory:import-legacy
php artisan schools:import-clean-data
php artisan schools:import-clean-data --apply
php artisan optimize:clear
cd ..
npm ci
npm run build
```

Lệnh không có `--apply` chỉ đối chiếu. Lệnh `--apply` tạo bản sao trước khi ghi và chỉ xử lý phần chắc chắn. Kết quả ghép trên VPS có thể khác local nếu danh mục cơ sở khác; xem phần đối chiếu trước khi áp dụng.

Các thay đổi hiện mới được thực hiện trên local, chưa chạy trên VPS.
