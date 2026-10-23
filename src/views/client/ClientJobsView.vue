<template>
  <div class="w-full h-full overflow-y-auto overflow-x-hidden bg-slate-50 flex flex-col font-sans select-text" @scroll.passive="closeQuickViewModal">
    
    <!-- Hero Search Section (System-Cohesive Light Theme) -->
    <section class="relative z-20 overflow-visible border-b border-slate-200/90 bg-gradient-to-b from-blue-50/70 via-indigo-50/30 to-slate-50 pt-6 sm:pt-9 pb-7 sm:pb-11 px-3 sm:px-6 flex-shrink-0">
      <div class="absolute inset-0 opacity-40 bg-[radial-gradient(#93c5fd_1px,transparent_1px)] [background-size:20px_20px] pointer-events-none"></div>

      <div class="relative max-w-5xl mx-auto text-center space-y-2.5 sm:space-y-3">
        <!-- Badge -->
        <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-white/95 text-blue-700 text-[11px] sm:text-xs font-bold shadow-2xs border border-blue-200/80 backdrop-blur-xs">
          <i class="fa-solid fa-briefcase text-blue-600"></i>
          <span>{{ selectedCareerField ? `${selectedCareerFieldJobCount} vị trí đang tuyển thuộc ${selectedCareerField.name}` : 'Kết nối nhu cầu doanh nghiệp với chương trình đào tạo GDNN' }}</span>
        </div>

        <button v-if="selectedCareerField" type="button" class="mx-auto flex w-fit items-center gap-2 rounded-xl px-3 py-2 text-xs font-bold text-blue-700 transition hover:bg-blue-100/70" @click="backToCareerFields">
          <i class="fa-solid fa-arrow-left"></i>
          Quay lại danh sách lĩnh vực
        </button>

        <!-- Headline (System Standard text-slate-900 typography) -->
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
          {{ selectedCareerField ? `Việc làm lĩnh vực ${selectedCareerField.name}` : 'Khám phá lĩnh vực nghề nghiệp phù hợp với bạn' }}
        </h1>
        <p class="text-xs sm:text-sm md:text-base text-slate-600 max-w-2xl mx-auto px-1 leading-relaxed">
          {{ selectedCareerField
            ? 'Danh sách các vị trí tuyển dụng cụ thể phù hợp với lĩnh vực đã chọn.'
            : 'Tra cứu nhu cầu nhân lực, kỹ năng cần học và các cơ sở giáo dục nghề nghiệp có chương trình đào tạo phù hợp.' }}
        </p>

        <!-- Unified Search & Filter Box (Gộp chung thanh tìm kiếm & bộ lọc) -->
        <div class="pt-2 sm:pt-3 max-w-5xl mx-auto">
          <div class="bg-white rounded-2xl shadow-sm hover:shadow-soft transition-shadow p-2 sm:p-2.5 border border-slate-200/90 text-left">
            
            <div v-if="selectedCareerField" class="flex flex-col md:flex-row items-stretch md:items-center gap-2 sm:gap-2.5">
              
              <!-- 1. Keyword Input -->
              <div class="flex-1 w-full relative flex items-center min-w-0">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm">
                  <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input 
                  v-model="searchQuery"
                  @keyup.enter="scrollToJobs"
                  type="text" 
                  placeholder="Vị trí tuyển dụng, tên công việc, công ty..."
                  class="w-full h-11 pl-10 pr-8 text-xs sm:text-sm font-medium text-slate-800 placeholder:text-slate-400 bg-slate-50/70 hover:bg-slate-100/70 focus:bg-white rounded-xl border border-slate-200/90 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all shadow-2xs"
                />
                <button 
                  v-if="searchQuery"
                  @click="searchQuery = ''"
                  class="absolute right-2.5 text-slate-400 hover:text-slate-600 w-5 h-5 flex items-center justify-center cursor-pointer"
                  title="Xóa tìm kiếm">
                  <i class="fa-solid fa-xmark text-xs"></i>
                </button>
              </div>

              <div class="hidden md:block w-px h-8 bg-slate-200 flex-shrink-0"></div>

              <!-- 2. Ward/Commune Custom Popover Dropdown (Khu vực phường xã trong DB) -->
              <div ref="wardDropdownRef" class="w-full md:w-56 lg:w-64 relative flex-shrink-0">
                <!-- Trigger Button -->
                <button
                  type="button"
                  @click="toggleWardMenu"
                  class="w-full h-11 pl-10 pr-8 text-xs sm:text-sm font-medium rounded-xl border transition-all cursor-pointer flex items-center justify-between text-left shadow-2xs select-none relative"
                  :class="isWardMenuOpen 
                    ? 'bg-white border-blue-400 ring-2 ring-blue-100 text-blue-700' 
                    : (selectedWard !== 'all' 
                        ? 'bg-blue-50/40 border-blue-200 font-bold text-blue-700' 
                        : 'bg-slate-50/70 hover:bg-slate-100/70 border-slate-200/90 text-slate-700')">
                  
                  <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-blue-600 text-xs">
                    <i class="fa-solid fa-map-pin"></i>
                  </div>
                  
                  <span class="truncate block pr-2">{{ selectedWardLabel }}</span>

                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5">
                    <span 
                      v-if="selectedWard !== 'all'"
                      @click.stop="clearWard"
                      class="w-4 h-4 rounded-full text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors cursor-pointer"
                      title="Bỏ chọn xã/phường">
                      <i class="fa-solid fa-xmark text-[11px]"></i>
                    </span>
                    <i 
                      class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200 pointer-events-none"
                      :class="{ 'rotate-180 text-blue-600': isWardMenuOpen }"></i>
                  </div>
                </button>

                <!-- Searchable Popover Dropdown with Ward List -->
                <div 
                  v-if="isWardMenuOpen"
                  class="absolute left-0 right-0 md:left-0 md:right-auto md:w-72 top-full mt-1.5 w-full bg-white border border-slate-200 rounded-2xl shadow-xl p-2 z-[60] animate-in fade-in select-none">
                  
                  <!-- Mini Search Input for quick ward finding -->
                  <div class="relative mb-1.5">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    <input 
                      v-model="wardSearchQuery"
                      type="text"
                      placeholder="Tìm xã, phường, thị trấn..."
                      class="w-full pl-8 pr-7 py-1.5 bg-slate-100/90 hover:bg-slate-100 focus:bg-white text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-200 outline-none transition-all placeholder:text-slate-400"
                    />
                    <button 
                      v-if="wardSearchQuery"
                      @click="wardSearchQuery = ''"
                      class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs p-1">
                      <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                  </div>

                  <div class="max-h-60 overflow-y-auto space-y-0.5 pr-1 no-scrollbar">
                    <!-- "All Wards" option -->
                    <div 
                      @click="selectWard('all')"
                      :class="selectedWard === 'all' ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50'"
                      class="px-2.5 py-2 rounded-xl text-xs cursor-pointer flex items-center justify-between transition-colors">
                      <div class="flex items-center gap-2">
                        <i class="fa-solid fa-map-location-dot text-blue-600 text-xs"></i>
                        <span>Tất cả xã / phường ({{ wards.length }})</span>
                      </div>
                      <i v-if="selectedWard === 'all'" class="fa-solid fa-check text-xs text-blue-600"></i>
                    </div>

                    <div class="h-px bg-slate-100 my-1"></div>

                    <!-- Filtered Wards List -->
                    <div 
                      v-for="(w, idx) in filteredWardsList"
                      :key="typeof w === 'object' ? (w.id || w.code || idx) : idx"
                      @click="selectWard(w)"
                      :class="isWardSelected(w) ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50'"
                      class="px-2.5 py-1.5 rounded-xl text-xs cursor-pointer flex items-center justify-between transition-colors group">
                      <div class="flex items-center gap-2 min-w-0 pr-1">
                        <span 
                          class="text-[10px] px-1.5 py-0.5 rounded font-semibold flex-shrink-0"
                          :class="(typeof w === 'object' && w.unit_type === 'Phường') ? 'bg-blue-100 text-blue-700' : ((typeof w === 'object' && w.unit_type === 'Thị trấn') ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600')">
                          {{ typeof w === 'object' ? (w.unit_type || 'Xã') : (w.startsWith('Phường') ? 'Phường' : (w.startsWith('Thị trấn') ? 'Thị trấn' : 'Xã')) }}
                        </span>
                        <span class="truncate font-medium group-hover:text-blue-700" :title="getWardDisplayName(w)">
                          {{ getWardDisplayName(w) }}
                        </span>
                      </div>
                      <i v-if="isWardSelected(w)" class="fa-solid fa-check text-xs text-blue-600 flex-shrink-0 ml-1"></i>
                    </div>

                    <div v-if="filteredWardsList.length === 0" class="py-3 text-center text-xs text-slate-400">
                      Không tìm thấy xã/phường khớp với "{{ wardSearchQuery }}"
                    </div>
                  </div>

                </div>
              </div>

              <div class="hidden md:block w-px h-8 bg-slate-200 flex-shrink-0"></div>

              <!-- 3. Skill Custom Popover Dropdown (Kỹ năng chuyên ngành dạng select) -->
              <div ref="skillDropdownRef" class="w-full md:w-52 lg:w-60 relative flex-shrink-0">
                <!-- Trigger Button -->
                <button
                  type="button"
                  @click="toggleSkillMenu"
                  class="w-full h-11 pl-10 pr-8 text-xs sm:text-sm font-medium rounded-xl border transition-all cursor-pointer flex items-center justify-between text-left shadow-2xs select-none relative"
                  :class="isSkillMenuOpen 
                    ? 'bg-white border-indigo-400 ring-2 ring-indigo-100 text-indigo-700' 
                    : (selectedSkill !== 'all' 
                        ? 'bg-indigo-50/40 border-indigo-200 font-bold text-indigo-700' 
                        : 'bg-slate-50/70 hover:bg-slate-100/70 border-slate-200/90 text-slate-700')">
                  
                  <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-600 text-xs">
                    <i class="fa-solid fa-graduation-cap"></i>
                  </div>
                  
                  <span class="truncate block pr-2">{{ selectedSkillLabel }}</span>

                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5">
                    <span 
                      v-if="selectedSkill !== 'all'"
                      @click.stop="clearSkill"
                      class="w-4 h-4 rounded-full text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors cursor-pointer"
                      title="Bỏ chọn kỹ năng">
                      <i class="fa-solid fa-xmark text-[11px]"></i>
                    </span>
                    <i 
                      class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200 pointer-events-none"
                      :class="{ 'rotate-180 text-indigo-600': isSkillMenuOpen }"></i>
                  </div>
                </button>

                <!-- Searchable Popover Dropdown with Skill List -->
                <div 
                  v-if="isSkillMenuOpen"
                  class="absolute left-0 right-0 md:left-auto md:right-0 md:w-72 top-full mt-1.5 w-full bg-white border border-slate-200 rounded-2xl shadow-xl p-2 z-[60] animate-in fade-in select-none">
                  
                  <!-- Mini Search Input for quick skill finding -->
                  <div class="relative mb-1.5">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    <input 
                      v-model="skillSearchQuery"
                      type="text"
                      placeholder="Tìm kỹ năng chuyên ngành..."
                      class="w-full pl-8 pr-7 py-1.5 bg-slate-100/90 hover:bg-slate-100 focus:bg-white text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-200 outline-none transition-all placeholder:text-slate-400"
                    />
                    <button 
                      v-if="skillSearchQuery"
                      @click="skillSearchQuery = ''"
                      class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs p-1">
                      <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                  </div>

                  <div class="max-h-60 overflow-y-auto space-y-0.5 pr-1 no-scrollbar">
                    <!-- "All Skills" option -->
                    <div 
                      @click="selectSkill('all')"
                      :class="selectedSkill === 'all' ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-50'"
                      class="px-2.5 py-2 rounded-xl text-xs cursor-pointer flex items-center justify-between transition-colors">
                      <div class="flex items-center gap-2">
                        <i class="fa-solid fa-graduation-cap text-indigo-600 text-xs"></i>
                        <span>Tất cả kỹ năng</span>
                      </div>
                      <i v-if="selectedSkill === 'all'" class="fa-solid fa-check text-xs text-indigo-600"></i>
                    </div>

                    <div class="h-px bg-slate-100 my-1"></div>

                    <!-- Filtered Skills List -->
                    <div 
                      v-for="s in filteredAvailableSkills"
                      :key="s"
                      @click="selectSkill(s)"
                      :class="selectedSkill === s ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-50'"
                      class="px-2.5 py-1.5 rounded-xl text-xs cursor-pointer flex items-center justify-between transition-colors group">
                      <div class="flex items-center gap-2 min-w-0">
                        <i class="fa-solid fa-certificate text-[10px] flex-shrink-0 text-slate-400 group-hover:text-indigo-600"></i>
                        <span class="truncate group-hover:text-indigo-700">{{ s }}</span>
                      </div>
                      <i v-if="selectedSkill === s" class="fa-solid fa-check text-xs text-indigo-600 flex-shrink-0 ml-1"></i>
                    </div>

                    <div v-if="filteredAvailableSkills.length === 0" class="py-3 text-center text-xs text-slate-400">
                      Không tìm thấy kỹ năng phù hợp
                    </div>
                  </div>

                </div>
              </div>

              <!-- 4. Search CTA Button (System Primary Blue / Indigo gradient) -->
              <button 
                type="button"
                @click="scrollToJobs"
                class="w-full md:w-auto h-11 px-6 sm:px-7 rounded-xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 hover:opacity-95 text-white font-bold text-xs sm:text-sm shadow-xs hover:shadow-soft transition-all flex items-center justify-center gap-2 flex-shrink-0 cursor-pointer active:scale-95">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                <span>Tìm việc ngay</span>
              </button>

            </div>

            <div v-else class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-2.5">
              <div class="relative flex min-w-0 flex-1 items-center">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 text-sm text-slate-400"></i>
                <input
                  v-model="professionSearchQuery"
                  type="text"
                  placeholder="Tên lĩnh vực, nghề nghiệp, kỹ năng hoặc cơ sở..."
                  class="h-11 w-full rounded-xl border border-slate-200/90 bg-slate-50/70 pl-10 pr-9 text-xs font-medium text-slate-800 shadow-2xs outline-none transition-all placeholder:text-slate-400 hover:bg-slate-100/70 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 sm:text-sm"
                  @keyup.enter="scrollToJobs"
                />
                <button v-if="professionSearchQuery" type="button" class="absolute right-2.5 flex h-6 w-6 items-center justify-center text-slate-400 transition hover:text-slate-600" title="Xóa tìm kiếm" @click="professionSearchQuery = ''">
                  <i class="fa-solid fa-xmark text-xs"></i>
                </button>
              </div>

              <div
                ref="careerFieldDropdownRef"
                class="relative w-full shrink-0 sm:w-72"
                @keydown.esc="isCareerFieldMenuOpen = false"
              >
                <button
                  type="button"
                  class="relative flex h-11 w-full items-center rounded-xl border pl-10 pr-9 text-left text-xs font-semibold shadow-2xs outline-none transition sm:text-sm"
                  :class="isCareerFieldMenuOpen
                    ? 'border-blue-400 bg-white text-blue-700 ring-2 ring-blue-100'
                    : selectedCareerFieldFilter !== 'all'
                      ? 'border-blue-200 bg-blue-50/40 text-blue-700'
                      : 'border-slate-200/90 bg-slate-50/70 text-slate-700 hover:bg-slate-100/70'"
                  :aria-expanded="isCareerFieldMenuOpen"
                  aria-haspopup="listbox"
                  @click="toggleCareerFieldMenu"
                >
                  <i class="fa-solid fa-layer-group pointer-events-none absolute left-3.5 text-xs text-blue-600"></i>
                  <span class="block min-w-0 flex-1 truncate">{{ selectedCareerFieldFilterLabel }}</span>
                  <i
                    class="fa-solid fa-chevron-down pointer-events-none absolute right-3 text-xs text-slate-400 transition-transform duration-200"
                    :class="{ 'rotate-180 text-blue-600': isCareerFieldMenuOpen }"
                  ></i>
                </button>

                <div
                  v-if="isCareerFieldMenuOpen"
                  class="absolute left-0 right-0 top-full z-[70] mt-1.5 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl"
                  role="listbox"
                >
                  <div class="max-h-72 space-y-0.5 overflow-y-auto pr-1">
                    <button
                      type="button"
                      role="option"
                      class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-left text-xs transition sm:text-sm"
                      :class="selectedCareerFieldFilter === 'all' ? 'bg-blue-50 font-bold text-blue-700' : 'font-medium text-slate-700 hover:bg-slate-50'"
                      :aria-selected="selectedCareerFieldFilter === 'all'"
                      @click="selectCareerFieldFilter('all')"
                    >
                      <span class="truncate">Tất cả lĩnh vực nghề nghiệp</span>
                      <i v-if="selectedCareerFieldFilter === 'all'" class="fa-solid fa-check shrink-0 text-xs text-blue-600"></i>
                    </button>

                    <div class="my-1 h-px bg-slate-100"></div>

                    <button
                      v-for="field in careerFieldOptions"
                      :key="field.id"
                      type="button"
                      role="option"
                      class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2 text-left text-xs transition sm:text-sm"
                      :class="selectedCareerFieldFilter === field.id ? 'bg-blue-50 font-bold text-blue-700' : 'font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-700'"
                      :aria-selected="selectedCareerFieldFilter === field.id"
                      :title="field.name"
                      @click="selectCareerFieldFilter(field.id)"
                    >
                      <span class="min-w-0 leading-snug">{{ field.name }}</span>
                      <i v-if="selectedCareerFieldFilter === field.id" class="fa-solid fa-check shrink-0 text-xs text-blue-600"></i>
                    </button>
                  </div>
                </div>
              </div>

              <button type="button" class="flex h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-6 text-xs font-bold text-white shadow-xs transition-all hover:opacity-95 active:scale-95 sm:w-auto sm:text-sm" @click="scrollToJobs">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                Tìm ngành phù hợp
              </button>
            </div>

          </div>
        </div>

      </div>
    </section>

    <!-- 3. Main Content: Full-Width 3-Column Layout with Horizontal Zone Selector (Vieclam24h Exact Style) -->
    <main id="job-listings-section" class="max-w-7xl w-full mx-auto px-3 sm:px-6 py-4 sm:py-8 flex-1">

      <ProfessionExplorer
        v-if="!selectedCareerField"
        :professions="filteredCareerFields"
        :period="professionPortal.period"
        :loading="isProfessionLoading"
        :error="professionError"
        @view-school="openSchoolDetailFromQuickView"
        @view-jobs="openCareerFieldJobs"
        @reload="loadProfessionData"
      />

      <!-- Tầng 2: hai cột việc làm và một cột cơ sở đào tạo -->
      <div v-else class="grid w-full grid-cols-1 items-start gap-5 xl:grid-cols-3">
        <section class="space-y-6 xl:col-span-2">
          <template v-if="filteredJobs.length > 0">
            <div class="grid grid-cols-1 gap-3.5 md:grid-cols-2 sm:gap-5">
              <JobCard
                v-for="job in paginatedJobs"
                :key="job.id"
                :job="job"
                :preview-active="quickViewJob?.id === job.id"
                @select="goToJobDetail($event)"
                @quick-view="openQuickViewModal($event)"
              />
            </div>

            <div
              v-if="totalPages > 1"
              class="flex flex-col items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-3 shadow-2xs select-none sm:flex-row sm:gap-4 sm:p-4">
              <div class="order-2 text-center text-xs text-slate-500 sm:order-1 sm:text-left">
                <span class="sm:hidden">
                  <span class="font-bold text-slate-800">{{ (currentPage - 1) * pageSize + 1 }} - {{ Math.min(currentPage * pageSize, filteredJobs.length) }}</span>
                  <span class="text-slate-400"> / </span>
                  <span class="font-bold text-blue-600">{{ filteredJobs.length }}</span> việc làm
                </span>
                <span class="hidden sm:inline">
                  Hiển thị <span class="font-bold text-slate-800">{{ (currentPage - 1) * pageSize + 1 }} - {{ Math.min(currentPage * pageSize, filteredJobs.length) }}</span>
                  trên <span class="font-bold text-blue-600">{{ filteredJobs.length }}</span> việc làm
                </span>
              </div>

              <div class="order-1 flex items-center gap-1 sm:order-2 sm:gap-1.5">
                <button
                  @click="prevPage"
                  :disabled="currentPage === 1"
                  :class="currentPage === 1
                    ? 'opacity-40 cursor-not-allowed text-slate-300 border-slate-100 bg-slate-50'
                    : 'hover:bg-blue-50 text-slate-700 hover:text-blue-700 border-slate-200 hover:border-blue-200 bg-white cursor-pointer active:scale-95 shadow-2xs'"
                  class="flex h-8 items-center gap-1 rounded-xl border px-2.5 text-xs font-semibold transition-all sm:h-9 sm:px-3"
                  title="Trang trước">
                  <i class="fa-solid fa-chevron-left text-[10px]"></i>
                  <span class="hidden sm:inline">Trước</span>
                </button>

                <template v-for="(p, idx) in displayedPages" :key="idx">
                  <span v-if="p === '...'" class="flex h-8 w-6 items-center justify-center text-xs font-bold text-slate-400 sm:h-9 sm:w-8">...</span>
                  <button
                    v-else
                    @click="goToPage(p)"
                    :class="currentPage === p
                      ? 'bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white font-bold shadow-xs border-transparent'
                      : 'bg-white hover:bg-blue-50 text-slate-700 hover:text-blue-700 border-slate-200 hover:border-blue-200 font-semibold shadow-2xs'"
                    class="flex h-8 w-8 items-center justify-center rounded-xl border text-xs transition-all active:scale-95 cursor-pointer sm:h-9 sm:w-9">
                    {{ p }}
                  </button>
                </template>

                <button
                  @click="nextPage"
                  :disabled="currentPage === totalPages"
                  :class="currentPage === totalPages
                    ? 'opacity-40 cursor-not-allowed text-slate-300 border-slate-100 bg-slate-50'
                    : 'hover:bg-blue-50 text-slate-700 hover:text-blue-700 border-slate-200 hover:border-blue-200 bg-white cursor-pointer active:scale-95 shadow-2xs'"
                  class="flex h-8 items-center gap-1 rounded-xl border px-2.5 text-xs font-semibold transition-all sm:h-9 sm:px-3"
                  title="Trang sau">
                  <span class="hidden sm:inline">Sau</span>
                  <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </button>
              </div>
            </div>
          </template>

          <div v-else class="space-y-3 rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-2xs sm:p-10">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-xl text-blue-600 sm:h-14 sm:w-14">
              <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-800 sm:text-base">Chưa có vị trí tuyển dụng cụ thể trong lĩnh vực {{ selectedCareerField.name }}</h3>
            <p class="mx-auto max-w-md text-xs text-slate-500">Vị trí chỉ hiển thị khi doanh nghiệp đã đăng tin tuyển dụng đầy đủ.</p>
            <button @click="resetFilters" class="rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white shadow-xs transition-colors hover:bg-blue-700 cursor-pointer">Xóa bộ lọc</button>
          </div>
        </section>

        <aside class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xs xl:sticky xl:top-24">
          <div class="flex items-start justify-between gap-3 border-b border-slate-100 bg-slate-50/80 px-4 py-3.5">
            <div class="min-w-0">
              <h3 class="flex items-center gap-2 text-sm font-black text-slate-900">
                <i class="fa-solid fa-graduation-cap text-blue-600"></i>
                <span>Cơ sở đào tạo</span>
              </h3>
              <p class="mt-1 line-clamp-2 text-[11px] leading-relaxed text-slate-500">Các cơ sở có ngành/nghề thuộc lĩnh vực {{ selectedCareerField.name }}</p>
            </div>
            <span class="shrink-0 rounded-lg bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">{{ schoolSearchResultLabel }}</span>
          </div>

          <div v-if="selectedCareerField.schools?.length" class="border-b border-slate-100 p-3">
            <div class="relative">
              <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
              <input
                :value="schoolSearchQuery"
                type="text"
                placeholder="Tìm tên hoặc địa điểm cơ sở..."
                class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-9 text-xs font-medium text-slate-800 outline-none transition placeholder:text-slate-400 hover:bg-slate-100/70 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                @input="handleSchoolSearchInput"
                @keydown.esc.prevent="schoolSearchQuery = ''"
              />
              <button
                v-if="schoolSearchQuery"
                type="button"
                class="absolute right-2 top-1/2 flex h-6 w-6 -translate-y-1/2 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                title="Xóa tìm kiếm"
                @click="schoolSearchQuery = ''"
              >
                <i class="fa-solid fa-xmark text-xs"></i>
              </button>
            </div>
          </div>

          <div v-if="filteredSelectedCareerFieldSchools.length" class="max-h-[360px] space-y-2 overflow-y-auto overscroll-contain p-3 pr-2">
            <button
              v-for="school in filteredSelectedCareerFieldSchools"
              :key="school.id || school.name"
              type="button"
              :disabled="isSchoolDetailLoading"
              class="group flex w-full items-center gap-3 rounded-xl border border-slate-200 bg-white p-3 text-left transition hover:border-blue-200 hover:bg-blue-50/50 disabled:cursor-wait disabled:opacity-60"
              @click="openSchoolDetailFromQuickView(school)">
              <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                <i class="fa-solid fa-school text-xs"></i>
              </span>
              <span class="min-w-0 flex-1">
                <strong class="line-clamp-2 block text-xs leading-snug text-slate-800 transition group-hover:text-blue-700">{{ school.name }}</strong>
                <span class="mt-1 block truncate text-[10px] text-slate-500" :title="[school.level, school.location].filter(Boolean).join(' · ')">
                  {{ [school.level, school.location].filter(Boolean).join(' · ') || 'Đang cập nhật thông tin' }}
                </span>
              </span>
              <i class="fa-solid fa-chevron-right shrink-0 text-[10px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-blue-600"></i>
            </button>
          </div>

          <div v-else-if="selectedCareerField.schools?.length" class="px-5 py-8 text-center">
            <span class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
            <p class="mt-3 text-xs font-semibold text-slate-600">Không tìm thấy cơ sở phù hợp</p>
            <button type="button" class="mt-2 text-[11px] font-bold text-blue-600 hover:text-blue-700" @click="schoolSearchQuery = ''">Xóa từ khóa</button>
          </div>

          <div v-else class="px-5 py-8 text-center">
            <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-400"><i class="fa-solid fa-school"></i></span>
            <p class="mt-3 text-xs font-semibold text-slate-600">Chưa có cơ sở đào tạo phù hợp</p>
          </div>

          <p v-if="schoolDetailLookupMessage" class="mx-3 mb-3 rounded-lg border border-amber-100 bg-amber-50 px-3 py-2 text-[11px] text-amber-700">{{ schoolDetailLookupMessage }}</p>
        </aside>
      </div>

      <!-- Popup xem nhanh: neo cạnh tiêu đề của thẻ việc làm đang được hover -->
      <Teleport to="body">
        <Transition name="job-quick-preview">
          <aside
            v-if="quickViewJob"
            :key="quickViewJob.id"
            ref="quickViewPopupRef"
            :data-side="quickViewPlacement.side"
            :style="quickViewPopupStyle"
            class="job-quick-preview-panel fixed z-[4000] w-[min(38rem,calc(100vw-1.5rem))] select-text">
            <span
              aria-hidden="true"
              :style="quickViewArrowStyle"
              :class="quickViewPlacement.side === 'right' ? '-left-2' : '-right-2'"
              class="absolute z-0 h-4 w-4 rotate-45 border border-slate-200 bg-white shadow-sm"></span>

            <div class="relative z-10 flex max-h-[calc(100dvh-1.5rem)] flex-col overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-2xl">
          
          <!-- Modal Header: Tinh giản, trang nhã -->
          <div class="p-4 sm:p-5 border-b border-slate-100 flex items-start justify-between gap-3 bg-gradient-to-r from-blue-50/70 via-white to-indigo-50/30 flex-shrink-0">
            <div class="flex items-start gap-3 min-w-0 pr-2">
              <!-- Company Logo Box -->
              <div 
                :class="quickViewJob.company_color || 'bg-blue-700'"
                class="w-11 h-11 rounded-xl text-white font-black flex items-center justify-center flex-shrink-0 shadow-2xs border border-white/20 select-none text-xs p-1">
                <span class="truncate uppercase font-black">{{ quickViewJob.company_logo || 'DN' }}</span>
              </div>

              <div class="min-w-0">
                <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-snug line-clamp-1" :title="quickViewJob.title">
                  {{ quickViewJob.title }}
                </h3>
                <p class="text-xs text-slate-500 font-medium truncate mt-0.5" :title="quickViewJob.company">
                  {{ quickViewJob.company }}
                </p>
              </div>
            </div>

            <!-- Close Button -->
            <button 
              @click="closeQuickViewModal" 
              class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center cursor-pointer flex-shrink-0 transition-colors"
              title="Đóng cửa sổ">
              <i class="fa-solid fa-xmark text-sm"></i>
            </button>
          </div>

          <!-- Modal Body: Thông tin cô đọng, hiển thị trọn vẹn -->
          <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain p-4 text-xs sm:p-5">
            <div class="space-y-3.5">
            
            <!-- Highlight Row: Lương & Hạn nộp -->
            <div class="flex items-center justify-between gap-2 p-3 rounded-xl bg-emerald-50/70 border border-emerald-100/80">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-money-bill-wave text-emerald-600 text-sm"></i>
                <span class="text-sm sm:text-base font-extrabold text-emerald-700">
                  {{ quickViewJob.salary }}
                </span>
              </div>
              <div class="text-[11px] sm:text-xs text-slate-500 font-medium flex items-center gap-1.5 flex-shrink-0">
                <i class="fa-regular fa-clock text-slate-400"></i>
                <span>Hạn nộp: <strong class="text-slate-700">{{ quickViewJob.deadline || '30/10/2026' }}</strong></span>
              </div>
            </div>

            <!-- Thông số tuyển dụng: Cân đối hoàn hảo, thẳng hàng tuyệt đối -->
            <div class="rounded-xl border border-slate-200/80 bg-slate-50/70 divide-y divide-slate-200/70 text-xs overflow-hidden">
              <!-- Hàng 1: Nơi làm việc -->
              <div class="px-3.5 py-2.5 flex items-start sm:items-center gap-2.5">
                <i class="fa-solid fa-location-dot text-rose-500 w-4 text-center text-xs mt-0.5 sm:mt-0 flex-shrink-0"></i>
                <span class="font-bold text-slate-700 w-24 sm:w-28 flex-shrink-0">Nơi làm việc:</span>
                <span class="text-slate-800 font-medium leading-relaxed flex-1">
                  {{ quickViewJob.address || quickViewJob.ward_name || 'Ninh Bình' }}
                </span>
              </div>

              <!-- Hàng 2: Trình độ yêu cầu -->
              <div class="px-3.5 py-2.5 flex items-start sm:items-center gap-2.5">
                <i class="fa-solid fa-user-graduate text-indigo-600 w-4 text-center text-xs mt-0.5 sm:mt-0 flex-shrink-0"></i>
                <span class="font-bold text-slate-700 w-24 sm:w-28 flex-shrink-0">Trình độ:</span>
                <span class="text-slate-800 font-medium leading-relaxed flex-1">
                  {{ quickViewJob.education || 'Trung cấp / Cao đẳng' }}
                </span>
              </div>

              <!-- Hàng 3: Kinh nghiệm -->
              <div class="px-3.5 py-2.5 flex items-start sm:items-center gap-2.5">
                <i class="fa-solid fa-briefcase text-amber-600 w-4 text-center text-xs mt-0.5 sm:mt-0 flex-shrink-0"></i>
                <span class="font-bold text-slate-700 w-24 sm:w-28 flex-shrink-0">Kinh nghiệm:</span>
                <span class="text-slate-800 font-medium leading-relaxed flex-1">
                  {{ quickViewJob.experience || 'Không yêu cầu' }}
                </span>
              </div>

              <!-- Hàng 4: Nhu cầu tuyển dụng -->
              <div class="px-3.5 py-2.5 flex items-start sm:items-center gap-2.5">
                <i class="fa-solid fa-users text-blue-600 w-4 text-center text-xs mt-0.5 sm:mt-0 flex-shrink-0"></i>
                <span class="font-bold text-slate-700 w-24 sm:w-28 flex-shrink-0">Chỉ tiêu tuyển:</span>
                <span class="text-slate-800 font-medium leading-relaxed flex-1">
                  {{ quickViewJob.recruitment_count ? `${quickViewJob.recruitment_count} người` : 'Tuyển liên tục' }}
                </span>
              </div>
            </div>

            <!-- Khối 3: Kỹ năng yêu cầu (Cân đối, rõ ràng) -->
            <div v-if="quickViewJob.skills && quickViewJob.skills.length" class="rounded-xl border border-slate-200/80 bg-slate-50/70 p-3 space-y-2">
              <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-slate-800 flex items-center gap-1.5">
                  <i class="fa-solid fa-tags text-blue-600 text-xs"></i>
                  <span>Kỹ năng yêu cầu:</span>
                </span>
                <span class="text-[10.5px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100/80">
                  {{ quickViewJob.skills.length }} kỹ năng
                </span>
              </div>
              <div class="flex flex-wrap gap-1.5">
                <span 
                  v-for="s in quickViewJob.skills" 
                  :key="s"
                  class="px-2.5 py-1 rounded-lg bg-white text-slate-700 text-xs font-medium border border-slate-200/90 shadow-2xs">
                  {{ s }}
                </span>
              </div>
            </div>

            <!-- Khối 4: Nơi đào tạo kỹ năng cho việc này (Cân đối, đồng bộ) -->
            <div v-if="quickViewJob.training_schools && quickViewJob.training_schools.length" class="rounded-xl border border-slate-200/80 bg-slate-50/70 p-3 space-y-2">
              <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-slate-800 flex items-center gap-1.5">
                  <i class="fa-solid fa-graduation-cap text-indigo-600 text-xs"></i>
                  <span>Nơi đào tạo kỹ năng cho việc này:</span>
                </span>
                <span class="text-[10.5px] font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100/80">
                  {{ quickViewJob.training_schools.length }} cơ sở GDNN
                </span>
              </div>

              <!-- Danh sách trường nhóm trong 1 card phân chia divide-y -->
              <div class="rounded-xl border border-slate-200/80 bg-white divide-y divide-slate-100 overflow-hidden shadow-2xs">
                <div 
                  v-for="(school, sIdx) in quickViewJob.training_schools" 
                  :key="sIdx"
                  class="p-2.5 hover:bg-blue-50/60 transition-colors flex items-center justify-between gap-3 group">
                  <div class="flex items-center gap-2.5 min-w-0 pr-1">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center flex-shrink-0 text-xs group-hover:scale-105 transition-transform">
                      <i class="fa-solid fa-school"></i>
                    </div>
                    <div class="min-w-0">
                      <div class="font-bold text-xs text-slate-900 group-hover:text-blue-700 truncate" :title="school.name">
                        {{ school.name }}
                      </div>
                      <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1.5 truncate">
                        <span class="font-medium text-slate-600">{{ school.level }}</span>
                        <span class="text-slate-300">·</span>
                        <span class="text-slate-500">{{ school.district }}</span>
                      </div>
                    </div>
                  </div>

                  <button
                    type="button"
                    :disabled="isSchoolDetailLoading"
                    @click.stop="openSchoolDetailFromQuickView(school)"
                    class="px-2.5 py-1 rounded-lg bg-slate-50 hover:bg-blue-50 border border-slate-200/80 hover:border-blue-200 text-blue-600 font-bold text-xs flex items-center gap-1 flex-shrink-0 transition-all shadow-2xs disabled:cursor-wait disabled:opacity-60">
                    <i v-if="isSchoolDetailLoading" class="fa-solid fa-spinner animate-spin text-[9px]"></i>
                    <span>{{ isSchoolDetailLoading ? 'Đang mở' : 'Chi tiết' }}</span>
                    <i v-if="!isSchoolDetailLoading" class="fa-solid fa-angle-right text-[9px] transition-transform"></i>
                  </button>
                </div>
              </div>
              <p v-if="schoolDetailLookupMessage" class="text-[11px] text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-2.5 py-2">
                {{ schoolDetailLookupMessage }}
              </p>
            </div>

            </div>
          </div>

          <!-- Modal Footer: Gọn gàng, rõ ràng -->
          <div class="p-3.5 sm:p-4 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between gap-2.5 flex-shrink-0">
            <button 
              type="button"
              @click="closeQuickViewModal"
              class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold text-xs transition-colors cursor-pointer">
              Đóng
            </button>

            <button 
              type="button"
              @click="goToJobDetailFromQuickView(quickViewJob)"
              class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center gap-1.5 transition-all cursor-pointer shadow-xs active:scale-95">
              <span>Xem chi tiết việc làm</span>
              <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </button>
          </div>

            </div>
          </aside>
        </Transition>
      </Teleport>

    </main>

    <SchoolDetailDrawer
      v-if="selectedSchoolDetail"
      :school="selectedSchoolDetail"
      @close="selectedSchoolDetail = null"
      @view-related-campus="openSchoolDetailFromQuickView"
    />

    <ClientFooter class="mt-auto" />

  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { CAREER_FIELDS, jobService } from '../../services/jobService'
