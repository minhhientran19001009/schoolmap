<template>
  <div>
    <!-- Mobile & Tablet Backdrop overlay -->
    <div 
      v-if="school"
      @click="$emit('close')"
      class="fixed inset-0 bg-slate-900/40 backdrop-blur-2xs z-[1190] lg:hidden animate-in fade-in duration-200">
    </div>

    <!-- Main Right Sidebar Drawer -->
    <div 
      v-if="school"
      class="fixed top-0 right-0 bottom-0 z-[1200] w-full sm:w-[460px] md:w-[500px] bg-white shadow-[-10px_0_30px_rgba(0,0,0,0.15)] flex flex-col border-l border-slate-200 transition-all duration-300 ease-in-out select-none">
      
      <!-- Top Header (Inspired by reference screenshot) -->
      <div class="relative bg-gradient-to-r from-blue-700 via-indigo-700 to-blue-800 text-white p-4 sm:p-5 flex-shrink-0 shadow-md">
        <!-- Close Button -->
        <button 
          @click="$emit('close')"
          class="absolute top-4 right-4 text-white/80 hover:text-white bg-white/10 hover:bg-white/20 w-8 h-8 rounded-full flex items-center justify-center transition-all cursor-pointer z-10"
          title="Đóng ngăn chi tiết">
          <i class="fa-solid fa-xmark text-sm"></i>
        </button>

        <!-- Top Badge row -->
        <div class="flex items-center gap-2 mb-2">
          <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
            <i :class="levelIcon" class="text-xs text-white"></i>
          </div>
          <span class="text-[11px] font-bold uppercase tracking-wider text-blue-100">
            {{ levelLabel || 'CƠ SỞ GIÁO DỤC - ĐÀO TẠO' }}
          </span>
        </div>

        <!-- School Title -->
        <h2 class="text-base sm:text-lg font-bold leading-snug pr-10 text-white">
          {{ school.name }}
        </h2>

        <!-- Status & Location tags -->
        <div class="flex flex-wrap items-center gap-1.5 mt-2.5 text-xs">
          <!-- Active Status Pill -->
          <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/25 text-emerald-200 border border-emerald-400/30">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>Hoạt động</span>
          </span>

          <!-- Location Badge -->
          <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-white/15 text-white/90 border border-white/20">
            <i class="fa-solid fa-location-dot text-[10px] text-blue-200"></i>
            <span>{{ school.ward || school.district_name || 'Ninh Bình' }}</span>
          </span>

        </div>
      </div>

      <!-- Tab Navigation (3 Clean Tabs: Tổng quan, Số liệu, Doanh nghiệp) -->
      <div class="flex border-b border-slate-200 bg-slate-50/80 px-2 flex-shrink-0 text-xs font-semibold">
        <!-- Tab 1: Tổng quan -->
        <button 
          @click="activeTab = 'general'"
          :class="activeTab === 'general' 
            ? 'text-blue-700 border-b-2 border-blue-700 bg-white shadow-xs font-bold' 
            : 'text-slate-600 hover:text-slate-900'"
          class="flex-1 py-3 px-2 text-center transition-all flex items-center justify-center gap-1.5 cursor-pointer">
          <i class="fa-solid fa-circle-info text-xs"></i>
          <span>Tổng quan</span>
        </button>

        <!-- Tab 2: Số liệu -->
        <button 
          @click="activeTab = 'personnel'"
          :class="activeTab === 'personnel' 
            ? 'text-blue-700 border-b-2 border-blue-700 bg-white shadow-xs font-bold' 
            : 'text-slate-600 hover:text-slate-900'"
          class="flex-1 py-3 px-2 text-center transition-all flex items-center justify-center gap-1.5 cursor-pointer">
          <i class="fa-solid fa-chart-column text-xs"></i>
          <span>Số liệu</span>
        </button>

        <!-- Tab 3: Doanh nghiệp -->
        <button 
          @click="activeTab = 'enterprises'"
          :class="activeTab === 'enterprises' 
            ? 'text-blue-700 border-b-2 border-blue-700 bg-white shadow-xs font-bold' 
            : 'text-slate-600 hover:text-slate-900'"
          class="flex-1 py-3 px-2 text-center transition-all flex items-center justify-center gap-1.5 cursor-pointer">
          <i class="fa-solid fa-handshake text-xs"></i>
          <span>Doanh nghiệp</span>
        </button>
      </div>

      <!-- Tab Content Area (Scrollable) -->
      <div class="flex-1 overflow-y-auto p-4 space-y-4 text-xs text-slate-700 bg-slate-50/30">
        
        <!-- ============================================================== -->
        <!-- TAB 1: TỔNG QUAN & ĐÀO TẠO                                    -->
        <!-- ============================================================== -->
        <div v-if="activeTab === 'general'" class="space-y-4 animate-in fade-in duration-200">
          
          <!-- 1. Danh sách Ban Giám hiệu, Trưởng khoa -->
          <div class="bg-white rounded-xl p-3.5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-2.5">
              <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                <i class="fa-solid fa-user-tie text-blue-600"></i>
                <span>Ban Giám hiệu & Lãnh đạo</span>
              </span>
              <span class="text-[10px] text-slate-400 font-medium">
                {{ displayLeaders.length }} cán bộ
              </span>
            </div>

            <div v-if="displayLeaders.length > 0" class="space-y-2">
              <div 
                v-for="(leader, idx) in displayLeaders" 
                :key="idx"
                class="flex items-center justify-between p-2 rounded-lg bg-slate-50/80 border border-slate-100 hover:border-blue-200 transition-colors">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs flex-shrink-0">
                    {{ leader.name ? leader.name.charAt(0) : 'L' }}
                  </div>
                  <div>
                    <div class="font-bold text-slate-800 text-[12px]">{{ leader.name }}</div>
                    <div class="text-[11px] text-blue-600 font-medium">{{ leader.position }}</div>
                  </div>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-semibold">
                  Lãnh đạo
                </span>
              </div>
            </div>
            <div v-else class="text-[11px] text-slate-400 italic text-center py-2">
              Đang cập nhật thông tin Ban Giám hiệu
            </div>
          </div>



          <!-- 3. Các ngành / nghề đào tạo -->
          <div class="bg-white rounded-xl p-3.5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-2.5">
              <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                <i class="fa-solid fa-book-bookmark text-emerald-600"></i>
                <span>Các ngành / nghề đào tạo</span>
              </span>
              <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                {{ displayMajors.length }} chuyên ngành
              </span>
            </div>

            <div v-if="displayMajors.length > 0" class="space-y-2">
              <div 
                v-for="(major, idx) in displayMajors" 
                :key="idx"
                class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 flex flex-col gap-1">
                <div class="flex items-center justify-between">
                  <span class="font-bold text-slate-800 text-[12px]">{{ major.name }}</span>
                  <span 
                    :class="getDegreeBadgeColor(major.degree_level)"
                    class="text-[10px] font-bold px-2 py-0.5 rounded-full">
                    {{ formatDegreeLevel(major.degree_level) }}
                  </span>
                </div>
                <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-0.5">
                  <span v-if="major.major_code" class="font-mono text-slate-400">
                    Mã: <strong>{{ major.major_code }}</strong>
                  </span>
                  <span v-if="major.annual_quota">
                    Chỉ tiêu: <strong class="text-blue-700">{{ major.annual_quota }}</strong> SV/năm
                  </span>
                </div>
              </div>
            </div>
            <div v-else class="text-[11px] text-slate-400 italic text-center py-3 bg-slate-50 rounded-lg border border-dashed border-slate-200">
              <span v-if="school.education_level === 'gdtx'">Chương trình đào tạo GDTX & Phổ thông</span>
              <span v-else>Chưa có danh mục ngành đào tạo trong CSDL</span>
            </div>
          </div>

          <!-- 4. Cơ sở vật chất & Thư viện hình ảnh -->
          <div class="bg-white rounded-xl p-3.5 border border-slate-200 shadow-xs">
            <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5 mb-2.5">
              <i class="fa-solid fa-shapes text-amber-600"></i>
              <span>Cơ sở vật chất & Quy mô</span>
            </span>

            <!-- Metric Boxes -->
            <div class="grid grid-cols-3 gap-2 mb-3 text-center">
              <div class="p-2 bg-slate-50 rounded-lg border border-slate-100">
                <div class="text-slate-400 text-[10px] font-medium">Khuôn viên</div>
                <div class="text-sm font-black text-slate-800 font-mono mt-0.5">
                  {{ formatArea(school.campus_area_m2 || school.area_m2) }}
                </div>
              </div>
              <div class="p-2 bg-slate-50 rounded-lg border border-slate-100">
                <div class="text-slate-400 text-[10px] font-medium">Phòng học</div>
                <div class="text-sm font-black text-blue-700 font-mono mt-0.5">
                  {{ school.classroom_count || school.class_count || '—' }}
                </div>
              </div>
              <div class="p-2 bg-slate-50 rounded-lg border border-slate-100">
                <div class="text-slate-400 text-[10px] font-medium">Xưởng / Lab</div>
                <div class="text-sm font-black text-emerald-700 font-mono mt-0.5">
                  {{ school.workshops_count || school.lab_count || '0' }}
                </div>
              </div>
            </div>

            <!-- Image Gallery (Click to open Modal Slider) -->
            <div>
              <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium mb-1.5">
                <span>Hình ảnh khuôn viên & xưởng thực hành:</span>
                <span class="text-blue-600 cursor-pointer hover:underline" @click="openGallerySlider(0)">
                  Xem phóng to ({{ displayGallery.length }} ảnh)
                </span>
              </div>

              <div class="grid grid-cols-3 gap-2">
                <div 
                  v-for="(img, i) in displayGallery.slice(0, 3)" 
                  :key="i"
                  @click="openGallerySlider(i)"
                  class="relative aspect-video rounded-lg overflow-hidden border border-slate-200 cursor-pointer group shadow-xs">
                  <img 
                    :src="img" 
                    :alt="`Ảnh cơ sở ${i+1}`"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" 
                  />
                  <div class="absolute inset-0 bg-black/30 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                    <i class="fa-solid fa-magnifying-glass-plus text-white text-xs opacity-0 group-hover:opacity-100 transition-opacity"></i>
                  </div>
                  <span v-if="i === 2 && displayGallery.length > 3" class="absolute inset-0 bg-slate-900/70 text-white font-bold flex items-center justify-center text-xs">
                    +{{ displayGallery.length - 3 }} ảnh
                  </span>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- ============================================================== -->
        <!-- TAB 2: NHÂN LỰC & HỌC VIÊN                                    -->
        <!-- ============================================================== -->
        <div v-if="activeTab === 'personnel'" class="space-y-4 animate-in fade-in duration-200">
          
          <!-- 1. Số liệu sinh viên (tuyển sinh, tốt nghiệp, đang học) -->
          <div class="bg-white rounded-xl p-3.5 border border-slate-200 shadow-xs">
            <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5 mb-2.5">
              <i class="fa-solid fa-chart-pie text-blue-600"></i>
              <span>Số liệu Sinh viên / Học viên</span>
            </span>

            <div class="grid grid-cols-2 gap-2">
              <div class="p-2.5 bg-blue-50/60 rounded-xl border border-blue-100">
                <div class="text-[10px] text-blue-600 font-semibold">Tuyển sinh hàng năm</div>
                <div class="text-base font-black text-blue-800 font-mono mt-0.5">
                  {{ (school.annual_enrollment ?? 0).toLocaleString('vi-VN') }}
                </div>
                <div class="text-[10px] text-slate-400">chỉ tiêu/năm</div>
              </div>

              <div class="p-2.5 bg-indigo-50/60 rounded-xl border border-indigo-100">
                <div class="text-[10px] text-indigo-600 font-semibold">Quy mô đang học</div>
                <div class="text-base font-black text-indigo-800 font-mono mt-0.5">
                  {{ ((school.student_count || school.current_students) ?? 0).toLocaleString('vi-VN') }}
                </div>
                <div class="text-[10px] text-slate-400">học sinh / sinh viên</div>
              </div>

              <div class="p-2.5 bg-emerald-50/60 rounded-xl border border-emerald-100">
                <div class="text-[10px] text-emerald-600 font-semibold">Tốt nghiệp hàng năm</div>
                <div class="text-base font-black text-emerald-800 font-mono mt-0.5">
                  {{ (school.annual_graduates ?? 0).toLocaleString('vi-VN') }}
                </div>
                <div class="text-[10px] text-slate-400">ra trường/năm</div>
              </div>

              <div class="p-2.5 bg-purple-50/60 rounded-xl border border-purple-100">
                <div class="text-[10px] text-purple-600 font-semibold">Tỷ lệ có việc làm</div>
                <div class="text-base font-black text-purple-800 font-mono mt-0.5">
                  {{ school.employment_rate !== null && school.employment_rate !== undefined ? school.employment_rate : '—' }}%
                </div>
                <div class="text-[10px] text-slate-400">trong 6 tháng đầu</div>
              </div>
            </div>
          </div>

          <!-- 2. Số lượng giáo viên & Thiếu / Thừa -->
          <div class="bg-white rounded-xl p-3.5 border border-slate-200 shadow-xs">
            <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5 mb-2.5">
              <i class="fa-solid fa-chalkboard-user text-indigo-600"></i>
              <span>Giáo viên & Cân đối biên chế</span>
            </span>

            <div class="grid grid-cols-2 gap-2 mb-2">
              <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                <div class="text-[10px] text-slate-500 font-medium">Tổng số GV / Giảng viên</div>
                <div class="text-base font-black text-slate-800 font-mono mt-0.5">
                  {{ school.teacher_count ?? 0 }}
                </div>
                <div class="text-[10px] text-slate-400">người hiện có</div>
              </div>

              <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                <div class="text-[10px] text-slate-500 font-medium">Định biên giao</div>
                <div class="text-base font-black text-slate-800 font-mono mt-0.5">
                  {{ school.teacher_quota ?? 0 }}
                </div>
                <div class="text-[10px] text-slate-400">chỉ tiêu phân bổ</div>
              </div>
            </div>

            <!-- Status banner: Thiếu / Thừa -->
            <div 
              v-if="school.teachers_shortage > 0"
              class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 flex items-center justify-between text-rose-800">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                <span class="font-semibold text-[11px]">Tình trạng: Còn THIẾU giáo viên</span>
              </div>
              <span class="font-bold text-xs bg-rose-200/80 px-2 py-0.5 rounded text-rose-900">
                -{{ school.teachers_shortage }} GV
              </span>
            </div>
            <div 
              v-else-if="school.teachers_surplus > 0"
              class="p-2.5 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-between text-amber-800">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-amber-600"></i>
                <span class="font-semibold text-[11px]">Tình trạng: Dôi dư giáo viên</span>
              </div>
              <span class="font-bold text-xs bg-amber-200/80 px-2 py-0.5 rounded text-amber-900">
                +{{ school.teachers_surplus }} GV
              </span>
            </div>
            <div 
              v-else-if="school.teacher_quota > 0"
              class="p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-800">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span class="font-semibold text-[11px]">Tình trạng: Cân đối đủ định biên</span>
              </div>
              <span class="font-bold text-xs bg-emerald-200/80 px-2 py-0.5 rounded text-emerald-900">
                Đủ chỉ tiêu
              </span>
            </div>
            <div 
              v-else
              class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between text-slate-500">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-slate-400"></i>
                <span class="font-semibold text-[11px]">Định biên: Đang cập nhật</span>
              </div>
              <span class="text-[11px] text-slate-400 italic">
                Chưa có dữ liệu
              </span>
            </div>
          </div>

          <!-- 3. Phân loại giảng viên theo Ngạch / Hạng -->
          <div class="bg-white rounded-xl p-3.5 border border-slate-200 shadow-xs">
            <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5 mb-2.5">
              <i class="fa-solid fa-ranking-star text-purple-600"></i>
              <span>Phân loại Giảng viên theo Ngạch</span>
            </span>

            <div class="space-y-2">
              <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100">
                <div class="flex items-center gap-2">
                  <span class="w-6 h-6 rounded bg-purple-100 text-purple-800 font-bold flex items-center justify-center text-[10px]">I</span>
                  <div>
                    <div class="font-bold text-slate-800 text-[11px]">Loại 1 (Giảng viên cao cấp)</div>
                    <div class="text-[10px] text-slate-400">Ngạch viên chức Hạng I</div>
                  </div>
                </div>
                <div class="font-black text-purple-700 text-sm font-mono">
                  {{ school.faculty_rank_1 ?? 0 }}
                </div>
              </div>

              <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100">
                <div class="flex items-center gap-2">
                  <span class="w-6 h-6 rounded bg-blue-100 text-blue-800 font-bold flex items-center justify-center text-[10px]">II</span>
                  <div>
                    <div class="font-bold text-slate-800 text-[11px]">Loại 2 (Giảng viên chính)</div>
                    <div class="text-[10px] text-slate-400">Ngạch viên chức Hạng II</div>
                  </div>
                </div>
                <div class="font-black text-blue-700 text-sm font-mono">
                  {{ school.faculty_rank_2 ?? 0 }}
                </div>
              </div>

              <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100">
                <div class="flex items-center gap-2">
                  <span class="w-6 h-6 rounded bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-[10px]">III</span>
                  <div>
                    <div class="font-bold text-slate-800 text-[11px]">Giảng viên Hạng III</div>
                    <div class="text-[10px] text-slate-400">Giảng viên tiêu chuẩn</div>
                  </div>
                </div>
                <div class="font-black text-slate-700 text-sm font-mono">
                  {{ school.faculty_rank_3 ?? 0 }}
                </div>
              </div>
            </div>
          </div>

          <!-- 4. Học hàm / Học vị -->
          <div class="bg-white rounded-xl p-3.5 border border-slate-200 shadow-xs">
            <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5 mb-2.5">
              <i class="fa-solid fa-award text-amber-600"></i>
              <span>Học hàm / Học vị sau Đại học</span>
            </span>

            <div class="grid grid-cols-3 gap-2 text-center">
              <div class="p-2.5 bg-amber-50/60 rounded-xl border border-amber-100">
                <div class="text-[10px] text-amber-700 font-semibold">Tiến sĩ (TS)</div>
                <div class="text-base font-black text-amber-900 font-mono mt-0.5">
                  {{ school.faculty_doctors ?? 0 }}
                </div>
                <div class="text-[10px] text-amber-600">cán bộ</div>
              </div>

              <div class="p-2.5 bg-blue-50/60 rounded-xl border border-blue-100">
                <div class="text-[10px] text-blue-700 font-semibold">Thạc sĩ (ThS)</div>
                <div class="text-base font-black text-blue-900 font-mono mt-0.5">
                  {{ school.faculty_masters ?? 0 }}
                </div>
                <div class="text-[10px] text-blue-600">cán bộ</div>
              </div>

              <div class="p-2.5 bg-rose-50/60 rounded-xl border border-rose-100">
                <div class="text-[10px] text-rose-700 font-semibold">GS / PGS</div>
                <div class="text-base font-black text-rose-900 font-mono mt-0.5">
                  {{ school.faculty_professors ?? 0 }}
                </div>
                <div class="text-[10px] text-rose-600">chuyên gia</div>
              </div>
            </div>
          </div>

        </div>

        <!-- ============================================================== -->
        <!-- TAB 3: DOANH NGHIỆP                                           -->
        <!-- ============================================================== -->
        <div v-if="activeTab === 'enterprises'" class="space-y-4 animate-in fade-in duration-200">
          
          <!-- Slider 4-5 Doanh nghiệp tiêu biểu -->
          <div class="bg-white rounded-xl p-3.5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-3">
              <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                <i class="fa-solid fa-briefcase text-blue-600"></i>
                <span>Doanh nghiệp liên kết tiêu biểu</span>
              </span>
              <div class="flex items-center gap-1">
                <button 
                  @click="prevEnterpriseSlide" 
                  class="w-6 h-6 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center cursor-pointer transition-colors"
                  title="Trước">
                  <i class="fa-solid fa-chevron-left text-[10px]"></i>
                </button>
                <span class="text-[10px] font-mono text-slate-400 px-1">
                  {{ currentEnterpriseIndex + 1 }}/{{ featuredEnterprises.length || 1 }}
                </span>
                <button 
                  @click="nextEnterpriseSlide" 
                  class="w-6 h-6 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center cursor-pointer transition-colors"
                  title="Tiếp theo">
                  <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </button>
              </div>
            </div>

            <!-- Single Slide Display -->
            <div v-if="featuredEnterprises.length > 0" class="overflow-hidden">
              <div class="p-3 bg-gradient-to-br from-slate-50 to-blue-50/40 rounded-xl border border-blue-100 flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 p-1 flex items-center justify-center flex-shrink-0 shadow-xs overflow-hidden">
                  <img 
                    v-if="featuredEnterprises[currentEnterpriseIndex]?.logo"
                    :src="featuredEnterprises[currentEnterpriseIndex].logo"
                    alt="Logo doanh nghiệp"
                    class="w-full h-full object-contain" 
                  />
                  <i v-else class="fa-solid fa-building text-blue-600 text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                  <h4 class="font-bold text-slate-800 text-[13px] leading-snug">
                    {{ featuredEnterprises[currentEnterpriseIndex]?.name }}
                  </h4>
                </div>
              </div>
            </div>
            <div v-else class="text-[11px] text-slate-400 italic text-center py-4">
              Chưa có dữ liệu doanh nghiệp liên kết
            </div>

            <!-- Dots indicator -->
            <div v-if="featuredEnterprises.length > 1" class="flex items-center justify-center gap-1.5 mt-2.5">
              <span 
                v-for="(_, i) in featuredEnterprises" 
                :key="i"
                @click="currentEnterpriseIndex = i"
                :class="i === currentEnterpriseIndex ? 'w-4 bg-blue-600' : 'w-1.5 bg-slate-300 hover:bg-slate-400'"
                class="h-1.5 rounded-full transition-all cursor-pointer">
              </span>
            </div>

            <!-- Button: Open full modal -->
            <button 
              v-if="allEnterprises.length > 0"
              @click="isEnterprisesModalOpen = true"
              class="w-full mt-3 py-2 px-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs flex items-center justify-center gap-2 transition-colors cursor-pointer shadow-xs">
              <i class="fa-solid fa-list-check"></i>
              <span>Xem toàn bộ danh sách doanh nghiệp ({{ allEnterprises.length }})</span>
            </button>
          </div>

        </div>

      </div>

      <!-- Bottom Quick Action Bar -->
      <div class="p-3 border-t border-slate-200 bg-white flex items-center gap-2 flex-shrink-0">
        <a 
          :href="`https://www.google.com/maps/dir/?api=1&destination=${school.lat},${school.lng}`" 
          target="_blank"
          class="flex-1 py-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 font-semibold text-xs flex items-center justify-center gap-1.5 transition-colors cursor-pointer text-decoration-none">
          <i class="fa-solid fa-diamond-turn-right text-xs"></i>
          <span>Chỉ đường</span>
        </a>

        <button 
          @click="$emit('buffer-analyze', school)"
          class="flex-1 py-2 rounded-lg bg-purple-50 text-purple-700 hover:bg-purple-100 font-semibold text-xs flex items-center justify-center gap-1.5 transition-colors cursor-pointer">
          <i class="fa-solid fa-bullseye text-xs"></i>
          <span>Bán kính 3km</span>
        </button>

        <button 
          @click="$emit('close')"
          class="px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition-colors cursor-pointer">
          Đóng
        </button>
      </div>

    </div>

    <!-- ============================================================== -->
    <!-- MODAL SLIDER: PHOTO LIGHTBOX CAROUSEL                         -->
    <!-- ============================================================== -->
    <div 
      v-if="isGalleryModalOpen" 
      class="fixed inset-0 z-[2500] bg-black/90 backdrop-blur-sm flex flex-col items-center justify-center p-4 animate-in fade-in select-none">
      
      <!-- Top controls -->
      <div class="absolute top-4 left-4 right-4 flex items-center justify-between text-white z-10">
        <div class="flex items-center gap-2 text-xs">
          <span class="font-bold text-sm">{{ school?.name }}</span>
          <span class="text-white/40">|</span>
          <span class="bg-white/20 px-2.5 py-0.5 rounded-full text-xs font-mono">
            {{ currentGalleryIndex + 1 }} / {{ displayGallery.length }}
          </span>
        </div>
        <button 
          @click="isGalleryModalOpen = false"
          class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center cursor-pointer transition-colors text-lg">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <!-- Main Slider Image -->
      <div class="relative max-w-4xl max-h-[75vh] w-full flex items-center justify-center">
        <button 
          @click="prevGalleryImage"
          class="absolute left-2 sm:-left-12 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 text-white flex items-center justify-center cursor-pointer transition-all z-10">
          <i class="fa-solid fa-chevron-left text-base"></i>
        </button>

        <img 
          :src="displayGallery[currentGalleryIndex]" 
          :alt="`Ảnh cơ sở ${currentGalleryIndex + 1}`"
          class="max-w-full max-h-[75vh] object-contain rounded-xl shadow-2xl animate-in zoom-in-95 duration-200" 
        />

        <button 
          @click="nextGalleryImage"
          class="absolute right-2 sm:-right-12 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 text-white flex items-center justify-center cursor-pointer transition-all z-10">
          <i class="fa-solid fa-chevron-right text-base"></i>
        </button>
      </div>

      <!-- Thumbnail Strip at bottom -->
      <div class="flex items-center gap-2 mt-4 overflow-x-auto max-w-xl py-1 px-2">
        <div 
          v-for="(img, idx) in displayGallery" 
          :key="idx"
          @click="currentGalleryIndex = idx"
          :class="idx === currentGalleryIndex ? 'ring-2 ring-blue-500 scale-105' : 'opacity-50 hover:opacity-100'"
          class="w-14 h-10 rounded-lg overflow-hidden flex-shrink-0 cursor-pointer transition-all border border-white/20">
          <img :src="img" class="w-full h-full object-cover" />
        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL POPUP: ALL PARTNER ENTERPRISES                          -->
    <!-- ============================================================== -->
    <div 
      v-if="isEnterprisesModalOpen"
      class="fixed inset-0 z-[2500] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 animate-in fade-in select-none">
      
      <div class="bg-white rounded-2xl max-w-xl w-full max-h-[85vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden animate-in zoom-in-95 duration-150">
        
        <!-- Header -->
        <div class="p-4 bg-gradient-to-r from-blue-800 to-indigo-800 text-white flex items-center justify-between flex-shrink-0">
          <div class="flex items-center gap-2">
            <i class="fa-solid fa-handshake text-base text-blue-200"></i>
            <div>
              <h3 class="font-bold text-sm leading-tight">Danh sách Doanh nghiệp liên kết</h3>
              <p class="text-[11px] text-blue-100">{{ school?.name }}</p>
            </div>
          </div>
          <button 
            @click="isEnterprisesModalOpen = false"
            class="w-7 h-7 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center cursor-pointer transition-colors">
            <i class="fa-solid fa-xmark text-xs"></i>
          </button>
        </div>

        <!-- Search bar -->
        <div class="p-3 border-b border-slate-100 bg-slate-50 flex items-center gap-2">
          <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs ml-1"></i>
          <input 
            v-model="enterpriseSearchQuery"
            type="text" 
            placeholder="Tìm theo tên doanh nghiệp..."
            class="bg-transparent border-none text-xs w-full focus:outline-none text-slate-700 placeholder:text-slate-400"
          />
        </div>

        <!-- List container -->
        <div class="flex-1 overflow-y-auto p-4 space-y-2.5">
          <div 
            v-for="(ent, i) in filteredAllEnterprises" 
            :key="i"
            class="p-3 rounded-xl bg-slate-50 border border-slate-200 hover:border-blue-300 transition-colors flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-white border border-slate-200 p-1 flex items-center justify-center flex-shrink-0 overflow-hidden">
              <img 
                v-if="ent.logo" 
                :src="ent.logo" 
                alt="Logo" 
                class="w-full h-full object-contain" 
              />
              <i v-else class="fa-solid fa-building text-blue-600 text-base"></i>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="font-bold text-slate-800 text-xs truncate">{{ ent.name }}</h4>
            </div>
          </div>

          <div v-if="filteredAllEnterprises.length === 0" class="text-center py-6 text-slate-400 text-xs italic">
            Không tìm thấy doanh nghiệp phù hợp với từ khóa
          </div>
        </div>

        <!-- Footer -->
        <div class="p-3 border-t border-slate-200 bg-slate-50 flex justify-end">
          <button 
            @click="isEnterprisesModalOpen = false"
            class="px-4 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold transition-colors cursor-pointer">
            Đóng
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { LEVEL_MAP } from '../../services/schoolService'

