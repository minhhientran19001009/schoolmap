# Kế hoạch thu thập và đồng bộ dữ liệu trường học qua Google Form

## 1. Mục tiêu

Cho phép các trường nhập dữ liệu thông qua Google Form. Google Form tự động lưu phản hồi vào Google Sheet. Admin kiểm tra dữ liệu, xem trước thay đổi và xác nhận cập nhật vào database của hệ thống.

Mô hình tổng quát:

```text
Nhà trường điền Google Form
        ↓
Google Form lưu phản hồi vào Google Sheet
        ↓
Admin kiểm tra dữ liệu
        ↓
Hệ thống đọc dữ liệu từ Sheet
        ↓
Hiển thị bản xem trước thay đổi
        ↓
Admin xác nhận
        ↓
Cập nhật database và bản đồ
```

Google Form chỉ là kênh thu thập dữ liệu. Database của hệ thống là nguồn dữ liệu chính thức sau khi Admin duyệt.

## 2. Phạm vi áp dụng

Phương án phù hợp với giai đoạn đầu khi các trường chưa cần thao tác trực tiếp trên hệ thống hoặc chưa quen với giao diện quản trị.

Tài khoản nhà trường vẫn có thể được giữ lại để:

- Xem thông tin trường đã được duyệt.
- Xem trạng thái lần gửi dữ liệu gần nhất.
- Xem lỗi dữ liệu cần bổ sung.
- Xem lịch sử cập nhật.
- Nhận link Google Form tương ứng.

Không nên để Google Form và tài khoản nhà trường cùng chỉnh sửa trực tiếp database theo hai luồng độc lập, vì sẽ phát sinh xung đột dữ liệu.

## 3. Cấu trúc Google Form

Không nên gom toàn bộ dữ liệu vào một Form duy nhất vì trường có nhiều dữ liệu lặp như cơ sở, ngành nghề, lãnh đạo và doanh nghiệp.

### 3.1. Form thông tin trường

Mỗi phản hồi tương ứng với một trường:

- Mã trường.
- Tên trường.
- Địa chỉ chi tiết.
- Phường/xã.
- Kinh độ.
- Vĩ độ.
- Số điện thoại.
- Email.
- Website.
- Người đại diện hoặc hiệu trưởng.
- Số người học.
- Số giáo viên.
- Cơ sở vật chất.
- Hình ảnh nếu cần.

### 3.2. Form cơ sở trực thuộc

Mỗi phản hồi tương ứng với một cơ sở:

- Mã trường chính.
- Mã cơ sở.
- Tên cơ sở.
- Loại cơ sở.
- Địa chỉ.
- Phường/xã.
- Kinh độ.
- Vĩ độ.
- Số điện thoại.
- Thông tin cơ sở vật chất.

### 3.3. Form ngành/nghề đào tạo

Mỗi phản hồi tương ứng với một ngành của một trường:

- Mã trường.
- Mã chuyên ngành.
- Tên chuyên ngành để đối chiếu.
- Chỉ tiêu tuyển sinh.

Trường không được tự tạo chuyên ngành mới. Nếu ngành chưa có trong danh mục hệ thống, dữ liệu được đưa vào danh sách cảnh báo để Admin xử lý.

### 3.4. Form doanh nghiệp hợp tác

Mỗi phản hồi tương ứng với một doanh nghiệp:

- Mã trường.
- Tên doanh nghiệp.
- Nội dung hợp tác.
- Website.
- Thông tin liên hệ nếu cần.

Các Form có thể lưu vào các tab khác nhau trong cùng một Google Sheet hoặc các Sheet riêng.

## 4. Quy tắc định danh dữ liệu

Không đồng bộ dựa trên tên trường hoặc tên ngành vì tên có thể thay đổi và có thể bị nhập sai chính tả.

Sử dụng các mã ổn định:

```text
school_code
campus_code
training_major_id
```

Ví dụ:

```text
school_code: NB-CD-001
campus_code: NB-CD-001-CS01
training_major_id: 125
```

Mã trường được lấy từ database và không cho trường tự sửa. Link Form có thể được tạo sẵn với mã trường, nhưng vẫn phải kiểm tra lại khi đồng bộ.

## 5. Xác định trường gửi Form

Nên kết hợp nhiều thông tin để đối chiếu:

- Mã trường được điền sẵn trong Form.
- Email người gửi.
- Mã trường trong database.
- Tài khoản nhà trường tương ứng nếu có.

Ví dụ:

```text
school_code: NB-CD-001
email: daotao@truongabc.edu.vn
```

Nếu email và mã trường không khớp, hệ thống không tự cập nhật mà đưa dòng đó vào danh sách lỗi chờ Admin xử lý.

Link điền sẵn của Google Form không phải cơ chế bảo mật tuyệt đối. Với dữ liệu quan trọng, nên bật thu thập email hoặc giới hạn người trả lời bằng tài khoản Google.

## 6. Xử lý nhiều lần gửi Form

Google Form thường tạo một dòng mới cho mỗi lần gửi. Vì vậy một trường có thể có nhiều phiên bản phản hồi:

```text
10/09/2026 - Lần gửi 1
15/09/2026 - Lần gửi 2
20/09/2026 - Lần gửi 3
```

Hệ thống cần:

- Dùng `Timestamp` để xác định lần gửi mới nhất.
- Nhóm dữ liệu theo `school_code` hoặc mã định danh tương ứng.
- Không tự cập nhật ngay khi có dòng mới.
- Cho Admin xem bản ghi mới nhất trước khi xác nhận.
- Lưu lại mã dòng, thời gian gửi và người gửi.

Không xóa lịch sử phản hồi gốc trong Google Sheet.

## 7. Cách đồng bộ vào hệ thống

### 7.1. Giai đoạn đơn giản

Admin tải Google Sheet về dạng Excel và sử dụng chức năng nhập Excel hiện có.

Ưu điểm:

- Triển khai nhanh.
- Không cần Google Sheets API.
- Có thể tái sử dụng logic import Excel hiện tại.

Nhược điểm:

- Admin phải tải file thủ công.
- Không có nút đồng bộ trực tiếp.

### 7.2. Giai đoạn hoàn chỉnh

Backend kết nối Google Sheets API. Admin bấm nút `Lấy dữ liệu từ Google Sheet`.

Quy trình:

1. Đọc các tab phản hồi.
2. Lấy dữ liệu theo tên cột cố định.
3. Xác định phiên bản mới nhất.
4. Kiểm tra mã trường, mã cơ sở và mã chuyên ngành.
5. Kiểm tra dữ liệu bắt buộc.
6. So sánh với dữ liệu hiện tại.
7. Hiển thị bản xem trước thay đổi.
8. Admin xác nhận cập nhật.
9. Ghi database bằng transaction.
10. Ghi nhật ký đồng bộ.

## 8. Google Sheets API

Phương án khuyến nghị là dùng Google Sheets API với Service Account.

Các bước cấu hình:

1. Tạo Google Cloud Project.
2. Bật Google Sheets API.
3. Tạo Service Account.
4. Cấp quyền đọc cho Service Account trên các Sheet.
5. Lưu thông tin xác thực trong biến môi trường hoặc Secret Manager.
6. Không đưa file credential vào Git hoặc thư mục public.

Không nên dùng Google Sheet ở chế độ công khai nếu có dữ liệu liên hệ, dữ liệu nội bộ hoặc thông tin người phụ trách.

## 9. Database cần bổ sung

### 9.1. Bảng liên kết nguồn dữ liệu

Đề xuất bảng `school_data_sources`:

```text
id
school_id
provider                 google_sheets
google_spreadsheet_id
google_spreadsheet_url
status
last_synced_at
last_synced_by
last_sync_status
last_sync_error
created_at
updated_at
```

Trạng thái đề xuất:

```text
CONNECTED
DISABLED
ERROR
PENDING
```

### 9.2. Bảng lịch sử đồng bộ

Đề xuất bảng `school_sheet_sync_logs`:

```text
id
school_id
sync_type
source_spreadsheet_id
started_at
finished_at
total_rows
valid_rows
error_rows
created_rows
updated_rows
status
error_summary
executed_by
created_at
```

Bảng này phục vụ việc kiểm tra, truy vết và xử lý khi dữ liệu bị sai.

## 10. Kiểm tra dữ liệu trước khi cập nhật

Hệ thống cần kiểm tra:

- Trường có tồn tại không.
- Mã trường có chính xác không.
- Cơ sở trực thuộc có thuộc đúng trường chính không.
- Mã cơ sở có bị trùng không.
- Kinh độ có nằm trong khoảng từ -180 đến 180 không.
- Vĩ độ có nằm trong khoảng từ -90 đến 90 không.
- Email có đúng định dạng không.
- Website có đúng định dạng không.
- Chỉ tiêu tuyển sinh có phải số không âm không.
- Chuyên ngành có tồn tại trong danh mục chung không.
- Có bị trùng ngành trong cùng trường không.
- Các trường bắt buộc có bị bỏ trống không.