import { schoolService } from '../../services/schoolService'
import { supplyDemandService } from '../../services/supplyDemandService'
import JobCard from '../../components/job/JobCard.vue'
import ProfessionExplorer from '../../components/job/ProfessionExplorer.vue'
import SchoolDetailDrawer from '../../components/school/SchoolDetailDrawer.vue'

const router = useRouter()

const selectedSchoolDetail = ref(null)
const isSchoolDetailLoading = ref(false)
const schoolDetailLookupMessage = ref('')
const quickViewJob = ref(null)
const quickViewAnchor = ref(null)
const quickViewPopupRef = ref(null)
const quickViewPlacement = ref({
  top: 12,
  left: 12,
  side: 'right',
  arrowTop: 40,
})
const selectedCareerField = ref(null)

const professionPortal = ref({
  period: null,
  totals: { demand: 0, capacity: 0, gap: 0, proposed: 0 },
  professions: [],
})
const professionSearchQuery = ref('')
const selectedCareerFieldFilter = ref('all')
const careerFieldOptions = CAREER_FIELDS
const isCareerFieldMenuOpen = ref(false)
const careerFieldDropdownRef = ref(null)
const schoolSearchQuery = ref('')
const isProfessionLoading = ref(false)
const professionError = ref('')

const quickViewPopupStyle = computed(() => ({
  top: `${quickViewPlacement.value.top}px`,
  left: `${quickViewPlacement.value.left}px`,
  '--quick-preview-enter-x': quickViewPlacement.value.side === 'right' ? '-14px' : '14px',
  transformOrigin: quickViewPlacement.value.side === 'right' ? 'left center' : 'right center',
}))

