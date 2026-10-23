# QUY ĐỊNH CHUẨN THIẾT KẾ VÀ STYLE GIAO DIỆN DỰ ÁN SCHOOLMAP NINH BÌNH

> **Mục đích**: Tài liệu này quy định toàn bộ tiêu chuẩn về tông màu, kích thước, khoảng cách (padding/margin), cách thiết kế các ô nhập liệu, dropdown select và bố cục trang công khai của hệ thống Bản đồ số GDNN Ninh Bình. 
> **Quy tắc bắt buộc**: Bất kỳ tính năng mới hoặc chỉnh sửa giao diện nào đều phải tuân thủ nghiêm ngặt tài liệu này để tránh tình trạng lệch style, đè chữ hay vỡ khung giao diện.

---

## 1. Triết lý Thiết kế & Tông màu Chủ đạo (Color Palette)

### 1.1. Chuẩn Theme: 100% Light Theme (Giao diện Sáng)
- Toàn bộ các trang công khai (Client Portal) của hệ thống **bắt buộc sử dụng Light Theme**.
- **Tuyệt đối không dùng**: Các khối nền đen, xanh đen tối (`bg-slate-900`, `bg-indigo-950`, `bg-blue-950`) làm nền Hero Banner hay thanh điều hướng vì sẽ gây xung đột thị giác và lệch với phần còn lại của hệ thống.

### 1.2. Bảng mã màu chuẩn:
| Mục đích | Mã màu / Tailwind Class | Mô tả ứng dụng |
| :--- | :--- | :--- |
| **Primary Blue** | `#2563eb` (`blue-600`), `#1d4ed8` (`blue-700`) | Màu thương hiệu chính, nút CTA, icon định danh, focus ring |
| **Accent Indigo** | `#4f46e5` (`indigo-600`), `#4338ca` (`indigo-700`) | Điểm nhấn tab Cơ sở, icon xã/phường |
| **Accent Purple** | `#7c3aed` (`purple-600`), `#6c3fb8` | Ngành nghề đào tạo, portal Doanh nghiệp, tuyển dụng |
| **Accent Emerald**| `#059669` (`emerald-600`), `#10b981` (`emerald-500`)| Cấp học, chỉ báo đang mở, tuyển sinh thành công |
| **Accent Amber**  | `#d97706` (`amber-600`), `#b45309` (`amber-700`) | Chỉ tiêu tuyển sinh, huy hiệu nổi bật |
| **Page Background**| `#f8f9fa` hoặc `bg-slate-50` | Nền toàn trang, sạch sẽ và dịu mắt |
| **Card Surface**  | `#ffffff` (`bg-white`) | Mặt thẻ trường, bảng lọc, thẻ thống kê |
| **Borders**       | `border-slate-200/90`, `border-slate-100` | Đường kẻ và viền thẻ mờ tinh tế |
| **Text Primary**  | `text-slate-900` | Tiêu đề chính `h1`, `h2`, số liệu lớn |
| **Text Body**     | `text-slate-700 font-medium` | Tên cơ sở, nhãn dropdown, nội dung chính |
| **Text Muted**    | `text-slate-500` / `text-slate-400` | Phụ đề, placeholder, ngày cập nhật |

### 1.3. Phân cấp chữ thống nhất trên Client Portal

- Font mặc định toàn hệ thống là **Roboto**; không khai báo font riêng tại từng trang.
- Tiêu đề trang danh sách/portal (`h1`): `text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight leading-tight text-slate-900`.
- Tiêu đề trang chi tiết (`h1`): `text-xl sm:text-2xl lg:text-3xl font-black leading-snug text-slate-900`.
- Tiêu đề khối nội dung (`h2`): `text-base font-bold text-slate-900`.
- Tiêu đề thẻ: `text-sm sm:text-base font-bold` hoặc `font-extrabold text-slate-900`.
- Nội dung chính: `text-xs sm:text-sm leading-relaxed text-slate-600`; nội dung cần nhấn mạnh dùng `text-slate-700`.
- Chú thích, metadata và placeholder: `text-[11px]` hoặc `text-xs text-slate-500`; không dùng màu xám quá nhạt cho nội dung bắt buộc phải đọc.
- Chỉ dùng `font-black` cho số liệu lớn và tiêu đề trang chi tiết. Tiêu đề trang cấp một dùng `font-extrabold` để đồng đều giữa các màn hình.

