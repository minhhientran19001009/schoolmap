# Phân tích quy mô đào tạo từ tài liệu ngày 17/11/2026

Nguồn: `C:\Users\Admin\Desktop\tai_lieu\Copy of BẢNG TH NGÀNH NGHỀ ĐỂ XÂY DỰNG BẢN ĐỒ SỐ GDNNNGDTX.xlsx`. Workbook đầu vào: `Danh_Sach_Truong_Hoc_Ninh_Binh_2026_11_17_Bo_Sung_Day_Du.xlsx`. Workbook kết quả: `Danh_Sach_Truong_Hoc_Ninh_Binh_2026_11_17_Nganh_Va_Quy_Mo.xlsx`.

Phạm vi: cột F–I của sheet Mau 1 - KQTS. Không dùng cột J–M dự kiến tuyển sinh. Giữ nguyên toàn bộ các sheet cũ, gồm 102 tên ngành đã bổ sung. Không đổi tổng sinh viên đang học, chỉ tiêu, tuyển sinh năm hoặc mã ngành.

Tệp mới có 15 dòng tổng cơ sở, 256 dòng ngành đã gộp và 304 dòng ngành gốc. Số 0 được giữ là 0, ô trống được giữ trống. Không cộng trùng các dòng tổng với các dòng ngành hoặc sheet gộp với sheet gốc.

| Cơ sở | Tổng nguồn | CĐ | TC | SC và khác | Chưa phân bổ | Tổng từ dòng ngành để đối chiếu | Trạng thái |
| --- | ---: | ---: | ---: | ---: | ---: | ---: | --- |
| Trường Cao đẳng Văn hóa Nghệ thuật và Du lịch Nam Định | 235 | 75 | 160 | 0 | 0 | 235 | Khớp |
| Trường Cao đẳng công nghệ kinh tế và chế biến lâm sản | 1.445 | 185 | 780 | 480 | 0 | 1.445 | Khớp |
| Trường Cao đẳng Công nghiệp Nam Định | 2.915 | 960 | 1.395 | 560 | 0 | 2.915 | Khớp |
| Trường Cao đẳng Cơ điện Nông nghiệp và Môi trường | 9.650 | 2.460 | 4.480 | 2.610 | 100 | 9.650 | Có số chưa phân bổ trình độ |
| Trường cao đẳng Phát thanh - Truyền hình I | 1.273 | 135 | 1.138 | 0 | 0 | 1.273 | Khớp |
| Trường Cao đẳng nghề số 20/BQP | 2.725 | 335 | 390 | 2.000 | 0 | 2.725 | Khớp |
| Trường Cao đẳng Xây dựng Nam Định | 1.660 | 320 | 960 | 380 | 0 | 1.660 | Khớp |
| Trường Cao đẳng Cơ điện và Thủy lợi Bắc Bộ tại tỉnh Ninh Bình | 1.105 | 355 | 750 | 0 | 0 | 1.105 | Khớp |
| Trường Cao đẳng Kinh tế và Công nghệ tỉnh Ninh Bình | 6.005 | 1.160 | 4.380 | Trống | 465 | 6.005 | Có số chưa phân bổ trình độ |
| Trường trung cấp Đại Lâm | 0 | 0 | 450 | 4.700 | Trống | 5.150 | Cần xác minh: tổng nhỏ hơn tổng trình độ |
| Trường Trung cấp Kỹ thuật - Du lịch Công đoàn Ninh Bình | 810 | 0 | 810 | 0 | 0 | 810 | Khớp |
| Trường Trung cấp Công nghệ số 8 Nam Định | 690 | Trống | 550 | 140 | 0 | 690 | Khớp |
| Trường trung cấp nghề Quốc Tế | 3.283 | 0 | 2.683 | 600 | 0 | 3.283 | Khớp |
| Trường Trung cấp GTVT Ninh Bình | 700 | 0 | 490 | 210 | 0 | 700 | Khớp |
| Trung tâm GDNN Nam Tam Điệp | Trống | Trống | Trống | 8.500 | Trống | 8.500 | Tổng trống; chỉ có số theo trình độ |

Các điểm cần xác minh:

- Cơ điện Nông nghiệp và Môi trường: tổng 9.650, các trình độ cộng 9.550. Phần 100 nằm ở Sư phạm GDNN (dòng 135); không tự phân bổ.
- Kinh tế và Công nghệ tỉnh Ninh Bình: tổng 6.005, CĐ + TC là 5.540. Ngành Đào tạo lái xe (dòng 282) ghi tổng 500 nhưng TC 35, phần chênh 465 chưa phân bổ.
- Đại Lâm: tổng nguồn ghi 0, TC 450 và SC/khác 4.700. Tổng 5.150 chỉ là số cộng đối chiếu, chưa thay tổng gốc thành số này.
- Nam Tam Điệp: tổng nguồn trống, SC và khác 8.500; chỉ tính 8.500 ở cột đối chiếu, giữ ô tổng nguồn trống.

Cộng các dòng ngành phục vụ đối chiếu là 46.146, gồm CĐ 5.985, TC 19.416, SC/khác 20.180 và chưa phân bổ 565. Đây không phải tổng đã xác nhận để tự ghi vào chỉ tiêu hoặc số sinh viên.

Sau khi cập nhật code mới, vào Admin → Danh sách Trường học → Nhập quy mô đào tạo. Chọn workbook này và chạy chế độ Chỉ kiểm tra trước; bỏ chọn để ghi. Bộ nhập riêng chỉ xử lý các sheet QM_Tong_hop và QM_Theo_nganh; giữ nguyên mã ngành, chỉ tiêu, số sinh viên và thông tin trường. Bảng chính ở Tổng quan hiển thị quy mô, đơn vị người học; nguồn trống hiển thị —, nguồn 0 hiển thị 0; tổng mâu thuẫn không tự chốt. Có sao lưu báo cáo bị tác động và transaction trước khi ghi.
