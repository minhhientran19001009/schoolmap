# Đối chiếu lãnh đạo cơ sở GDNN: Excel và website ngày 17/11/2026

## Nguồn và cách kiểm tra

- File đọc trực tiếp: `C:\Users\Admin\Desktop\tai_lieu\5.11.2026. danh sách cơ sở giáo dục nghề nghiệp.xlsx`.
- Sheet dữ liệu: `Tính đến T32026`, 49 dòng, 14 cột. Sheet `Kangatang` không có dữ liệu cơ sở hữu ích.
- Đọc cột B (Tên cơ sở), cột J (Hiệu trưởng). Không trích số điện thoại.
- Có 34 dòng cơ sở có số thứ tự: 28 dòng ghi tên lãnh đạo, 6 dòng trống tên. Không tính các tiêu đề nhóm và dòng thuộc ô gộp là cơ sở riêng.
- Website: https://ninhbinh.poly-hna.com/map. Đọc trực tiếp API danh sách `/api/schools` và hồ sơ `/api/schools/{id}`, kiểm tra `principal`, `leaders`, chức vụ và quan hệ cơ sở trực thuộc.
- Không thay đổi code nghiệp vụ, dữ liệu local hoặc VPS. Báo cáo ghi nhận khác biệt so với tài liệu, không xác minh độc lập ai đang giữ chức vụ thực tế. Tên file có ngày 05/11/2026 nhưng tên sheet ghi T32026; không tự suy ra ngày hiệu lực của từng lãnh đạo.

## Tổng hợp 28 dòng có lãnh đạo

| Nhóm | Số dòng |
| --- | ---: |
| Tên lãnh đạo khớp ở cơ sở tương ứng, gồm tên viết tắt rõ nghĩa | 14 |
| Xác định được cùng trường nhưng tên lãnh đạo khác | 2 |
| Tên lãnh đạo khớp ở bản ghi ứng viên có tên trường khác; cần xác nhận đổi tên | 1 |
| Trường sau sáp nhập hoặc cấp trường/cơ sở cần đối chiếu thêm | 5 |
| Chưa tìm thấy đúng cơ sở | 6 |
| Tổng | 28 |

Những khác biệt dấu gạch, viết hoa, CĐ/Cao đẳng, NĐ/Nam Định được coi là khác biệt trình bày khi vẫn xác định được trường duy nhất. Không dùng việc trùng tên lãnh đạo để tự xác định hai trường là cùng đơn vị.

## Hai trường xác định được nhưng khác tên hiệu trưởng

| Dòng Excel | Trường | Excel | Website | Ghi nhận |
| ---: | --- | --- | --- | --- |
| 10 | Trường CĐ Công nghệ Dệt - May NĐ | Nguyễn Khắc Tuất | BS. Tôn Thất Hưng | Tên khác ở cả `principal` và `leaders`; web gán chức vụ Hiệu trưởng. |
| 23 | Trường Cao đẳng Phát thanh - Truyền hình I | Nguyễn Văn Hùng | TS. Nguyễn Văn Sơn | Web có cả bản ghi VOV và VOV – Cơ sở 1, đều hiển thị Nguyễn Văn Sơn. |

ID VPS liên quan:

- Dệt-May: `truong-cao-dang-cong-nghe-det-may-nam-di-918`.
- VOV: `HN-CD-004` và `truong-cao-dang-phat-thanh-truyen-hinh-i-114`.

## 14 cơ sở có tên lãnh đạo khớp