### 1.4. Quy chuẩn Hero và màu nhận diện vai trò

- Hero của mọi trang công khai phải là nền sáng: `from-blue-50/70 via-indigo-50/30 to-slate-50` hoặc biến thể sáng theo vai trò.
- Không đặt `text-white` cho toàn bộ hero. Tiêu đề luôn là `text-slate-900`, mô tả là `text-slate-600`.
- Màu vai trò chỉ dùng làm điểm nhấn:
  - Cơ sở GDNN: `emerald-600/700` (bao gồm cả School Panel của Filament).
  - Doanh nghiệp: `purple-600/700`.
  - Quản trị hệ thống, tra cứu công khai và CTA chung: `blue-600/700`.
- Nút chính dùng nền màu nhận diện và chữ trắng. Nút phụ dùng `bg-white border-slate-200 text-slate-700`, chỉ đổi sang màu nhận diện khi hover.
- Không dùng mã màu hex tùy ý trong template nếu Tailwind đã có màu tương đương. Ví dụ dùng `bg-blue-600` thay cho `bg-[#0068ff]`, `bg-slate-50` thay cho `bg-[#f8f9fa]`.

---

## 2. Quy chuẩn Form Controls, Inputs & Select Dropdowns

Đây là khu vực quan trọng nhất để chống lỗi lệch giao diện và đè chữ lên icon.

### 2.1. Kích thước & Hình khối chuẩn
- **Chiều cao cố định**: Tất cả ô tìm kiếm, dropdown lọc, nút sắp xếp đều dùng chiều cao chuẩn **`h-11` (44px)**.
- **Bo góc chuẩn**: `rounded-xl` (12px).
- **Màu nền**: `bg-slate-50/70 hover:bg-slate-100/70 focus:bg-white`.
- **Viền & Hiệu ứng Focus**:
  ```html
  border border-slate-200/90 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all shadow-2xs
  ```

### 2.2. Quy chuẩn Padding khi có Icon bên trái (CHỐNG LỖI ĐÈ CHỮ)
Khi một input hoặc select có icon đặt bên trái (`absolute left-0 pl-3.5` ~ 14px):
- **BẮT BUỘC dùng padding-left**: `pl-10` (40px).
- **CẤM DÙNG**: Các class tự chế không hợp lệ trong Tailwind như `pl-8.5` (sẽ bị Tailwind bỏ qua và dẫn đến padding = 0, khiến chữ đè thẳng lên icon).
- **Padding-right**: `pr-8` hoặc `pr-9` để chữ không chạm vào mũi tên chevron hoặc nút xóa `x`.

### 2.3. Quy định về Dropdown Select (CẤM TUYỆT ĐỐI DÙNG THẺ `<select>` MẶC ĐỊNH TRÊN CLIENT PORTAL)
> **Lưu ý tối quan trọng**: Thẻ HTML `<select>` mặc định của trình duyệt trên hệ điều hành Windows sẽ mở ra một menu dạng bảng thô nhọn góc, viền xanh dương đậm (`#0060df`) và font chữ hệ điều hành rất xấu, phá hỏng hoàn toàn tính thẩm mỹ hiện đại của giao diện. Vì vậy:
> 
> **Toàn bộ các dropdown trên các trang công khai (Client Portal) BẮT BUỘC dùng Custom Popover Dropdown (Vue template custom)**.

1. **Đối với danh sách dài hoặc dữ liệu từ CSDL (Ví dụ: Xã/Phường, Ngành nghề đào tạo)**:
   - **Bắt buộc** dùng **Custom Searchable Popover Dropdown**:
     - Nút Trigger cao `h-11`, có icon định danh bên trái (`pl-10`), nhãn đã chọn, nút `x` gỡ nhanh và mũi tên `fa-chevron-down` xoay 180° khi mở.
     - Popover mở ra: `rounded-2xl border border-slate-200 shadow-xl p-2 z-50 bg-white`.
     - Phải có ô tìm kiếm nhanh `Tìm kiếm...` bên trong để người dùng gõ từ khóa lọc tức thì.
     - Danh sách có scrollbar mềm (`max-h-64 overflow-y-auto`), có dấu tick `fa-check` xanh ở mục đang chọn.
     - Có sự kiện `handleClickOutside` để tự động đóng dropdown khi nhấn ra ngoài.

