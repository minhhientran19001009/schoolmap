<template>
  <div class="w-full h-full overflow-y-auto bg-slate-50 py-8 px-4 sm:px-8">
    <div class="max-w-4xl mx-auto space-y-6">
      
      <!-- Hero Banner -->
      <div class="bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 rounded-2xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 space-y-3">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-semibold backdrop-blur-xs text-blue-200">
            <i class="fa-solid fa-compass"></i>
            <span>Đề án Chuyển đổi số Ngành Giáo dục</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
            Hệ Thống Bản Đồ Số Mạng Lưới Giáo Dục Tỉnh Ninh Bình
          </h1>
          <p class="text-blue-100 text-sm sm:text-base leading-relaxed max-w-2xl">
            Công cụ trực quan hóa toàn bộ mạng lưới cơ sở giáo dục từ mầm non đến đại học trên nền tảng bản đồ số và hệ thống thông tin địa lý (GIS), phục vụ công tác tra cứu cho người dân và quản lý quy hoạch trường lớp.
          </p>
          <div class="pt-2 flex flex-wrap gap-3">
            <router-link to="/map" class="px-5 py-2 rounded-xl bg-white text-blue-900 font-bold text-xs hover:bg-blue-50 transition-all shadow-md flex items-center gap-2">
              <i class="fa-solid fa-map-location-dot"></i>
              <span>Khám phá Bản đồ số</span>
            </router-link>
            <router-link to="/thong-ke" class="px-5 py-2 rounded-xl bg-blue-700/80 hover:bg-blue-700 text-white font-semibold text-xs transition-all border border-white/20 flex items-center gap-2">
              <i class="fa-solid fa-chart-pie"></i>
              <span>Xem Thống kê Báo cáo</span>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Highlights Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2">
          <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-lg">
            <i class="fa-solid fa-map-marked-alt"></i>
          </div>
          <h3 class="font-bold text-slate-800 text-base">Địa giới 129 Xã/Phường</h3>
          <p class="text-xs text-slate-500 leading-relaxed">
            Dữ liệu ranh giới không gian chi tiết của 8 Huyện/Thành phố và 129 xã, phường, thị trấn chuẩn hóa theo cơ sở dữ liệu địa giới hành chính quốc gia.
          </p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-lg">
            <i class="fa-solid fa-bullseye"></i>
          </div>
          <h3 class="font-bold text-slate-800 text-base">Phân tích Vùng đệm Buffer</h3>
          <p class="text-xs text-slate-500 leading-relaxed">
            Hỗ trợ phụ huynh và nhà quản lý tính toán bán kính phục vụ 1km, 3km, 5km để kiểm tra mật độ các trường lân cận và quy hoạch tuyển sinh hợp lý.
          </p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2">
          <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-lg">
            <i class="fa-solid fa-award"></i>
          </div>
          <h3 class="font-bold text-slate-800 text-base">Chuẩn Quốc gia & Cơ sở vật chất</h3>
          <p class="text-xs text-slate-500 leading-relaxed">
            Minh bạch hóa các thông tin về trường đạt chuẩn Quốc gia mức 1 & mức 2, diện tích khuôn viên, phòng học, phòng chức năng và thông tin liên hệ.
          </p>
        </div>
      </div>

      <!-- Contact Info Box -->
      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h4 class="font-bold text-slate-800 text-sm">Sở Giáo dục và Đào tạo Tỉnh Ninh Bình</h4>
          <p class="text-xs text-slate-500 mt-1">
            <i class="fa-solid fa-location-dot mr-1 text-slate-400"></i> Phường Đông Thành, Thành phố Ninh Bình, Tỉnh Ninh Bình
          </p>
          <p class="text-xs text-slate-500 mt-0.5">
            <i class="fa-solid fa-envelope mr-1 text-slate-400"></i> sogddt@ninhbinh.edu.vn • <i class="fa-solid fa-phone mr-1 text-slate-400"></i> 0229 387 1005
          </p>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
</script>
