# Đối chiếu tên ngành/nghề trong Excel và website ngày 17/11/2026

Nguồn: `C:\Users\Admin\Desktop\tai_lieu\Copy of BẢNG TH NGÀNH NGHỀ ĐỂ XÂY DỰNG BẢN ĐỒ SỐ GDNNNGDTX.xlsx`. Sheet `Mau 1 - KQTS`. Website: https://ninhbinh.poly-hna.com/map.

Đọc trực tiếp 15 trường, 304 dòng ngành, gộp thành 256 cặp trường–tên ngành. Chỉ so sánh tên; không so sánh số liệu, chỉ tiêu hoặc trình độ.

Đọc API công khai `/api/schools/{id}`. Tách hai nơi: danh mục `training_majors` và báo cáo `training_source_report.programs`. Một ngành có trong báo cáo nhưng thiếu trong danh mục được ghi riêng. Các dòng cùng ngành khác trình độ được gộp một tên trong phép đếm. Không chỉnh sửa dữ liệu.

Chuẩn hóa Unicode, hoa/thường, khoảng trắng, dấu câu, &/và, ĐHKK/điều hòa không khí, C.nghệ/Công nghệ, Công nghệ KT/Công nghệ kỹ thuật, cách đặt dấu hoà/hòa, hoá/hóa, hoạ/họa, thuỷ/thủy và các lỗi chính tả rõ ràng đã có trong bộ làm sạch. Không tự coi các tên gần giống hoặc cùng mã là một ngành. Những gợi ý gần giống trong CSV cần người kiểm tra, không được tính là khớp.

Tên chỉ có trên web không đồng nghĩa sai hoặc phải xóa: file có thể chưa liệt kê hết, hoặc web giữ chương trình cũ. Với trường chia cơ sở/sáp nhập, số đếm chỉ áp dụng cho bản ghi ứng viên đã ghi rõ, không suy rộng toàn trường.

## Tổng hợp theo bản ghi web

| Trường trong file | Bản ghi web / phạm vi | Tên trong file | Tên danh mục web | Khớp | Chưa có tên tương ứng trong danh mục | Chỉ có trên web | Báo cáo khớp/file |
| --- | --- | ---: | ---: | ---: | ---: | ---: | --- |
| Trường Cao đẳng Văn hóa Nghệ thuật và Du lịch Nam Định | Trường Cao đẳng Văn hóa Nghệ thuật và Du lịch Nam Định — Cùng trường | 10 | 10 | 10 | 0 | 0 | 10/10 |
| Trường Cao đẳng công nghệ kinh tế và chế biến lâm sản | Trường Cao đẳng Công nghệ, Kinh tế và Chế biến lâm sản — Cùng trường | 9 | 9 | 9 | 0 | 0 | 9/9 |
| Trường Cao đẳng Công nghiệp Nam Định | Trường Cao đẳng Công nghiệp Nam Định — Cùng trường | 40 | 40 | 40 | 0 | 0 | 40/40 |
| Trường Cao đẳng Cơ điện Nông nghiệp và Môi trường | Trường Cao đẳng Cơ điện Nông nghiệp và Môi trường — Cùng trường; kiểm tra riêng cơ sở con | 46 | 7 | 7 | 39 | 0 | Chưa có báo cáo |
| Trường cao đẳng Phát thanh - Truyền hình I | Trường Cao đẳng Phát thanh - Truyền hình I (VOV) — Cùng trường; kiểm tra riêng bản ghi Cơ sở 1 | 9 | 9 | 9 | 0 | 0 | 9/9 |
| Trường Cao đẳng nghề số 20/BQP | Trường Cao đẳng nghề Số 20 - BQP - Vị Khê — File cấp trường; web chia Vị Khê/Trường Thi, chưa phân bổ ngành theo cơ sở | 23 | 2 | 0 | 23 | 2 | Chưa có báo cáo |
| Trường Cao đẳng nghề số 20/BQP | Trường Cao đẳng nghề Số 20 - BQP - Trường Thi — File cấp trường; web chia Vị Khê/Trường Thi, chưa phân bổ ngành theo cơ sở | 23 | 24 | 23 | 0 | 1 | 23/23 |
| Trường Cao đẳng Xây dựng Nam Định | Trường Cao đẳng Xây dựng Nam Định — Cùng trường; kiểm tra riêng cơ sở con | 20 | 20 | 20 | 0 | 0 | 20/20 |
| Trường Cao đẳng Cơ điện và Thủy lợi Bắc Bộ tại tỉnh Ninh Bình | Trường Cao đẳng Cơ điện và Thủy lợi Bắc Bộ – Phân hiệu Ninh Bình — Ứng viên phân hiệu Ninh Bình; cần xác nhận cùng đơn vị | 20 | 5 | 4 | 16 | 1 | Chưa có báo cáo |
| Trường Cao đẳng Kinh tế và Công nghệ tỉnh Ninh Bình | Trường Cao đẳng Kinh tế Và Công Nghệ Nam Định, trụ sở chính, Khu A — Ứng viên Khu A thuộc nhóm NCET; cần xác nhận phạm vi sau sáp nhập | 44 | 6 | 2 | 42 | 4 | Chưa có báo cáo |
| Trường trung cấp Đại Lâm | Trường Trung cấp Đại Lâm — Cùng trường | 4 | 4 | 4 | 0 | 0 | 4/4 |
| Trường Trung cấp Kỹ thuật - Du lịch Công đoàn Ninh Bình | Trường Trung cấp Kỹ thuật - Du lịch Công đoàn Ninh Bình — Cùng trường | 8 | 8 | 8 | 0 | 0 | 8/8 |
| Trường Trung cấp Công nghệ số 8 Nam Định | Trường Trung cấp Công nghệ số 8 Nam Định — Cùng trường | 5 | 7 | 5 | 0 | 2 | 5/5 |
| Trường trung cấp nghề Quốc Tế | Trường Trung cấp nghề Quốc tế (IVS) — Cùng trường, web thêm (IVS) | 9 | 5 | 4 | 5 | 1 | Chưa có báo cáo |
| Trường Trung cấp GTVT Ninh Bình | Trường Trung cấp Giao thông vận tải tỉnh Ninh Bình — GTVT được mở rộng thành Giao thông vận tải | 7 | 8 | 7 | 0 | 1 | 7/7 |
| Trung tâm GDNN Nam Tam Điệp | Trung tâm GDNN Nam Tam Điệp — Cùng trung tâm | 2 | 5 | 2 | 0 | 3 | 2/2 |

