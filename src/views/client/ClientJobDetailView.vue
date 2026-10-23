<template>
  <div class="w-full h-full overflow-y-auto bg-slate-50 flex flex-col font-sans select-text">
    
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
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-colors shadow-xs">
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

            <!-- Section 4: Thông tin chung -->
            <section class="space-y-3 pt-2">
              <h2 class="text-base font-bold text-slate-900">
                Thông tin chung
              </h2>
              
              <div class="bg-slate-50/80 rounded-2xl border border-slate-200/80 p-4 sm:p-5 divide-y divide-slate-200/70 text-xs">
                
                <!-- Hàng 1: Ngày đăng & Cấp bậc -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-8 pb-3.5 first:pt-0">
                  <div>
                    <span class="text-slate-400 block mb-1">Ngày đăng</span>
                    <span class="font-bold text-slate-800 text-[13px]">22/09/2026</span>
                  </div>
                  <div>
                    <span class="text-slate-400 block mb-1">Cấp bậc</span>
                    <span class="font-bold text-slate-800 text-[13px]">Nhân viên / Kỹ thuật viên</span>
                  </div>
                </div>

                <!-- Hàng 2: Số lượng tuyển & Hình thức làm việc -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-8 py-3.5">
                  <div>
                    <span class="text-slate-400 block mb-1">Số lượng tuyển</span>
                    <span class="font-bold text-slate-800 text-[13px]">{{ job.recruitment_count ? `${job.recruitment_count} người` : 'Tuyển liên tục' }}</span>
                  </div>
                  <div>
                    <span class="text-slate-400 block mb-1">Hình thức làm việc</span>
                    <span class="font-bold text-slate-800 text-[13px]">Toàn thời gian cố định</span>
                  </div>
                </div>

                <!-- Hàng 3: Yêu cầu kinh nghiệm & Trình độ đào tạo GDNN -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-8 py-3.5">
                  <div>
                    <span class="text-slate-400 block mb-1">Yêu cầu kinh nghiệm</span>
                    <span class="font-bold text-slate-800 text-[13px] leading-snug">{{ job.experience || 'Không yêu cầu' }}</span>
                  </div>
                  <div>
                    <span class="text-slate-400 block mb-1">Trình độ đào tạo GDNN</span>
                    <span class="font-bold text-slate-800 text-[13px] leading-snug">{{ job.education || 'Trung cấp / Cao đẳng' }}</span>
                  </div>
                </div>

                <!-- Hàng 4: Địa điểm làm việc & Ngành nghề tuyển dụng -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-8 pt-3.5 last:pb-0">
                  <div>
                    <span class="text-slate-400 block mb-1">Địa điểm làm việc</span>
                    <div class="font-bold text-slate-800 text-[13px] leading-snug flex items-start gap-1.5">
                      <i class="fa-solid fa-location-dot text-rose-500 mt-0.5 text-xs flex-shrink-0"></i>
                      <span>{{ job.address || (job.ward_name + ', Ninh Bình') }}</span>
                    </div>
                  </div>
                  <div>
                    <span class="text-slate-400 block mb-1">Ngành nghề tuyển dụng</span>
                    <div class="font-bold text-slate-800 text-[13px] leading-snug flex items-center gap-1.5 flex-wrap">
                      <router-link 
                        :to="{ path: '/tuyen-dung', query: { industry: job.industry } }"
                        class="text-blue-600 hover:text-blue-700 hover:underline">
                        {{ job.industry_name }}
                      </router-link>
                      <span class="text-slate-300 font-normal">/</span>
                      <span class="text-slate-600 font-medium">Doanh nghiệp tỉnh Ninh Bình</span>
                    </div>
                  </div>
                </div>

              </div>
            </section>

            <!-- Section: Nơi có chương trình đào tạo kỹ năng cho công việc này -->
            <section v-if="job.training_schools && job.training_schools.length" class="space-y-3 pt-2">
              <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                  <i class="fa-solid fa-graduation-cap text-indigo-600"></i>
                  <span>Nơi có chương trình đào tạo kỹ năng cho công việc này</span>
                </h2>
                <span class="text-xs text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-full font-bold border border-indigo-100/80">
                  {{ job.training_schools.length }} cơ sở GDNN
                </span>
              </div>
              <p class="text-xs text-slate-500">
                Các cơ sở giáo dục nghề nghiệp tại tỉnh Ninh Bình và khu vực lân cận đào tạo chuyên môn, kỹ năng đáp ứng yêu cầu tuyển dụng của vị trí này:
              </p>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div 
                  v-for="(school, sIdx) in job.training_schools" 
                  :key="sIdx"
                  class="p-4 rounded-xl border border-slate-200/90 bg-slate-50/70 hover:bg-white hover:border-blue-300 hover:shadow-xs transition-all flex flex-col justify-between h-full group">
                  
                  <!-- Top: Logo & School info -->
                  <div>
                    <div class="flex items-start gap-2.5">
                      <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs flex-shrink-0 mt-0.5 border border-indigo-100/80">
                        <i class="fa-solid fa-school"></i>
                      </div>
                      <div class="min-w-0 flex-1">
                        <!-- Fixed title height slot so 1-line and 2-line titles align perfectly -->
                        <h4 
                          class="font-bold text-xs sm:text-sm text-slate-900 group-hover:text-blue-700 leading-snug line-clamp-2 h-9 sm:h-10 flex items-start" 
                          :title="school.name">
                          {{ school.name }}
                        </h4>
                        <p class="text-[11px] text-slate-500 mt-1 truncate">
                          {{ school.level }} · {{ school.district }}
                        </p>
                      </div>
                    </div>

                    <!-- Middle: Program & Duration with fixed height -->
                    <div 
                      v-if="school.duration || school.program" 
                      class="text-[11px] text-slate-600 bg-white px-2.5 py-2 rounded-lg border border-slate-100 flex items-center justify-between h-9 my-3">
                      <span class="font-semibold text-slate-800 truncate mr-2">{{ school.program || 'Đào tạo kỹ năng thực hành' }}</span>
                      <span class="text-slate-500 font-medium flex-shrink-0 flex items-center gap-1 text-[11px] whitespace-nowrap">
                        <i class="fa-regular fa-clock text-[10px] text-slate-400"></i>
                        <span>{{ school.duration || 'Theo chuẩn GDNN' }}</span>
                      </span>
                    </div>
                  </div>

                  <!-- Bottom: Action buttons aligned to bottom -->
                  <div class="pt-1 flex items-center gap-2 mt-auto">
                    <router-link 
                      :to="{ path: '/co-so-gdnn', query: { q: school.name } }"
                      class="flex-1 py-1.5 px-3 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors border border-blue-100/60">
                      <i class="fa-solid fa-circle-info text-[11px]"></i>
                      <span>Xem cơ sở</span>
                    </router-link>
                    <router-link 
                      :to="{ path: '/map', query: { q: school.name } }"
                      class="flex-1 py-1.5 px-3 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors border border-indigo-100/60">
                      <i class="fa-solid fa-map-location-dot text-[11px]"></i>
                      <span>Bản đồ GDNN</span>
                    </router-link>
                  </div>

                </div>
              </div>
            </section>


            <!-- Section 6: Thông tin liên hệ trực tiếp nhà tuyển dụng -->
            <section id="contact-section" class="space-y-3 pt-2">
              <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-address-card text-blue-600"></i>
                <span>Thông tin liên hệ nộp hồ sơ</span>
              </h2>

              <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2 text-xs sm:text-sm text-slate-700">
                <p><strong>Người liên hệ:</strong> {{ job.contact?.contact_person }}</p>
                <p><strong>Địa chỉ nộp hồ sơ:</strong> {{ job.contact?.address }}</p>
                <p v-if="job.contact?.email">
                  <strong>Email:</strong> 
                  <a :href="`mailto:${job.contact?.email}`" class="text-blue-600 font-semibold underline ml-1">
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
                  class="w-full sm:flex-1 py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-xs transition-colors">
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
                  class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-700 text-xs transition-colors border border-slate-200 hover:border-blue-200">
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
                :class="job.company_color || 'bg-blue-700'"
                class="w-16 h-16 rounded-2xl text-white font-black flex items-center justify-center shadow-sm border border-slate-100 overflow-hidden p-1.5 text-center select-none">
                <span class="max-w-full truncate text-xs sm:text-sm font-black uppercase tracking-tight block">
                  {{ job.company_logo || 'DN' }}
                </span>
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
                :to="companyPageRoute"
                class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1">
                <span>Xem trang công ty</span>
                <i class="fa-solid fa-angle-right text-[10px]"></i>
              </router-link>
            </div>

          </div>

          <!-- Card 2: Việc làm tương tự cho bạn -->
          <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-2xs space-y-3">
            
            <div class="flex items-center justify-between pb-0.5">
              <h3 class="font-bold text-sm text-slate-900">
                Việc làm tương tự cho bạn
              </h3>
              <router-link 
                to="/tuyen-dung" 
                class="text-xs text-blue-600 hover:text-blue-700 hover:underline font-semibold inline-flex items-center gap-1 group/more">
                <span>Xem thêm</span>
                <i class="fa-solid fa-arrow-right text-[10px] group-hover/more:translate-x-0.5 transition-transform"></i>
              </router-link>
            </div>

            <!-- Job List items -->
            <div class="divide-y divide-slate-100">
              
              <div 
                v-for="rel in relatedJobs" 
                :key="rel.id"
                class="py-3 first:pt-1 last:pb-0 group">
                
                <div class="flex items-start gap-3">
                  <!-- Mini Logo -->
                  <router-link 
                    :to="`/tuyen-dung/${rel.id}`"
                    :class="rel.company_color || 'bg-slate-700'"
                    class="w-10 h-10 rounded-xl text-white font-black flex items-center justify-center flex-shrink-0 shadow-2xs border border-slate-100 overflow-hidden p-1 text-center select-none group-hover:scale-105 transition-transform duration-150"
                    :title="rel.company">
                    <span class="max-w-full truncate text-[9px] font-black uppercase tracking-tight block">
                      {{ rel.company_logo || 'DN' }}
                    </span>
                  </router-link>

                  <!-- Job info -->
                  <div class="flex-1 min-w-0">
                    <!-- Title -->
                    <router-link 
                      :to="`/tuyen-dung/${rel.id}`" 
                      class="block font-bold text-xs sm:text-[13px] text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-2 leading-snug"
                      :title="rel.title">
                      {{ rel.title }}
                    </router-link>

                    <!-- Company -->
                    <p class="text-[11px] text-slate-500 truncate mt-0.5" :title="rel.company">
                      {{ rel.company }}
                    </p>

                    <!-- Balanced Bottom Row: Salary (Left) + Location (Right) -->
                    <div class="mt-2 flex items-center justify-between text-xs gap-2">
                      <!-- Salary -->
                      <span class="font-bold text-blue-600 text-xs sm:text-[13px] truncate" :title="rel.salary">
                        {{ rel.salary }}
                      </span>

                      <!-- Location -->
                      <div class="flex items-center gap-1 text-[11px] text-slate-500 font-medium flex-shrink-0" :title="rel.ward_name">
                        <i class="fa-solid fa-location-dot text-[10px] text-slate-400"></i>
                        <span>{{ rel.ward_name }}</span>
                      </div>
                    </div>

                  </div>

                </div>

              </div>

            </div>

          </div>

          <!-- Card 3: Hotline hỗ trợ GDNN Ninh Bình -->
          <div class="bg-gradient-to-r from-blue-50 via-indigo-50/40 to-slate-50 border border-blue-100/80 rounded-2xl p-4 shadow-2xs space-y-2.5">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm">
                <i class="fa-solid fa-headset"></i>
              </div>
              <div>
                <h4 class="font-bold text-xs text-slate-800 leading-tight">Cần tư vấn việc làm GDNN?</h4>
                <p class="text-[10px] text-slate-500">Sở LĐ-TB&XH Tỉnh Ninh Bình</p>
              </div>
            </div>
            <a 
              href="tel:02293868686" 
              class="block w-full text-center py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-colors shadow-2xs">
              <i class="fa-solid fa-phone mr-1.5"></i>
              <span>Hotline: 0229.386.8686</span>
            </a>
          </div>

        </aside>

      </div>



    </main>

    <ClientFooter class="mt-auto" />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { jobService } from '../../services/jobService'
