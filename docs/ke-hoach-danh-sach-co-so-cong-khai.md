# Kế hoạch bổ sung trang danh sách cơ sở GDNN công khai

## 1. Mục tiêu

Người dân, học sinh, phụ huynh và người lao động có thêm một cách tra cứu cơ sở GDNN ngoài bản đồ: xem danh sách dạng thẻ, tìm kiếm, lọc và mở hồ sơ chi tiết của từng cơ sở.

Trang này lấy dữ liệu trực tiếp từ cơ sở dữ liệu của hệ thống hiện tại. Bản đồ vẫn là kênh xem vị trí; trang danh sách tối ưu cho việc so sánh, tổng hợp và tra cứu nhiều cơ sở liên tục.

## 2. Phạm vi phiên bản đầu tiên

- Đường dẫn công khai đề xuất: `/co-so-gdnn`, kèm alias `/danh-sach-co-so`.
- Thêm mục điều hướng **Danh sách cơ sở** bên cạnh Bản đồ GDNN, Kết nối cung cầu và Việc làm GDNN.
- Hiển thị cơ sở theo lưới thẻ; màn hình nhỏ chuyển thành một cột.
- Tìm kiếm không phân biệt dấu tiếng Việt theo: tên cơ sở, mã trường, tên ngành/nghề và mã ngành.
- Lọc theo cấp học, loại hình cơ sở, địa bàn huyện/thành phố và xã/phường.
- Phân trang phía máy chủ; không tải toàn bộ dữ liệu lên trình duyệt.
- Nhấn **Xem chi tiết** mở lại ngăn chi tiết cơ sở hiện có, gồm thông tin liên hệ, ngành/nghề, chỉ tiêu theo cấp học, cơ sở trực thuộc và doanh nghiệp liên kết.
- Có nút **Xem trên bản đồ** để chuyển tới `/map` và định vị đúng cơ sở.

Không triển khai chức năng chỉnh sửa, xuất Excel hoặc hiển thị dữ liệu nội bộ cho người dân trong phạm vi này.

## 3. Bố cục trang

### 3.1. Phần đầu trang

- Tiêu đề: **Danh sách cơ sở GDNN Ninh Bình**.
- Mô tả ngắn: tra cứu cơ sở, ngành/nghề đào tạo và chỉ tiêu tuyển sinh.
- Các số tổng quan động theo bộ lọc đang áp dụng:
  - Tổng số cơ sở phù hợp.
  - Số cấp học đang có cơ sở phù hợp.
  - Tổng ngành/nghề đào tạo (không đếm trùng theo cơ sở).
  - Tổng chỉ tiêu/năm, chỉ tính từ dữ liệu chỉ tiêu đã khai báo.
- Breadcrumb hoặc nút quay lại bản đồ GDNN.

### 3.2. Khối lọc

- Ô tìm kiếm chính có gợi ý và cho phép xóa nhanh nội dung.
- Bộ lọc cấp học: GDTX - GDNN, Trung cấp, Cao đẳng, Đại học. Danh sách lấy từ bảng `education_levels` trong DB.
- Bộ lọc loại hình: công lập, ngoài công lập/tư thục (nếu dữ liệu có).
- Bộ lọc địa bàn: huyện/thành phố, sau đó thu hẹp theo xã/phường.
- Bộ lọc ngành/nghề: chọn một ngành, có ô tìm kiếm tiếng Việt không dấu.
- Nút **Đặt lại** và nhãn hiển thị tổng số kết quả.
- Bộ lọc được đồng bộ lên URL query, ví dụ: `/co-so-gdnn?level=cao_dang&major=12&district=...`; người dùng có thể sao chép liên kết kết quả.

### 3.3. Thẻ cơ sở

Mỗi thẻ hiển thị ngắn gọn, đồng nhất với mẫu người dùng cung cấp:

- Biểu tượng giáo dục, nhãn cấp học và mã trường.
- Tên cơ sở; tên dài xuống tối đa hai hoặc ba dòng, không cắt mất khi có thể mở chi tiết.
- Địa chỉ ngắn: xã/phường và huyện/thành phố.
- Danh sách tối đa ba ngành/nghề nổi bật; mỗi mục có thể kèm mã ngành nếu đã có trong danh mục.
- Dòng `Còn N ngành/nghề` khi còn ngành chưa hiển thị.
- Khối **Tổng chỉ tiêu/năm** tính từ tổng `annual_quota` của các ngành/nghề gắn với cơ sở. Nếu chưa có số liệu, hiển thị `0`, không tạo số liệu thay thế.
- Hai hành động: **Xem chi tiết** và biểu tượng **Xem trên bản đồ**.