const props = defineProps({
  school: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'buffer-analyze'])

// Dynamic level icon matching map pin
const levelIcon = computed(() => {
  return LEVEL_MAP[props.school?.education_level]?.icon || 'fa-solid fa-graduation-cap'
})

// Active tab in drawer
const activeTab = ref('general') // 'general' | 'personnel' | 'enterprises'

// Enterprise Slider & Modal
const currentEnterpriseIndex = ref(0)
const isEnterprisesModalOpen = ref(false)
const enterpriseSearchQuery = ref('')

// Image Gallery Modal Lightbox
const isGalleryModalOpen = ref(false)
const currentGalleryIndex = ref(0)

// Helper: level label
const levelLabel = computed(() => {
  return props.school?.education_level_name || props.school?.education_level || 'Cơ sở giáo dục'
})

// Safe parser for JSON or array fields from Database
function parseArrayField(val) {
  if (!val) return []
  if (Array.isArray(val)) return val
  if (typeof val === 'string') {
    try {
      const parsed = JSON.parse(val)
      return Array.isArray(parsed) ? parsed : []
    } catch (e) {
      return []
    }
  }
  return []
}

// Leaders list directly from DB
const displayLeaders = computed(() => {
  const leaders = parseArrayField(props.school?.leaders)
  if (leaders.length > 0) {
    return leaders
  }
  if (props.school?.principal) {
    return [
      { name: props.school.principal, position: 'Hiệu trưởng' }
    ]
  }
  return []
})

