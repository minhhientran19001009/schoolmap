# Kiểm tra cơ sở giáo dục trùng lặp — 12/11/2026

## Kết quả kiểm tra CSDL local

- Có **83 bản ghi trường/cơ sở**; không đồng nghĩa với 83 đơn vị độc lập đã xác minh.
- Phát hiện **12 cặp trùng tên hoặc tương đương sau chuẩn hóa cách viết**, tổng cộng 24 bản ghi. Đây là danh sách cần đối chiếu danh tính, chưa phải quyết định tự động gộp.
- Có thêm **1 cặp nghi trùng** của Trường Cao đẳng Phát thanh - Truyền hình I (VOV), cần xác minh quan hệ cơ sở.
- Trong 12 cặp, cả hai bản ghi đều có `campus_type=MAIN`, `parent_school_id=null`; hệ thống đang coi chúng là hai đơn vị chính riêng biệt.
- Nhiều cặp khác nhau về địa chỉ, tọa độ và danh mục ngành. Không chọn dữ liệu giữ lại chỉ bằng ngày tạo hoặc độ dài thông tin.

| STT | Cơ sở cần đối chiếu | ID dạng seed | ID còn lại |
| --- | --- | --- | --- |
| 1 | Cao đẳng FPT Polytechnic — Cơ sở Hà Nam | HN-CD-001 | truong-cao-dang-fpt-polytechnic-co-so-ha-339 |
| 2 | Cao đẳng Y tế Hà Nam | HN-CD-002 | truong-cao-dang-y-te-ha-nam-697 |
| 3 | Đại học Công nghiệp Hà Nội — Cơ sở Hà Nam | HN-DH-001 | truong-dai-hoc-cong-nghiep-ha-noi-co-so--877 |
| 4 | Đại học Thương mại — Cơ sở Hà Nam | HN-DH-002 | truong-dai-hoc-thuong-mai-co-so-ha-nam-681 |
| 5 | Đại học Sư phạm Hà Nội — Cơ sở Hà Nam | HN-DH-003 | truong-dai-hoc-su-pham-ha-noi-co-so-ha-n-912 |
| 6 | Trung tâm GDNN-GDTX Duy Tiên | HN-GDTX-002 | trung-tam-gdnn-gdtx-duy-tien-741 |
| 7 | Trung tâm GDNN-GDTX Kim Bảng | HN-GDTX-003 | trung-tam-gdnn-gdtx-kim-bang-936 |
| 8 | Trung tâm GDNN-GDTX Thanh Liêm | HN-GDTX-004 | trung-tam-gdnn-gdtx-thanh-liem-759 |
| 9 | Trung tâm GDNN-GDTX Lý Nhân | HN-GDTX-005 | trung-tam-gdnn-gdtx-ly-nhan-158 |
| 10 | Trung tâm GDNN-GDTX Bình Lục | HN-GDTX-006 | trung-tam-gdnn-gdtx-binh-luc-969 |
| 11 | Cao đẳng Y tế Ninh Bình | NB-CD-002 | truong-cao-dang-y-te-ninh-binh-254 |
| 12 | Đại học Hoa Lư | NB-DH-001 | truong-dai-hoc-hoa-lu-962 |
| 13 | VOV / VOV – Cơ sở 1: nghi trùng, cùng số 136 đường Quy Lưu | HN-CD-004 | truong-cao-dang-phat-thanh-truyen-hinh-i-736 |

## Nguyên nhân và bằng chứng