const quickViewArrowStyle = computed(() => ({
  top: `${quickViewPlacement.value.arrowTop}px`,
}))

const searchQuery = ref('')
const selectedWard = ref('all')
const selectedSkill = ref('all')

const wards = ref([])
const isWardMenuOpen = ref(false)
const wardDropdownRef = ref(null)
const wardSearchQuery = ref('')

// Pagination State (8 jobs per page: 4 rows across the two job columns)
const currentPage = ref(1)
const pageSize = ref(8)

// Skill Dropdown State
const isSkillMenuOpen = ref(false)
const skillDropdownRef = ref(null)
const skillSearchQuery = ref('')

// Tạm comment phần Khu công nghiệp vì DB không quản lý
// const selectedZone = ref('all')
// const filterMode = ref('zone')
// const filterModeDropdownRef = ref(null)

// function setFilterMode(mode) {
//   filterMode.value = mode
//   isFilterModeMenuOpen.value = false
// }

// Ward Dropdown Functions
function toggleWardMenu() {
  isWardMenuOpen.value = !isWardMenuOpen.value
  if (isWardMenuOpen.value) {
    wardSearchQuery.value = ''
    isSkillMenuOpen.value = false
  }
}

function selectWard(w) {
  if (w === 'all') {
    selectedWard.value = 'all'
  } else {
    selectedWard.value = typeof w === 'object' ? (w.full_name || w.name) : String(w)
  }
  isWardMenuOpen.value = false
  scrollToJobs()
}

