# Nhập hai workbook dữ liệu hệ thống

Chạy `node scripts/importSourceWorkbooks.cjs` từ thư mục dự án để tạo lại:

- `src/data/jobPostings.json`: 80 tin từ workbook tuyển dụng. Số lượng tuyển được giữ theo file; điện thoại và hạn nộp được tạo lại. Liên hệ trong dataset không bật liên kết gọi/Zalo.
- `src/data/admissionResults.json`: báo cáo tuyển sinh của 14 cơ sở, gồm 195 dòng ngành/nghề thực tế. Sáu dòng tiêu đề nhóm (ví dụ “Hệ cao đẳng”) không được tính là ngành đào tạo. Dữ liệu này được nối vào chi tiết cơ sở theo ID trường và hiển thị thành mục riêng.

Ô trống và dấu gạch trong báo cáo tuyển sinh được lưu thành `null` và hiển thị là “—”, để không biến dữ liệu thiếu thành số 0. Ngày hạn tuyển dụng và số liên hệ được tạo lại.

Các file Excel gốc không bị sửa. Từ đợt cập nhật ngày 12/11/2026, doanh nghiệp, tin việc làm và báo cáo tuyển sinh được lưu trong CSDL; frontend đọc API và không import các tệp số liệu JSON này nữa. Tệp JSON chỉ còn dùng làm đầu vào lệnh chuyển đổi và đối chiếu nguồn. Xem [kết quả nhập dữ liệu sạch](ket-qua-nhap-du-lieu-sach-2026-11-12.md).

## Đồng bộ danh mục ngành/nghề và ngành đào tạo của trường

Sau khi có `admissionResults.json`, chạy trong thư mục dự án:

```powershell
node scripts/importSourceWorkbooks.cjs --admissions-only
php backend/artisan majors:import-admission-report
php backend/artisan majors:import-admission-report --apply
```

Lệnh không có `--apply` chỉ xem trước; `--apply` sao lưu toàn bộ hai bảng `training_majors` và `school_training_major` tại `backend/storage/app/import-backups/` trước khi ghi. Trên VPS dùng `php artisan majors:import-admission-report --apply` từ thư mục `backend` sau khi cập nhật code và dữ liệu JSON. Việc `git pull` đơn thuần không cập nhật hai bảng này. Lệnh kiểm tra ID và mã của đủ 14 cơ sở trước khi ghi, có thể chạy lại mà không thêm trùng.

Danh mục được gộp theo tên ngành chuẩn hóa. Mã ngành chỉ được điền khi nguồn có một mã số hợp lệ, không mâu thuẫn với tên ngành khác; mã theo từng dòng vẫn giữ trong báo cáo gốc. Mỗi cặp trường–ngành mới được tạo một liên kết. Nếu không xác định được trình độ từ số liệu hoặc mã nguồn, liên kết được đánh dấu `chua_xac_dinh` thay vì gán đoán là cao đẳng/trung cấp. Các liên kết cũ, chỉ tiêu hằng năm và hồ sơ trường không bị sửa hay xóa. Liên kết mới có `annual_quota = 0` vì bảng nguồn là **kết quả tuyển sinh của kỳ báo cáo**, không phải chỉ tiêu tuyển sinh hằng năm.

Đã áp dụng trên CSDL local: 58 ngành mới, 20 mã bổ sung vào ngành cũ, 131 liên kết trường–ngành mới. Tổng hiện có 206 ngành và 384 liên kết; chạy lại chế độ xem trước cho kết quả 0 mục cần thêm.