| Dòng Excel | Cơ sở trong file | Lãnh đạo khớp | Chức vụ trên web / lưu ý |
| ---: | --- | --- | --- |
| 8 | Trường CĐ công nghiệp NĐ | Nguyễn Duy Phấn | Hiệu trưởng |
| 9 | Trường CĐ xây dựng NĐ | Lương Văn Doanh | Phó Hiệu trưởng phụ trách trường, đúng chức vụ ghi trong file |
| 19 | Trường Cao đẳng Cơ điện Nông nghiệp và Môi trường | Dương Văn Cường | Hiệu trưởng; bản ghi trường và Cơ sở 2 đều có cùng tên người |
| 21 | Trường Cao đẳng nghề LiLaMa 1 | Nguyễn Duy Bảy | Hiệu trưởng |
| 22 | Trường Cao đẳng Công nghệ, kinh tế và chế biến lâm sản | Dương Duy Triều | Hiệu trưởng |
| 24 | Trường Cao đẳng Thủy lợi Bắc Bộ | Phạm Đức Thắng | Phó Hiệu trưởng phụ trách, đúng chức vụ ghi trong file |
| 28 | Trường Trung cấp Công nghệ Số 8 Nam Định | Nguyễn Chí Thuấn | Hiệu trưởng |
| 29 | Trường Trung cấp Đại Lâm | Trần Thọ Hiền | Hiệu trưởng |
| 30 | Trường Trung cấp Y - Dược Tôn Thất Tùng | Nguyễn Xuân Cảnh | Hiệu trưởng |
| 31 | Trường Trung cấp Kỹ thuật - Du lịch Công đoàn Ninh Bình | Dương Mạnh Hiền | Hiệu trưởng |
| 32 | Trường Trung cấp nghề Quốc tế | Trần Minh Chất | Hiệu trưởng; web có hậu tố (IVS) |
| 33 | Trường Trung cấp nghề Giao thông - Xây dựng Việt Úc | Nguyễn Văn Hoàn | Hiệu trưởng |
| 41 | Trung tâm Dạy nghề Nam Tam Điệp | Nguyễn Viết Thân | Giám đốc; web đặt tên Trung tâm GDNN Nam Tam Điệp |
| 43 | Trung tâm GDNN Minh Vuông | Đoàn Thị Kim Dung | Giám đốc |

Đặc biệt, không nhầm **Trường Cao đẳng Thủy lợi Bắc Bộ** (Phạm Đức Thắng, khớp) với **Trường Cao đẳng Cơ điện và Thủy lợi Bắc Bộ – Phân hiệu Ninh Bình** (Phạm Gia Điệp) là bản ghi có tên khác trên website.

## Một cơ sở khớp người nhưng khác tên trường

| Dòng Excel | Trong file | Bản ghi trên web | Lãnh đạo | Kết luận |
| ---: | --- | --- | --- | --- |
| 27 | Trường Trung cấp Giao thông Vận tải Nam Định | Trường Trung cấp Giao thông vận tải tỉnh Ninh Bình | Phạm Quang Thể, Hiệu trưởng, ở cả hai nguồn | Người khớp; cần xác nhận quan hệ đổi tên/đơn vị trước khi ghép chính thức. |

ID ứng viên: `truong-trung-cap-giao-thong-van-tai-tinh-811`.

## Năm trường cần đối chiếu cấp trường, cơ sở hoặc sáp nhập

| Dòng Excel | Trường và lãnh đạo trong file | Website hiện có | Nhận định |
| ---: | --- | --- | --- |
| 7 | Trường CĐ nghề Số 20-BQP — Trần Quang Dũng | Vị Khê: “Ban Giám đốc Cơ sở 3 - CĐ 20 (Đại tá Đặng Cao Cường phụ trách)”; Trường Thi: “Ban Giám đốc Cơ sở 5 - CĐ 20” | Chưa thấy Trần Quang Dũng trên hai bản ghi này. Đây là thông tin cấp cơ sở, cần phân biệt với hiệu trưởng toàn trường. Web còn đặt các chuỗi “Ban Giám đốc...” vào tên người, chức vụ Hiệu trưởng. |
| 11 | Trường Cao đẳng Kinh tế và Công nghệ tỉnh Ninh Bình — Đinh Văn Hoản; file ghi sáp nhập ba trường | Bản ghi Khu A mang tên Cao đẳng Kinh tế Và Công Nghệ Nam Định có Đinh Văn Hoản, Hiệu trưởng. Các cơ sở con có chuỗi “Điểm đào tạo NCET...” trong tên lãnh đạo. Trường Cao đẳng nghề Hà Nam còn hiển thị BS. Tôn Thất Hưng | Tên lãnh đạo đã xuất hiện đúng ở Khu A; tên pháp nhân sau sáp nhập, thông tin các đơn vị thành phần và các chuỗi “Điểm đào tạo...” chưa đồng nhất. Không kết luận toàn nhóm đã cập nhật xong. |
| 14 | Trường Cao đẳng Sư phạm và Nghệ thuật tỉnh Ninh Bình — Trần Hữu Toàn; file ghi sáp nhập hai trường | Chưa có đúng tên trường mới. Còn Cao đẳng Sư phạm Nam Định: Trần Duy Hưng; Cao đẳng Văn hóa Nghệ thuật và Du lịch Nam Định: Trần Thị Kim Oanh | Chưa thấy Trần Hữu Toàn trong các bản ghi liên quan; cần đối chiếu cơ cấu sau sáp nhập. |
| 16 | Trường Cao đẳng Y tế tỉnh Ninh Bình — Nguyễn Mạnh Cường; file ghi sáp nhập ba trường | Cao đẳng Y tế Ninh Bình: Nguyễn Thị Thu Hà; Cao đẳng Y tế Hà Nam: Nguyễn Thị Thu; Trung cấp Y tế Nam Định: Tôn Thất Hưng | Chưa thấy Nguyễn Mạnh Cường. Tên bản ghi Cao đẳng Y tế Ninh Bình gần tên trường mới, nhưng chưa có căn cứ xác nhận bản ghi này đại diện pháp nhân sau sáp nhập. |
| 25 | Trường Cao đẳng FPT Polytechnic — Vũ Thị Thu Hương | Chỉ có FPT Polytechnic – Cơ sở Hà Nam: Trần Hữu Thiện, Giám đốc cơ sở | Khác cấp quản lý; không kết luận Giám đốc cơ sở sai chỉ vì khác tên Hiệu trưởng trong file. |