function clearWard() {
  selectedWard.value = 'all'
}

function isWardSelected(w) {
  if (w === 'all') return selectedWard.value === 'all'
  const name = typeof w === 'object' ? (w.full_name || w.name) : String(w)
  return selectedWard.value === name
}

function getWardDisplayName(w) {
  if (typeof w === 'object' && w !== null) {
    return w.full_name || (w.unit_type ? `${w.unit_type} ${w.name}` : w.name)
  }
  return String(w || '')
}

const selectedWardLabel = computed(() => {
  if (selectedWard.value === 'all') {
    return `Tất cả xã / phường (${wards.value.length || 0})`
  }
  return selectedWard.value
})

const filteredWardsList = computed(() => {
  const q = wardSearchQuery.value.trim().toLowerCase()
  if (!q) return wards.value
  return wards.value.filter(w => {
    const name = typeof w === 'object' ? (w.full_name || w.name) : String(w)
    return name.toLowerCase().includes(q)
  })
})

const selectedSkillLabel = computed(() => {
  if (selectedSkill.value === 'all') return 'Tất cả kỹ năng'
  return selectedSkill.value
})

function toggleSkillMenu() {
  isSkillMenuOpen.value = !isSkillMenuOpen.value
  if (isSkillMenuOpen.value) {
    skillSearchQuery.value = ''
    isWardMenuOpen.value = false
  }
}