2. **Đối với danh sách ngắn cố định (Ví dụ: Sắp xếp Sort, Số lượng hiển thị mỗi trang Per-page)**:
   - **Bắt buộc** dùng **Custom Popover Dropdown (không cần ô search nội bộ)**:
     - Nút trigger cao `h-11` (hoặc `h-8` với per-page), bo góc `rounded-xl` (hoặc `rounded-lg`), có icon bên trái (`pl-10`), mũi tên chevron bên phải xoay khi mở.
     - Popover menu bo tròn `rounded-2xl shadow-xl border border-slate-200 p-1.5 bg-white z-50`.
     - Mỗi mục chọn có icon tương ứng (ví dụ: `fa-arrow-down-a-z`, `fa-user-graduate`, `fa-clock-rotate-left`), hiệu ứng hover nhẹ `hover:bg-slate-50`, chữ đậm và tick xanh `fa-check` khi được kích hoạt.
     - Đóng khi chọn hoặc khi click outside ra ngoài.

---

## 3. Quy chuẩn Dữ liệu Bộ lọc (Filter Data Integrity)

- **Chỉ hiển thị các bộ lọc mà CSDL thực sự có dữ liệu**:
  - Không tự thêm dropdown lọc theo Huyện/Thành phố nếu bảng `schools` có cột `district_id` toàn bộ là `null`.
  - Không tự thêm dropdown lọc theo Loại hình trường nếu CSDL chỉ có duy nhất 1 giá trị `cong_lap`.
  - Hiện tại trang danh sách cơ sở tập trung vào 2 bộ lọc cốt lõi thực tế:
    1. **Tất cả xã / phường**: Lấy từ bảng `wards` (đủ 129 xã, phường, thị trấn của Ninh Bình).
    2. **Tất cả ngành nghề**: Lấy từ bảng `training_majors` (118+ ngành nghề thực tế có chỉ tiêu).
- **Bố cục bộ lọc 2 tầng cân đối**:
  - **Tầng 1**: Ô tìm kiếm từ khóa linh hoạt (`flex-1`) + Ô sắp xếp A→Z (`min-w-[170px]`) + Nút đặt lại khi có lọc.
  - **Tầng 2**: Lưới 2 cột chia đôi cân xứng 50% - 50% (`grid grid-cols-1 md:grid-cols-2 gap-3`) cho 2 dropdown Xã/Phường và Ngành nghề.
  - **Tầng 3**: Thanh chip tag hiển thị các điều kiện đang lọc (nhấn `x` gỡ nhanh) kèm chỉ báo số lượng cơ sở tìm thấy.

---

## 4. Quy chuẩn Thẻ Thống kê Hero (Stat Cards)

- **Vị trí**: Đặt dưới tiêu đề trang tại phần Hero Banner.
- **Quy cách thẻ**:
  ```html
  <div class="rounded-2xl border border-slate-200/90 bg-white p-3.5 sm:p-4 shadow-2xs hover:shadow-soft transition-all">
    <div class="flex items-center justify-between">
      <span class="text-xs font-semibold text-slate-500">Tiêu đề chỉ số</span>
      <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-100/80 flex items-center justify-center text-xs">
        <i class="fa-solid fa-icon"></i>
      </div>
    </div>
    <div class="mt-2 flex items-baseline gap-1.5">
      <span class="text-xl sm:text-2xl font-black text-slate-900">123</span>
      <span class="text-xs font-medium text-slate-500">đơn vị</span>
    </div>
  </div>
  ```
- **Bộ 4 thẻ chuẩn của trang GDNN**:
  1. *Cơ sở phù hợp* (Icon trường học, xanh dương)
  2. *Địa bàn xã / phường* (Icon ghim bản đồ, màu chàm)
  3. *Ngành/nghề đào tạo* (Icon mũ cử nhân, màu tím)
  4. *Tổng chỉ tiêu/năm* (Icon sinh viên, màu hổ phách, chữ số `text-amber-600 font-black`)

---