1. `backend/database/seeders/HaNamSchoolsSeeder.php:1107` dùng `School::updateOrCreate(['id' => $data['id']], $data)`. Nếu cơ sở đã tồn tại với ID khác, seeder vẫn tạo thêm bản ghi dù tên giống hệt. `DatabaseSeeder.php` đang gọi seeder này.
2. Các ID dạng seed trong bảng có thời điểm tạo ghi trong CSDL là `2026-10-20 08:51:36`; bản ghi tương ứng với ID dài là ngày `2026-09-21`. Các ID dạng seed khớp định nghĩa trong seeder. Đây là bằng chứng phù hợp với việc chồng dữ liệu seed lên dữ liệu đã có; chưa có nhật ký thực thi để xác định người/lệnh đã chạy.
3. `SchoolExcelService.php:897,1050` lập chỉ mục tên nguyên văn và tìm theo tên ở một số luồng nhập. Khi không khớp, tạo `School` mới. Khác dấu gạch, khoảng trắng, hoặc thêm “Huyện/Thị xã” có thể bị hiểu là cơ sở mới. Đây là rủi ro tái diễn, chưa phải bằng chứng file Excel đã tạo các cặp trên.
4. API danh sách công khai truy vấn trực tiếp các trường `VERIFIED`. Vì CSDL thực sự có hai hàng riêng biệt nên giao diện hiển thị cả hai, không phải chỉ lặp phần tử giao diện.

## Đối chiếu với lần nhập dữ liệu sạch gần nhất

- Bản sao trước nhập: `backend/storage/app/import-backups/clean-gdnn-20261112-042031-682756.json`.
- Trước nhập: **83** trường; sau nhập và tại thời điểm kiểm tra: **83** trường.
- So sánh tập ID: **không có ID trường mới**. Các cặp trong danh sách đã tồn tại trong bản sao trước nhập.
- `CleanGdnnImportService` chỉ cập nhật địa chỉ của cơ sở đã ghép và thêm hồ sơ nguồn/danh mục ngành/liên kết đào tạo; không tạo trường mới.
- Kiểm tra trước đó về số lượng và nhập lại không phát sinh thay đổi chưa bao quát được trùng danh tính cơ sở. Cần bổ sung bước kiểm tra này trước những lần nhập tiếp theo.

## Cách xử lý tiếp theo

1. Sao lưu dữ liệu, lập ánh xạ ID trùng sang ID chuẩn; xác minh các địa chỉ/phân hiệu khác nhau bằng hồ sơ nguồn. Không tự gộp trường chỉ vì tên gần giống.
2. Kiểm kê và chuyển đầy đủ liên kết ngành, cấp học, tài khoản, cơ sở trực thuộc, năng lực, đề xuất và hồ sơ nguồn trước khi loại bản ghi dư. Kiểm tra xung đột/quy tắc duy nhất của từng bảng liên kết.
3. Chỉ giữ thông tin có nguồn chắc chắn; không lấy số điện thoại lãnh đạo để điền bổ sung.
4. Sửa luồng seed/nhập để đối chiếu danh tính trước khi tạo; trường hợp nhiều ứng viên phải báo cần xác minh. Không đặt ràng buộc tên duy nhất toàn hệ thống vì các cơ sở hợp lệ có thể cùng tên.
5. Kiểm tra lại danh sách, bản đồ, bộ chọn đăng nhập và số liệu sau khi xử lý; chạy lại quy trình nhập để bảo đảm không sinh thêm trường.

**Phạm vi lần kiểm tra này:** chỉ đọc CSDL và mã nguồn, lưu báo cáo; chưa gộp, xóa hoặc thay đổi dữ liệu trường. Chưa kiểm tra trực tiếp CSDL VPS.

## Cập nhật sau khi được phép xử lý theo dữ liệu mới

Đã áp dụng trên **CSDL local** ngày 12/11/2026. Phần kết luận kiểm tra phía trên ghi nhận trạng thái trước xử lý.

| Hạng mục | Kết quả thực tế |
| --- | --- |
| Cơ sở trước / sau xử lý | 83 / 71 |
| Bản ghi seed dư đã loại khỏi danh sách | 12 |
| Bản ghi giữ lại bị thay đổi trường thông tin | 0 |
| Hồ sơ nguồn trước / sau | 44 / 44 |
| Hồ sơ nguồn chuyển sang ID chuẩn | 1 hồ sơ tuyển sinh từ NB-CD-002 |
| Liên kết ngành trước / sau | 482 / 479 |
| Liên kết ngành cũ lưu trong bản sao | 3 liên kết của NB-CD-002; giữ nguyên 4 liên kết ngành trên bản ghi chuẩn, không cộng thêm ngành/chỉ tiêu seed |
| Liên kết cấp học cũ lưu trong bản sao | 12; giữ nguyên cấp học của từng bản ghi chuẩn |
| Liên kết khóa ngoại trường bị mồ côi | 0 |
| Nhóm trùng tên theo chuẩn hóa còn lại | 0 |
| Cặp VOV / VOV – Cơ sở 1 | Giữ nguyên, chưa đủ căn cứ gộp |

