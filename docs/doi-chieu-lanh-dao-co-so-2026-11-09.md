# Đối chiếu lãnh đạo cơ sở GDNN (09/11/2026)

Nguồn: `5.11.2026. danh sách cơ sở giáo dục nghề nghiệp.xlsx` và `DANH SÁCH LĐ CÁC TRUNG TÂM GDNN-GDTX.docx`. Chỉ trích họ tên và chức vụ lãnh đạo; không nhập số điện thoại, năm thành lập hay các cột khác. Bản trích dùng để đồng bộ nằm tại `backend/database/data/school_leaders_2026_11_05.json` (53 cơ sở có cán bộ: 28 từ Excel, 25 từ Word).

Đã đối chiếu với MySQL **local**: 30 cơ sở được cập nhật `principal` và `leaders`, 5 cơ sở vốn đã khớp; 18 mục nguồn chưa tìm thấy cơ sở chính có tên khớp duy nhất nên được giữ nguyên. Các dòng Excel không có tên lãnh đạo cũng được bỏ qua. Bản sao 30 giá trị cũ: `backend/storage/app/leader-sync-backups/20261109_032059_738456.json`. Không có thao tác nào trên VPS.

## Chưa tự ghép vào cơ sở trong CSDL

| Nguồn | Tên trong bản trích |
| --- | --- |
| Excel 7 | Trường Cao đẳng nghề Số 20 - BQP (CSDL chỉ có cơ sở Vị Khê và cơ sở trực thuộc) |
| Excel 11 | Trường Cao đẳng Kinh tế và Công nghệ tỉnh Ninh Bình |
| Excel 14 | Trường Cao đẳng Sư phạm và Nghệ thuật tỉnh Ninh Bình |
| Excel 16 | Trường Cao đẳng Y tế tỉnh Ninh Bình (CSDL có nhiều bản ghi tên gần giống) |
| Excel 23 | Trường Cao đẳng Phát thanh - Truyền hình I |
| Excel 25 | Trường Cao đẳng FPT Polytechnic |
| Excel 27 | Trường Trung cấp Giao thông Vận tải Nam Định |
| Excel 37 | Trung tâm GDNN Thanh niên |
| Excel 40 | Trung tâm Dạy chữ Braille và Dạy nghề cho người mù |
| Excel 42 | Trung tâm GDNN Hồng Hà |
| Excel 44 | Trung tâm GDNN K27 |
| Excel 45 | Trung tâm Dạy nghề Giao thông vận tải Thành Nam |
| Word 2 | GDTX Tỉnh |
| Word 3 | GDTX Trần Phú |
| Word 8 | Trung tâm GDNN-GDTX Trực Ninh |
| Word 11 | Trung tâm GDNN-GDTX Hải Hậu |
| Word 13 | Hướng nghiệp KTTH |
| Word 17 | Trung tâm GDNN-GDTX Tam Điệp |

Một số tên đơn vị trong nguồn đã đổi hoặc sáp nhập, nhưng chưa đủ căn cứ để chuyển lãnh đạo sang bản ghi cũ/chi nhánh. Cần xác nhận mã cơ sở trước khi bổ sung các mục trên. Tên “Nguyên Xuân Thủy” (Word 25) được giữ nguyên đúng theo tệp nguồn; cần xác minh nếu nghi là lỗi chính tả.

## Cách đối chiếu lại

Tại `backend/`, chạy `php artisan schools:sync-leaders` để xem trước. Sau khi xác nhận danh sách, chạy `php artisan schools:sync-leaders --apply`. Lệnh chỉ sửa hai trường lãnh đạo và `updated_at` của cơ sở chính trùng tên duy nhất, tự sao lưu giá trị cũ trước khi ghi. Lặp lại lệnh không tạo thay đổi khi dữ liệu đã khớp.
