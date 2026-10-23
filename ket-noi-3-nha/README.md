# KẾT NỐI CUNG CẦU – Bộ mã dành cho Google Sites

**Cổng kết nối 3 nhà Ninh Bình · Phiên bản chuyển đổi 01/10/2026**

Bộ mã giữ giao diện hiện có và bốn góc khai thác: Cơ quan quản lý, Doanh nghiệp, Cơ sở GDNN và Người dân. Google Sites chứa khung nhúng; Google Apps Script chạy ứng dụng; Google Sheets lưu dữ liệu. Không cần cài Node.js hoặc thuê máy chủ để sử dụng các tệp trong thư mục `install`.

## Dữ liệu và chức năng được giữ

- Tiêu đề: **KẾT NỐI CUNG CẦU**.
- 24 hồ sơ doanh nghiệp Ninh Bình, gồm nguồn, phạm vi thống kê, đầu mối đã được ghi nhận và lưu ý trong cổng gốc.
- 14 cơ sở GDNN, 195 dòng ngành nghề từ danh mục đã cung cấp; giữ nguyên tên nghề, mã, số liệu, dòng nguồn và ghi chú bất nhất.
- Đăng nhu cầu nhân lực và kỹ năng; khai báo năng lực, đội ngũ, thiết bị; đối chiếu theo nghề, trình độ và kỳ; ghi đề xuất đáp ứng.
- Tổng hợp cung–cầu, lọc nghề và xuất CSV; tra cứu cơ sở, nghề và nhu cầu tại góc người dân.
- Khởi tạo 15 bản ghi nhu cầu/năng lực và 2 đề xuất **Minh họa**, không phải thông báo tuyển dụng hoặc tuyển sinh thật.

Đây là bộ mã độc lập. Không sao chép các bản ghi do người dùng tự nhập trong cơ sở dữ liệu của cổng đang chạy. Danh mục doanh nghiệp và cơ sở GDNN trong bộ mã được lấy từ phiên bản đã cập nhật của cổng. Số tuyển sinh trong tài liệu nguồn không được chuyển thành năng lực đào tạo của Quý IV/2026. Tên tệp nguồn ghi 2026, nội dung báo cáo ghi 9 tháng năm 2025; cổng tiếp tục hiển thị lưu ý này.

## 1. Tạo dự án Apps Script