function selectSkill(skill) {
  selectedSkill.value = skill
  isSkillMenuOpen.value = false
  scrollToJobs()
}

function clearSkill() {
  selectedSkill.value = 'all'
}

const selectedCareerFieldFilterLabel = computed(() => {
  if (selectedCareerFieldFilter.value === 'all') return 'Tất cả lĩnh vực nghề nghiệp'
  return careerFieldOptions.find((field) => field.id === selectedCareerFieldFilter.value)?.name
    || 'Tất cả lĩnh vực nghề nghiệp'
})

function toggleCareerFieldMenu() {
  isCareerFieldMenuOpen.value = !isCareerFieldMenuOpen.value
  if (isCareerFieldMenuOpen.value) {
    isWardMenuOpen.value = false
    isSkillMenuOpen.value = false
  }
}

function selectCareerFieldFilter(fieldId) {
  selectedCareerFieldFilter.value = fieldId
  isCareerFieldMenuOpen.value = false
}

const initialJobs = jobService.getAll()

function normalizeLookup(value) {
  return String(value || '')
    .toLocaleLowerCase('vi')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/đ/g, 'd')
    .replace(/\s+/g, ' ')
    .trim()
}

function jobsForCareerField(field) {
  if (!field?.id) return []
  return initialJobs.filter((job) => job.industry === field.id)
}