import { enterpriseService } from '../../services/enterpriseService'

const route = useRoute()
const copiedLink = ref(false)

const job = computed(() => {
  const id = route.params.id
  if (!id) return null
  return jobService.getById(id)
})

function normalizeCompanyName(value) {
  return String(value || '')
    .toLocaleLowerCase('vi')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/đ/g, 'd')
    .replace(/[^a-z0-9]+/g, ' ')
    .trim()
}

const companyEnterprise = computed(() => {
  const companyName = normalizeCompanyName(job.value?.company)
  if (!companyName) return null

  return enterpriseService.getAll().find((enterprise) => {
    const fullName = normalizeCompanyName(enterprise.name)
    const shortName = normalizeCompanyName(enterprise.short_name)
    return fullName === companyName
      || fullName.includes(companyName)
      || companyName.includes(fullName)
      || (shortName && (companyName.includes(shortName) || shortName.includes(companyName)))
  }) || null
})

const companyPageRoute = computed(() => companyEnterprise.value
  ? { name: 'ClientEnterpriseDetail', params: { id: companyEnterprise.value.id } }
  : { name: 'ClientEnterpriseList', query: { search: job.value?.company || '' } })

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

watch(() => route.params.id, () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
})

onMounted(() => {
  window.scrollTo({ top: 0, behavior: 'instant' })
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
