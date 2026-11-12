# Yêu cầu kỹ năng theo chuyên ngành đào tạo

Cập nhật: 12/11/2026.

## Quy tắc dữ liệu

- Doanh nghiệp chọn chuyên ngành từ `training_majors`, có liên kết với cơ sở đào tạo qua `school_training_major`.
- Biểu mẫu doanh nghiệp chỉ đưa ra chuyên ngành đang sử dụng và có cơ sở hoạt động đào tạo chuyên ngành đó. Khi chọn ngành, hiển thị các cơ sở tương ứng từ DB; mỗi cơ sở chỉ xuất hiện một lần dù có nhiều trình độ.
- Mỗi nhu cầu lưu `training_major_id`, trình độ, kỳ báo cáo và các kỹ năng chi tiết do doanh nghiệp nhập. Nhà trường khai báo năng lực theo cùng danh mục chuyên ngành.
- Kỹ năng được nhập bằng dấu phẩy hoặc xuống dòng, lưu thành mảng JSON trên từng nhu cầu/năng lực. Không cần duyệt kỹ năng, không tự sinh một chuyên ngành từ nội dung kỹ năng.
- Khi đối chiếu nhu cầu và năng lực, chuyên ngành, trình độ và kỳ phải khớp. Kỹ năng chi tiết được so sánh sau đó; cùng một kỹ năng không tự tạo liên kết giữa hai chuyên ngành khác nhau.
- Danh sách trường đào tạo thể hiện ngành đã khai báo trong danh mục. Năng lực cung ứng hiện tại phải lấy từ khai báo năng lực riêng.
- Đề xuất theo lĩnh vực vẫn giữ phạm vi lĩnh vực như luồng hiện tại.

## Chuyển đổi dữ liệu

Migration `2026_11_12_000004_store_skills_on_supply_demand_records` thêm cột JSON và sao chép tên kỹ năng từ các bảng liên kết cũ. Giữ bảng cũ để khôi phục; ngừng dùng menu quản trị kỹ năng. Chạy `php artisan migrate --force` khi cập nhật VPS.

Không tự gán chuyên ngành cho tin việc làm cũ dựa trên tên kỹ năng hoặc tên công việc. Việc gán cần căn cứ dữ liệu xác nhận.