Màu nhãn cấp học dùng cấu hình `color` từ bảng `education_levels`; không cố định màu ở giao diện.

### 3.4. Danh sách và trạng thái

- Desktop: 3 thẻ mỗi hàng ở chiều rộng phù hợp; 2 thẻ trên tablet; 1 thẻ trên điện thoại.
- Có skeleton khi tải dữ liệu, trạng thái rỗng rõ nguyên nhân và nút đặt lại bộ lọc.
- Phân trang có số trang, nút trước/sau và hiển thị dải bản ghi, ví dụ `1–12 / 88 cơ sở`.
- Giữ vị trí cuộn khi đổi trang; cuộn lên đầu vùng danh sách khi thay đổi bộ lọc.

## 4. Dữ liệu và quy tắc hiển thị

| Nội dung | Nguồn dữ liệu chuẩn | Quy tắc |
| --- | --- | --- |
| Tên, mã, địa chỉ, tọa độ | `schools` | Hiển thị dữ liệu đã lưu; thiếu địa chỉ thì dùng xã/phường và huyện/thành phố nếu có. |
| Cấp học | `education_levels` và `school_education_levels` | Tên, màu và thứ tự lấy từ danh mục DB. |
| Ngành/nghề | `training_majors` và `school_training_major` | Dùng quan hệ chuẩn; dữ liệu JSON cũ chỉ là phương án tương thích trong giai đoạn chuyển đổi. |
| Chỉ tiêu | `school_training_major.annual_quota` | Cộng theo cơ sở và ngành/nghề; thiếu dữ liệu là `0`. |
| Loại hình | `school_types` / `schools.school_type_id` | Hiển thị nhãn danh mục khi có. |
| Địa bàn | `districts`, `wards`, trường `ward` | Không công khai dữ liệu nội bộ ngoài các trường đã được duyệt. |

Quy tắc công khai cần được chốt trước khi triển khai: chỉ hiển thị cơ sở có `status = VERIFIED`, hay hiển thị tất cả trừ `INACTIVE`. Khuyến nghị cho người dân chỉ hiển thị `VERIFIED`; quản trị viên vẫn xem toàn bộ trong Filament.

## 5. Thiết kế API

Tạo endpoint công khai mới, không thay đổi cấu trúc của `/api/schools` đang phục vụ bản đồ:

```text
GET /api/public/schools
```

Tham số dự kiến:

| Tham số | Ví dụ | Mục đích |
| --- | --- | --- |
| `q` | `cong nghe o to` | Tìm tên, mã trường, ngành/nghề hoặc mã ngành không phân biệt dấu. |
| `level` | `cao_dang` | Lọc cấp học. |
| `major_id` | `12` | Lọc một ngành/nghề. |
| `school_type` | `cong_lap` | Lọc loại hình. |
| `district_id` | `tp-ninh-binh` | Lọc huyện/thành phố. |
| `ward` | `Phường ...` | Lọc xã/phường. |
| `page` / `per_page` | `2` / `12` | Phân trang, giới hạn `per_page` an toàn từ 9 đến 24. |
| `sort` | `name` | Sắp xếp: tên, cấp học hoặc mới cập nhật. |

Phản hồi dự kiến:

```json
{
  "data": [
    {
      "id": "...",
      "code": "...",
      "name": "...",
      "education_levels": [{ "id": "cao_dang", "name": "Cao đẳng", "color": "#16a34a" }],
      "district_name": "...",
      "ward": "...",
      "address": "...",
      "training_major_count": 8,
      "training_major_preview": [{ "id": 12, "name": "...", "code": "..." }],
      "annual_quota_total": 350,
      "lat": 20.25,
      "lng": 105.97
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 12,
    "total": 88,
    "last_page": 8,
    "summary": {
      "school_count": 88,
      "level_count": 4,
      "major_count": 195,
      "annual_quota_total": 12345
    }
  }
}
```

API chỉ trả về trường dữ liệu cần hiển thị, không trả về email quản trị, tài khoản, ghi chú nội bộ hoặc JSON nặng. Thống kê phải được tính cùng điều kiện lọc với danh sách kết quả.

## 6. Công việc theo giai đoạn

### Giai đoạn 0 — Chốt nghiệp vụ và dữ liệu (0,5 ngày)

1. Chốt quy tắc công khai trạng thái cơ sở.
2. Chốt tên route, vị trí trên thanh điều hướng và số thẻ mỗi trang mặc định.
3. Rà soát dữ liệu quan hệ ngành/nghề và chỉ tiêu hiện có; ghi nhận các trường chưa có chỉ tiêu là `0`.
4. Chốt thứ tự danh mục cấp học theo `education_levels.display_order`.