## 5. Quy chuẩn Thẻ Trường học (School Cards Grid)

- **Lưới hiển thị**: `grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6`.
- **Cấu trúc thẻ**:
  - **Nền & Viền**: `bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md transition-all`.
  - **Đầu thẻ**: Badge cấp học (`#16a34a15`, chữ xanh lá) + Mã trường `#SCH...` ở góc phải.
  - **Tên trường**: `text-slate-900 font-extrabold text-base line-clamp-2 group-hover:text-blue-700 cursor-pointer`.
  - **Địa chỉ**: Có icon ghim `text-slate-400`, `text-xs text-slate-500 line-clamp-2`.
  - **Xem trước ngành nghề**: Top 3 ngành nghề đào tạo kèm chỉ tiêu (`bg-slate-50 py-1 px-2 rounded-lg`), kèm dòng `+ Còn N ngành khác...`.
  - **Chỉ tiêu năm**: Dòng tóm tắt tổng chỉ tiêu với số lượng `text-amber-700 font-extrabold`.
  - **Hai nút hành động dưới đáy**:
    - Nút 1: *"Xem chi tiết"* (`bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200/80 rounded-xl py-2 font-bold text-xs`).
    - Nút 2: *"Xem bản đồ"* (`bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 rounded-xl py-2 font-bold text-xs`).

---

## 6. Mẫu Code Chuẩn (Template Snippets)

### Mẫu 1: Dropdown Sắp xếp (Sort Select ngắn)
```html
<div class="relative min-w-[170px] sm:min-w-[185px]">
  <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
    <i class="fa-solid fa-arrow-down-a-z text-slate-500"></i>
  </div>
  <select
    v-model="filters.sort"
    @change="applyFilters"
    class="w-full h-11 pl-10 pr-8 text-xs sm:text-sm font-medium text-slate-700 bg-slate-50/70 hover:bg-slate-100/70 focus:bg-white rounded-xl border border-slate-200/90 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none appearance-none cursor-pointer transition-all shadow-2xs">
    <option value="name">Tên: A → Z</option>
    <option value="name_desc">Tên: Z → A</option>
    <option value="quota_desc">Chỉ tiêu cao nhất</option>
    <option value="newest">Mới cập nhật</option>
  </select>
  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400 text-xs">
    <i class="fa-solid fa-chevron-down"></i>
  </div>
</div>
```

### Mẫu 2: Custom Searchable Popover Dropdown (Dành cho Xã/Phường, Ngành nghề)
```html
<div ref="dropdownRef" class="relative w-full">
  <!-- Trigger Button -->
  <button
    type="button"
    @click="isOpen = !isOpen"
    class="w-full h-11 pl-10 pr-8 text-xs sm:text-sm font-medium rounded-xl border transition-all cursor-pointer flex items-center justify-between text-left shadow-2xs select-none"
    :class="isOpen 
      ? 'bg-white border-blue-400 ring-2 ring-blue-100 text-blue-700' 
      : (hasSelected 
          ? 'bg-blue-50/40 border-blue-200 font-bold text-blue-700' 
          : 'bg-slate-50/70 hover:bg-slate-100/70 border-slate-200/90 text-slate-700')">
    
    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-blue-600 text-xs">
      <i class="fa-solid fa-map-pin"></i>
    </div>
    
    <span class="truncate block pr-2">{{ selectedLabel }}</span>

    <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5">
      <span
        v-if="hasSelected"
        @click.stop="clearSelection"
        class="w-4 h-4 rounded-full text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors cursor-pointer">
        <i class="fa-solid fa-xmark text-[11px]"></i>
      </span>
      <i
        class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200 pointer-events-none"
        :class="{ 'rotate-180 text-blue-600': isOpen }"></i>
    </div>
  </button>

  <!-- Searchable Popover Card -->
  <div
    v-if="isOpen"
    class="absolute left-0 top-full mt-1.5 w-full bg-white border border-slate-200 rounded-2xl shadow-xl p-2 z-50 animate-in fade-in select-none">
    
    <!-- Mini Search Input -->
    <div class="relative mb-2">
      <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Tìm kiếm..."
        class="w-full pl-8 pr-7 py-2 bg-slate-100/90 hover:bg-slate-100 focus:bg-white text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-200 outline-none transition-all placeholder:text-slate-400"
      />
    </div>

    <!-- Scrollable Items List -->
    <div class="max-h-64 overflow-y-auto space-y-0.5 pr-1 text-xs">
      <!-- Options render here -->
    </div>
  </div>
</div>
```