Kết quả kiểm tra phân thành:

```text
Hợp lệ
Cảnh báo
Lỗi không thể cập nhật
```

Nếu có lỗi nghiêm trọng, không cập nhật một phần dữ liệu. Sử dụng transaction để cập nhật toàn bộ hoặc rollback toàn bộ.

## 11. Quy tắc xử lý xóa dữ liệu

Không được tự động xóa dữ liệu trong hệ thống chỉ vì dòng đó bị thiếu trong Google Sheet.

Nếu cần ngừng sử dụng, sử dụng cột:

```text
action
```

Giá trị:

```text
UPSERT
DISABLE
DELETE
```

- `UPSERT`: thêm mới hoặc cập nhật.
- `DISABLE`: ngừng hiển thị nhưng giữ dữ liệu.
- `DELETE`: chỉ thực hiện sau khi Admin xác nhận.

## 12. Giao diện Admin cần bổ sung

Trong trang quản lý trường học, thêm khu vực `Kết nối Google Sheet` với các chức năng:

- Nhập URL Google Sheet.
- Kiểm tra kết nối.
- Hiển thị trạng thái kết nối.
- Lấy dữ liệu từ Google Sheet.
- Xem trước thay đổi.
- Xác nhận cập nhật.
- Xem lần đồng bộ gần nhất.
- Xem lỗi đồng bộ.
- Ngắt liên kết Google Sheet.

Không nên có thao tác ghi đè trực tiếp mà không hiển thị bản xem trước.

## 13. Tái sử dụng code hiện tại

Hệ thống hiện đã có logic xử lý Excel, trường học, cơ sở trực thuộc, chuyên ngành và chỉ tiêu tuyển sinh. Có thể tái sử dụng:

- Logic mapping trường.
- Logic mapping cơ sở.
- Logic mapping chuyên ngành.
- Logic kiểm tra chỉ tiêu.
- Logic cập nhật quan hệ nhiều-nhiều.
- Cơ chế thông báo kết quả nhập dữ liệu.

Phần mới cần bổ sung chủ yếu là bộ đọc Google Sheets, cơ chế xem trước và lịch sử đồng bộ.

## 14. Lộ trình triển khai

### Giai đoạn 1: Chuẩn hóa Form

- Thiết kế các Form và câu hỏi.
- Chuẩn hóa mã trường, mã cơ sở, mã ngành.
- Khóa hoặc hạn chế thay đổi hàng tiêu đề.
- Thêm hướng dẫn nhập dữ liệu.

### Giai đoạn 2: Thử nghiệm thủ công

- Chọn 3–5 trường thử nghiệm.
- Tải Sheet về Excel.
- Nhập bằng chức năng Excel hiện có.
- Ghi nhận các lỗi dữ liệu và điều chỉnh Form.

### Giai đoạn 3: Kết nối API

- Cấu hình Google Cloud.
- Tạo Service Account.
- Lưu thông tin liên kết Sheet.
- Kiểm tra khả năng đọc dữ liệu.

### Giai đoạn 4: Đồng bộ có xem trước

- Đọc phản hồi mới nhất.
- Validate dữ liệu.
- So sánh dữ liệu cũ và mới.
- Hiển thị danh sách thay đổi.
- Cho Admin xác nhận.

### Giai đoạn 5: Hoàn thiện vận hành

- Ghi lịch sử đồng bộ.
- Thêm chức năng thử lại khi lỗi.
- Thêm bộ lọc các trường có dữ liệu lỗi.
- Thiết lập quy trình định kỳ yêu cầu trường cập nhật dữ liệu.

## 15. Kết luận

Mô hình Google Form → Google Sheet → Admin duyệt → Database là khả thi và phù hợp với giai đoạn đầu của dự án.

Khuyến nghị triển khai theo các nguyên tắc:

```text
Google Form là kênh nhập liệu
Google Sheet là dữ liệu trung gian
Database là dữ liệu chính thức
Admin là người kiểm tra và xác nhận
Đồng bộ một chiều
Không xóa tự động
Luôn có mã định danh ổn định
```

Nên bắt đầu bằng quy trình tải Sheet về Excel để thử nghiệm với một số trường, sau đó mới phát triển kết nối Google Sheets API và nút đồng bộ trực tiếp trong Admin.
