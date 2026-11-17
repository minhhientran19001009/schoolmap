# Đối chiếu thông tin trung tâm GDTX trên VPS ngày 17/11/2026

## Phạm vi và phương pháp

- Nguồn đối chiếu: `C:\Users\Admin\Desktop\tai_lieu\Địa chỉ các Trung tâm GDTX.docx` (23 dòng trung tâm).
- Website: https://ninhbinh.poly-hna.com/map.
- Đọc dữ liệu đang phục vụ website qua `GET /api/schools` và `GET /api/schools/{id}`; kiểm tra cả `principal`, danh sách `leaders`, `address` và `ward`. API danh sách không trả đủ danh sách lãnh đạo, nên kết luận dựa thêm vào API chi tiết.
- Không chỉnh sửa CSDL hay code nghiệp vụ. Không đưa số điện thoại lãnh đạo vào báo cáo.
- File là mốc đối chiếu do người dùng cung cấp, không có nghĩa thông tin trong file đã được xác minh độc lập là mới nhất.
- Chuẩn hóa viết hoa, dấu câu, `TDP` → `Tổ dân phố`, `NB` → `Ninh Bình`; khác biệt trình bày này không tính là chưa cập nhật.

## Kết quả

| Nội dung | Số lượng |
| --- | ---: |
| Trung tâm trong file | 23 |
| Ghép chắc chắn với bản ghi website | 19 |
| Địa chỉ của các bản ghi ghép chắc chắn khớp nội dung file | 19 |
| Giám đốc khớp tên trong file | 9 |
| Giám đốc khác tên trong file | 4 |
| Bản ghi đã ghép nhưng file không ghi tên giám đốc | 6 |
| Trung tâm chưa tìm thấy đúng tên | 3 |
| Trung tâm có ứng viên tên gần giống, cần xác minh | 1 |

File có tên giám đốc ở 15/23 dòng. Hai dòng có tên nhưng chưa ghép chắc chắn là Hải Hậu và Tin học và Ngoại ngữ Ninh Bình. Tám dòng còn lại không ghi tên, vì vậy không dùng file này để kết luận tên giám đốc hiện tại của những cơ sở đó đúng hay sai. File không cung cấp danh sách đầy đủ phó giám đốc; không kết luận các cán bộ bổ sung trên web là sai.

## Bốn giám đốc khác nguồn

| Trung tâm | Giám đốc trên website | Giám đốc trong file | Địa chỉ |
| --- | --- | --- | --- |
| GDNN-GDTX Lý Nhân | Trần Văn Lực | Phạm Văn Lực | Đã khớp: thôn Nam Cao, xã Vĩnh Trụ |
| GDNN-GDTX Kim Bảng | Phạm Văn Cường | Trần Huy Cường | Đã khớp: tổ dân phố số 2, phường Kim Bảng |
| GDNN-GDTX Nam Định | Giám đốc: ThS. Trần Trọng Tuyên | Trần Đức Hậu | Đã khớp: tổ dân phố số 1, khu dân cư Đệ Tứ, phường Thiên Trường |
| GDNN-GDTX Yên Mô | Nguyễn Xuân Tiến | Vũ Xuân Tiến | Đã khớp: xóm Nam Thành, xã Yên Từ |

Các tên trên web được xác nhận ở cả `principal` và bản ghi đứng đầu `leaders`. Riêng Nam Định còn gán `position = Hiệu trưởng` cho người có tên bắt đầu bằng `Giám đốc:`; chức danh này cần đồng bộ thành Giám đốc khi áp dụng nguồn của trung tâm.

## Các cơ sở chưa ghép chắc chắn