// Majors list directly from DB
const displayMajors = computed(() => {
  return parseArrayField(props.school?.training_majors)
})

function formatDegreeLevel(lvl) {
  if (lvl === 'cao_dang') return 'Cao đẳng'
  if (lvl === 'trung_cap') return 'Trung cấp'
  if (lvl === 'so_cap') return 'Sơ cấp'
  if (lvl === 'dai_hoc') return 'Đại học'
  return 'Chính quy'
}

function getDegreeBadgeColor(lvl) {
  if (lvl === 'cao_dang') return 'bg-blue-100 text-blue-800'
  if (lvl === 'trung_cap') return 'bg-amber-100 text-amber-800'
  if (lvl === 'so_cap') return 'bg-teal-100 text-teal-800'
  return 'bg-purple-100 text-purple-800'
}

function formatArea(area) {
  if (!area) return '—'
  if (area >= 10000) {
    return `${(area / 10000).toFixed(1)} ha`
  }
  return `${area.toLocaleString('vi-VN')} m²`
}

// Gallery list directly from DB
const displayGallery = computed(() => {
  const gallery = parseArrayField(props.school?.gallery)
  if (gallery.length > 0) {
    return gallery.map(img => {
      if (typeof img === 'string' && (img.startsWith('http') || img.startsWith('/'))) return img
      const storageBase = window.location.origin.includes('localhost:517') ? 'http://localhost:8000' : ''
      return `${storageBase}/storage/${img}`
    })
  }
  if (props.school?.image_url) {
    return [props.school.image_url]
  }
  return [
    'https://images.unsplash.com/photo-1562774053-701939374585?w=800&auto=format&fit=crop&q=80',
    'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=800&auto=format&fit=crop&q=80'
  ]
})