## Trường Cao đẳng Văn hóa Nghệ thuật và Du lịch Nam Định → Trường Cao đẳng Văn hóa Nghệ thuật và Du lịch Nam Định

Nguồn: `Mau 1 - KQTS!6:19`. ID web: `truong-cao-dang-van-hoa-nghe-thuat-va-du-470`. Phạm vi: Cùng trường.

### Tên trong file chưa có tương ứng trong danh mục

Không thiếu tên sau chuẩn hóa.

### Tên chỉ có trong danh mục web

Không có.

### Khác cách viết nhưng khớp sau chuẩn hóa

Không có.

Báo cáo đào tạo trên web: 10/10 tên nguồn khớp; chỉ có trong báo cáo: không có.

## Trường Cao đẳng công nghệ kinh tế và chế biến lâm sản → Trường Cao đẳng Công nghệ, Kinh tế và Chế biến lâm sản

Nguồn: `Mau 1 - KQTS!20:29`. ID web: `truong-cao-dang-cong-nghe-kinh-te-va-che-629`. Phạm vi: Cùng trường.

### Tên trong file chưa có tương ứng trong danh mục

Không thiếu tên sau chuẩn hóa.

### Tên chỉ có trong danh mục web

Không có.

### Khác cách viết nhưng khớp sau chuẩn hóa

| File | Web |
| --- | --- |
| Gia công và thiết kế sản phẩm mộc | Gia công và Thiết kế sản phẩm Mộc |
| Điện Công nghiệp | Điện công nghiệp |
| Kế toán doanh nghiệp | Kế toán Doanh nghiệp |
| Quản Trị Mạng máy tính | Quản trị mạng Máy tính |

Báo cáo đào tạo trên web: 9/9 tên nguồn khớp; chỉ có trong báo cáo: không có.

## Trường Cao đẳng Công nghiệp Nam Định → Trường Cao đẳng Công nghiệp Nam Định

Nguồn: `Mau 1 - KQTS!30:89`. ID web: `truong-cao-dang-cong-nghiep-nam-dinh-192`. Phạm vi: Cùng trường.

### Tên trong file chưa có tương ứng trong danh mục

Không thiếu tên sau chuẩn hóa.

### Tên chỉ có trong danh mục web

Không có.

### Khác cách viết nhưng khớp sau chuẩn hóa

| File | Web |
| --- | --- |
| Công nghệ kỹ thuật điện tử, truyền thông | Công nghệ kỹ thuật điện tử truyền thông |
| Công nghệ kỹ thuật điện, điện tử | Công nghệ kỹ thuật điện - điện tử |
| Nguội lắp rắp cơ khí | Nguội lắp ráp cơ khí |
| Kế toán doanh nghiệp | Kế toán Doanh nghiệp |
| Quản trị mạng máy tính | Quản trị mạng Máy tính |

Báo cáo đào tạo trên web: 40/40 tên nguồn khớp; chỉ có trong báo cáo: không có.

## Trường Cao đẳng Cơ điện Nông nghiệp và Môi trường → Trường Cao đẳng Cơ điện Nông nghiệp và Môi trường

Nguồn: `Mau 1 - KQTS!90:136`. ID web: `truong-cao-dang-co-dien-nong-nghiep-va-m-218`. Phạm vi: Cùng trường; kiểm tra riêng cơ sở con.

### Tên trong file chưa có tương ứng trong danh mục