Mở [Google Apps Script](https://script.google.com/) bằng tài khoản sẽ sở hữu cổng, tạo dự án mới và đặt tên **Cổng kết nối 3 nhà Ninh Bình**.

Sao chép toàn bộ nội dung từng tệp trong `install` vào dự án theo bảng sau. Dùng trình soạn thảo văn bản để mở tệp, tránh mở bằng Word. Với các tệp dài, chọn tất cả rồi sao chép.

| Tệp trong bộ mã | Tệp trong trình biên tập Apps Script | Cách tạo |
| --- | --- | --- |
| `Code.gs` | `Code.gs` | Thay nội dung tệp có sẵn |
| `Rules.gs` | `Rules.gs` | Dấu + ở Files → Script → tên `Rules` |
| `Data.gs` | `Data.gs` | Dấu + ở Files → Script → tên `Data` |
| `Index.html` | `Index.html` | Dấu + ở Files → HTML → tên `Index` |
| `appsscript.json` | `appsscript.json` | Project Settings → bật “Show appsscript.json manifest file in editor” rồi thay nội dung |

Giữ nguyên tên các tệp, đặc biệt `Index.html`. Lưu tất cả thay đổi.

## 2. Khởi tạo bảng dữ liệu riêng tư

Trong thanh công cụ Apps Script, chọn hàm `setupPortal`, bấm **Run / Chạy** và chấp thuận quyền truy cập bằng chính tài khoản chủ sở hữu. Ứng dụng cần quyền tạo/đọc/ghi Google Sheets và xác định email tài khoản.

Hàm tạo một bảng tính mới tên **KẾT NỐI CUNG CẦU – Cổng 3 nhà**. Mở **Execution log / Nhật ký thực thi** để lấy địa chỉ bảng tính. Giữ bảng tính riêng tư. Chạy lại hàm sẽ dùng bảng hiện có, không xóa hay ghi đè các bản ghi đã lưu.

Các trang dữ liệu: `NhuCau_NangLuc`, `DeXuat`, `DoanhNghiep`, `CoSoGDNN`, `Nguon`. Hai cột `id` và `record_json` là cấu trúc lưu của ứng dụng. Nhập nhu cầu, năng lực và đề xuất qua cổng; không sửa trực tiếp chuỗi JSON nếu chưa quen cấu trúc dữ liệu.

## 3. Triển khai ứng dụng riêng tư

Trong Apps Script chọn **Deploy → New deployment → Web app**:

- Execute as / Thực thi với tư cách: **Me / Tôi**.
- Who has access / Ai có quyền truy cập: **Only myself / Chỉ mình tôi**.

Bấm Deploy, sao chép địa chỉ kết thúc bằng `/exec`, mở trong thẻ mới và kiểm tra tải dữ liệu. Dùng địa chỉ `/exec` cho Google Sites. Địa chỉ `/dev` chỉ dành cho kiểm tra của người có quyền biên tập dự án.

`appsscript.json` đặt `access: MYSELF` và `executeAs: USER_DEPLOYING`. Máy chủ kiểm tra email đang đăng nhập ở mỗi lần đọc/ghi. Email trống hoặc tài khoản khác đều bị từ chối, kể cả nếu cấu hình triển khai vô tình được mở rộng. Không dùng email “effective user” thay cho email người truy cập để vượt kiểm tra.

## 4. Nhúng vào Google Sites

Mở [Google Sites](https://sites.google.com/), tạo hoặc mở trang của anh. Chọn **Insert / Chèn → Embed / Nhúng → By URL / Theo URL**, dán địa chỉ `/exec`, bấm Insert. Kéo khung rộng hết phần nội dung và cao khoảng 1.200–1.600 px để dễ xem bảng và biểu mẫu.

Trong thiết lập chia sẻ của Google Sites, đặt **Published site / Trang web đã xuất bản → Restricted / Hạn chế**, chỉ thêm tài khoản chủ sở hữu để thử nghiệm. Chỉ bấm Publish khi anh quyết định sử dụng trang. Quyền của Google Sites và quyền của ứng dụng Apps Script được thiết lập riêng; người xem khung nhúng vẫn phải có quyền với ứng dụng.

Không dán `Code.gs`, `Rules.gs` hoặc `Data.gs` vào ô “Embed code” của Google Sites. Các tệp này chạy ở Apps Script. `Index.html` cũng phải ở Apps Script để gọi được chức năng lưu vào Sheets.

## 5. Kiểm tra sau khi cài

1. Đăng nhập đúng tài khoản chủ sở hữu, mở `/exec` trực tiếp: thấy tiêu đề và dữ liệu cung–cầu.
2. Doanh nghiệp → Danh bạ: kiểm tra 24 hồ sơ; chọn một hồ sơ để mở biểu mẫu nhu cầu với tên đơn vị đã điền. Nhập một bản ghi thử có tên rõ ràng, ví dụ “KIỂM THỬ – Doanh nghiệp”.
3. Cơ sở GDNN → Cơ sở & ngành, nghề: kiểm tra 14 cơ sở; chọn nghề, thêm năng lực thử cùng nghề, trình độ và kỳ với nhu cầu thử.
4. Đối chiếu và đề xuất: ghi số lượng trong khả năng còn lại. Khi lưu, nhập mã xác nhận hiển thị trên màn hình; thao tác này bảo vệ việc ghi dữ liệu trong khung nhúng. Thử số lượng vượt giới hạn để kiểm tra thông báo từ chối.
5. Tải lại trang: các bản ghi thử vẫn còn. Kiểm tra bảng tổng hợp, lọc nghề, xuất CSV và góc người dân.
6. Mở bằng tài khoản khác: ứng dụng phải từ chối. Kiểm tra lại cả trang nhúng trong Google Sites.

Các bản ghi thử sau khi lưu là dữ liệu của bảng tính mới. Cổng chưa có chức năng xóa trên giao diện; nên thực hiện thử nghiệm trước khi nhập dữ liệu sử dụng chính thức.

## Nếu trang nhúng không tải

- Mở `/exec` trong thẻ riêng và đăng nhập đúng tài khoản chủ sở hữu trước.
- Kiểm tra đã chạy `setupPortal`, đã cấp quyền và đã lưu đủ 5 tệp.
- Đảm bảo triển khai đúng **Me / Only myself** và dùng URL `/exec`.
- Chính sách Google Workspace hoặc việc trình duyệt chặn đăng nhập trong iframe có thể ảnh hưởng khung nhúng. Nếu mở trực tiếp được nhưng nhúng không được, đặt thêm nút liên kết mở cổng trong thẻ mới trên Google Sites.
- Nếu Apps Script không cung cấp email người dùng, mã từ chối truy cập để giữ dữ liệu riêng tư. Không đổi sang “Anyone” để xử lý lỗi này.
- Sau khi sửa mã, dùng **Deploy → Manage deployments → Edit → New version → Deploy** để cập nhật phiên bản tại URL hiện có.

## Phạm vi quyền truy cập

Bộ mã mặc định dành cho **một tài khoản chủ sở hữu**. Bốn tab là bốn góc khai thác, chưa phải phân quyền theo tài khoản doanh nghiệp, nhà trường, cơ quan quản lý và người dân. Việc cho nhiều đơn vị đăng nhập riêng cần bổ sung quản lý tài khoản và quyền trước khi mở rộng truy cập. Bộ mã không tự chia sẻ bảng tính, không xuất bản Google Sites và không thay đổi cổng hiện có.

## Các tệp khác

- `XEM_THU.html`: mở trực tiếp bằng trình duyệt để xem giao diện và tra cứu dữ liệu ngoại tuyến. Bản này không lưu bản ghi.
- `HUONG_DAN.html`: hướng dẫn có định dạng, mở được bằng trình duyệt hoặc in thành PDF.
- `KIEM_THU.json`: kết quả 24 kiểm tra hành vi cục bộ; dùng các dịch vụ Apps Script giả lập và bộ mã giao tiếp thật. Chưa xác nhận triển khai trên tài khoản Google hay chạy trong iframe Google Sites; bước 5 là kiểm tra sau cài đặt.
- `source/`: mã React/TypeScript, dữ liệu nguồn và CSS đã biên dịch; `scripts/`: trình tạo bộ cài và kiểm thử.
- `THIRD_PARTY_NOTICES.txt`: giấy phép của các thư viện được đưa vào mã.

Để xây dựng lại bằng Node.js 22 trở lên: trong `source`, chạy `npm install`, `npm run build`, `npm test`. Các tệp cài đặt đã được tạo sẵn nên bước này không cần thiết cho người chỉ muốn cài cổng.

## Tài liệu chính thức

- [Google Apps Script: Web apps và nhúng Google Sites](https://developers.google.com/apps-script/guides/web)
- [Manifest: các chế độ truy cập và tài khoản thực thi](https://developers.google.com/apps-script/manifest/web-app-api-executable)
- [Google Sites: xuất bản và chia sẻ có giới hạn](https://support.google.com/sites/answer/6372880?hl=vi)
- [Apps Script: định danh tài khoản đang truy cập](https://developers.google.com/apps-script/reference/base/session)
- [HTML Service: giao tiếp với hàm máy chủ](https://developers.google.com/apps-script/guides/html/communication)
- [HTML Service: chế độ cho phép nhúng iframe](https://developers.google.com/apps-script/reference/html/x-frame-options-mode)
