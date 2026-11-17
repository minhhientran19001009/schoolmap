# Quy mô giả lập phục vụ demo local — 17/11/2026

Theo yêu cầu người dùng, bổ sung số quy mô đang trống hoặc cơ sở chưa có báo cáo để trình diễn. Không ghi lên VPS.

## Kết quả

- File riêng: `exports/Danh_Sach_Truong_Hoc_Ninh_Binh_2026_11_17_Quy_Mo_Demo.xlsx`.
- 71 cơ sở, 523 dòng ngành/chương trình; không tự tạo tên ngành mới.
- Điền 2.769 ô số giả lập và tính 7 tổng từ các số đã có.
- Cập nhật local: tạo 60 báo cáo mới, cập nhật 11 báo cáo đã có.
- Sau nhập, API trả 71/71 cơ sở có báo cáo; không còn ô trình độ trống trong 523 dòng ngành.
- Đối chiếu với backup: 342 ô số nguồn đã có trong các báo cáo cũ được giữ nguyên, 0 ô số nguồn bị sửa.
- Chạy lại kiểm tra: 0 tạo mới, 0 cập nhật, 71 không đổi.

## Phân biệt số gốc và số demo

- Workbook gốc và workbook quy mô thật được giữ riêng.
- Sheet `QM_Tong_hop_goc`, `QM_Theo_nganh_goc`, `QM_Dong_goc` giữ các số nguồn.
- Sheet `Theo_doi_so_demo` ghi từng ô bổ sung, giá trị và cách tạo số.
- Cột `Trường số giả lập` trong các sheet nhập được lưu thành `demo_fields` ở báo cáo và `source_metadata.contains_demo_figures` trong CSDL; không chèn nhãn kỹ thuật vào bảng công khai.
- Giữ nguyên các số 0 có sẵn và các tổng mâu thuẫn. Ví dụ Đại Lâm vẫn cần đối chiếu tổng nguồn 0, không tự thay bằng tổng 5.150 đã được tính từ các trình độ.
- Số Đại học và GDTX được lưu riêng vào `dai_hoc`, `gdtx`; không đưa chúng sang cột Cao đẳng hoặc Sơ cấp.
- Không cập nhật mã ngành, chỉ tiêu, số sinh viên, lãnh đạo, địa chỉ, điện thoại hoặc tài khoản.

## Sao lưu và thao tác

Bản sao trước khi áp dụng local:

`backend/storage/app/import-backups/training-scale-20261117-161028-700104.json`

Chứa báo cáo cũ và danh sách trường bị tác động. Với trường trước đó chưa có báo cáo, phục hồi cần xóa đúng các báo cáo mới của lần nhập; với báo cáo cũ, phục hồi bản sao tương ứng. Không chạy xóa hàng loạt hoặc ghi đè toàn bộ DB.

Kiểm tra lại file:

```powershell
php scripts/applyLocalTrainingScale.php "docs/exports/Danh_Sach_Truong_Hoc_Ninh_Binh_2026_11_17_Quy_Mo_Demo.xlsx"
```

Chỉ công cụ local mới dùng `--apply` để ghi; công cụ từ chối APP_ENV khác local hoặc cấu hình MySQL không ở localhost. Trên admin, dùng chức năng **Nhập quy mô đào tạo** cho workbook này, không dùng chức năng nhập thông tin trường. Dữ liệu trong file demo không phải số liệu báo cáo chính thức.