| Tên ngành trong file | Dòng Excel | Có trong báo cáo web |
| --- | --- | --- |
| Kỹ thuật chế biến món ăn | 94 | Không |
| Kỹ thuật máy lạnh & ĐHKK | 95 | Không |
| Cắt gọt kim loại | 96 | Không |
| Lập trình máy tính | 97 | Không |
| Kế toán doanh nghiệp | 98 | Không |
| Sửa chữa máy thi công xây dựng | 99 | Không |
| Công tác xã hội | 100 | Không |
| Bảo trì hệ thống thiết bị cơ khí | 101 | Không |
| Văn thư hành chính | 102 | Không |
| Kỹ thuật sửa chữa lắp giáp máy tính | 103 | Không |
| Quản trị mạng máy tính | 104 | Không |
| Thiết kế đồ hoạ | 106 | Không |
| Công nghệ thông tin (Ứng dụng phần mềm) | 107 | Không |
| Kế toán tin học | 108 | Không |
| Xây dựng dân dụng và công nghiệp | 110 | Không |
| Tiếng Nhật | 111 | Không |
| Vận hành máy thi công mặt đường | 113 | Không |
| Vận hành cần, cầu trục | 114 | Không |
| Cấp thoát nước | 115 | Không |
| Điện nước | 116 | Không |
| Vận hành máy xúc | 117 | Không |
| Vận hành máy lu | 118 | Không |
| Vận hành máy ủi | 119 | Không |
| Vận hành cần trục | 120 | Không |
| Vận hành cầu trục | 121 | Không |
| Vận hành xe nâng hàng | 122 | Không |
| Vận hành máy xúc lật | 123 | Không |
| Hàn 3G; 5G; 6G | 124 | Không |
| May công nghiệp | 125 | Không |
| Kỹ thuật pha chế đồ uống | 126 | Không |
| Lái xe ôtô | 127 | Không |
| Thú Y | 128 | Không |
| Kế toán | 129 | Không |
| Điện tử dân dụng | 130 | Không |
| Vận hành và sửa chữa máy nông nghiệp | 131 | Không |
| Vận hành máy nâng | 132 | Không |
| Ký thuật trông rau an toàn | 133 | Không |
| Giám đốc hợp tác xã nông nghiệp | 134 | Không |
| Sư phạm GDNN | 135 | Không |

### Tên chỉ có trong danh mục web

Không có.

### Khác cách viết nhưng khớp sau chuẩn hóa

Không có.

Bản ghi này chưa có báo cáo đào tạo từ nguồn.

## Trường cao đẳng Phát thanh - Truyền hình I → Trường Cao đẳng Phát thanh - Truyền hình I (VOV)

Nguồn: `Mau 1 - KQTS!137:149`. ID web: `HN-CD-004`. Phạm vi: Cùng trường; kiểm tra riêng bản ghi Cơ sở 1.

### Tên trong file chưa có tương ứng trong danh mục

Không thiếu tên sau chuẩn hóa.

### Tên chỉ có trong danh mục web

Không có.

### Khác cách viết nhưng khớp sau chuẩn hóa

| File | Web |
| --- | --- |
| Kế toán doanh nghiệp | Kế toán Doanh nghiệp |
| Công nghệ kỹ thuật điện, điện tử | Công nghệ kỹ thuật điện - điện tử |
| Công nghệ kỹ thuật điện tử, truyền thông | Công nghệ kỹ thuật điện tử truyền thông |

Báo cáo đào tạo trên web: 9/9 tên nguồn khớp; chỉ có trong báo cáo: không có.

## Trường Cao đẳng nghề số 20/BQP → Trường Cao đẳng nghề Số 20 - BQP - Vị Khê

Nguồn: `Mau 1 - KQTS!150:188`. ID web: `truong-cao-dang-nghe-so-20-bqp-vi-khe-509`. Phạm vi: File cấp trường; web chia Vị Khê/Trường Thi, chưa phân bổ ngành theo cơ sở.

### Tên trong file chưa có tương ứng trong danh mục

| Tên ngành trong file | Dòng Excel | Có trong báo cáo web |
| --- | --- | --- |
| Công nghệ ô tô | 150, 151, 152 | Không |
| Kỹ thuật máy lạnh và điều hoà không khí | 153, 154, 155 | Không |
| Kỹ thuật lắp đặt điện và điều khiển trong công nghiệp | 156, 157 | Không |
| Sửa chữa điện - điện lạnh ô tô | 158 | Không |
| Điện công nghiệp | 159 | Không |
| Điện tử công nghiệp | 160, 161, 162 | Không |
| Kỹ thuật sửa chữa, lắp ráp máy tính | 163, 164 | Không |
| Công nghệ thông tin | 165 | Không |
| May thời trang | 166, 167 | Không |
| Sửa chữa thiết bị may | 168 | Không |
| Bảo trì máy may | 169 | Không |
| Hàn | 170, 171, 172 | Không |
| Hàn công nghệ cao (CNC) | 173 | Không |
| Cắt gọt kim loại | 174, 175 | Không |
| Sửa chữa thiết bị tự động hoá | 176 | Không |
| Vận hành máy thi công nền | 177, 178, 179 | Không |
| Điều khiển phương tiện thuỷ nội địa | 180, 181 | Không |
| Sửa chữa cơ khí động cơ | 182 | Không |
| Sửa chữa xe máy | 183 | Không |
| Cốt thép - Hàn | 184 | Không |
| Tiếng Anh | 185 | Không |
| Tiếng Nhật | 186 | Không |
| Tiếng Hàn Quốc | 187 | Không |