function openGallerySlider(index = 0) {
  currentGalleryIndex.value = index
  isGalleryModalOpen.value = true
}

function nextGalleryImage() {
  currentGalleryIndex.value = (currentGalleryIndex.value + 1) % displayGallery.value.length
}

function prevGalleryImage() {
  currentGalleryIndex.value = (currentGalleryIndex.value - 1 + displayGallery.value.length) % displayGallery.value.length
}

// Enterprises directly from DB
const allEnterprises = computed(() => {
  return parseArrayField(props.school?.partner_enterprises)
})

const featuredEnterprises = computed(() => {
  const featured = allEnterprises.value.filter(e => e.is_featured)
  return featured.length > 0 ? featured : allEnterprises.value.slice(0, 5)
})

const filteredAllEnterprises = computed(() => {
  if (!enterpriseSearchQuery.value) return allEnterprises.value
  const q = enterpriseSearchQuery.value.toLowerCase().trim()
  return allEnterprises.value.filter(e => 
    e.name && e.name.toLowerCase().includes(q)
  )
})

function nextEnterpriseSlide() {
  if (featuredEnterprises.value.length === 0) return
  currentEnterpriseIndex.value = (currentEnterpriseIndex.value + 1) % featuredEnterprises.value.length
}

function prevEnterpriseSlide() {
  if (featuredEnterprises.value.length === 0) return
  currentEnterpriseIndex.value = (currentEnterpriseIndex.value - 1 + featuredEnterprises.value.length) % featuredEnterprises.value.length
}

// Keyboard navigation (Esc to close modals)
function handleKeydown(e) {
  if (e.key === 'Escape') {
    if (isGalleryModalOpen.value) {
      isGalleryModalOpen.value = false
    } else if (isEnterprisesModalOpen.value) {
      isEnterprisesModalOpen.value = false
    } else if (props.school) {
      emit('close')
    }
  } else if (e.key === 'ArrowRight' && isGalleryModalOpen.value) {
    nextGalleryImage()
  } else if (e.key === 'ArrowLeft' && isGalleryModalOpen.value) {
    prevGalleryImage()
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown)
})
</script>