const selectedCareerFieldJobCount = computed(() => jobsForCareerField(selectedCareerField.value).length)

const careerFieldRows = computed(() => jobService.getCareerFields(
  professionPortal.value.professions || [],
  schoolService.liveSchools.value || [],
  schoolService.liveTrainingMajors.value || [],
))

const filteredSelectedCareerFieldSchools = computed(() => {
  const schools = selectedCareerField.value?.schools || []
  const query = normalizeLookup(schoolSearchQuery.value)
  if (!query) return schools

  const queryTokens = query.split(' ').filter(Boolean)

  return schools.filter((school) => {
    const searchableText = normalizeLookup([
      school.name,
      school.code,
      school.level,
      school.location,
      school.address,
      school.ward,
      school.ward_name,
      school.district_name,
      school.province_name,
    ].filter(Boolean).join(' '))

    return queryTokens.every((token) => searchableText.includes(token))
  })
})

const schoolSearchResultLabel = computed(() => {
  const total = selectedCareerField.value?.schools?.length || 0
  if (!normalizeLookup(schoolSearchQuery.value)) return `${total} cơ sở`
  return `${filteredSelectedCareerFieldSchools.value.length}/${total} cơ sở`
})

function handleSchoolSearchInput(event) {
  schoolSearchQuery.value = event?.target?.value || ''
}

