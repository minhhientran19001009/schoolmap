# Báo cáo làm sạch dữ liệu GDNN/GDTX (2026-11-10)

## Kết quả

- Excel nguồn: **15 cơ sở**, **304 dòng ngành/nghề**, còn **256 bản ghi trường–ngành/nghề** sau khi gộp các dòng cùng tên trong từng cơ sở.
- Ghép với dữ liệu dự án hiện có: **10 khớp chắc chắn**, **2 cần duyệt**, **3 chưa ghép**.
- Word nguồn: **23 trung tâm** có địa chỉ và số điện thoại giám đốc đã chuẩn hóa.
- Tổng số cảnh báo/truy vết: **137**. Cảnh báo không đồng nghĩa bản ghi sai; chúng chỉ ngăn việc nhập tự động thiếu kiểm soát.

Phân loại cảnh báo chính:

- **60** bản ghi ngành/nghề không có mã trong nguồn.
- **38** bản ghi sạch được gộp từ nhiều dòng cùng tên của một cơ sở.
- **2** bản ghi có số tổng và số theo trình độ chưa khớp.
- **8** tên ngành/nghề được sửa lỗi chính tả/cách viết rõ ràng.
- **22** địa chỉ được tách tên cán bộ và chuẩn hóa cách viết.

## Quy tắc làm sạch

1. Không sửa hai file nguồn và không xóa giá trị gốc; mọi bản ghi đều có `source_ref` hoặc `source_rows`.
2. Loại dòng tiêu đề/dòng tổng; gộp ngành trùng tên trong cùng cơ sở nhưng giữ toàn bộ mã và dòng nguồn.
3. Chuẩn hóa khoảng trắng, Unicode, cách viết “ô tô”, “điều hòa không khí”, ký hiệu `&`, cùng một số lỗi chính tả rõ ràng.
4. Tổng sạch ưu tiên tổng theo các trình độ. Chênh lệch còn lại được giữ ở `unallocated`, không tự phân bổ vào cao đẳng/trung cấp/sơ cấp.
5. Địa chỉ Word đã bỏ tên cán bộ khỏi cột địa chỉ và chuẩn hóa viết hoa/đơn vị hành chính.
6. Số điện thoại trong Word là **số của giám đốc**, không phải số tổng đài cơ sở; vì vậy không tự ghi vào `schools.phone`.

## Điểm chưa được nhập tự động

- Mã cơ sở trùng trong nguồn: **TCD2506**.
- Hai tên có khả năng là cơ sở đổi tên/sáp nhập chỉ được đưa vào `candidate_project_school_id`, chưa coi là khớp.
- Cơ sở `unmatched` chưa có `project_school_id`; cần xác nhận bản ghi trường trong CSDL trước khi nhập quan hệ ngành/nghề.
- Các tổng không khớp vẫn được bảo toàn trong JSON và sheet `Can_xac_minh`; không làm tròn hoặc tự chia số.

## Tệp đầu ra

- `tai_lieu/du_lieu_sach/du_lieu_gdnn_sach_2026_11_10.xlsx`: bản Excel sạch để duyệt.
- `tai_lieu/du_lieu_sach/co_so_gdnn_sach.csv`: danh sách cơ sở.
- `tai_lieu/du_lieu_sach/nganh_nghe_sach.csv`: dữ liệu ngành/nghề dạng phẳng.
- `tai_lieu/du_lieu_sach/dia_chi_trung_tam_sach.csv`: địa chỉ và số giám đốc.
- `tai_lieu/du_lieu_sach/du_lieu_can_xac_minh.csv`: toàn bộ cảnh báo.
- `backend/database/data/gdnn_map_clean_2026_11_10.json`: dữ liệu có cấu trúc để viết lệnh import an toàn.

## Khuyến nghị nhập dự án

Chỉ cho phép lệnh import xử lý các cơ sở có `match_status=matched`, chạy **dry-run** trước và sao lưu các bảng `schools`, `training_majors`, `school_training_major`. Địa chỉ trung tâm phải ghép duy nhất theo tên/mã cơ sở; số điện thoại giám đốc nên lưu ở trường dữ liệu liên hệ lãnh đạo, không ghi đè số điện thoại chung.