### Giai đoạn 1 — API và truy vấn dữ liệu (1 ngày)

1. Thêm route `/api/public/schools` và validation tham số truy vấn.
2. Xây dựng truy vấn phân trang, eager-load cấp học và tối đa ba ngành/nghề xem trước.
3. Thêm tìm kiếm không dấu tiếng Việt cho tên/mã cơ sở và ngành/nghề.
4. Tính `annual_quota_total`, số ngành/nghề và thống kê tổng quan từ dữ liệu đã lọc.
5. Bổ sung index phù hợp nếu kiểm tra truy vấn cho thấy cần thiết: tên/mã trường, pivot ngành-nghề, cấp học và địa bàn.
6. Viết test API cho lọc, phân trang, dữ liệu `0` và bảo vệ dữ liệu không công khai.

### Giai đoạn 2 — Giao diện danh sách công khai (1,5 ngày)

1. Tạo `ClientSchoolListView.vue` và route `/co-so-gdnn`.
2. Tạo thành phần bộ lọc dùng lại logic chuẩn hóa tiếng Việt đang có ở bản đồ.
3. Tạo thẻ cơ sở, skeleton, trạng thái rỗng và phân trang responsive.
4. Đồng bộ filter với URL, hỗ trợ quay lại/truyền liên kết.
5. Kết nối **Xem chi tiết** với `SchoolDetailDrawer.vue` và **Xem trên bản đồ** với `targetSchoolToFly` hiện có.
6. Thêm mục điều hướng và tiêu đề trang.

### Giai đoạn 3 — Kiểm thử và hoàn thiện (0,5–1 ngày)

1. Kiểm thử desktop, tablet và mobile.
2. Kiểm thử từ khóa có dấu/không dấu, tên dài, ngành/nghề dài và cơ sở không có chỉ tiêu.
3. Kiểm thử thay đổi bộ lọc, phân trang, liên kết URL và quay lại bản đồ.
4. Đo tải API với số lượng cơ sở hiện có; kiểm tra không xảy ra truy vấn N+1.
5. Chạy test backend, build frontend và rà soát quyền công khai trước khi triển khai VPS.

## 7. Tiêu chí nghiệm thu

- Người dân truy cập được trang danh sách mà không cần đăng nhập.
- Danh sách, bộ lọc, thống kê và phân trang phản hồi từ DB thật.
- Cấp học hiển thị đúng tên, thứ tự và màu trong danh mục DB.
- Các cơ sở/ngành không có chỉ tiêu hiển thị `0`; không xuất hiện giá trị giả hoặc `NaN`.
- Tìm `han`, `hàn`, `HÀN` cho cùng kết quả phù hợp theo quy tắc tìm kiếm tiếng Việt đã chọn.
- Mỗi thẻ mở đúng hồ sơ cơ sở; nút bản đồ định vị đúng tọa độ.
- Trên mobile không vỡ thẻ, bộ lọc và thao tác phân trang.
- Endpoint hiện có của bản đồ `/api/schools` không đổi dữ liệu trả về và không bị ảnh hưởng hiệu năng.

## 8. Rủi ro và cách xử lý

| Rủi ro | Cách xử lý |
| --- | --- |
| Dữ liệu ngành/nghề còn ở JSON cũ | Ưu tiên pivot chuẩn; chỉ dùng fallback tương thích và lập danh sách cần chuẩn hóa. |
| Tìm kiếm tiếng Việt chậm khi dữ liệu tăng | Chuẩn hóa chuỗi tìm kiếm, dùng index phù hợp; cân nhắc cột tìm kiếm không dấu nếu cần. |
| Tải danh sách làm chậm bản đồ | Dùng endpoint riêng, payload gọn và phân trang; không tải trường chi tiết trong thẻ. |
| Chỉ tiêu bị hiểu là năng lực thực tế | Gắn nhãn rõ là **Tổng chỉ tiêu/năm** và chỉ hiển thị dữ liệu đã khai báo. |
| Công khai dữ liệu chưa duyệt | Chốt điều kiện `VERIFIED` tại API, không chỉ ẩn ở giao diện. |

## 9. Ước lượng

Tổng thời gian dự kiến: **2,5 đến 3 ngày công**, chưa bao gồm thời gian rà soát/chỉnh sửa dữ liệu nguồn lớn.

Không cần migration mới cho phiên bản đầu nếu các quan hệ `education_levels`, `training_majors` và `school_training_major` đã được áp dụng. Chỉ bổ sung migration/index khi kiểm thử hiệu năng cho thấy truy vấn cần tối ưu.