Bản sao trước xử lý: `backend/storage/app/import-backups/school-dedup-20261112-043339-756028.json`. Bản sao lưu đầy đủ các bảng tham chiếu trường, có thể chứa thông tin tài khoản; giữ riêng trên máy chủ, không đưa lên Git hoặc thư mục public. Geometry được mã hóa hex để bảo toàn dữ liệu nhị phân.

Quyết định ID chuẩn được lưu tại `backend/config/school_identity.php`. Đây là ánh xạ chuyển đổi các ID cũ đã đối chiếu, không phải bộ dữ liệu trường tĩnh dùng để hiển thị.

### Ngăn tạo lại dữ liệu trùng

- Seeder bỏ qua bản ghi đã có hoặc đã có ID chuẩn tương ứng; không ghi đè dữ liệu đã sửa. Chạy thử lại thực tế: thêm **0** cơ sở, giữ nguyên **19** cơ sở; dữ liệu trường không thay đổi.
- Nhập Excel nhận diện tên khác dấu gạch/khoảng trắng và từ chỉ đơn vị hành chính; nhận diện mã seed cũ bằng ánh xạ đã đối chiếu. Nếu khớp nhiều cơ sở, báo cần đối chiếu thay vì chọn ngẫu nhiên hoặc tạo thêm.
- API chi tiết `/api/schools/{id}` nhận ID/mã cũ của 12 cơ sở và trả bản ghi chuẩn, tránh hỏng liên kết công khai đã lưu.
- Lệnh gộp chạy trong transaction, tạo bản sao trước ghi, kiểm tra các bảng có `school_id`, `parent_school_id` và khóa ngoại khác tham chiếu trường. Xung đột tài khoản hoặc dữ liệu nghiệp vụ làm hoàn tác toàn bộ; không tự xóa tài khoản/năng lực/đề xuất.

### Kiểm chứng

- Backend: **42 kiểm thử, 280 assertions đạt**; có kiểm tra giữ dữ liệu mới, chuyển tài khoản/năng lực/hồ sơ/cơ sở con, giữ liên kết đề xuất, rollback xung đột và chạy lại không thay đổi.
- Chạy lại lệnh gộp: **0** cặp cần xử lý.
- Chạy lại đối chiếu dữ liệu sạch: **0** hồ sơ cần ghi, **0** địa chỉ cần sửa, **0** ngành/liên kết cần thêm; vẫn bỏ qua 8 mục chưa chắc chắn.
- API bản đồ trả **71** cơ sở; API danh sách phân trang có `meta.total=71`; thử ID `NB-CD-002` và mã `37022` đều trả đúng bản ghi chuẩn, HTTP 200.
- Không bổ sung số điện thoại lãnh đạo; không sao chép thông tin seed chưa chắc chắn sang các trường thông tin của bản ghi chuẩn.

### Khi đưa thay đổi lên VPS

Sau khi cập nhật code và bảo đảm VPS đã có dữ liệu trường chuẩn tương ứng, chạy trong thư mục `backend`:

```bash
php artisan optimize:clear
php artisan schools:deduplicate
php artisan schools:deduplicate --apply
```

Lệnh đầu tiên đối chiếu gộp chưa ghi dữ liệu. Lệnh có `--apply` tự tạo bản sao trước khi xử lý. Nếu thiếu ID chuẩn hoặc không còn khớp ánh xạ, lệnh dừng; không ép gộp hay tạo lại dữ liệu bằng seeder. Chưa thực hiện các thao tác này trên VPS trong phiên làm việc này.