### Tên chỉ có trong danh mục web

Đào tạo lái xe ô tô hạng B2, C; Kỹ thuật cơ khí động lực

### Khác cách viết nhưng khớp sau chuẩn hóa

Không có.

Bản ghi này chưa có báo cáo đào tạo từ nguồn.

## Trường Cao đẳng nghề số 20/BQP → Trường Cao đẳng nghề Số 20 - BQP - Trường Thi

Nguồn: `Mau 1 - KQTS!150:188`. ID web: `truong-cao-dang-nghe-so-20-bqp-truong-th-471`. Phạm vi: File cấp trường; web chia Vị Khê/Trường Thi, chưa phân bổ ngành theo cơ sở.

### Tên trong file chưa có tương ứng trong danh mục

Không thiếu tên sau chuẩn hóa.

### Tên chỉ có trong danh mục web

Thực hành lái xe & cơ điện

### Khác cách viết nhưng khớp sau chuẩn hóa

| File | Web |
| --- | --- |
| Kỹ thuật máy lạnh và điều hoà không khí | Kỹ thuật máy lạnh và điều hòa không khí |

Báo cáo đào tạo trên web: 23/23 tên nguồn khớp; chỉ có trong báo cáo: không có.

## Trường Cao đẳng Xây dựng Nam Định → Trường Cao đẳng Xây dựng Nam Định

Nguồn: `Mau 1 - KQTS!189:212`. ID web: `truong-cao-dang-xay-dung-nam-dinh-204`. Phạm vi: Cùng trường; kiểm tra riêng cơ sở con.

### Tên trong file chưa có tương ứng trong danh mục

Không thiếu tên sau chuẩn hóa.

### Tên chỉ có trong danh mục web

Không có.

### Khác cách viết nhưng khớp sau chuẩn hóa

| File | Web |
| --- | --- |
| Kế toán doanh nghiệp | Kế toán Doanh nghiệp |
| Quản trị mạng máy tính | Quản trị mạng Máy tính |
| Thiết kế đồ hoạ | Thiết kế đồ họa |
| Cấp, thoát nước | Cấp thoát nước |
| Mộc XD và T.Trí nội thất | Mộc xây dựng và trang trí nội thất |
| K.thuật Sơn mài & khảm trai | Kỹ thuật Sơn mài và khảm trai |

Báo cáo đào tạo trên web: 20/20 tên nguồn khớp; chỉ có trong báo cáo: không có.

## Trường Cao đẳng Cơ điện và Thủy lợi Bắc Bộ tại tỉnh Ninh Bình → Trường Cao đẳng Cơ điện và Thủy lợi Bắc Bộ – Phân hiệu Ninh Bình

Nguồn: `Mau 1 - KQTS!213:238`. ID web: `truong-cao-dang-co-dien-va-thuy-loi-bac--458`. Phạm vi: Ứng viên phân hiệu Ninh Bình; cần xác nhận cùng đơn vị.

### Tên trong file chưa có tương ứng trong danh mục

| Tên ngành trong file | Dòng Excel | Có trong báo cáo web |
| --- | --- | --- |
| Tài chính - Ngân hàng | 213 | Không |
| Kế toán | 214 | Không |
| Kế toán doanh nghiệp | 215 | Không |
| Quản lý khai thác công trình thủy lợi | 216 | Không |
| Kỹ thuật sửa chữa, lắp ráp máy tính | 217, 218 | Không |
| Công nghệ thông tin | 219 | Không |
| Công nghệ kỹ thuật xây dựng | 220 | Không |
| Công nghệ kỹ thuật công trình xây dựng | 221 | Không |
| Công trình thủy lợi | 222 | Không |
| Công nghệ kỹ thuật trắc địa | 224, 225 | Không |
| Địa chất công trình | 226 | Không |
| Điện công nghiệp và dân dụng | 227 | Không |
| Điện dân dụng | 228, 229 | Không |
| Vận hành và sửa chữa trạm bơm điện | 232, 233 | Không |
| Xây dựng dân dụng và công nghiệp | 235 | Không |
| Kỹ thuật chế biến món ăn | 236 | Không |

### Tên chỉ có trong danh mục web

Vận hành trạm bơm điện

### Khác cách viết nhưng khớp sau chuẩn hóa

| File | Web |
| --- | --- |
| Công nghệ kỹ thuật tài nguyên nước | Công nghệ KT Tài nguyên nước |
| Cấp, thoát nước | Cấp thoát nước |

Bản ghi này chưa có báo cáo đào tạo từ nguồn.

## Trường Cao đẳng Kinh tế và Công nghệ tỉnh Ninh Bình → Trường Cao đẳng Kinh tế Và Công Nghệ Nam Định, trụ sở chính, Khu A

