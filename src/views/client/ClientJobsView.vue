<template>
  <div class="w-full h-full overflow-y-auto overflow-x-hidden bg-[#f8f9fa] flex flex-col font-sans select-text">
    
    <!-- Hero Search Section (Authentic Vieclam24h Lavender Theme) -->
    <section class="bg-[#f3f0ff] pt-5 sm:pt-8 pb-6 sm:pb-10 px-3 sm:px-6 flex-shrink-0 border-b border-purple-100/60">
      <div class="max-w-5xl mx-auto text-center space-y-2.5 sm:space-y-3">
        <!-- Badge -->
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white text-[#6c3fb8] text-[11px] sm:text-xs font-bold shadow-xs border border-purple-200">
          <i class="fa-solid fa-sparkles text-amber-500"></i>
          <span>Hơn {{ initialJobsCount }}+ vị trí việc làm đang tuyển dụng tại Ninh Bình</span>
        </div>

        <!-- Headline -->
        <h1 class="text-xl sm:text-2xl md:text-4xl font-black text-[#2F0D7B] tracking-tight leading-tight">
          Tìm việc làm nhanh tại các Doanh nghiệp Tỉnh Ninh Bình
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 max-w-2xl mx-auto px-1">
          Cầu nối trực tiếp giữa doanh nghiệp, nhà máy trên địa bàn tỉnh Ninh Bình với học sinh, sinh viên các cơ sở giáo dục nghề nghiệp và người lao động.
        </p>

        <!-- Big Search Box (Vieclam24h style) -->
        <div class="pt-2 sm:pt-3">
          <div class="bg-white rounded-2xl shadow-xl p-2 sm:p-3 border border-purple-200/80 flex flex-col md:flex-row items-center gap-2 sm:gap-2.5 text-left">
            
            <!-- 1. Keyword Input -->
            <div class="flex-1 w-full relative flex items-center">
              <i class="fa-solid fa-magnifying-glass absolute left-3 text-slate-400 text-sm pointer-events-none"></i>
              <input 
                v-model="searchQuery"
                type="text" 
                placeholder="Vị trí tuyển dụng, tên công việc, công ty..."
                class="w-full pl-9 pr-7 py-2.5 text-xs sm:text-sm font-medium text-slate-800 placeholder:text-slate-400 outline-none rounded-xl bg-slate-50/70 hover:bg-slate-100/70 focus:bg-white focus:ring-1 focus:ring-[#6c3fb8] transition-all"
              />
              <button 
                v-if="searchQuery"
                @click="searchQuery = ''"
                class="absolute right-2.5 text-slate-400 hover:text-slate-600 w-5 h-5 flex items-center justify-center cursor-pointer">
                <i class="fa-solid fa-xmark text-xs"></i>
              </button>
            </div>

            <div class="hidden md:block w-px h-8 bg-slate-200"></div>

            <!-- 2. Industry Custom Popover Dropdown (No native select overflow) -->
            <div ref="industryDropdownRef" class="w-full md:w-64 lg:w-72 relative">
              <!-- Trigger Button -->
              <div 
                @click="toggleIndustryMenu"
                class="w-full pl-8 pr-8 py-2.5 text-xs sm:text-sm font-medium rounded-xl bg-slate-50/70 hover:bg-slate-100/70 border border-transparent hover:border-slate-200 transition-all cursor-pointer flex items-center justify-between select-none"
                :class="{ 'ring-1 ring-[#6c3fb8] bg-white border-purple-200 shadow-2xs': isIndustryMenuOpen }">
                
                <i class="fa-solid fa-layer-group absolute left-3 text-[#6c3fb8] text-xs pointer-events-none"></i>
                
                <span class="truncate block" :class="selectedIndustry !== 'all' ? 'text-[#6c3fb8] font-bold' : 'text-slate-700'">
                  {{ selectedIndustryLabel }}
                </span>

                <div class="absolute right-2.5 flex items-center gap-1">
                  <button 
                    v-if="selectedIndustry !== 'all'"
                    @click.stop="clearIndustry"
                    class="w-4 h-4 rounded-full text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center cursor-pointer transition-colors"
                    title="Bỏ chọn ngành nghề">
                    <i class="fa-solid fa-xmark text-[10px]"></i>
                  </button>
                  <i 
                    class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200"
                    :class="{ 'rotate-180 text-[#6c3fb8]': isIndustryMenuOpen }"></i>
                </div>
              </div>

              <!-- Popover Dropdown with Industry List -->
              <div 
                v-if="isIndustryMenuOpen"
                class="absolute left-0 right-0 md:left-0 md:right-auto md:w-72 top-full mt-1.5 w-full bg-white border border-slate-200 rounded-2xl shadow-2xl p-2 z-50 animate-in fade-in select-none">
                
                <div class="max-h-64 overflow-y-auto space-y-0.5 pr-1 no-scrollbar">
                  <!-- "All Industries" option -->
                  <div 
                    @click="selectIndustry('all')"
                    :class="selectedIndustry === 'all' ? 'bg-purple-50 text-[#6c3fb8] font-bold' : 'text-slate-700 hover:bg-slate-50'"
                    class="px-2.5 py-2 rounded-xl text-xs cursor-pointer flex items-center justify-between transition-colors">
                    <div class="flex items-center gap-2">
                      <i class="fa-solid fa-layer-group text-[#6c3fb8] text-xs"></i>
                      <span>Tất cả ngành nghề</span>
                    </div>
                    <i v-if="selectedIndustry === 'all'" class="fa-solid fa-check text-xs text-[#6c3fb8]"></i>
                  </div>

                  <div class="h-px bg-slate-100 my-1"></div>

                  <!-- 8 GDNN Industry Groups -->
                  <div 
                    v-for="ind in industries.filter(i => i.id !== 'all')"
                    :key="ind.id"
                    @click="selectIndustry(ind.id)"
                    :class="selectedIndustry === ind.id ? 'bg-purple-50 text-[#6c3fb8] font-bold' : 'text-slate-700 hover:bg-slate-50'"
                    class="px-2.5 py-2 rounded-xl text-xs cursor-pointer flex items-center justify-between transition-colors group">
                    <div class="flex items-center gap-2.5 min-w-0">
                      <i :class="ind.icon" class="text-xs flex-shrink-0 text-slate-400 group-hover:text-[#6c3fb8]"></i>
                      <span class="truncate group-hover:text-[#6c3fb8]">{{ ind.name }}</span>
                    </div>
                    <i v-if="selectedIndustry === ind.id" class="fa-solid fa-check text-xs text-[#6c3fb8] flex-shrink-0 ml-1"></i>
                  </div>
                </div>

              </div>
            </div>

            <!-- 4. Search CTA Button (Purple Gradient) -->
            <button 
              @click="scrollToJobs"
              class="w-full md:w-auto px-7 py-3 rounded-xl bg-gradient-to-r from-[#853AFF] to-[#6C5FFF] hover:opacity-95 text-white font-bold text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2 flex-shrink-0 cursor-pointer active:scale-95">
              <i class="fa-solid fa-magnifying-glass"></i>
              <span>Tìm việc ngay</span>
            </button>

          </div>
        </div>

      </div>
    </section>

    <!-- 3. Main Content: Full-Width 3-Column Layout with Horizontal Zone Selector (Vieclam24h Exact Style) -->
    <main id="job-listings-section" class="max-w-7xl w-full mx-auto px-3 sm:px-6 py-4 sm:py-8 flex-1">
      
      <!-- Section Header -->
      <div class="flex items-center justify-between flex-wrap gap-2 sm:gap-3 mb-3">
        <div class="flex items-center gap-2">
          <span class="text-xl sm:text-2xl text-orange-500 animate-bounce">🔥</span>
          <h2 class="text-base sm:text-xl md:text-2xl font-black text-slate-900 tracking-tight">
            Việc làm tuyển gấp
          </h2>
          <span class="text-[11px] sm:text-xs px-2.5 py-0.5 rounded-full bg-purple-100 text-[#6c3fb8] font-bold flex-shrink-0">
            {{ filteredJobs.length }} việc làm
          </span>
        </div>

        <div class="flex items-center gap-2">
          <!-- Reset filter button -->
          <button 
            v-if="hasActiveFilter"
            @click="resetFilters"
            class="text-xs text-[#6c3fb8] hover:text-[#522b94] font-bold flex items-center gap-1.5 transition-colors cursor-pointer bg-purple-50 hover:bg-purple-100 px-2.5 sm:px-3 py-1.5 rounded-xl border border-purple-200">
            <i class="fa-solid fa-rotate-left text-[11px]"></i>
            <span>Đặt lại bộ lọc</span>
          </button>
          <span v-else class="text-xs text-slate-500 font-medium hidden sm:inline">
            Cập nhật liên tục hôm nay
          </span>
        </div>
      </div>

      <!-- Horizontal Filter Bar (Exact Match to Photo 2 - fully mobile responsive) -->
      <div class="bg-white rounded-2xl p-2 sm:p-3 border border-slate-200 shadow-2xs mb-4 sm:mb-5 flex flex-col md:flex-row items-start md:items-center gap-2 sm:gap-2.5">
        
        <!-- Left Custom Dropdown: "Lọc theo: Khu công nghiệp / Ngành nghề" (No native select overflow) -->
        <div ref="filterModeDropdownRef" class="relative flex-shrink-0 self-start md:self-auto">
          <button 
            type="button"
            @click="isFilterModeMenuOpen = !isFilterModeMenuOpen"
            class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-3.5 py-1.5 sm:py-2 rounded-full border border-purple-200 bg-purple-50/70 hover:bg-purple-100 text-xs font-bold text-slate-800 transition-all cursor-pointer shadow-2xs select-none">
            <i class="fa-solid fa-sliders text-[#853AFF] text-[11px]"></i>
            <span class="text-slate-500 font-normal">Lọc theo:</span>
            <span class="text-[#853AFF] font-bold">
              {{ filterMode === 'zone' ? 'Khu công nghiệp' : 'Ngành nghề' }}
            </span>
            <i 
              class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 ml-0.5"
              :class="{ 'rotate-180 text-[#853AFF]': isFilterModeMenuOpen }"></i>
          </button>

          <!-- Custom Popover Dropdown (safe from screen overflow) -->
          <div 
            v-if="isFilterModeMenuOpen"
            class="absolute left-0 top-full mt-1.5 w-52 bg-white border border-purple-100 rounded-2xl shadow-xl p-1.5 z-40 animate-in fade-in select-none">
            
            <button 
              type="button"
              @click="setFilterMode('zone')"
              :class="filterMode === 'zone' ? 'bg-purple-50 text-[#853AFF] font-bold' : 'text-slate-700 hover:bg-slate-50'"
              class="w-full px-3 py-2 rounded-xl text-xs flex items-center justify-between transition-colors cursor-pointer text-left">
              <div class="flex items-center gap-2.5">
                <div 
                  :class="filterMode === 'zone' ? 'bg-purple-100 text-[#853AFF]' : 'bg-slate-100 text-slate-500'"
                  class="w-6 h-6 rounded-lg flex items-center justify-center text-[11px] flex-shrink-0">
                  <i class="fa-solid fa-industry"></i>
                </div>
                <div>
                  <div class="font-bold leading-none">Khu công nghiệp</div>
                  <div class="text-[10px] text-slate-400 font-normal mt-0.5">5 KCN & Cụm CN</div>
                </div>
              </div>
              <i v-if="filterMode === 'zone'" class="fa-solid fa-check text-xs text-[#853AFF] ml-2"></i>
            </button>

            <div class="h-px bg-slate-100 my-1"></div>

            <button 
              type="button"
              @click="setFilterMode('industry')"
              :class="filterMode === 'industry' ? 'bg-purple-50 text-[#853AFF] font-bold' : 'text-slate-700 hover:bg-slate-50'"
              class="w-full px-3 py-2 rounded-xl text-xs flex items-center justify-between transition-colors cursor-pointer text-left">
              <div class="flex items-center gap-2.5">
                <div 
                  :class="filterMode === 'industry' ? 'bg-purple-100 text-[#853AFF]' : 'bg-slate-100 text-slate-500'"
                  class="w-6 h-6 rounded-lg flex items-center justify-center text-[11px] flex-shrink-0">
                  <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                  <div class="font-bold leading-none">Ngành nghề</div>
                  <div class="text-[10px] text-slate-400 font-normal mt-0.5">8 nhóm ngành GDNN</div>
                </div>
              </div>
              <i v-if="filterMode === 'industry'" class="fa-solid fa-check text-xs text-[#853AFF] ml-2"></i>
            </button>

          </div>
        </div>

        <!-- Right: Horizontal Pills with Arrow Scroll Buttons (Exact Photo 2 Structure) -->
        <div class="flex-1 min-w-0 flex items-center gap-1 sm:gap-2">
          <!-- Prev button (hidden on very small screens to give max width to swipe track) -->
          <button 
            @click="scrollPills('left')"
            class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-50 hover:bg-purple-50 text-slate-600 hover:text-[#853AFF] border border-slate-200 flex items-center justify-center flex-shrink-0 transition-colors cursor-pointer shadow-2xs active:scale-95"
            title="Cuộn sang trái">
            <i class="fa-solid fa-chevron-left text-[9px] sm:text-[10px]"></i>
          </button>

          <!-- Scrollable Track with touch smooth pan -->
          <div 
            ref="pillsContainerRef"
            style="scrollbar-width: none; -ms-overflow-style: none;"
            class="flex-1 min-w-0 flex items-center gap-1.5 sm:gap-2 overflow-x-auto scroll-smooth py-0.5 no-scrollbar touch-pan-x">
            
            <!-- Mode 1: Khu công nghiệp (Default like Photo 2) -->
            <template v-if="filterMode === 'zone'">
              <button 
                @click="selectedZone = 'all'"
                :class="selectedZone === 'all' ? 'bg-[#853AFF] text-white shadow-xs font-bold' : 'bg-slate-100 hover:bg-purple-50 text-slate-700 hover:text-[#853AFF] border border-slate-200/80 font-medium'"
                class="px-3.5 sm:px-4 py-1.5 rounded-full text-xs transition-all whitespace-nowrap flex-shrink-0 cursor-pointer">
                Tất cả
              </button>

              <button 
                v-for="(zone, idx) in industrialZones" 
                :key="idx"
                @click="selectedZone = (selectedZone === zone.keyword ? 'all' : zone.keyword)"
                :class="selectedZone === zone.keyword ? 'bg-[#853AFF] text-white shadow-xs font-bold' : 'bg-white hover:bg-purple-50 text-slate-700 hover:text-[#853AFF] border border-slate-200 font-medium'"
                class="px-3 sm:px-4 py-1.5 rounded-full text-xs transition-all whitespace-nowrap flex-shrink-0 flex items-center gap-1.5 cursor-pointer shadow-2xs">
                <span>{{ zone.shortName || zone.name }}</span>
                <span 
                  :class="selectedZone === zone.keyword ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500'"
                  class="text-[10px] px-1.5 py-0.2 rounded-full font-bold">
                  {{ zone.count }} việc
                </span>
              </button>
            </template>

            <!-- Mode 2: Ngành nghề GDNN -->
            <template v-else>
              <button 
                @click="selectedIndustry = 'all'"
                :class="selectedIndustry === 'all' ? 'bg-[#853AFF] text-white shadow-xs font-bold' : 'bg-slate-100 hover:bg-purple-50 text-slate-700 hover:text-[#853AFF] border border-slate-200/80 font-medium'"
                class="px-3.5 sm:px-4 py-1.5 rounded-full text-xs transition-all whitespace-nowrap flex-shrink-0 cursor-pointer">
                Tất cả ngành
              </button>

              <button 
                v-for="ind in industries.filter(i => i.id !== 'all')" 
                :key="ind.id"
                @click="selectedIndustry = (selectedIndustry === ind.id ? 'all' : ind.id)"
                :class="selectedIndustry === ind.id ? 'bg-[#853AFF] text-white shadow-xs font-bold' : 'bg-white hover:bg-purple-50 text-slate-700 hover:text-[#853AFF] border border-slate-200 font-medium'"
                class="px-3 sm:px-4 py-1.5 rounded-full text-xs transition-all whitespace-nowrap flex-shrink-0 flex items-center gap-1.5 cursor-pointer shadow-2xs">
                <i :class="ind.icon" class="text-[11px]"></i>
                <span>{{ ind.name }}</span>
              </button>
            </template>

          </div>

          <!-- Next button -->
          <button 
            @click="scrollPills('right')"
            class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-50 hover:bg-purple-50 text-slate-600 hover:text-[#853AFF] border border-slate-200 flex items-center justify-center flex-shrink-0 transition-colors cursor-pointer shadow-2xs active:scale-95"
            title="Cuộn sang phải">
            <i class="fa-solid fa-chevron-right text-[9px] sm:text-[10px]"></i>
          </button>
        </div>

      </div>

      <!-- Quick Category Tabs (All / Tuyển gấp / Lương cao / v.v.) -->
      <div class="flex items-center gap-1.5 overflow-x-auto pb-1.5 mb-3 sm:mb-4 text-xs no-scrollbar touch-pan-x">
        <button 
          v-for="tab in jobTabs" 
          :key="tab.id"
          @click="selectedTab = tab.id"
          :class="selectedTab === tab.id ? 'bg-[#3b1d74] text-white font-bold shadow-2xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 font-medium'"
          class="px-3 sm:px-3.5 py-1.5 rounded-xl transition-all whitespace-nowrap cursor-pointer flex-shrink-0 text-xs">
          {{ tab.label }}
        </button>
      </div>

      <!-- 3-Column Job Grid with Pagination (Full Width, Wide & Spacious Cards) -->
      <div 
        v-if="filteredJobs.length > 0"
        class="space-y-6 sm:space-y-8">
        
        <!-- Job Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-5">
          <JobCard 
            v-for="job in paginatedJobs"
            :key="job.id"
            :job="job"
            @select="goToJobDetail($event)"
          />
        </div>

        <!-- Premium Modern Pagination Bar -->
        <div 
          v-if="totalPages > 1"
          class="bg-white rounded-2xl border border-slate-200 p-3 sm:p-4 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4 select-none">
          
          <!-- Left info: items counter (responsive format for mobile & desktop) -->
          <div class="text-xs text-slate-500 order-2 sm:order-1 text-center sm:text-left">
            <span class="sm:hidden">
              <span class="font-bold text-slate-800">{{ (currentPage - 1) * pageSize + 1 }} - {{ Math.min(currentPage * pageSize, filteredJobs.length) }}</span>
              <span class="text-slate-400"> / </span>
              <span class="font-bold text-[#6c3fb8]">{{ filteredJobs.length }}</span> việc làm
            </span>
            <span class="hidden sm:inline">
              Hiển thị <span class="font-bold text-slate-800">{{ (currentPage - 1) * pageSize + 1 }} - {{ Math.min(currentPage * pageSize, filteredJobs.length) }}</span>
              trên <span class="font-bold text-[#6c3fb8]">{{ filteredJobs.length }}</span> việc làm
            </span>
          </div>

          <!-- Right: Page Number Controls -->
          <div class="flex items-center gap-1 sm:gap-1.5 order-1 sm:order-2">
            <!-- Prev Button -->
            <button 
              @click="prevPage"
              :disabled="currentPage === 1"
              :class="currentPage === 1 
                ? 'opacity-40 cursor-not-allowed text-slate-300 border-slate-100 bg-slate-50' 
                : 'hover:bg-purple-50 text-slate-700 hover:text-[#6c3fb8] border-slate-200 hover:border-purple-200 bg-white cursor-pointer active:scale-95 shadow-2xs'"
              class="h-8 sm:h-9 px-2.5 sm:px-3 rounded-xl border text-xs font-semibold flex items-center gap-1 transition-all"
              title="Trang trước">
              <i class="fa-solid fa-chevron-left text-[10px]"></i>
              <span class="hidden sm:inline">Trước</span>
            </button>

            <!-- Page Numbers List -->
            <template v-for="(p, idx) in displayedPages" :key="idx">
              <!-- Ellipsis separator -->
              <span 
                v-if="p === '...'"
                class="h-8 sm:h-9 w-6 sm:w-8 flex items-center justify-center text-slate-400 text-xs font-bold">
                ...
              </span>
              <!-- Number Button -->
              <button 
                v-else
                @click="goToPage(p)"
                :class="currentPage === p 
                  ? 'bg-gradient-to-r from-[#853AFF] to-[#6C5FFF] text-white font-bold shadow-xs border-transparent' 
                  : 'bg-white hover:bg-purple-50 text-slate-700 hover:text-[#6c3fb8] border-slate-200 hover:border-purple-200 font-semibold shadow-2xs'"
                class="h-8 sm:h-9 w-8 sm:w-9 rounded-xl border text-xs flex items-center justify-center transition-all cursor-pointer active:scale-95">
                {{ p }}
              </button>
            </template>

            <!-- Next Button -->
            <button 
              @click="nextPage"
              :disabled="currentPage === totalPages"
              :class="currentPage === totalPages 
                ? 'opacity-40 cursor-not-allowed text-slate-300 border-slate-100 bg-slate-50' 
                : 'hover:bg-purple-50 text-slate-700 hover:text-[#6c3fb8] border-slate-200 hover:border-purple-200 bg-white cursor-pointer active:scale-95 shadow-2xs'"
              class="h-8 sm:h-9 px-2.5 sm:px-3 rounded-xl border text-xs font-semibold flex items-center gap-1 transition-all"
              title="Trang sau">
              <span class="hidden sm:inline">Sau</span>
              <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </button>
          </div>

        </div>

      </div>

      <!-- Empty State -->
      <div 
        v-else 
        class="bg-white rounded-2xl border border-slate-200 p-8 sm:p-10 text-center space-y-3 shadow-2xs">
        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-purple-50 text-[#6c3fb8] flex items-center justify-center text-xl mx-auto">
          <i class="fa-solid fa-magnifying-glass"></i>
        </div>
        <h3 class="text-sm sm:text-base font-bold text-slate-800">
          Không tìm thấy việc làm phù hợp
        </h3>
        <p class="text-xs text-slate-500 max-w-md mx-auto">
          Không có tin tuyển dụng nào khớp với khu công nghiệp, ngành nghề hoặc địa bàn đã chọn. Bạn hãy thử chọn lại bộ lọc.
        </p>
        <button 
          @click="resetFilters"
          class="px-4 py-2 rounded-xl bg-[#6c3fb8] hover:bg-[#5b32a0] text-white font-bold text-xs transition-colors cursor-pointer shadow-xs">
          Xem tất cả việc làm tại Ninh Bình
        </button>
      </div>

      <!-- Bottom Support & Hotline Banner (Mobile Stacked) -->
      <div class="mt-6 sm:mt-8 bg-gradient-to-r from-blue-50 via-purple-50 to-indigo-50 border border-purple-100/80 rounded-2xl p-3.5 sm:p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-3.5 sm:gap-4 shadow-2xs">
        <div class="flex items-center gap-3 sm:gap-3.5">
          <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-br from-[#853AFF] to-[#6C5FFF] text-white flex items-center justify-center text-base sm:text-lg shadow-sm flex-shrink-0">
            <i class="fa-solid fa-headset"></i>
          </div>
          <div>
            <h4 class="font-bold text-slate-800 text-xs sm:text-sm">Trung tâm Dịch vụ Việc làm — Sở LĐ-TB&XH Tỉnh Ninh Bình</h4>
            <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Hỗ trợ kết nối thông tin việc làm và tiếp nhận hồ sơ học sinh, sinh viên các cơ sở GDNN</p>
          </div>
        </div>
        <div class="flex flex-col sm:flex-row w-full md:w-auto items-stretch sm:items-center gap-2 sm:gap-3 flex-shrink-0">
          <a href="tel:02293868686" class="px-4 py-2 rounded-xl bg-white border border-purple-200 text-[#6c3fb8] hover:bg-purple-50 font-bold text-xs shadow-2xs transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-phone"></i>
            <span>0229.386.8686</span>
          </a>
          <router-link to="/admin" class="px-4 py-2 rounded-xl bg-[#6c3fb8] hover:bg-[#582da0] text-white font-bold text-xs shadow-xs transition-all flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-building"></i>
            <span>Dành cho Nhà tuyển dụng</span>
          </router-link>
        </div>
      </div>

    </main>

  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { jobService } from '../../services/jobService'
import JobCard from '../../components/job/JobCard.vue'

const router = useRouter()

const searchQuery = ref('')
const selectedIndustry = ref('all')
const selectedZone = ref('all')
const filterMode = ref('zone')
const selectedTab = ref('all')

// Pagination State (Always 9 jobs per page: 3 rows of 3 columns)
const currentPage = ref(1)
const pageSize = ref(9)

// Horizontal Pills scroll ref
const pillsContainerRef = ref(null)

// Filter Mode Dropdown State (KCN vs Nganh nghe)
const isFilterModeMenuOpen = ref(false)
const filterModeDropdownRef = ref(null)

function setFilterMode(mode) {
  filterMode.value = mode
  isFilterModeMenuOpen.value = false
  nextTick(() => {
    if (pillsContainerRef.value) {
      pillsContainerRef.value.scrollLeft = 0
    }
  })
}

// Industry Dropdown State
const isIndustryMenuOpen = ref(false)
const industryDropdownRef = ref(null)

function toggleIndustryMenu() {
  isIndustryMenuOpen.value = !isIndustryMenuOpen.value
}

function selectIndustry(id) {
  selectedIndustry.value = id
  isIndustryMenuOpen.value = false
  scrollToJobs()
}

function clearIndustry() {
  selectedIndustry.value = 'all'
}

const selectedIndustryLabel = computed(() => {
  if (selectedIndustry.value === 'all') return 'Tất cả ngành nghề'
  const matched = industries.find(i => i.id === selectedIndustry.value)
  return matched ? matched.name : 'Tất cả ngành nghề'
})

const industries = jobService.getIndustries()
const initialJobs = jobService.getAll()
const initialJobsCount = initialJobs.length

const jobTabs = [
  { id: 'all', label: 'Tất cả' },
  { id: 'hot', label: '⚡ Tuyển gấp' },
  { id: 'high_salary', label: '💰 Lương cao (>15Tr)' },
  { id: 'gian_khau', label: 'KCN Gián Khẩu' },
  { id: 'phuc_son', label: 'KCN Phúc Sơn' },
  { id: 'tam_diep', label: 'TP. Tam Điệp' }
]

const industrialZones = [
  { name: 'KCN Gián Khẩu (Gia Viễn)', shortName: 'KCN Gián Khẩu', keyword: 'Gián Khẩu', count: 4 },
  { name: 'KCN Phúc Sơn (TP. Ninh Bình)', shortName: 'KCN Phúc Sơn', keyword: 'Phúc Sơn', count: 3 },
  { name: 'KCN Khánh Phú (Yên Khánh)', shortName: 'KCN Khánh Phú', keyword: 'Khánh Phú', count: 3 },
  { name: 'KCN Tam Điệp (TP. Tam Điệp)', shortName: 'KCN Tam Điệp', keyword: 'Tam Điệp', count: 3 },
  { name: 'Cụm Công nghiệp Kim Sơn', shortName: 'CCN Kim Sơn', keyword: 'Kim Sơn', count: 2 }
]

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

function handleClickOutside(e) {
  if (industryDropdownRef.value && !industryDropdownRef.value.contains(e.target)) {
    isIndustryMenuOpen.value = false
  }
  if (filterModeDropdownRef.value && !filterModeDropdownRef.value.contains(e.target)) {
    isFilterModeMenuOpen.value = false
  }
}

const filteredJobs = computed(() => {
  let list = jobService.filterJobs({
    search: searchQuery.value,
    wardCode: 'all',
    industry: selectedIndustry.value
  })

  // Industrial Zone filter
  if (selectedZone.value !== 'all') {
    const zk = selectedZone.value.toLowerCase()
    list = list.filter(j => 
      j.address.toLowerCase().includes(zk) || 
      j.company.toLowerCase().includes(zk) ||
      j.title.toLowerCase().includes(zk)
    )
  }

  // Tab filter
  if (selectedTab.value === 'hot') {
    list = list.filter(j => j.is_hot)
  } else if (selectedTab.value === 'high_salary') {
    list = list.filter(j => j.salary_level === 'above_15' || j.salary_level === '10_15')
  } else if (selectedTab.value === 'gian_khau') {
    list = list.filter(j => j.address.includes('Gián Khẩu') || j.company.includes('Hyundai'))
  } else if (selectedTab.value === 'phuc_son') {
    list = list.filter(j => j.address.includes('Phúc Sơn') || j.company.includes('Mcnex'))
  } else if (selectedTab.value === 'tam_diep') {
    list = list.filter(j => j.address.includes('Tam Điệp') || j.company.includes('Đồng Giao') || j.company.includes('Doveco'))
  }

  return list
})

// Pagination Computed Properties
const totalPages = computed(() => {
  return Math.ceil(filteredJobs.value.length / pageSize.value) || 1
})

const paginatedJobs = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value
  return filteredJobs.value.slice(start, start + pageSize.value)
})

const displayedPages = computed(() => {
  const total = totalPages.value
  const current = currentPage.value
  if (total <= 7) {
    return Array.from({ length: total }, (_, i) => i + 1)
  }
  if (current <= 3) {
    return [1, 2, 3, 4, '...', total]
  } else if (current >= total - 2) {
    return [1, '...', total - 3, total - 2, total - 1, total]
  } else {
    return [1, '...', current - 1, current, current + 1, '...', total]
  }
})

function goToPage(page) {
  if (page < 1 || page > totalPages.value || page === currentPage.value) return
  currentPage.value = page
  scrollToJobs()
}

function prevPage() {
  if (currentPage.value > 1) {
    goToPage(currentPage.value - 1)
  }
}

function nextPage() {
  if (currentPage.value < totalPages.value) {
    goToPage(currentPage.value + 1)
  }
}

// Reset page to 1 whenever any filter criteria changes
watch([searchQuery, selectedIndustry, selectedZone, selectedTab], () => {
  currentPage.value = 1
})

// Keep currentPage within bounds if totalPages shrinks
watch(totalPages, (newTotal) => {
  if (currentPage.value > newTotal) {
    currentPage.value = Math.max(1, newTotal)
  }
})

const hasActiveFilter = computed(() => {
  return searchQuery.value || 
         selectedIndustry.value !== 'all' || 
         selectedZone.value !== 'all' ||
         selectedTab.value !== 'all'
})

function scrollPills(direction) {
  if (pillsContainerRef.value) {
    pillsContainerRef.value.scrollBy({
      left: direction === 'left' ? -220 : 220,
      behavior: 'smooth'
    })
  }
}

function filterByZone(keyword) {
  selectedZone.value = keyword
  scrollToJobs()
}

function resetFilters() {
  searchQuery.value = ''
  selectedIndustry.value = 'all'
  selectedZone.value = 'all'
  selectedTab.value = 'all'
  currentPage.value = 1
}

function goToJobDetail(job) {
  if (job?.id) {
    router.push(`/tuyen-dung/${job.id}`)
  }
}

function scrollToJobs() {
  const el = document.getElementById('job-listings-section')
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
  }
}
</script>