| Trung tâm trong file | Thông tin từ file | Kết quả trên web |
| --- | --- | --- |
| GDNN-GDTX Trực Ninh | Thôn Trường Giang, xã Cát Thành, tỉnh Ninh Bình; không ghi tên giám đốc | Chưa tìm thấy đúng tên. Có địa điểm Cát Thành – Trực Ninh của Trường Cao đẳng Kinh tế và Công nghệ tỉnh Ninh Bình; chưa có căn cứ coi là cùng trung tâm. |
| GDNN-GDTX Hải Hậu | Trần Văn Biên; xóm 9, xã Hải Hưng, tỉnh Ninh Bình | Chưa tìm thấy đúng tên trong danh sách cơ sở API. |
| GDNN-GDTX Tam Điệp | Tổ 3, phường Yên Sơn, tỉnh Ninh Bình; không ghi tên giám đốc | Chưa tìm thấy đúng tên. Có Trung tâm GDNN Nam Tam Điệp, địa chỉ tổ 4D, phường Trung Sơn; không tự coi là cùng cơ sở. |
| GDNN-GDTX Tin học và Ngoại ngữ Ninh Bình | Nguyễn Trọng Khiêm; ngõ 2, đường Tràng An, phường Hoa Lư, tỉnh Ninh Bình | Có ứng viên `NB-GDTX-001`: Trung tâm GDTX - Tin học Ngoại ngữ Tỉnh Ninh Bình; giám đốc Nguyễn Đăng Khánh; số 12 đường Lê Thái Tổ, TP. Ninh Bình. Nếu xác nhận cùng đơn vị thì cả tên giám đốc và địa chỉ chưa khớp file; hiện chưa đủ căn cứ ghép tự động. |

## Danh sách đối chiếu đầy đủ

| STT | Trung tâm | Giám đốc trong file | Kết quả tên giám đốc trên web | Địa chỉ trong file sau chuẩn hóa | Kết quả địa chỉ |
| ---: | --- | --- | --- | --- | --- |
| 1 | GDTX-HN Phủ Lý | Đinh Thanh Khương | Khớp | Ngõ 569, đường Lê Duẩn, phường Phủ Lý | Khớp |
| 2 | GDNN-GDTX Duy Tiên | Trần Văn Tiến | Khớp | Tổ dân phố số 3, phường Duy Tiên, tỉnh Ninh Bình | Khớp |
| 3 | GDNN-GDTX Lý Nhân | Phạm Văn Lực | Khác: Trần Văn Lực | Thôn Nam Cao, xã Vĩnh Trụ | Khớp |
| 4 | GDNN-GDTX Kim Bảng | Trần Huy Cường | Khác: Phạm Văn Cường | Tổ dân phố số 2, phường Kim Bảng, tỉnh Ninh Bình | Khớp |
| 5 | GDNN-GDTX Thanh Liêm | Không có trong file | Không đủ dữ liệu đối chiếu | Thôn Non, xã Tân Thanh, tỉnh Ninh Bình | Khớp |
| 6 | GDNN-GDTX Bình Lục | Không có trong file | Không đủ dữ liệu đối chiếu | Thôn Bình Nam, xã Bình Mỹ, tỉnh Ninh Bình | Khớp |
| 7 | GDNN-GDTX Nam Định | Trần Đức Hậu | Khác: Trần Trọng Tuyên | Tổ dân phố số 1, khu dân cư Đệ Tứ, phường Thiên Trường, tỉnh Ninh Bình | Khớp |
| 8 | GDNN-GDTX Mỹ Lộc | Nguyễn Văn Lương | Khớp | Tổ dân phố Trung Khuyên, phường Mỹ Lộc, tỉnh Ninh Bình | Khớp |
| 9 | GDNN-GDTX Ý Yên | Không có trong file | Không đủ dữ liệu đối chiếu | Thôn Tân Lâm, xã Ý Yên, tỉnh Ninh Bình | Khớp |
| 10 | GDNN-GDTX Vụ Bản | Không có trong file | Không đủ dữ liệu đối chiếu | Xóm Tiền, xã Liên Minh, tỉnh Ninh Bình | Khớp |
| 11 | GDNN-GDTX Nam Trực | Không có trong file | Không đủ dữ liệu đối chiếu | Đồng Côi, xã Nam Trực, tỉnh Ninh Bình | Khớp |
| 12 | GDNN-GDTX Trực Ninh | Không có trong file | Chưa tìm thấy đúng cơ sở | Thôn Trường Giang, xã Cát Thành, tỉnh Ninh Bình | Chưa đối chiếu được |
| 13 | GDNN-GDTX Hải Hậu | Trần Văn Biên | Chưa tìm thấy đúng cơ sở | Xóm 9, xã Hải Hưng, tỉnh Ninh Bình | Chưa đối chiếu được |
| 14 | GDNN-GDTX Giao Thủy | Nguyễn Xuân Trường | Khớp | Xóm Hồng Hải, xã Giao Hưng, tỉnh Ninh Bình | Khớp |
| 15 | GDNN-GDTX Xuân Trường | Đặng Văn Chỉnh | Khớp | Xóm 3, Thọ Nghiệp, xã Xuân Hưng, tỉnh Ninh Bình | Khớp |
| 16 | GDNN-GDTX Nghĩa Hưng | Vũ Thị Chuyền | Khớp | Số 30 Trần Phú, Nghĩa Hưng, Ninh Bình | Khớp |
| 17 | GDNN-GDTX Nho Quan | Phan Quý Quỳnh | Khớp | Thôn Tân Nhất, xã Nho Quan, tỉnh Ninh Bình | Khớp |
| 18 | GDNN-GDTX Gia Viễn | Bùi Thị Hòa | Khớp | Thôn Me, xã Gia Viễn, tỉnh Ninh Bình | Khớp |
| 19 | GDNN-GDTX Tin học và Ngoại ngữ Ninh Bình | Nguyễn Trọng Khiêm | Có ứng viên Nguyễn Đăng Khánh; cần xác minh | Ngõ 2, đường Tràng An, phường Hoa Lư, tỉnh Ninh Bình | Ứng viên khác địa chỉ; cần xác minh |
| 20 | GDNN-GDTX Tam Điệp | Không có trong file | Chưa tìm thấy đúng cơ sở | Tổ 3, phường Yên Sơn, tỉnh Ninh Bình | Chưa đối chiếu được |
| 21 | GDNN-GDTX Yên Mô | Vũ Xuân Tiến | Khác: Nguyễn Xuân Tiến | Xóm Nam Thành, xã Yên Từ, tỉnh Ninh Bình | Khớp |
| 22 | GDNN-GDTX Yên Khánh | Nguyễn Văn Thanh | Khớp | Xóm 3, xã Khánh Nhạc, tỉnh Ninh Bình | Khớp |
| 23 | GDNN-GDTX Kim Sơn | Không có trong file | Không đủ dữ liệu đối chiếu | Xóm 4 Thượng Kiệm, xã Phát Diệm, tỉnh Ninh Bình | Khớp |