---

## 7. Quy chuẩn Modal và lớp hiển thị (BẮT BUỘC)

Thanh điều hướng công khai trong `ClientLayout.vue` là header cố định có `z-[1200]`. Một số bộ lọc trên mobile có thể dùng tới `z-[3100]`. Vì vậy modal toàn màn hình không được dùng các lớp thấp như `z-50`, `z-[80]`, `z-[85]` hoặc `z-[100]`; các giá trị này làm tiêu đề, nút đóng và phần đầu biểu mẫu bị thanh menu che.

### 7.1. Thang lớp hiển thị chuẩn

| Thành phần | Z-index chuẩn |
| :--- | :--- |
| Nội dung trang | `z-0` đến `z-50` |
| Popover/dropdown cục bộ | `z-50` đến `z-[1000]` |
| Header công khai | `z-[1200]` |
| Drawer/preview thông tin | `z-[2000]` đến `z-[2500]` |
| Bộ lọc mobile toàn màn hình | `z-[3000]` đến `z-[3100]` |
| **Modal và loading toàn màn hình** | **`z-[4000]`** |

### 7.2. Cấu trúc modal biểu mẫu chuẩn

```html
<div class="fixed inset-0 z-[4000] flex items-center justify-center p-3 sm:p-6">
  <button class="absolute inset-0 bg-slate-950/55 backdrop-blur-[2px]"></button>

  <section class="relative flex max-h-[calc(100dvh-1.5rem)] w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl sm:max-h-[calc(100dvh-3rem)]">
    <header class="flex shrink-0 items-start justify-between border-b border-slate-100 px-5 py-4 sm:px-7 sm:py-5">
      <!-- Tiêu đề và nút đóng luôn nhìn thấy -->
    </header>

    <form class="flex min-h-0 flex-1 flex-col">
      <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-7">
        <!-- Chỉ vùng nội dung này được cuộn -->
      </div>

      <div class="flex shrink-0 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:px-7">
        <!-- Nút Hủy/Lưu luôn nhìn thấy -->
      </div>
    </form>
  </section>
</div>
```

Quy tắc bắt buộc:

- Dùng đơn vị `dvh`, không dùng riêng `vh`, để modal không vượt khung nhìn trên trình duyệt mobile.
- Khung modal phải có `flex flex-col overflow-hidden`.
- Header và footer phải có `shrink-0`; không đặt chúng trong vùng `overflow-y-auto`.
- Vùng cuộn phải có đủ `min-h-0 flex-1 overflow-y-auto`; thiếu `min-h-0` có thể khiến flex item tràn khỏi màn hình.
- Lớp nền mờ phải phủ `fixed inset-0`, bao gồm cả thanh menu.
- Không đặt modal client thấp hơn `z-[4000]` nếu modal có thể xuất hiện cùng header hoặc bộ lọc mobile.

### 7.3. Checklist đối chiếu trước khi hoàn thành

- Tiêu đề và nút đóng nhìn thấy đầy đủ, không nằm dưới header.
- Nút Hủy/Lưu vẫn nhìn thấy khi nội dung biểu mẫu dài.
- Chỉ phần thân biểu mẫu cuộn; nền trang phía sau không cuộn.
- Kiểm tra ở desktop và mobile, đặc biệt với chiều cao màn hình thấp.
- Chạy `rg -n 'fixed inset-0.*z-(\\[[0-9]+\\]|[0-9]+)' src` để phát hiện cả dạng `z-50` và `z-[80]`, tránh bỏ sót modal dùng sai thang `z-index`.
- Chạy `npm run build` sau khi thay đổi cấu trúc template.

Các mẫu đang áp dụng đúng quy chuẩn: `ClientSchoolPortalView.vue` (khai báo năng lực, đề xuất đáp ứng), `ClientBusinessPortalView.vue` (khai báo nhu cầu nhân lực), `EnterpriseDetailModal.vue` (chi tiết doanh nghiệp) và màn hình chuyển vai trò trong `ClientLayout.vue`.

