# Chuyển đổi cổng Kết nối 3 nhà sang hệ thống web

## Phạm vi đã triển khai

- Trang công khai `/ket-noi-cung-cau`: tổng hợp cung–cầu, lọc theo kỳ/ngành/trình độ, tìm kiếm tiếng Việt, danh sách nhu cầu, năng lực đào tạo, danh bạ doanh nghiệp và xuất CSV.
- Cổng doanh nghiệp `/doanh-nghiep`: cập nhật hồ sơ, quản lý nhu cầu nhân lực và xem đề xuất từ nhà trường.
- Cổng nhà trường `/truong`: quản lý năng lực đào tạo của trường/cơ sở trực thuộc và gửi/rút đề xuất đáp ứng.
- Cổng quản trị `/admin`: quản lý kỳ báo cáo, doanh nghiệp, tài khoản doanh nghiệp, kỹ năng, nhu cầu, năng lực và đề xuất.
- Đối chiếu theo đúng ba khóa: ngành/nghề, trình độ đào tạo và kỳ báo cáo. Số lượng đề xuất được kiểm tra lại trong transaction để tránh vượt nhu cầu hoặc năng lực còn lại.
- Dữ liệu trình độ nghề dùng danh mục riêng: Dưới 3 tháng, Sơ cấp, Trung cấp và Cao đẳng; không làm thay đổi danh mục cấp học của bản đồ.

## Chuyển dữ liệu từ Apps Script

Lệnh nhập chấp nhận JSON trả về từ `/api/portal` của Apps Script. Dữ liệu mặc định được đưa vào trạng thái chờ rà soát; chỉ dùng `--publish` sau khi đã kiểm tra nội dung.

```bash
cd backend
php artisan supply-demand:import-apps-script /duong-dan/portal.json --dry-run
php artisan supply-demand:import-apps-script /duong-dan/portal.json
```

Các tùy chọn:

- `--dry-run`: kiểm tra toàn bộ nhưng hoàn tác thay đổi.
- `--include-samples`: nhập cả dữ liệu có cờ `sample`.
- `--publish`: công bố nhu cầu, kích hoạt năng lực và đề xuất ngay sau khi nhập.

Lệnh cũng nhận tệp danh mục có khóa `employers`, ví dụ `catalog-export.json`. Mã nguồn của hồ sơ doanh nghiệp được lưu riêng ở `source_code`, không tự coi là mã số thuế nếu nguồn chưa xác minh.

## Triển khai lên VPS

1. Sao lưu cơ sở dữ liệu và thư mục tệp tải lên.
2. Cập nhật mã nguồn, sau đó cài dependency PHP ở chế độ production.
3. Chạy migration bằng `php artisan migrate --force`.
4. Chạy `php artisan db:seed --class=SupplyDemandPeriodSeeder --force` nếu VPS chưa có hai kỳ Quý IV/2026 và Quý I/2027.
5. Chạy `php artisan optimize:clear`, sau đó tạo lại cache theo quy trình triển khai hiện tại.
6. Build frontend bằng `npm ci && npm run build`, rồi triển khai thư mục `dist` theo cấu hình web server hiện tại.
7. Kiểm tra `/admin/login`, `/truong/login`, `/doanh-nghiep/login`, `/api/supply-demand/catalogs` và `/ket-noi-cung-cau`.

Ba migration mới chỉ tạo bảng/cột bổ sung và không xóa hay cập nhật dữ liệu trường học hiện có. Không chạy rollback trên VPS đang có dữ liệu cung–cầu nếu chưa sao lưu.