## Trường xã/phường còn khác địa chỉ chi tiết

| Trung tâm | `ward` trên API chi tiết | Xã/phường trong file và `address` hiện tại |
| --- | --- | --- |
| GDNN-GDTX Nam Định | Phường Nam Định | Phường Thiên Trường |
| GDNN-GDTX Vụ Bản | Xã Vụ Bản | Xã Liên Minh |
| GDNN-GDTX Xuân Trường | Xã Xuân Trường | Xã Xuân Hưng |

Như vậy địa chỉ dạng văn bản đã khớp ở 19 cơ sở, nhưng trường xã/phường riêng còn ba trường hợp chưa đồng bộ. Điều này có thể làm nhãn địa bàn và bộ lọc khác với địa chỉ hiển thị. Chưa dùng file này để kết luận vị trí ghim bản đồ hoặc tọa độ đúng/sai vì file không có tọa độ.

## ID tham chiếu cho các trường hợp khác nguồn

| Trung tâm | ID VPS |
| --- | --- |
| Lý Nhân | `trung-tam-gdnn-gdtx-ly-nhan-957` |
| Kim Bảng | `trung-tam-gdnn-gdtx-kim-bang-934` |
| Nam Định | `trung-tam-gdnn-gdtx-nam-dinh-294` |
| Yên Mô | `trung-tam-gdnn-gdtx-yen-mo-188` |
| Vụ Bản | `trung-tam-gdnn-gdtx-vu-ban-728` |
| Xuân Trường | `trung-tam-gdnn-gdtx-xuan-truong-396` |
| Ứng viên Tin học và Ngoại ngữ | `NB-GDTX-001` |

Các kết quả chỉ phản ánh dữ liệu website tại thời điểm đọc ngày 17/11/2026. Việc cập nhật cần thực hiện đồng thời `principal` và đúng mục giám đốc trong `leaders`, giữ các cán bộ khác mà tài liệu không đề cập; ba địa bàn cần đối chiếu danh mục xã/phường trước khi sửa.