---

## 8. Quy chuẩn Thẻ Việc làm & Cung Cầu GDNN (Theo Mẫu Apps Script)

Khi thiết kế giao diện danh sách công việc và ngành nghề đào tạo kết nối cung - cầu (theo mẫu Apps Script cổng 3 nhà):

### 8.0. Quy chuẩn popup xem nhanh việc làm

- Không hiển thị nút `Xem nhanh` trực tiếp trên thẻ việc làm.
- Trên thiết bị có chuột, popup xem nhanh tự mở sau khi con trỏ dừng trên thẻ **1,2 giây**.
- Nếu con trỏ rời thẻ hoặc người dùng bấm vào thẻ trước khi đủ thời gian, phải hủy timer ngay; không được để popup mở muộn ngoài ý muốn.
- Chỉ kích hoạt khi media query `(hover: hover) and (pointer: fine)` đúng. Thiết bị cảm ứng không giả lập hover; chạm vào thẻ sẽ mở trang chi tiết như bình thường.
- Popup phải nằm ở lớp `z-[4000]`, neo cạnh thẻ được hover và có mũi tên hướng về vùng tiêu đề việc làm.
- Ưu tiên mở bên phải thẻ; tự lật sang trái khi thiếu không gian và luôn giới hạn trong viewport.
- Hiệu ứng mở chuẩn: fade + trượt ngang nhẹ + scale trong khoảng `220–280ms`; hỗ trợ `prefers-reduced-motion`.
- Popup không dùng backdrop làm tối toàn màn hình, có nút đóng và vẫn cho phép chuyển sang trang chi tiết việc làm.

### 8.1. Cấu trúc thông tin bắt buộc trong mỗi thẻ
1. **Tiêu đề định danh**: Icon vali `fa-briefcase` (hoặc logo doanh nghiệp) + Tên nghề / vị trí việc làm.
2. **Chỉ số nhu cầu tuyển dụng**: Số lượng người nổi bật (`text-blue-700 font-bold`) kèm nhãn `người trong nhu cầu tuyển dụng`.
3. **Kỹ năng doanh nghiệp tìm kiếm**: Danh sách các chip tag (`bg-slate-100 border border-slate-200 text-slate-700 text-xs px-2.5 py-1 rounded-md`).
4. **Nơi có chương trình đào tạo (QUAN TRỌNG)**:
   - Danh sách các cơ sở GDNN (trường cao đẳng, trung cấp, trung tâm GDNN) đào tạo chuyên ngành/kỹ năng tương ứng.
   - Mỗi mục hiển thị icon mũ cử nhân `fa-graduation-cap`, tên cơ sở đào tạo in đậm, trình độ đào tạo (`Cao đẳng`, `Trung cấp`, `Sơ cấp`) kèm khu vực địa bàn.
   - Nút hành động **`Xem`** (màu xanh `text-blue-600 hover:text-blue-800 font-bold`) để mở thông tin chi tiết hoặc định vị cơ sở trên Bản đồ số GDNN.
5. **Nhu cầu tuyển dụng**: Tên các doanh nghiệp đang có nhu cầu kèm số lượng tuyển dụng cụ thể.

### 8.2. Chế độ hiển thị linh hoạt
- Hỗ trợ thanh chuyển đổi góc nhìn (View Switcher):
  - **Theo tin việc làm**: Hiển thị thẻ tuyển dụng chi tiết của từng doanh nghiệp kèm thông tin nơi đào tạo.
  - **Theo nghề & Nơi đào tạo (Mẫu Apps Script)**: Nhóm toàn bộ dữ liệu cung - cầu theo từng nghề cụ thể chuẩn mẫu Apps Script.

---

## 9. Quy định về Ngôn ngữ, Thuật ngữ & Loại bỏ Từ Kỹ thuật Khó hiểu (CẤM TỪ "GIS")

### 9.1. Quy tắc cấm dùng thuật ngữ chuyên môn gây khó hiểu:
> **Quy định bắt buộc**: Đối tượng sử dụng Cổng thông tin là học sinh, sinh viên, phụ huynh, người lao động và doanh nghiệp. Vì vậy, **TUYỆT ĐỐI KHÔNG** sử dụng các thuật ngữ kỹ thuật chuyên sâu gây xa lạ, khó hiểu như: **`GIS`**, **`WGS84`**, **`Buffer Analysis`**, **`GeoJSON`**, v.v. trên bất kỳ phần giao diện người dùng nào (cả Client Portal lẫn Admin UI).

