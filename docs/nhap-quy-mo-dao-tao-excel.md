# Nhập quy mô đào tạo từ Excel

Ngày cập nhật: 17/11/2026.

Phiên bản demo local được ghi nhận tại [báo cáo dữ liệu demo](du-lieu-quy-mo-demo-2026-11-17.md). Các cột tùy chọn `Đại học`, `GDTX - GDNN` được đọc đúng theo trình độ; cột `Trường số giả lập` lưu nguồn gốc từng ô trong payload, không thay mã ngành hoặc chỉ tiêu.

## Hiển thị

Bảng chính ở Tổng quan hồ sơ cơ sở hiển thị Quy mô đào tạo, thay cho bảng chỉ tiêu tuyển sinh. Số lấy từ `training_source_report.programs[*].current_scale`, đơn vị người học; không lấy `annual_quota` làm dữ liệu dự phòng. Danh mục cấp học từ DB vẫn hiển thị đầy đủ; các trình độ trong nguồn gồm Cao đẳng, Trung cấp, Sơ cấp và khác được thêm khi danh mục DB chưa có. SC và khác được giữ là một nhóm, không tự gán riêng sang Sơ cấp.

Không hiển thị khối quy mô cũ thứ hai bên dưới bảng chính. Báo cáo tuyển sinh lịch sử vẫn là dữ liệu riêng. Mã ngành không thay cấu trúc.

Ô nguồn trống hiển thị `—`, số 0 giữ là 0. Các cấp học ngoài các trình độ nguồn hiển thị 0 khi đã có báo cáo. Khi chưa có báo cáo, tên ngành giữ từ danh mục nhưng mọi số quy mô hiển thị `—`. Phần chênh dương chưa phân bổ có cột riêng. Tổng nguồn nhỏ hơn tổng trình độ được giữ trong payload và đánh dấu cần xác minh, tổng hiển thị `—`; không tự biến thành số đã xác nhận. Nếu tổng nguồn trống nhưng có số trình độ thì tổng cộng các trình độ được dùng cho đối chiếu/hiển thị.

## File đã chuẩn bị

`docs/exports/Danh_Sach_Truong_Hoc_Ninh_Binh_2026_11_17_Nganh_Va_Quy_Mo.xlsx`

- Giữ các sheet nhập liệu ban đầu và 102 tên ngành bổ sung.
- `QM_Tong_hop`: 15 cơ sở; giữ tổng và phân bổ gốc.
- `QM_Theo_nganh`: 256 dòng đã gộp tên cùng ngành; đây là sheet số liệu chính để nhập.
- `QM_Dong_goc`: 304 dòng nguyên số liệu F–I để truy vết.
- `QM_Can_xac_minh`: các điểm cần đối chiếu.
- Chỉ phần Quy mô đào tạo hiện tại; không dùng dự kiến tuyển sinh đến 31/12/2026 để thay thế.

## Thao tác admin

1. Cập nhật backend và build frontend mới trên VPS.
2. Admin → Danh sách Trường học → **Nhập quy mô đào tạo**.
3. Chọn file, giữ ô **Chỉ kiểm tra, chưa ghi dữ liệu** để xem số hồ sơ/ngành.
4. Khi đối chiếu thành công, chọn lại file, bỏ chọn ô kiểm tra và nhập để ghi.
5. Với file này kỳ vọng 15 hồ sơ (tạo mới/cập nhật/không đổi) và 256 dòng ngành. Chạy lại không sinh báo cáo trùng.

Chức năng này chỉ ghi `school_source_records` loại `training_report`. Nó không nhập lại `Mau_Nhap_Lieu`, không xóa liên kết trường–ngành, không sửa mã ngành hoặc chỉ tiêu. Nếu chưa nhập 102 tên ngành bằng chức năng nhập trường trước đó, bảng quy mô vẫn hiển thị ngành từ báo cáo, nhưng danh mục dùng cho bộ lọc ngành không tự được bổ sung bởi lệnh nhập quy mô.

## Quy tắc đối chiếu và ghi

- Tên trường trong hai sheet QM phải khớp với dòng tham chiếu của Mau_Nhap_Lieu và phải tìm được duy nhất trên DB. ID/hậu tố và mã nhóm khác giữa local/VPS không được tự dùng để ghép nhầm.
- Khác tên cơ sở, trùng ngành trong một cơ sở, tham chiếu không hợp lệ, số âm/thập phân/công thức hoặc thiếu sheet/cột cần thiết đều dừng cả tệp trước khi ghi.
- Bộ nhập xác định lại tổng/phần chưa phân bổ từ bốn cột nguồn, không tin các cột tính đối chiếu trong Excel.
- Trước ghi lưu bản sao báo cáo bị tác động tại `backend/storage/app/import-backups/training-scale-*.json`, sau đó ghi trong transaction.
- Giữ các mã và kế hoạch của ngành đã có khi tên ghép được; nhập quy mô không cập nhật danh mục mã ngành. Các trường khác của payload được giữ nguyên. Danh sách ngành hiện tại được thay bằng danh sách trong sheet QM_Theo_nganh.

Các trường hợp được giữ để xác minh: Sư phạm GDNN tại Cơ điện Nông nghiệp và Môi trường có 100 chưa phân bổ; Đào tạo lái xe tại Kinh tế và Công nghệ có 465 chưa phân bổ; tổng Đại Lâm nguồn 0 khác tổng theo trình độ 5.150; Nam Tam Điệp tổng nguồn trống nhưng SC và khác là 8.500.

## Kiểm tra

```powershell
cd backend
php artisan test --filter=TrainingScaleExcelTest
cd ..
node --test scripts/tests/trainingScale.test.mjs scripts/tests/schoolScaleRender.test.mjs
npm.cmd run build
```

Các kiểm tra chạy trên CSDL test SQLite, không ghi dữ liệu local đang dùng hoặc VPS.