Nguồn: `Mau 1 - KQTS!239:283`. ID web: `truong-cao-dang-kinh-te-va-cong-nghe-nam-934`. Phạm vi: Ứng viên Khu A thuộc nhóm NCET; cần xác nhận phạm vi sau sáp nhập.

### Tên trong file chưa có tương ứng trong danh mục

| Tên ngành trong file | Dòng Excel | Có trong báo cáo web |
| --- | --- | --- |
| Logistics | 239 | Không |
| Kế toán | 240 | Không |
| Kế toán doanh nghiệp | 241 | Không |
| Quản lý khai thác công trình thuỷ lợi | 242 | Không |
| Kỹ thuật sửa chữa, lắp ráp máy tính | 243 | Không |
| Công nghệ thông tin (Ứng dụng phần mềm) | 244 | Không |
| Quản trị mạng máy tính | 245 | Không |
| Xây dựng và hoàn thiện Công trình thủy lợi | 246 | Không |
| Công nghệ kỹ thuật cơ khí | 247 | Không |
| Công nghệ kỹ thuật điện, điện tử | 249 | Không |
| Cắt gọt kim loại | 250 | Không |
| Hàn | 251 | Không |
| Sửa chữa thiết bị may | 252 | Không |
| Điện dân dụng | 254 | Không |
| Điện Công nghiệp | 255 | Không |
| Vận hành và sửa chữa trạm bơm điện | 256 | Không |
| Cơ điện nông thôn | 257 | Không |
| Cơ điện tử | 258 | Không |
| May thời trang | 259 | Không |
| Quản trị du lịch MICE | 260 | Không |
| Kỹ thuật điêu khắc gỗ | 261 | Không |
| Đúc, dát đồng mỹ nghệ | 262 | Không |
| Báo chí | 263 | Không |
| Kế toán hành chính sự nghiệp | 264 | Không |
| Công nghệ sinh học | 265 | Không |
| Vận hành máy nông nghiệp | 266 | Không |
| Điện công nghiệp và dân dụng | 267 | Không |
| Điện tử dân dụng | 268 | Không |
| Điện tử Công nghiệp | 269 | Không |
| Công nghệ kỹ thuật chế biến và bảo quản nông sản | 270 | Không |
| Trồng trọt và bảo vệ thực vật | 271 | Không |
| Chăn nuôi - Thú y | 272 | Không |
| Nuôi trồng thuỷ sản | 273 | Không |
| Hướng dẫn du lịch | 274 | Không |
| Quản trị khách sạn | 275 | Không |
| Nghiệp vụ lễ tân | 276 | Không |
| Nghiệp vụ nhà hàng | 277 | Không |
| Kỹ thuật chế biến món ăn | 278 | Không |
| Chăm sóc sắc đẹp | 279 | Không |
| Quản lý đất đai | 280 | Không |
| May Công nghiệp | 281 | Không |
| Đào tạo lái xe | 282 | Không |

### Tên chỉ có trong danh mục web

Chế tạo thiết bị cơ khí; Hàn công nghệ cao; Kỹ thuật lắp đặt điện; Lắp đặt ống công nghệ

### Khác cách viết nhưng khớp sau chuẩn hóa

| File | Web |
| --- | --- |
| Kỹ thuật máy lạnh và điều hoà không khí | Kỹ thuật máy lạnh và ĐHKK |

Bản ghi này chưa có báo cáo đào tạo từ nguồn.

## Trường trung cấp Đại Lâm → Trường Trung cấp Đại Lâm

Nguồn: `Mau 1 - KQTS!285:289`. ID web: `truong-trung-cap-dai-lam-800`. Phạm vi: Cùng trường.

### Tên trong file chưa có tương ứng trong danh mục

Không thiếu tên sau chuẩn hóa.

### Tên chỉ có trong danh mục web

Không có.

### Khác cách viết nhưng khớp sau chuẩn hóa

Không có.

Báo cáo đào tạo trên web: 4/4 tên nguồn khớp; chỉ có trong báo cáo: không có.

## Trường Trung cấp Kỹ thuật - Du lịch Công đoàn Ninh Bình → Trường Trung cấp Kỹ thuật - Du lịch Công đoàn Ninh Bình

Nguồn: `Mau 1 - KQTS!290:298`. ID web: `truong-trung-cap-ky-thuat-du-lich-cong-d-724`. Phạm vi: Cùng trường.

### Tên trong file chưa có tương ứng trong danh mục

Không thiếu tên sau chuẩn hóa.

### Tên chỉ có trong danh mục web

Không có.

### Khác cách viết nhưng khớp sau chuẩn hóa

Không có.

Báo cáo đào tạo trên web: 8/8 tên nguồn khớp; chỉ có trong báo cáo: không có.

## Trường Trung cấp Công nghệ số 8 Nam Định → Trường Trung cấp Công nghệ số 8 Nam Định

Nguồn: `Mau 1 - KQTS!299:304`. ID web: `truong-trung-cap-cong-nghe-so-8-nam-dinh-193`. Phạm vi: Cùng trường.

### Tên trong file chưa có tương ứng trong danh mục

Không thiếu tên sau chuẩn hóa.

### Tên chỉ có trong danh mục web