### 9.2. Bảng quy chuẩn chuyển đổi thuật ngữ bắt buộc:
| Thuật ngữ kỹ thuật (CẤM DÙNG) | Thuật ngữ chuẩn thay thế (BẮT BUỘC DÙNG) | Ngữ cảnh sử dụng |
| :--- | :--- | :--- |
| `GIS`, `Hệ thống GIS` | **Bản đồ số**, **Hệ thống Bản đồ số** | Menu, tiêu đề, giới thiệu hệ thống |
| `Bản đồ tương tác GDNN (GIS)` | **Bản đồ số GDNN** | Menu điều hướng, liên kết footer |
| `Hệ thống GIS trực tuyến` | **Dữ liệu trực tuyến** | Badge trạng thái hệ thống |
| `Tọa độ GIS`, `Tọa độ WGS84` | **Tọa độ vị trí** | Bảng danh sách, chi tiết cơ sở |
| `Lớp phân tích GIS` | **Lớp bản đồ phân tích** | Sidebar, thanh công cụ bản đồ |
| `Buffer Analysis`, `Phân tích GIS` | **Tìm kiếm lân cận**, **Bán kính xung quanh** | Công cụ tra cứu trường lân cận |

---

## 10. Quy chuẩn Chân trang (Footer) & Độ ngắn gọn nội dung (Concise Rule)

### 10.1. Nguyên tắc ngắn gọn, súc tích (Concise & Compact):
- Chân trang (Footer) và các khối thông tin hỗ trợ phải được thiết kế **gọn gàng, súc tích**, đi thẳng vào nội dung cần thiết.
- **Không chèn các đoạn văn dài dòng**: Tránh các đoạn văn bản mô tả triết lý, diễn giải định dạng dữ liệu kỹ thuật làm cồng kềnh giao diện.
- Trụ sở, số điện thoại, email và đường dây nóng cần hiển thị trực diện, rõ ràng, dễ bấm gọi ngay trên thiết bị di động.

### 10.2. Bố cục Footer chuẩn 4 cột:
1. **Cột 1 (Cơ quan chủ quản & Vận hành)**: Tên cơ quan, 1 câu tóm tắt ngắn gọn, địa chỉ trụ sở, số điện thoại, email, giờ hành chính.
2. **Cột 2 (Tra cứu & Dữ liệu GDNN)**: Bản đồ số GDNN, danh sách cơ sở, doanh nghiệp đối tác, việc làm & tuyển dụng, chỉ tiêu ngành nghề.
3. **Cột 3 (Cổng dịch vụ & Vai trò)**: Không gian trường học, không gian doanh nghiệp, khai báo chỉ tiêu, đăng nhu cầu, dịch vụ công quốc gia.
4. **Cột 4 (Tổng đài hỗ trợ & Trực ban)**: Hộp hotline tư vấn GDNN `0229 386 8686` và số hỗ trợ kỹ thuật `(0229) 387 1005`.
5. **Dòng bản quyền dưới cùng (Bottom Bar)**: Bản quyền Sở GD&ĐT tỉnh Ninh Bình, lưu ý ghi rõ nguồn khi trích dẫn, kèm liên kết chính sách dữ liệu ngắn.

### 10.3. Màu nền chuẩn cho Header và Footer (Màu nền nhạt):
- **Header**: Bắt buộc sử dụng màu nền nhạt `bg-slate-50/95 backdrop-blur-md` kết hợp đường viền phân tách mờ `border-b border-slate-200/90`. Không dùng màu trắng tinh `bg-white` để tạo cảm giác dịu mắt và tôn bật các nút điều hướng bên trong.
- **Footer**: Bắt buộc sử dụng màu nền nhạt `bg-slate-50` kết hợp viền trên `border-t border-slate-200/90`. Các khối thẻ chức năng bên trong (như hộp hotline) sử dụng nền trắng `bg-white` để nổi bật trên nền nhạt của footer.

