# Chuyển doanh nghiệp và tin việc làm sang CSDL

## Những phần đã chuyển

- Tin việc làm lưu trong bảng `job_postings`, liên kết `employer_id` với bảng `employers`.
- Danh sách doanh nghiệp, chi tiết doanh nghiệp, danh sách việc làm, chi tiết việc làm và tổng hợp lĩnh vực trên client dùng `/api/public/directory`.
- Danh mục chọn đăng nhập doanh nghiệp tiếp tục đọc cùng bảng `employers`.
- Giữ nguyên mã tin `source-job-...` và mã trang doanh nghiệp cũ qua `public_id`.
- Bản chụp chuyển đổi: `backend/database/data/legacy-public-directory.json`, gồm 94 doanh nghiệp và 80 tin.
- `src/data/jobPostings.json` được giữ làm tài liệu nguồn; giao diện không import tệp này nữa.

## Quy tắc chuyển đổi

Lệnh đối chiếu doanh nghiệp theo public_id, mã số thuế, rồi tên đã chuẩn hóa. Doanh nghiệp có sẵn được giữ nguyên thông tin chính; bổ sung định danh công khai và thông tin trình bày còn thiếu. Các doanh nghiệp ngoài bản chụp vẫn được giữ.

Tin chưa có được thêm mới. Chạy lại lệnh không nhân đôi tin và không ghi đè tin đã được sửa trong admin. Nếu gặp nhiều doanh nghiệp cùng tên cần đối chiếu, giao dịch dừng và hoàn tác.

Các trường chính của tin gồm mã, doanh nghiệp, tiêu đề, lĩnh vực, số người cần, hạn nộp, trạng thái. Mô tả, yêu cầu, quyền lợi, kỹ năng và các thông tin còn lại được lưu tại cột JSON `details` để giữ đủ cấu trúc nguồn.

## Sử dụng trong admin

Vào **Kết nối cung - cầu → Tin việc làm** (`/admin/job-postings`) để thêm/sửa tin, tải khung Excel, nhập và xuất danh sách.

Form thêm/sửa được chia thành thông tin tuyển dụng, địa điểm và điều kiện làm việc, nội dung tuyển dụng. Form sử dụng toàn bộ chiều ngang và tự giảm số cột trên màn hình nhỏ. Không hiển thị ô mã tin; tin tạo qua form được tự cấp mã `job-<UUID>`, các tin cũ giữ nguyên mã và đường dẫn. Mã trong Excel tiếp tục được dùng để đối chiếu cập nhật tin.

Mỗi dòng Excel có mã tin duy nhất. Dùng lại mã để cập nhật. Doanh nghiệp cần được tạo hoặc nhập Excel trước. Tệp có sheet danh mục doanh nghiệp, lĩnh vực và trạng thái; sheet hướng dẫn giải thích quy ước.

Chế độ kiểm tra thử được bật mặc định. Nếu tệp có lỗi, không có dòng nào được ghi. Các ô tùy chọn trống giữ lại thông tin cũ. Tin mới mặc định nháp; chọn `PUBLISHED` để công bố. `CLOSED` và `DRAFT` không xuất hiện ở client. Doanh nghiệp ngừng hoạt động và các tin thuộc doanh nghiệp đó cũng không xuất hiện.

**Nhu cầu nhân lực** là dữ liệu tổng hợp theo ngành/trình độ/kỳ báo cáo, có trang và khung Excel riêng. Lần chuyển đổi này không tự sinh nhu cầu tổng hợp từ tin việc làm để tránh cộng hai lần số người trong các báo cáo cung cầu.

## Cập nhật trên VPS

Thực hiện sau khi đã đưa toàn bộ code mới và bản chụp JSON lên Git. Sao lưu CSDL theo quy trình hiện có trước khi cập nhật.

```bash
cd /var/www/schoolmap
git pull --ff-only origin main
cd backend
php artisan migrate --force
php artisan directory:import-legacy --dry-run
php artisan directory:import-legacy
php artisan optimize:clear
cd ..
npm ci
npm run build
```

Migration tạo bảng mới và thêm hai cột vào employers; không xóa dữ liệu trường học hoặc nhu cầu hiện có. Cần chạy migration và chuyển dữ liệu trước khi phát hành bản build frontend mới.

## Kiểm tra sau cập nhật

```bash
curl -f https://ninhbinh.poly-hna.com/api/public/directory
```

Kiểm tra trang doanh nghiệp, tin tuyển dụng, các URL chi tiết cũ và danh mục chọn vai trò doanh nghiệp. Tổng doanh nghiệp CSDL có thể lớn hơn 94 do giữ các doanh nghiệp đã tồn tại ngoài bản chụp.

Frontend tải danh mục khi chuyển trang và dùng bộ nhớ tạm tối đa 30 giây; tải lại trang để nhận ngay dữ liệu sau khi nhập Excel. Khi API lỗi, giao diện báo lỗi và có nút thử lại.