const filteredCareerFields = computed(() => {
  const query = normalizeLookup(professionSearchQuery.value)

  return careerFieldRows.value
    .filter((field) => {
      const matchesField = selectedCareerFieldFilter.value === 'all' || field.id === selectedCareerFieldFilter.value
      if (!matchesField) return false
      if (!query) return true

      const searchable = [
        field.name,
        field.description,
        ...(field.majors || []),
        ...(field.skills || []),
        ...(field.capacity_skills || []),
        ...(field.schools || []).flatMap((school) => [school.name, school.location, school.address]),
        ...(field.demands || []).flatMap((demand) => [demand.employer, demand.industry, demand.location]),
      ]

      return normalizeLookup(searchable.filter(Boolean).join(' ')).includes(query)
    })
})

function openCareerFieldJobs(field) {
  selectedCareerField.value = field
  schoolSearchQuery.value = ''
  searchQuery.value = ''
  selectedWard.value = 'all'
  selectedSkill.value = 'all'
  currentPage.value = 1
  isWardMenuOpen.value = false
  isSkillMenuOpen.value = false
  quickViewJob.value = null
  requestAnimationFrame(scrollToJobs)
}

function backToCareerFields() {
  selectedCareerField.value = null
  schoolSearchQuery.value = ''
  searchQuery.value = ''
  selectedWard.value = 'all'
  selectedSkill.value = 'all'
  currentPage.value = 1
  isWardMenuOpen.value = false
  isSkillMenuOpen.value = false
  requestAnimationFrame(scrollToJobs)
}

async function loadProfessionData() {
  isProfessionLoading.value = true
  professionError.value = ''

  try {
    const payload = await supplyDemandService.getPortal()
    professionPortal.value = {
      period: payload?.period || null,
      totals: payload?.totals || { demand: 0, capacity: 0, gap: 0, proposed: 0 },
      professions: Array.isArray(payload?.professions) ? payload.professions : [],
    }
  } catch (error) {
    professionPortal.value = {
      period: null,
      totals: { demand: 0, capacity: 0, gap: 0, proposed: 0 },
      professions: [],
    }
    professionError.value = error?.message || 'Không thể kết nối dữ liệu cung – cầu.'
  } finally {
    isProfessionLoading.value = false
  }
}

// Tạm comment danh sách Khu công nghiệp vì DB không quản lý
// const industrialZones = [
//   { name: 'KCN Gián Khẩu (Gia Viễn)', shortName: 'KCN Gián Khẩu', keyword: 'Gián Khẩu', count: 4 },
//   { name: 'KCN Phúc Sơn (TP. Ninh Bình)', shortName: 'KCN Phúc Sơn', keyword: 'Phúc Sơn', count: 3 },
//   { name: 'KCN Khánh Phú (Yên Khánh)', shortName: 'KCN Khánh Phú', keyword: 'Khánh Phú', count: 3 },
//   { name: 'KCN Tam Điệp (TP. Tam Điệp)', shortName: 'KCN Tam Điệp', keyword: 'Tam Điệp', count: 3 },
//   { name: 'Cụm Công nghiệp Kim Sơn', shortName: 'CCN Kim Sơn', keyword: 'Kim Sơn', count: 2 }
// ]