Ở trường Cơ điện Nông nghiệp và Môi trường (dòng 19), tên lãnh đạo đã khớp. Tuy nhiên website vẫn có bản ghi Cao đẳng Cơ điện Xây dựng Việt Xô mang lãnh đạo Phạm Ngọc Vũ và chưa liên kết với trường mới; file ghi Việt Xô là một đơn vị tham gia sáp nhập. Đây là vấn đề cơ cấu/hiển thị bản ghi cũ cần rà soát riêng, không phải tên lãnh đạo trường mới đang sai.

## Sáu cơ sở có lãnh đạo trong file nhưng chưa tìm thấy đúng cơ sở trên web

| Dòng Excel | Cơ sở | Lãnh đạo trong file |
| ---: | --- | --- |
| 34 | Trường Trung cấp Y - Dược Hà Nam | Nguyễn Văn Phúc |
| 37 | Trung tâm GDNN Thanh niên | Lương Thanh Phong |
| 40 | Trung tâm Dạy chữ Braille và DN cho người mù | Đinh Thị Lương |
| 42 | Trung tâm GDNN Hồng Hà | Vũ Thu Trang |
| 44 | Trung tâm GDNN K27 | Phan Đình Thức |
| 45 | Trung tâm Dạy nghề GTVT Thành Nam | Phạm Thành Nam |

Website có **Trường trung cấp nghề Thành Nam**, lãnh đạo Đinh Xuân Thành. Chưa có căn cứ đồng nhất trường này với **Trung tâm Dạy nghề GTVT Thành Nam** trong file, nên không xếp thành trường hợp sai tên lãnh đạo. Tương tự không dùng Trung cấp Y tế Nam Định để thay cho Trung cấp Y - Dược Hà Nam.

## Sáu cơ sở không có tên lãnh đạo trong file

Không đủ dữ liệu từ file để kết luận lãnh đạo trên web đúng hoặc sai:

| Dòng Excel | Cơ sở |
| ---: | --- |
| 35 | Trường Trung cấp nghề Kỹ thuật cao Quốc tế |
| 38 | Trung tâm Dạy nghề và Hỗ trợ nông dân tỉnh Nam Định |
| 39 | Trung tâm Khuyến nông Hà Nam |
| 47 | Công ty TNHH Truyền thông DQT Hà Nội |
| 48 | Công ty TNHH Đức Huy |
| 49 | Công Ty TNHH MTV Minh Thư |

## Hướng xử lý theo kết quả đối chiếu

1. Hai trường khác tên rõ ràng: đối chiếu nguồn được chọn làm chuẩn rồi cập nhật đồng thời `principal` và mục tương ứng trong `leaders`.
2. Giữ đúng chức danh phụ trách ở Cao đẳng Xây dựng Nam Định và Cao đẳng Thủy lợi Bắc Bộ; hiện đang khớp.
3. Nhóm sáp nhập/cấp trường–cơ sở: xác nhận bản ghi đại diện đơn vị và quan hệ cơ sở trước khi áp dụng lãnh đạo toàn trường.
4. Sáu cơ sở chưa tìm thấy: xác minh danh tính trước khi tạo mới hoặc ghép với bản ghi khác.
5. File chỉ ghi một lãnh đạo ở mỗi dòng; không dùng để xóa các phó hiệu trưởng, phó giám đốc hoặc cán bộ khác trên web. Không đưa số điện thoại vào dữ liệu đồng bộ lãnh đạo.