Bảo trì và sửa chữa ô tô; Điện công nghiệp

### Khác cách viết nhưng khớp sau chuẩn hóa

| File | Web |
| --- | --- |
| Công nghệ kỹ thuật điện, điện tử | Công nghệ kỹ thuật điện - điện tử |

Báo cáo đào tạo trên web: 5/5 tên nguồn khớp; chỉ có trong báo cáo: không có.

## Trường trung cấp nghề Quốc Tế → Trường Trung cấp nghề Quốc tế (IVS)

Nguồn: `Mau 1 - KQTS!305:314`. ID web: `truong-trung-cap-nghe-quoc-te-ivs-235`. Phạm vi: Cùng trường, web thêm (IVS).

### Tên trong file chưa có tương ứng trong danh mục

| Tên ngành trong file | Dòng Excel | Có trong báo cáo web |
| --- | --- | --- |
| Chăm sóc sắc đẹp | 305 | Không |
| Công nghệ ô tô | 306 | Không |
| Công nghệ thông tin (ứng dụng phần mềm ) | 307 | Không |
| Điện dân dụng | 309 | Không |
| Kỹ thuật máy lạnh và điều hòa không khí | 310 | Không |

### Tên chỉ có trong danh mục web

Công nghệ thông tin

### Khác cách viết nhưng khớp sau chuẩn hóa

Không có.

Bản ghi này chưa có báo cáo đào tạo từ nguồn.

## Trường Trung cấp GTVT Ninh Bình → Trường Trung cấp Giao thông vận tải tỉnh Ninh Bình

Nguồn: `Mau 1 - KQTS!315:322`. ID web: `truong-trung-cap-giao-thong-van-tai-tinh-811`. Phạm vi: GTVT được mở rộng thành Giao thông vận tải.

### Tên trong file chưa có tương ứng trong danh mục

Không thiếu tên sau chuẩn hóa.

### Tên chỉ có trong danh mục web

Sửa chữa máy tính

### Khác cách viết nhưng khớp sau chuẩn hóa

| File | Web |
| --- | --- |
| Kế toán doanh nghiệp | Kế toán Doanh nghiệp |

Báo cáo đào tạo trên web: 7/7 tên nguồn khớp; chỉ có trong báo cáo: không có.

## Trung tâm GDNN Nam Tam Điệp → Trung tâm GDNN Nam Tam Điệp

Nguồn: `Mau 1 - KQTS!324:326`. ID web: `trung-tam-gdnn-nam-tam-diep-802`. Phạm vi: Cùng trung tâm.

### Tên trong file chưa có tương ứng trong danh mục

Không thiếu tên sau chuẩn hóa.

### Tên chỉ có trong danh mục web

Đào tạo lái xe mô tô A1, A2; Lái xe ô tô B1, B2, C; Nghiệp vụ nhà hàng khách sạn

### Khác cách viết nhưng khớp sau chuẩn hóa

Không có.

Báo cáo đào tạo trên web: 2/2 tên nguồn khớp; chỉ có trong báo cáo: không có.

## Kiểm tra thêm các bản ghi trong nhóm trường

Các bản ghi dưới đây được kiểm tra riêng; không tự cộng danh mục của cơ sở con vào trường chính.