onMounted(async () => {
  document.addEventListener('click', handleClickOutside)
  window.addEventListener('resize', closeQuickViewModal)
  loadProfessionData()
  Promise.allSettled([
    schoolService.loadSchools(),
    schoolService.getTrainingMajors(),
  ])
  try {
    const w = await schoolService.getWards()
    if (Array.isArray(w) && w.length > 0) {
      wards.value = w
    }
  } catch (err) {
    console.error('Failed to load wards for jobs filter:', err)
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  window.removeEventListener('resize', closeQuickViewModal)
})

function handleClickOutside(e) {
  if (wardDropdownRef.value && !wardDropdownRef.value.contains(e.target)) {
    isWardMenuOpen.value = false
  }
  if (skillDropdownRef.value && !skillDropdownRef.value.contains(e.target)) {
    isSkillMenuOpen.value = false
  }
  if (careerFieldDropdownRef.value && !careerFieldDropdownRef.value.contains(e.target)) {
    isCareerFieldMenuOpen.value = false
  }
  if (quickViewJob.value && quickViewPopupRef.value && !quickViewPopupRef.value.contains(e.target)) {
    closeQuickViewModal()
  }
}

const availableSkills = computed(() => {
  const set = new Set()
  let pool = selectedCareerField.value ? jobsForCareerField(selectedCareerField.value) : initialJobs
  if (selectedWard.value !== 'all') {
    const wRaw = selectedWard.value.toLowerCase().trim()
    const wClean = wRaw.replace(/^(xã|phường|thị trấn)\s+/i, '').trim()
    const wardMatched = pool.filter(j => 
      j.ward_code === selectedWard.value || 
      (j.ward_name && (j.ward_name.toLowerCase() === wRaw || j.ward_name.toLowerCase() === wClean)) ||
      (j.address && (j.address.toLowerCase().includes(wRaw) || j.address.toLowerCase().includes(wClean)))
    )
    if (wardMatched.length > 0) {
      pool = wardMatched
    }
  }
  pool.forEach(j => {
    ;(j.skills || []).forEach(s => set.add(s))
  })
  return Array.from(set)
})

const filteredAvailableSkills = computed(() => {
  if (!skillSearchQuery.value.trim()) {
    return availableSkills.value
  }
  const q = skillSearchQuery.value.trim().toLowerCase()
  return availableSkills.value.filter(s => s.toLowerCase().includes(q))
})

const filteredJobs = computed(() => {
  const jobs = jobService.filterJobs({
    search: searchQuery.value,
    wardCode: selectedWard.value,
    industry: 'all',
    skill: selectedSkill.value
  })

  if (!selectedCareerField.value) return jobs
  return jobs.filter((job) => job.industry === selectedCareerField.value.id)
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
watch([searchQuery, selectedWard, selectedSkill], () => {
  currentPage.value = 1
})

// Auto reset skill if not in availableSkills when ward changes
watch(selectedWard, () => {
  if (selectedSkill.value !== 'all' && !availableSkills.value.includes(selectedSkill.value)) {
    selectedSkill.value = 'all'
  }
})

// Keep currentPage within bounds if totalPages shrinks
watch(totalPages, (newTotal) => {
  if (currentPage.value > newTotal) {
    currentPage.value = Math.max(1, newTotal)
  }
})

const hasActiveFilter = computed(() => {
  return searchQuery.value || 
         selectedWard.value !== 'all' ||
         selectedSkill.value !== 'all'
})

function resetFilters() {
  if (!selectedCareerField.value) {
    professionSearchQuery.value = ''
    selectedCareerFieldFilter.value = 'all'
    return
  }

  searchQuery.value = ''
  selectedWard.value = 'all'
  selectedSkill.value = 'all'
  currentPage.value = 1
}

const QUICK_VIEW_MARGIN = 12
const QUICK_VIEW_GAP = 14
const QUICK_VIEW_DEFAULT_WIDTH = 608
const QUICK_VIEW_ESTIMATED_HEIGHT = 640

function clamp(value, min, max) {
  return Math.min(Math.max(value, min), Math.max(min, max))
}

function updateQuickViewPlacement(popupRect = null) {
  if (!quickViewAnchor.value || typeof window === 'undefined') return

  const viewportWidth = document.documentElement.clientWidth
  const viewportHeight = document.documentElement.clientHeight
  const anchorRect = quickViewAnchor.value.anchorRect
  const titleRect = quickViewAnchor.value.titleRect || anchorRect
  const popupWidth = Math.min(
    popupRect?.width || QUICK_VIEW_DEFAULT_WIDTH,
    viewportWidth - (QUICK_VIEW_MARGIN * 2),
  )
  const popupHeight = Math.min(
    popupRect?.height || QUICK_VIEW_ESTIMATED_HEIGHT,
    viewportHeight - (QUICK_VIEW_MARGIN * 2),
  )

  const spaceRight = viewportWidth - anchorRect.right - QUICK_VIEW_GAP - QUICK_VIEW_MARGIN
  const spaceLeft = anchorRect.left - QUICK_VIEW_GAP - QUICK_VIEW_MARGIN
  let side = 'right'

  if (spaceRight < popupWidth && spaceLeft >= popupWidth) {
    side = 'left'
  } else if (spaceRight < popupWidth && spaceLeft < popupWidth) {
    side = spaceRight >= spaceLeft ? 'right' : 'left'
  }

  const desiredLeft = side === 'right'
    ? anchorRect.right + QUICK_VIEW_GAP
    : anchorRect.left - popupWidth - QUICK_VIEW_GAP
  const left = clamp(
    desiredLeft,
    QUICK_VIEW_MARGIN,
    viewportWidth - popupWidth - QUICK_VIEW_MARGIN,
  )

  const titleCenterY = titleRect.top + (titleRect.height / 2)
  const desiredTop = titleCenterY - Math.min(84, Math.max(48, popupHeight * 0.14))
  const top = clamp(
    desiredTop,
    QUICK_VIEW_MARGIN,
    viewportHeight - popupHeight - QUICK_VIEW_MARGIN,
  )
  const arrowTop = clamp(
    titleCenterY - top - 8,
    24,
    popupHeight - 32,
  )

  quickViewPlacement.value = { top, left, side, arrowTop }
}

async function openQuickViewModal(payload) {
  const job = payload?.job || payload
  if (!job) return

  quickViewAnchor.value = {
    anchorRect: payload?.anchorRect,
    titleRect: payload?.titleRect,
  }
  quickViewJob.value = job
  schoolDetailLookupMessage.value = ''

  if (!quickViewAnchor.value.anchorRect) return

  updateQuickViewPlacement()
  await nextTick()

  if (quickViewPopupRef.value) {
    updateQuickViewPlacement(quickViewPopupRef.value.getBoundingClientRect())
  }
}

function closeQuickViewModal() {
  quickViewJob.value = null
}

function comparableSchoolName(value) {
  return String(value || '')
    .toLocaleLowerCase('vi')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/đ/g, 'd')
    .replace(/\bcd\b/g, 'cao dang')
    .replace(/\btc\b/g, 'trung cap')
    .replace(/[^a-z0-9]+/g, ' ')
    .trim()
}

function findSchoolForDetail(reference, schools) {
  if (reference?.id || reference?.school_id || reference?.code) {
    const identifier = String(reference.id || reference.school_id || reference.code)
    const direct = schools.find((school) => school.id === identifier || school.code === identifier)
    if (direct) return direct
  }

  const target = comparableSchoolName(reference?.name)
  if (!target) return null

  const exactMatches = schools.filter((school) => comparableSchoolName(school.name) === target)
  if (exactMatches.length === 1) return exactMatches[0]

  const relatedMatches = schools.filter((school) => {
    const name = comparableSchoolName(school.name)
    return name.includes(target) || target.includes(name)
  })
  const mainMatches = relatedMatches.filter((school) => school.campus_type === 'MAIN')

  return mainMatches.length === 1 ? mainMatches[0] : (relatedMatches.length === 1 ? relatedMatches[0] : null)
}

async function openSchoolDetailFromQuickView(school) {
  if (!school || isSchoolDetailLoading.value) return

  isSchoolDetailLoading.value = true
  schoolDetailLookupMessage.value = ''

  try {
    const schools = await schoolService.loadSchools()
    const matchedSchool = findSchoolForDetail(school, schools)

    if (!matchedSchool) {
      schoolDetailLookupMessage.value = `Chưa tìm thấy hồ sơ chính thức của “${school.name}” trong danh mục cơ sở GDNN.`
      return
    }

    closeQuickViewModal()
    selectedSchoolDetail.value = matchedSchool
    const detail = await schoolService.getDetails(matchedSchool.id)
    if (detail && selectedSchoolDetail.value?.id === matchedSchool.id) {
      selectedSchoolDetail.value = detail
    }
  } finally {
    isSchoolDetailLoading.value = false
  }
}

function goToJobDetailFromQuickView(job) {
  closeQuickViewModal()
  goToJobDetail(job)
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

<style scoped>
.job-quick-preview-panel {
  will-change: opacity, transform, top, left;
}

.job-quick-preview-enter-active {
  transition:
    opacity 220ms ease-out,
    transform 280ms cubic-bezier(0.16, 1, 0.3, 1),
    filter 220ms ease-out,
    top 160ms ease-out,
    left 160ms ease-out;
}

.job-quick-preview-leave-active {
  pointer-events: none;
  transition:
    opacity 150ms ease-in,
    transform 180ms ease-in,
    filter 150ms ease-in;
}

.job-quick-preview-enter-from,
.job-quick-preview-leave-to {
  opacity: 0;
  filter: blur(2px);
  transform: translate3d(var(--quick-preview-enter-x), 6px, 0) scale(0.975);
}

.job-quick-preview-enter-to,
.job-quick-preview-leave-from {
  opacity: 1;
  filter: blur(0);
  transform: translate3d(0, 0, 0) scale(1);
}

@media (prefers-reduced-motion: reduce) {
  .job-quick-preview-enter-active,
  .job-quick-preview-leave-active {
    transition-duration: 1ms;
  }
}
</style>
