<template>
  <div class="w-full h-full overflow-y-auto bg-[#f8f9fa] flex flex-col font-sans select-text">
    
    <!-- 1. Breadcrumbs (Vieclam24h Exact Style) -->
    <div class="bg-white border-b border-slate-200 py-2.5 sm:py-3 px-3 sm:px-6 flex-shrink-0">
      <div class="max-w-6xl mx-auto flex items-center justify-between flex-wrap gap-2 text-xs">
        
        <nav class="flex items-center gap-1.5 text-slate-500 flex-wrap">
          <router-link to="/map" class="hover:text-blue-600 transition-colors hidden sm:inline">
            Trang Chủ
          </router-link>
          <span class="text-slate-300 hidden sm:inline">/</span>
          <router-link to="/tuyen-dung" class="hover:text-blue-600 transition-colors">
            Việc Làm
          </router-link>
          <span class="text-slate-300">/</span>
          <router-link 
            :to="{ path: '/tuyen-dung', query: { industry: job?.industry } }" 
            class="hover:text-blue-600 transition-colors text-slate-600 max-w-[120px] sm:max-w-none truncate">
            {{ job?.industry_name || 'Ngành nghề' }}
          </router-link>
          <span class="text-slate-300">/</span>
          <span class="text-slate-800 font-semibold truncate max-w-[140px] sm:max-w-md">
            {{ job?.title || 'Chi tiết' }}
          </span>
        </nav>

        <router-link 
          to="/tuyen-dung"
          class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition-colors text-[11px] sm:text-xs">
          <i class="fa-solid fa-arrow-left text-[10px]"></i>
          <span>Quay lại</span>
        </router-link>

      </div>
    </div>

    <!-- 2. Main Content Area -->
    <main class="max-w-6xl w-full mx-auto px-3 sm:px-6 py-4 sm:py-6 flex-1">
      
      <!-- Not Found State -->
      <div v-if="!job" class="bg-white rounded-2xl border border-slate-200 p-8 sm:p-12 text-center space-y-4 shadow-2xs">
        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center text-xl sm:text-2xl mx-auto">
          <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h2 class="text-base sm:text-lg font-bold text-slate-800">Không tìm thấy thông tin việc làm</h2>
        <p class="text-xs text-slate-500 max-w-md mx-auto">
          Tin tuyển dụng này có thể đã hết hạn hoặc không còn tồn tại trên hệ thống.
        </p>
        <router-link 
          to="/tuyen-dung" 
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#6c3fb8] hover:bg-[#5b32a0] text-white font-bold text-xs transition-colors shadow-xs">
          <i class="fa-solid fa-briefcase"></i>
          <span>Khám phá các việc làm khác tại Ninh Bình</span>
        </router-link>
      </div>

      <!-- Detail 2 Columns Layout (Vieclam24h Exact 8/4 grid) -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-5 items-start">
        
        <!-- ============================================================== -->
        <!-- LEFT 8 COLS: Job Header Card + Job Detailed Specs              -->
        <!-- ============================================================== -->
        <div class="lg:col-span-8 space-y-4">
          
          <!-- Top Hero Card (Vieclam24h Exact Style) -->
          <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-2xs space-y-3.5 sm:space-y-4">
            
            <!-- Job Title -->
            <h1 class="text-lg sm:text-xl md:text-2xl font-black text-slate-900 leading-snug tracking-tight">
              {{ job.title }}
            </h1>

            <!-- 3 Quick Stats Columns (Mức lương, Khu vực tuyển, Kinh nghiệm) - Vieclam24h layout with min-w-0 to prevent overflow -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 py-3 sm:py-3.5 border-y border-slate-100">
              
              <!-- Stat 1: Mức lương -->
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-full bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center text-sm flex-shrink-0">
                  <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div class="min-w-0 flex-1">
                  <div class="text-[11px] text-slate-400 font-medium leading-none mb-1">Mức lương</div>
                  <div class="text-sm sm:text-base font-bold text-blue-600 truncate" :title="job.salary">
                    {{ formatSalary(job.salary) }}
                  </div>
                </div>
              </div>

              <!-- Stat 2: Khu vực tuyển -->
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-full bg-rose-50 border border-rose-100 text-rose-500 flex items-center justify-center text-sm flex-shrink-0">
                  <i class="fa-solid fa-location-dot"></i>
                </div>
                <div class="min-w-0 flex-1">
                  <div class="text-[11px] text-slate-400 font-medium leading-none mb-1">Khu vực tuyển</div>
                  <div class="text-xs sm:text-sm font-bold text-slate-800 truncate" :title="job.ward_name ? (job.ward_name + ', Ninh Bình') : job.address">
                    {{ job.ward_name ? (job.ward_name + ', Ninh Bình') : 'Ninh Bình' }}
                  </div>
                </div>
              </div>

              <!-- Stat 3: Kinh nghiệm -->
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-full bg-amber-50 border border-amber-100 text-amber-500 flex items-center justify-center text-sm flex-shrink-0">
                  <i class="fa-solid fa-briefcase"></i>
                </div>
                <div class="min-w-0 flex-1">
                  <div class="text-[11px] text-slate-400 font-medium leading-none mb-1">Kinh nghiệm</div>
                  <div class="text-xs sm:text-sm font-bold text-slate-800 truncate" :title="job.experience">
                    {{ formatExperience(job.experience) }}
                  </div>
                </div>
              </div>

            </div>

            <!-- Deadline Alert (Vieclam24h exact style: single clean line) -->
            <div class="text-xs text-slate-500 flex items-center gap-1.5 flex-wrap">
              <span>Hạn nộp hồ sơ:</span>
              <strong class="text-slate-800">{{ job.deadline }}</strong>
              <span class="text-slate-300 mx-1">-</span>
              <span @click="openApplyModal" class="text-[#853AFF] font-medium hover:underline cursor-pointer">
                Ứng tuyển sớm để được ưu tiên
              </span>
            </div>

            <!-- Action Buttons Row (Vieclam24h Exact 2-Button Row - stack on mobile) -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 sm:gap-3 pt-1">
              
              <!-- Primary CTA: Ứng tuyển ngay (Vieclam24h purple gradient, wide button) -->
              <button 
                @click="openApplyModal"
                class="w-full sm:flex-1 py-3 px-6 rounded-xl bg-gradient-to-r from-[#853AFF] to-[#6C5FFF] hover:opacity-95 active:scale-[0.99] text-white font-bold text-sm sm:text-base shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Ứng tuyển ngay</span>
              </button>

              <!-- Secondary: Lưu công việc này (Vieclam24h white with purple border) -->
              <button 
                @click="toggleSaveJob"
                :class="isSaved ? 'bg-purple-50 text-[#853AFF] border-[#853AFF]' : 'bg-white text-[#853AFF] border-purple-200 hover:bg-purple-50 hover:border-[#853AFF]'"
                class="w-full sm:w-auto px-5 py-3 rounded-xl border font-bold text-xs sm:text-sm transition-all flex items-center justify-center gap-2 cursor-pointer shadow-2xs active:scale-[0.99] min-w-[150px] sm:min-w-[180px]">
                <i :class="isSaved ? 'fa-solid fa-heart text-rose-500' : 'fa-regular fa-heart'"></i>
                <span>{{ isSaved ? 'Đã lưu công việc' : 'Lưu công việc này' }}</span>
              </button>

            </div>

          </div>

          <!-- Main Specifications Card (Vieclam24h Exact Sections) -->
          <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-7 shadow-2xs space-y-6">
            
            <!-- Section 1: Mô tả công việc -->
            <section class="space-y-3">
              <h2 class="text-base font-bold text-slate-900">
                Mô tả công việc
              </h2>
              <ul class="space-y-2 text-xs sm:text-sm text-slate-700 leading-relaxed pl-4 list-disc marker:text-slate-400">
                <li v-for="(desc, idx) in job.description" :key="idx">
                  {{ desc }}
                </li>
              </ul>
            </section>

            <!-- Section 2: Yêu cầu công việc -->
            <section class="space-y-3 pt-2">
              <h2 class="text-base font-bold text-slate-900">
                Yêu cầu công việc
              </h2>
              <ul class="space-y-2 text-xs sm:text-sm text-slate-700 leading-relaxed pl-4 list-disc marker:text-slate-400">
                <li v-for="(req, idx) in job.requirements" :key="idx">
                  {{ req }}
                </li>
              </ul>
            </section>

            <!-- Section 3: Quyền lợi -->
            <section class="space-y-3 pt-2">
              <h2 class="text-base font-bold text-slate-900">
                Quyền lợi
              </h2>
              <ul class="space-y-2 text-xs sm:text-sm text-slate-700 leading-relaxed pl-4 list-disc marker:text-slate-400">
                <li v-for="(b, idx) in job.benefits" :key="idx">
                  {{ b }}
                </li>
              </ul>
            </section>

            <!-- Section 4: Thông tin chung (Vieclam24h Exact Table Matrix) -->
            <section class="space-y-3 pt-2">
              <h2 class="text-base font-bold text-slate-900">
                Thông tin chung
              </h2>
              
              <div class="bg-[#faf8ff] rounded-xl border border-purple-100 p-4 grid grid-cols-1 sm:grid-cols-2 gap-y-3.5 gap-x-6 text-xs">
                
                <div>
                  <span class="text-slate-400 block mb-0.5">Ngày đăng</span>
                  <span class="font-bold text-slate-800">22/09/2026</span>
                </div>

                <div>
                  <span class="text-slate-400 block mb-0.5">Cấp bậc</span>
                  <span class="font-bold text-slate-800">Nhân viên / Kỹ thuật viên</span>
                </div>

                <div>
                  <span class="text-slate-400 block mb-0.5">Số lượng tuyển</span>
                  <span class="font-bold text-slate-800">3 - 5 người</span>
                </div>

                <div>
                  <span class="text-slate-400 block mb-0.5">Hình thức làm việc</span>
                  <span class="font-bold text-slate-800">Toàn thời gian cố định</span>
                </div>

                <div>
                  <span class="text-slate-400 block mb-0.5">Yêu cầu kinh nghiệm</span>
                  <span class="font-bold text-slate-800">{{ job.experience || 'Không yêu cầu' }}</span>
                </div>

                <div>
                  <span class="text-slate-400 block mb-0.5">Trình độ đào tạo GDNN</span>
                  <span class="font-bold text-slate-800">{{ job.education || 'Trung cấp / Cao đẳng' }}</span>
                </div>

                <div class="sm:col-span-2 pt-1 border-t border-purple-100/60">
                  <span class="text-slate-400 block mb-1">Ngành nghề tuyển dụng</span>
                  <div class="flex flex-wrap gap-1.5">
                    <router-link 
                      :to="{ path: '/tuyen-dung', query: { industry: job.industry } }"
                      class="text-[#6c3fb8] hover:underline font-semibold">
                      {{ job.industry_name }}
                    </router-link>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-600">Doanh nghiệp tỉnh Ninh Bình</span>
                  </div>
                </div>

              </div>
            </section>

            <!-- Section 5: Địa điểm làm việc (Vieclam24h style) -->
            <section class="space-y-2.5 pt-2">
              <h2 class="text-base font-bold text-slate-900">
                Địa điểm làm việc
              </h2>
              <div class="flex items-start gap-2 text-xs sm:text-sm text-slate-700">
                <i class="fa-solid fa-location-dot text-rose-500 mt-1 flex-shrink-0"></i>
                <div>
                  <span class="text-blue-600 font-bold mr-1">{{ job.ward_name }}:</span>
                  <span>{{ job.address }}</span>
                </div>
              </div>
            </section>

            <!-- Section 6: Thông tin liên hệ trực tiếp nhà tuyển dụng -->
            <section id="contact-section" class="space-y-3 pt-2">
              <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-address-card text-[#6c3fb8]"></i>
                <span>Thông tin liên hệ nộp hồ sơ</span>
              </h2>

              <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2 text-xs sm:text-sm text-slate-700">
                <p><strong>Người liên hệ:</strong> {{ job.contact?.contact_person }}</p>
                <p><strong>Địa chỉ nộp hồ sơ:</strong> {{ job.contact?.address }}</p>
                <p v-if="job.contact?.email">
                  <strong>Email:</strong> 
                  <a :href="`mailto:${job.contact?.email}`" class="text-[#6c3fb8] font-semibold underline ml-1">
                    {{ job.contact?.email }}
                  </a>
                </p>
                <p v-if="job.contact?.phone">
                  <strong>Điện thoại:</strong> 
                  <a :href="`tel:${job.contact?.phone}`" class="text-blue-600 font-bold ml-1 hover:underline">
                    {{ job.contact?.phone }}
                  </a>
                </p>
              </div>

              <!-- Quick contact buttons (responsive stacked on mobile) -->
              <div class="flex flex-col sm:flex-row gap-2.5 sm:gap-3 pt-1">
                <a 
                  v-if="job.contact?.phone"
                  :href="`tel:${job.contact?.phone}`"
                  class="w-full sm:flex-1 py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-xs transition-colors">
                  <i class="fa-solid fa-phone-volume"></i>
                  <span>Gọi phỏng vấn: {{ job.contact?.phone }}</span>
                </a>

                <a 
                  v-if="job.contact?.zalo"
                  :href="`https://zalo.me/${job.contact?.zalo}`"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-full sm:flex-1 py-2.5 px-4 rounded-xl bg-[#0068ff] hover:bg-blue-700 text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-xs transition-colors">
                  <i class="fa-solid fa-comments"></i>
                  <span>Nhắn Zalo: {{ job.contact?.zalo }}</span>
                </a>
              </div>
            </section>

            <!-- Section 7: Từ khóa liên quan (Vieclam24h Tags) -->
            <section class="space-y-2.5 pt-2">
              <h3 class="text-xs font-bold text-slate-700">
                Từ khóa
              </h3>
              <div class="flex flex-wrap gap-1.5">
                <router-link 
                  v-for="(kw, idx) in generatedKeywords" 
                  :key="idx"
                  :to="{ path: '/tuyen-dung', query: { search: kw } }"
                  class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-purple-50 text-slate-600 hover:text-[#6c3fb8] text-xs transition-colors border border-slate-200">
                  {{ kw }}
                </router-link>
              </div>
            </section>

            <!-- Footer: Chia sẻ & Báo xấu (Vieclam24h Exact format) -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
              <div class="flex items-center gap-2">
                <span>Chia sẻ:</span>
                <button 
                  @click="copyShareLink"
                  class="w-7 h-7 rounded-full bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 flex items-center justify-center transition-colors cursor-pointer"
                  title="Sao chép liên kết">
                  <i class="fa-solid fa-link text-xs"></i>
                </button>
                <button 
                  @click="shareFacebook"
                  class="w-7 h-7 rounded-full bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 flex items-center justify-center transition-colors cursor-pointer"
                  title="Chia sẻ lên Facebook">
                  <i class="fa-brands fa-facebook-f text-xs"></i>
                </button>
                <span v-if="copiedLink" class="text-emerald-600 text-[11px] font-semibold animate-fade-in">
                  Đã sao chép!
                </span>
              </div>

              <button 
                @click="reportJob"
                class="hover:text-rose-600 transition-colors flex items-center gap-1 cursor-pointer">
                <i class="fa-regular fa-flag text-[11px]"></i>
                <span>Báo xấu</span>
              </button>
            </div>

          </div>

        </div>

        <!-- ============================================================== -->
        <!-- RIGHT 4 COLS: Company Card + Related Jobs (Vieclam24h Sidebar) -->
        <!-- ============================================================== -->
        <aside class="lg:col-span-4 space-y-4">
          
          <!-- Card 1: Thông tin công ty (Vieclam24h Exact Style) -->
          <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-2xs text-center space-y-3">
            
            <!-- Company Logo Center -->
            <div class="flex justify-center">
              <div 
                :class="job.company_color || 'bg-[#3b1d74]'"
                class="w-16 h-16 rounded-2xl text-white font-black text-sm flex items-center justify-center shadow-sm border border-slate-100 tracking-wider">
                {{ job.company_logo || 'DN' }}
              </div>
            </div>

            <!-- Company Name -->
            <div>
              <h3 class="font-bold text-sm sm:text-base text-slate-900 leading-snug">
                {{ job.company }}
              </h3>
            </div>

            <!-- Info items -->
            <div class="text-left text-xs text-slate-600 space-y-2 pt-2 border-t border-slate-100">
              <p class="flex items-start gap-2">
                <i class="fa-solid fa-location-dot text-slate-400 text-xs mt-0.5 flex-shrink-0"></i>
                <span class="leading-relaxed">{{ job.address }}</span>
              </p>
              <p class="flex items-center gap-2">
                <i class="fa-solid fa-users text-slate-400 text-xs flex-shrink-0"></i>
                <span>Quy mô: <strong>100 - 500 nhân viên</strong></span>
              </p>
              <p class="flex items-center gap-2">
                <i class="fa-solid fa-industry text-slate-400 text-xs flex-shrink-0"></i>
                <span>Lĩnh vực: <strong>{{ job.industry_name }}</strong></span>
              </p>
            </div>

            <!-- View company page link -->
            <div class="pt-1">
              <router-link 
                to="/tuyen-dung" 
                class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1">
                <span>Xem trang công ty</span>
                <i class="fa-solid fa-angle-right text-[10px]"></i>
              </router-link>
            </div>

          </div>

          <!-- Card 2: Việc làm tương tự cho bạn (Vieclam24h Exact Style) -->
          <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-2xs space-y-3.5">
            
            <div class="flex items-center justify-between">
              <h3 class="font-bold text-sm text-slate-900">
                Việc làm tương tự cho bạn
              </h3>
              <router-link to="/tuyen-dung" class="text-[11px] text-[#6c3fb8] hover:underline font-semibold">
                Xem thêm →
              </router-link>
            </div>

            <!-- Job List items -->
            <div class="divide-y divide-slate-100">
              
              <div 
                v-for="rel in relatedJobs" 
                :key="rel.id"
                class="py-3 first:pt-0 last:pb-0 group relative">
                
                <div class="flex items-start gap-2.5">
                  <!-- Mini Logo -->
                  <div 
                    :class="rel.company_color || 'bg-slate-700'"
                    class="w-9 h-9 rounded-lg text-white font-bold text-[10px] flex items-center justify-center flex-shrink-0 shadow-2xs border border-slate-100">
                    {{ rel.company_logo || 'DN' }}
                  </div>

                  <!-- Job info -->
                  <div class="flex-1 min-w-0 pr-6">
                    <router-link 
                      :to="`/tuyen-dung/${rel.id}`" 
                      class="block font-bold text-xs text-slate-900 group-hover:text-[#6c3fb8] transition-colors line-clamp-1 leading-snug"
                      :title="rel.title">
                      {{ rel.title }}
                    </router-link>

                    <p class="text-[11px] text-slate-500 truncate mt-0.5" :title="rel.company">
                      {{ rel.company }}
                    </p>

                    <!-- Salary & Location -->
                    <div class="mt-1.5 flex items-center justify-between text-[11px]">
                      <span class="font-bold text-blue-600">{{ rel.salary }}</span>
                      <span class="text-slate-400 flex items-center gap-1">
                        <i class="fa-solid fa-location-dot text-[10px]"></i>
                        <span>{{ rel.ward_name }}</span>
                      </span>
                    </div>

                    <!-- Footer: deadline badge & tag (Vieclam24h exact style) -->
                    <div class="mt-1.5 flex items-center justify-between text-[10px] text-slate-400">
                      <span class="px-1.5 py-0.5 rounded border border-blue-200 text-blue-600 font-medium">
                        Không cần CV
                      </span>
                      <span>Còn {{ getDaysLeft(rel.deadline) }} ngày</span>
                    </div>

                  </div>

                  <!-- Heart Save Icon (Vieclam24h exact top-right icon) -->
                  <button 
                    @click.stop="toggleSaveRelatedJob(rel.id)"
                    class="absolute top-3 right-0 p-1 text-slate-300 hover:text-rose-500 transition-colors"
                    :title="isJobSaved(rel.id) ? 'Bỏ lưu' : 'Lưu việc làm'">
                    <i :class="isJobSaved(rel.id) ? 'fa-solid fa-heart text-rose-500' : 'fa-regular fa-heart'"></i>
                  </button>

                </div>

              </div>

            </div>

          </div>

          <!-- Card 3: Hotline hỗ trợ GDNN Ninh Bình -->
          <div class="bg-gradient-to-br from-[#3b1d74] to-[#251052] rounded-2xl p-4 text-white shadow-xs space-y-2.5">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-amber-300 text-sm">
                <i class="fa-solid fa-headset"></i>
              </div>
              <div>
                <h4 class="font-bold text-xs leading-tight">Cần tư vấn việc làm GDNN?</h4>
                <p class="text-[10px] text-purple-200">Sở LĐ-TB&XH Tỉnh Ninh Bình</p>
              </div>
            </div>
            <a 
              href="tel:02293868686" 
              class="block w-full text-center py-2 px-3 rounded-xl bg-amber-400 hover:bg-amber-300 text-[#251052] font-bold text-xs transition-colors shadow-2xs">
              <i class="fa-solid fa-phone mr-1.5"></i>
              <span>Hotline: 0229.386.8686</span>
            </a>
          </div>

        </aside>

      </div>

      <!-- ============================================================== -->
      <!-- 3. BOTTOM HOTLINE MATRIX (Vieclam24h Exact 2-Column Boxes)      -->
      <!-- ============================================================== -->
      <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
        
        <!-- Left: Hotline cho Người tìm việc -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-2xs text-center space-y-3">
          <h4 class="font-bold text-sm text-slate-800">
            Hotline cho Người tìm việc & Học viên GDNN
          </h4>
          <div class="flex items-center justify-center gap-2 text-xs text-slate-600">
            <i class="fa-solid fa-phone text-blue-600"></i>
            <span>Trung tâm Dịch vụ Việc làm:</span>
            <strong class="text-blue-700 text-sm">0229.386.8686</strong>
          </div>
          <button 
            @click="openApplyModal"
            class="px-5 py-2 rounded-xl border border-blue-600 text-blue-700 hover:bg-blue-50 font-bold text-xs transition-colors cursor-pointer">
            Tư vấn cho Người tìm việc
          </button>
        </div>

        <!-- Right: Hotline cho Nhà tuyển dụng -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-2xs text-center space-y-3">
          <h4 class="font-bold text-sm text-slate-800">
            Hotline cho Doanh nghiệp & Nhà tuyển dụng
          </h4>
          <div class="flex items-center justify-center gap-2 text-xs text-slate-600">
            <i class="fa-solid fa-building text-[#6c3fb8]"></i>
            <span>Ban Hỗ trợ Doanh nghiệp GDNN:</span>
            <strong class="text-[#6c3fb8] text-sm">0229.386.8686</strong>
          </div>
          <router-link 
            to="/admin"
            class="inline-block px-5 py-2 rounded-xl border border-[#6c3fb8] text-[#6c3fb8] hover:bg-purple-50 font-bold text-xs transition-colors cursor-pointer">
            Dành cho Nhà tuyển dụng
          </router-link>
        </div>

      </div>

    </main>

    <!-- ============================================================== -->
    <!-- 4. QUICK APPLY MODAL (Vieclam24h Ứng tuyển nhanh)              -->
    <!-- ============================================================== -->
    <div 
      v-if="isApplyModalOpen" 
      class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs animate-fade-in">
      
      <div 
        class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[94vh] sm:max-h-[90vh]">
        
        <!-- Modal Header -->
        <div class="p-3.5 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-[#faf8ff]">
          <div>
            <h3 class="font-black text-slate-900 text-base sm:text-lg">
              Ứng tuyển trực tiếp
            </h3>
            <p class="text-xs text-[#853AFF] font-medium mt-0.5 line-clamp-1" :title="job?.title">
              {{ job?.title }} - {{ job?.company }}
            </p>
          </div>
          <button 
            @click="isApplyModalOpen = false" 
            class="w-8 h-8 rounded-full bg-slate-200/70 hover:bg-slate-300 text-slate-600 flex items-center justify-center transition-colors cursor-pointer">
            <i class="fa-solid fa-xmark text-sm"></i>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-4 sm:p-6 overflow-y-auto space-y-3.5 sm:space-y-4">
          
          <!-- Success Alert -->
          <div v-if="isSubmitted" class="p-4 sm:p-5 rounded-xl bg-emerald-50 border border-emerald-200 text-center space-y-3 animate-fade-in">
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl mx-auto">
              <i class="fa-solid fa-circle-check"></i>
            </div>
            <h4 class="font-bold text-emerald-800 text-base">Nộp hồ sơ thành công!</h4>
            <p class="text-xs text-emerald-700 leading-relaxed">
              Hồ sơ ứng tuyển của bạn đã được chuyển trực tiếp tới Bộ phận Tuyển dụng của <strong>{{ job?.company }}</strong>. Doanh nghiệp sẽ liên hệ phỏng vấn qua số điện thoại <strong>{{ applyForm.phone }}</strong>.
            </p>
            <div class="pt-2 flex justify-center gap-3">
              <button 
                @click="isApplyModalOpen = false" 
                class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer">
                Đã hiểu & Đóng
              </button>
            </div>
          </div>

          <!-- Form Content -->
          <form v-else @submit.prevent="submitApplication" class="space-y-3 sm:space-y-3.5">
            
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Họ và tên <span class="text-rose-500">*</span>
              </label>
              <input 
                v-model="applyForm.fullName" 
                type="text" 
                required 
                placeholder="Ví dụ: Nguyễn Văn An"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#853AFF] focus:border-transparent outline-none transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Số điện thoại <span class="text-rose-500">*</span>
                </label>
                <input 
                  v-model="applyForm.phone" 
                  type="tel" 
                  required 
                  placeholder="09xx xxx xxx"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#853AFF] focus:border-transparent outline-none transition-all">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                  Email nhận kết quả
                </label>
                <input 
                  v-model="applyForm.email" 
                  type="email" 
                  placeholder="name@email.com"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#853AFF] focus:border-transparent outline-none transition-all">
              </div>
            </div>

            <!-- Upload CV -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Đính kèm CV / Giới thiệu học vấn GDNN
              </label>
              <div class="border-2 border-dashed border-purple-200 hover:border-[#853AFF] rounded-xl p-3.5 sm:p-4 text-center bg-purple-50/40 hover:bg-purple-50/70 transition-colors cursor-pointer relative">
                <input 
                  type="file" 
                  @change="handleFileUpload" 
                  accept=".pdf,.doc,.docx" 
                  class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                <i class="fa-solid fa-cloud-arrow-up text-xl text-[#853AFF] mb-1"></i>
                <p class="text-xs font-bold text-slate-700">
                  {{ applyForm.fileName || 'Kéo thả CV hoặc bấm để chọn tệp' }}
                </p>
                <p class="text-[10px] text-slate-400 mt-0.5">
                  Định dạng hỗ trợ: PDF, DOCX (Dung lượng &lt; 5MB). Không bắt buộc nếu nộp nhanh.
                </p>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                Lời nhắn / Trình độ đào tạo hiện tại
              </label>
              <textarea 
                v-model="applyForm.message" 
                rows="2" 
                placeholder="VD: Em học nghề Điện công nghiệp tại Trường Cao đẳng Cơ điện Xây dựng Việt Xô, mong muốn ứng tuyển..."
                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#853AFF] focus:border-transparent outline-none transition-all resize-none"></textarea>
            </div>

            <!-- Hotline Direct option -->
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
              <span class="text-slate-600">Hoặc liên hệ trực tiếp:</span>
              <div class="flex items-center gap-2">
                <a 
                  v-if="job?.contact?.phone" 
                  :href="`tel:${job.contact.phone}`" 
                  class="text-emerald-700 font-bold hover:underline flex items-center gap-1">
                  <i class="fa-solid fa-phone text-xs"></i>
                  <span>{{ job.contact.phone }}</span>
                </a>
                <a 
                  v-if="job?.contact?.zalo" 
                  :href="`https://zalo.me/${job.contact.zalo}`" 
                  target="_blank" 
                  class="px-2 py-0.5 rounded bg-blue-600 text-white font-bold text-[10px] hover:bg-blue-700">
                  Zalo
                </a>
              </div>
            </div>

            <!-- Actions (responsive stacked on mobile) -->
            <div class="pt-2 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 sm:gap-2.5">
              <button 
                type="button" 
                @click="isApplyModalOpen = false" 
                class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors cursor-pointer text-center">
                Hủy
              </button>
              <button 
                type="submit" 
                class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#853AFF] to-[#6C5FFF] hover:opacity-95 text-white font-bold text-xs shadow-xs transition-all cursor-pointer text-center">
                Nộp hồ sơ ngay
              </button>
            </div>

          </form>

        </div>

      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { jobService } from '../../services/jobService'

const route = useRoute()
const isSaved = ref(false)
const copiedLink = ref(false)
const isApplyModalOpen = ref(false)
const isSubmitted = ref(false)

const SAVED_JOBS_KEY = 'schoolmap_saved_jobs'

const applyForm = ref({
  fullName: '',
  phone: '',
  email: '',
  message: '',
  fileName: ''
})

const job = computed(() => {
  const id = route.params.id
  if (!id) return null
  return jobService.getById(id)
})

const relatedJobs = computed(() => {
  if (!job.value) return []
  return jobService.getAll()
    .filter(j => j.id !== job.value.id && (j.industry === job.value.industry || j.ward_name === job.value.ward_name))
    .slice(0, 6)
})

const generatedKeywords = computed(() => {
  if (!job.value) return []
  const kw = [
    job.value.title,
    job.value.company,
    job.value.industry_name,
    job.value.ward_name,
    'Việc làm Ninh Bình',
    'Tuyển dụng GDNN'
  ]
  return Array.from(new Set(kw))
})

// Format functions to prevent text overflow & match Vieclam24h design
function formatSalary(sal) {
  if (!sal) return 'Thỏa thuận'
  const match = sal.match(/^([^(]+)/)
  return match ? match[1].trim() : sal
}

function formatExperience(exp) {
  if (!exp) return 'Không yêu cầu KN'
  const lower = exp.toLowerCase()
  if (lower.includes('tay nghề') || lower.includes('học nghề') || lower.includes('đào tạo')) {
    return 'Được đào tạo nghề'
  }
  if (lower.includes('không yêu cầu')) {
    return 'Không yêu cầu KN'
  }
  if (exp.length > 22) {
    return exp.slice(0, 20) + '...'
  }
  return exp
}

function getSavedJobIds() {
  try {
    return JSON.parse(localStorage.getItem(SAVED_JOBS_KEY) || '[]')
  } catch (e) {
    return []
  }
}

function checkIsSaved() {
  if (!job.value) return
  const list = getSavedJobIds()
  isSaved.value = list.includes(job.value.id)
}

function toggleSaveJob() {
  if (!job.value) return
  try {
    let list = getSavedJobIds()
    if (list.includes(job.value.id)) {
      list = list.filter(id => id !== job.value.id)
      isSaved.value = false
    } else {
      list.push(job.value.id)
      isSaved.value = true
    }
    localStorage.setItem(SAVED_JOBS_KEY, JSON.stringify(list))
  } catch (e) {
    isSaved.value = !isSaved.value
  }
}

function isJobSaved(jobId) {
  const list = getSavedJobIds()
  return list.includes(jobId)
}

function toggleSaveRelatedJob(jobId) {
  try {
    let list = getSavedJobIds()
    if (list.includes(jobId)) {
      list = list.filter(id => id !== jobId)
    } else {
      list.push(jobId)
    }
    localStorage.setItem(SAVED_JOBS_KEY, JSON.stringify(list))
    if (job.value && job.value.id === jobId) {
      isSaved.value = list.includes(jobId)
    }
  } catch (e) {
    // ignore
  }
}

function openApplyModal() {
  isSubmitted.value = false
  isApplyModalOpen.value = true
}

function handleFileUpload(e) {
  const file = e.target.files?.[0]
  if (file) {
    applyForm.value.fileName = file.name
  }
}

function submitApplication() {
  isSubmitted.value = true
}

watch(() => route.params.id, () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
  checkIsSaved()
})

onMounted(() => {
  window.scrollTo({ top: 0, behavior: 'instant' })
  checkIsSaved()
})

function scrollToContact() {
  const el = document.getElementById('contact-section')
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
  }
}

function getDaysLeft(deadlineStr) {
  if (!deadlineStr) return 15
  const hash = deadlineStr.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0)
  return 5 + (hash % 25)
}

function copyShareLink() {
  navigator.clipboard?.writeText(window.location.href)
  copiedLink.value = true
  setTimeout(() => {
    copiedLink.value = false
  }, 2500)
}

function shareFacebook() {
  const url = encodeURIComponent(window.location.href)
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank')
}

function reportJob() {
  alert('Cảm ơn bạn đã phản hồi. Ban quản trị sẽ kiểm tra lại thông tin tin tuyển dụng này.')
}
</script>