- Trường Cao đẳng Phát thanh - Truyền hình I (VOV) (`HN-CD-004`), cha: `không có`. Danh mục 9 tên: Báo chí; Công nghệ kỹ thuật điện - điện tử; Công nghệ kỹ thuật điện tử truyền thông; Công nghệ thông tin; Kế toán; Kế toán Doanh nghiệp; Quan hệ công chúng; Tin học ứng dụng; Tiếng Anh. Báo cáo: 9 tên.
- Trung tâm GDNN Nam Tam Điệp (`trung-tam-gdnn-nam-tam-diep-802`), cha: `không có`. Danh mục 5 tên: Lái xe ô tô B1, B2, C; Nghiệp vụ nhà hàng khách sạn; Đào tạo lái xe Mô tô; Đào tạo lái xe mô tô A1, A2; Đào tạo lái xe Ô tô. Báo cáo: 2 tên.
- Trường Cao đẳng Cơ điện Nông nghiệp và Môi trường (`truong-cao-dang-co-dien-nong-nghiep-va-m-218`), cha: `không có`. Danh mục 7 tên: Công nghệ ô tô; Hàn; Kỹ thuật xây dựng; Tin học văn phòng; Vận hành máy thi công nền; Điện công nghiệp; Điện dân dụng. Báo cáo: 0 tên.
- Trường Cao đẳng Cơ điện Nông nghiệp và Môi trường – Cơ sở 2 (`truong-cao-dang-co-dien-nong-nghiep-va-m-664`), cha: `truong-cao-dang-co-dien-nong-nghiep-va-m-218`. Danh mục 1 tên: Đào tạo nghề trình độ cao đẳng & liên kết thực hành sản xuất. Báo cáo: 0 tên.
- Trường Cao đẳng Cơ điện và Thủy lợi Bắc Bộ – Phân hiệu Ninh Bình (`truong-cao-dang-co-dien-va-thuy-loi-bac--458`), cha: `không có`. Danh mục 5 tên: Công nghệ KT Tài nguyên nước; Cấp thoát nước; Tin học ứng dụng; Vận hành trạm bơm điện; Điện công nghiệp. Báo cáo: 0 tên.
- Trường Cao đẳng Công nghệ, Kinh tế và Chế biến lâm sản (`truong-cao-dang-cong-nghe-kinh-te-va-che-629`), cha: `không có`. Danh mục 9 tên: Gia công và Thiết kế sản phẩm Mộc; Kế toán Doanh nghiệp; Kỹ thuật Sơn mài và khảm trai; Kỹ thuật máy lạnh và điều hòa không khí; Kỹ thuật Điêu khắc gỗ; Quản trị cơ sở dữ liệu; Quản trị mạng Máy tính; Điện công nghiệp; Điện dân dụng. Báo cáo: 9 tên.
- Trường Cao đẳng Công nghiệp Nam Định (`truong-cao-dang-cong-nghiep-nam-dinh-192`), cha: `không có`. Danh mục 40 tên: Công nghệ chế tạo vỏ tàu thủy; Công nghệ kỹ thuật cơ khí; Công nghệ kỹ thuật cơ điện tử; Công nghệ kỹ thuật môi trường; Công nghệ kỹ thuật nhiệt; Công nghệ kỹ thuật điều khiển và tự động hóa; Công nghệ kỹ thuật điện - điện tử; Công nghệ kỹ thuật điện tử truyền thông; Công nghệ may; Công nghệ thông tin; Công nghệ ô tô; Cắt gọt kim loại; Hàn; Kinh doanh thương mại và dịch vụ; Kế toán; Kế toán Doanh nghiệp; Kế toán hành chính sự nghiệp; Kỹ thuật lắp đặt ống công nghệ; Kỹ thuật máy lạnh và điều hòa không khí; Kỹ thuật sửa chữa, lắp ráp máy tính; May công nghiệp; May thời trang; Nguội lắp ráp cơ khí; Nguội sửa chữa máy công cụ; Quản lý và bán hàng siêu thị; Quản trị cơ sở dữ liệu; Quản trị kinh doanh; Quản trị mạng Máy tính; Sửa chữa thiết bị may; Thiết kế thời trang; Thiết kế đồ họa; Tin học văn phòng; Tin học ứng dụng; Truyền thông và mạng máy tính; Tài chính - Ngân hàng; Điện công nghiệp; Điện công nghiệp và dân dụng; Điện dân dụng; Điện tử công nghiệp; Điện tử công nghiệp và dân dụng. Báo cáo: 40 tên.
- Trường Cao đẳng Kinh tế và Công nghệ Nam Định - Trụ sở chính, Khu B (`truong-cao-dang-kinh-te-va-cong-nghe-nam-131`), cha: `truong-cao-dang-kinh-te-va-cong-nghe-nam-934`. Danh mục 1 tên: Điện tử - Tin học. Báo cáo: 0 tên.
- Trường Cao đẳng Kinh tế và Công nghệ Nam Định cơ sở Đoàn Khuê (`truong-cao-dang-kinh-te-va-cong-nghe-nam-186`), cha: `truong-cao-dang-kinh-te-va-cong-nghe-nam-934`. Danh mục 1 tên: Kế toán doanh nghiệp & Quản trị kinh doanh. Báo cáo: 0 tên.
- Trường Cao đẳng kinh tế và công nghệ Nam Định (`truong-cao-dang-kinh-te-va-cong-nghe-nam-290`), cha: `truong-cao-dang-kinh-te-va-cong-nghe-nam-934`. Danh mục 1 tên: May thời trang & Thủ công mỹ nghệ. Báo cáo: 0 tên.
- Trường Cao đẳng Kinh tế Và Công Nghệ Nam Định, trụ sở chính, Khu A (`truong-cao-dang-kinh-te-va-cong-nghe-nam-934`), cha: `không có`. Danh mục 6 tên: Chế tạo thiết bị cơ khí; Công nghệ ô tô; Hàn công nghệ cao; Kỹ thuật lắp đặt điện; Kỹ thuật máy lạnh và ĐHKK; Lắp đặt ống công nghệ. Báo cáo: 0 tên.
- Trường Cao đẳng Kinh tế và Công nghệ tỉnh Ninh Bình – Địa điểm Cát Thành – Trực Ninh (`truong-cao-dang-kinh-te-va-cong-nghe-tin-714`), cha: `truong-cao-dang-kinh-te-va-cong-nghe-nam-934`. Danh mục 1 tên: May công nghiệp & Kỹ thuật điện. Báo cáo: 0 tên.
- Trường Cao đẳng nghề Số 20 - BQP - Trường Thi (`truong-cao-dang-nghe-so-20-bqp-truong-th-471`), cha: `truong-cao-dang-nghe-so-20-bqp-vi-khe-509`. Danh mục 24 tên: Bảo trì máy may; Công nghệ thông tin; Công nghệ ô tô; Cắt gọt kim loại; Cốt thép - Hàn; Hàn; Hàn công nghệ cao (CNC); Kỹ thuật lắp đặt điện và điều khiển trong công nghiệp; Kỹ thuật máy lạnh và điều hòa không khí; Kỹ thuật sửa chữa, lắp ráp máy tính; May thời trang; Sửa chữa cơ khí động cơ; Sửa chữa thiết bị may; Sửa chữa thiết bị tự động hoá; Sửa chữa xe máy; Sửa chữa điện - điện lạnh ô tô; Thực hành lái xe & cơ điện; Tiếng Anh; Tiếng Hàn Quốc; Tiếng Nhật; Vận hành máy thi công nền; Điều khiển phương tiện thuỷ nội địa; Điện công nghiệp; Điện tử công nghiệp. Báo cáo: 23 tên.
- Trường Cao đẳng nghề Số 20 - BQP - Vị Khê (`truong-cao-dang-nghe-so-20-bqp-vi-khe-509`), cha: `không có`. Danh mục 2 tên: Kỹ thuật cơ khí động lực; Đào tạo lái xe ô tô hạng B2, C. Báo cáo: 0 tên.
- Trường Cao đẳng Văn hóa Nghệ thuật và Du lịch Nam Định (`truong-cao-dang-van-hoa-nghe-thuat-va-du-470`), cha: `không có`. Danh mục 10 tên: Biểu diễn nhạc cụ truyền thống; Hướng dẫn du lịch; Hội họa; Khoa học thư viện; Nghệ thuật biểu diễn Chèo; Nghệ thuật biểu diễn Kịch nói; Organ; Thanh nhạc; Thiết kế đồ họa; Thư viện. Báo cáo: 10 tên.
- Trường Cao đẳng Xây dựng Nam Định (`truong-cao-dang-xay-dung-nam-dinh-204`), cha: `không có`. Danh mục 22 tên: C.nghệ Kỹ thuật C.trình XD; Chạm khắc đá; Công nghệ Kỹ thuật C.trình XD; Cấp thoát nước; Cốt thép - Hàn; Hàn; Kế toán Doanh nghiệp; Kỹ thuật Sơn mài và khảm trai; Kỹ thuật xây dựng; Kỹ thuật Điêu khắc gỗ; Mộc Mỹ nghệ; Mộc XD và T.Trí nội thất; Mộc dân dụng; Mộc xây dựng và trang trí nội thất; Nề hoàn thiện; Quản lý Xây dựng; Quản trị mạng Máy tính; Thiết kế đồ họa; Thương mại điện tử; Tin học văn phòng; XD dân dụng và C.nghiệp; Điện dân dụng. Báo cáo: 20 tên.
- Trường Cao đẳng Xây dựng Nam Định - Cơ sở 2 (`truong-cao-dang-xay-dung-nam-dinh-co-so--176`), cha: `truong-cao-dang-xay-dung-nam-dinh-204`. Danh mục 1 tên: Thực nghiệm sản xuất vật liệu & kiểm định công trình. Báo cáo: 0 tên.
- Trường Trung cấp Công nghệ số 8 Nam Định (`truong-trung-cap-cong-nghe-so-8-nam-dinh-193`), cha: `không có`. Danh mục 7 tên: Bảo trì và sửa chữa ô tô; Công nghệ kỹ thuật ô tô; Công nghệ kỹ thuật điện - điện tử; Công nghệ thông tin; Hàn; May thời trang; Điện công nghiệp. Báo cáo: 5 tên.
- Trường Trung cấp Đại Lâm (`truong-trung-cap-dai-lam-800`), cha: `không có`. Danh mục 4 tên: Công nghệ ô tô; Kỹ thuật chế biến món ăn; Lái xe ô tô các hạng; May thời trang. Báo cáo: 4 tên.
- Trường Trung cấp Giao thông vận tải tỉnh Ninh Bình (`truong-trung-cap-giao-thong-van-tai-tinh-811`), cha: `không có`. Danh mục 8 tên: Công nghệ ô tô; Hàn điện; Khai thác vận tải đường bộ; Kế toán Doanh nghiệp; Kỹ thuật sửa chữa, lắp ráp máy tính; Sửa chữa máy tính; Vận hành máy xúc, ủi, lu; Điện công nghiệp. Báo cáo: 7 tên.
- Trường Trung cấp Kỹ thuật - Du lịch Công đoàn Ninh Bình (`truong-trung-cap-ky-thuat-du-lich-cong-d-724`), cha: `không có`. Danh mục 8 tên: Công nghệ thông tin; Hướng dẫn du lịch; Kỹ thuật chế biến món ăn; May thời trang; Nghiệp vụ nhà hàng; Quản trị khách sạn; Điện công nghiệp và dân dụng; Điện dân dụng. Báo cáo: 8 tên.
- Trường Trung cấp nghề Quốc tế (IVS) (`truong-trung-cap-nghe-quoc-te-ivs-235`), cha: `không có`. Danh mục 5 tên: Công nghệ thông tin; Kỹ thuật chế biến món ăn; May thời trang; Tiếng Trung Quốc; Điện công nghiệp. Báo cáo: 0 tên.
