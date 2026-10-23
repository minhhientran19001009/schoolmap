<template>
  <footer class="w-full shrink-0 border-t border-slate-200/90 bg-slate-50 font-sans text-slate-600 selection:bg-blue-100 selection:text-blue-900">
    <!-- Top subtle gradient accent strip -->
    <div class="h-1 w-full bg-gradient-to-r from-blue-700 via-indigo-600 to-purple-600"></div>

    <div class="mx-auto w-full max-w-7xl px-4 py-7 sm:px-6 sm:py-8 lg:px-8">
      
      <!-- Top Agency Banner -->
      <div class="mb-6 flex flex-col gap-3 border-b border-slate-200/80 pb-5 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-blue-700 via-indigo-600 to-purple-600 text-base text-white shadow-xs">
            <i class="fa-solid fa-graduation-cap"></i>
          </div>
          <div>
            <div class="flex flex-wrap items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-500">
              <span class="text-blue-700">Sở Giáo dục và Đào tạo</span>
            </div>
            <h2 class="text-sm font-extrabold text-slate-900 sm:text-base">
              Cổng thông tin & Bản đồ số Giáo dục Nghề nghiệp tỉnh Ninh Bình
            </h2>
          </div>
        </div>

        
      </div>

      <!-- 4 Columns Standard Footer Layout (Gọn gàng, súc tích) -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-12 lg:gap-8">
        
        <!-- Column 1: Headquarters & Agency Contact (lg:col-span-4) -->
        <div class="space-y-2.5 lg:col-span-4">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">
            Cơ quan chủ quản & Vận hành
          </h3>
          <p class="text-xs leading-relaxed text-slate-500">
            Số hóa mạng lưới cơ sở giáo dục nghề nghiệp và kết nối cung - cầu nhân lực tỉnh Ninh Bình.
          </p>

          <ul class="space-y-1.5 text-xs text-slate-600">
            <li class="flex items-start gap-2">
              <i class="fa-solid fa-location-dot mt-0.5 text-slate-400 shrink-0 text-xs"></i>
              <span>Phường Đông Thành, TP. Ninh Bình, Tỉnh Ninh Bình</span>
            </li>
            <li class="flex items-center gap-2">
              <i class="fa-solid fa-phone text-slate-400 shrink-0 text-xs"></i>
              <span>Điện thoại: <strong class="text-slate-800">(0229) 387 1005</strong></span>
            </li>
            <li class="flex items-center gap-2">
              <i class="fa-solid fa-envelope text-slate-400 shrink-0 text-xs"></i>
              <span>Email: <a href="mailto:sogddt@ninhbinh.edu.vn" class="font-medium text-blue-700 hover:underline">sogddt@ninhbinh.edu.vn</a></span>
            </li>
            <li class="flex items-center gap-2">
              <i class="fa-solid fa-clock text-slate-400 shrink-0 text-xs"></i>
              <span>Giờ làm việc: Thứ 2 – Thứ 6 (07:30 – 17:00)</span>
            </li>
          </ul>
        </div>

        <!-- Column 2: Tra cứu & Dữ liệu GDNN (lg:col-span-3) -->
        <div class="space-y-2.5 lg:col-span-3">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">
            Tra cứu & Dữ liệu GDNN
          </h3>
          <ul class="space-y-1.5 text-xs">
            <li>
              <router-link to="/map" class="group flex items-center gap-2 text-slate-600 transition hover:text-blue-700">
                <i class="fa-solid fa-map-location-dot text-[11px] text-slate-400 group-hover:text-blue-600"></i>
                <span>Bản đồ số GDNN</span>
              </router-link>
            </li>
            <li>
              <router-link to="/co-so-gdnn" class="group flex items-center gap-2 text-slate-600 transition hover:text-blue-700">
                <i class="fa-solid fa-building-columns text-[11px] text-slate-400 group-hover:text-blue-600"></i>
                <span>Danh sách cơ sở GDNN tỉnh</span>
              </router-link>
            </li>
            <li>
              <router-link to="/danh-sach-doanh-nghiep" class="group flex items-center gap-2 text-slate-600 transition hover:text-blue-700">
                <i class="fa-solid fa-city text-[11px] text-slate-400 group-hover:text-blue-600"></i>
                <span>Doanh nghiệp đối tác</span>
              </router-link>
            </li>
            <li>
              <router-link to="/tuyen-dung" class="group flex items-center gap-2 text-slate-600 transition hover:text-blue-700">
                <i class="fa-solid fa-briefcase text-[11px] text-slate-400 group-hover:text-blue-600"></i>
                <span>Việc làm & Tuyển dụng</span>
              </router-link>
            </li>
            <li>
              <router-link to="/co-so-gdnn" class="group flex items-center gap-2 text-slate-600 transition hover:text-blue-700">
                <i class="fa-solid fa-user-graduate text-[11px] text-slate-400 group-hover:text-blue-600"></i>
                <span>Chỉ tiêu & Ngành nghề đào tạo</span>
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Column 3: Dành cho Đơn vị & Vai trò (lg:col-span-2) -->
        <div class="space-y-2.5 lg:col-span-2">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">
            Cổng dịch vụ & Vai trò
          </h3>
          <ul class="space-y-1.5 text-xs">
            <li>
              <router-link to="/vai-tro/nha-truong" class="group flex items-center gap-2 text-slate-600 transition hover:text-emerald-700">
                <i class="fa-solid fa-school text-[11px] text-slate-400 group-hover:text-emerald-600"></i>
                <span>Dành cho Cơ sở GDNN</span>
              </router-link>
            </li>
            <li>
              <router-link to="/vai-tro/doanh-nghiep" class="group flex items-center gap-2 text-slate-600 transition hover:text-purple-700">
                <i class="fa-solid fa-user-tie text-[11px] text-slate-400 group-hover:text-purple-600"></i>
                <span>Dành cho Doanh nghiệp</span>
              </router-link>
            </li>
            <li>
              <router-link to="/vai-tro/nha-truong" class="group flex items-center gap-2 text-slate-600 transition hover:text-blue-700">
                <i class="fa-solid fa-file-pen text-[11px] text-slate-400 group-hover:text-blue-600"></i>
                <span>Khai báo chỉ tiêu</span>
              </router-link>
            </li>
            <li>
              <router-link to="/vai-tro/doanh-nghiep" class="group flex items-center gap-2 text-slate-600 transition hover:text-blue-700">
                <i class="fa-solid fa-bullhorn text-[11px] text-slate-400 group-hover:text-blue-600"></i>
                <span>Đăng nhu cầu nhân lực</span>
              </router-link>
            </li>
            <li>
              <a href="https://dichvucong.gov.vn" target="_blank" rel="noopener noreferrer" class="group flex items-center gap-2 text-slate-600 transition hover:text-blue-700">
                <i class="fa-solid fa-arrow-up-right-from-square text-[11px] text-slate-400 group-hover:text-blue-600"></i>
                <span>Dịch vụ công Quốc gia</span>
              </a>
            </li>
          </ul>
        </div>

        <!-- Column 4: Hotline & Hỗ trợ kỹ thuật (lg:col-span-3) -->
        <div class="space-y-2.5 lg:col-span-3">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">
            Tổng đài hỗ trợ & Trực ban
          </h3>

          <div class="rounded-2xl border border-slate-200/90 bg-white p-3 space-y-2.5 shadow-2xs">
            <div class="flex items-center gap-2.5">
              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700 font-bold">
                <i class="fa-solid fa-headset text-sm"></i>
              </div>
              <div class="min-w-0">
                <p class="text-[11px] font-semibold text-slate-500">Đường dây nóng tư vấn GDNN</p>
                <a href="tel:02293868686" class="block text-sm font-extrabold text-blue-700 hover:text-blue-800 tracking-tight">
                  0229 386 8686
                </a>
              </div>
            </div>

            <div class="border-t border-slate-200/70 pt-2 text-[11px] text-slate-500">
              <p class="flex items-center justify-between">
                <span>Hỗ trợ kỹ thuật:</span>
                <strong class="text-slate-700">(0229) 387 1005</strong>
              </p>
            </div>
          </div>
        </div>

      </div>

      <!-- Bottom Bar: Copyright & Legal Notice -->
      <div class="mt-6 flex flex-col gap-2.5 border-t border-slate-200/80 pt-4 sm:flex-row sm:items-center sm:justify-between text-xs text-slate-500">
        <div class="space-y-0.5">
          <p class="font-semibold text-slate-700 text-[11px] sm:text-xs">
            © {{ currentYear }} Bản đồ số Giáo dục nghề nghiệp tỉnh Ninh Bình. Giữ toàn bộ bản quyền.
          </p>
          <p class="text-[10.5px] text-slate-400">
            Ghi rõ nguồn "Bản đồ số GDNN Ninh Bình" khi phát hành lại thông tin từ Cổng thông tin này.
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-500">
          <router-link to="/co-so-gdnn" class="hover:text-blue-700 transition">Quy chế dữ liệu</router-link>
          <span>•</span>
          <router-link to="/danh-sach-doanh-nghiep" class="hover:text-blue-700 transition">Hợp tác đào tạo</router-link>
          <span>•</span>
          <router-link to="/tuyen-dung" class="hover:text-blue-700 transition">Thị trường lao động</router-link>
          <span>•</span>
          <router-link to="/map" class="hover:text-blue-700 transition">Bản đồ số</router-link>
        </div>
      </div>

    </div>
  </footer>
</template>

<script setup>
const currentYear = new Date().getFullYear()
</script>
