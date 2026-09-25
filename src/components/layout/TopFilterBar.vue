<template>
  <div class="flex items-center gap-1.5 sm:gap-2 flex-1 max-w-xl justify-end md:justify-center min-w-0">
    <!-- Search Bar with Live Suggestions (hidden on mobile < md) -->
    <div class="hidden md:block relative flex-1 min-w-0 sm:max-w-xs md:max-w-sm" ref="searchContainer">
      <div class="relative flex items-center">
        <i class="fa-solid fa-magnifying-glass absolute left-2.5 sm:left-3 text-slate-400 text-xs pointer-events-none"></i>
        <input 
          v-model="filterStore.search"
          @focus="showSuggestions = true"
          @keydown.esc="showSuggestions = false"
          @keydown.enter="handleEnterKey"
          type="text"
          placeholder="Tìm trường, địa chỉ..."
          class="w-full pl-7 sm:pl-8 pr-7 py-1.5 bg-slate-100/90 hover:bg-slate-100 focus:bg-white text-xs font-medium text-slate-800 rounded-xl border border-slate-200/80 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all placeholder:text-slate-400 shadow-2xs"
        />
        <button 
          v-if="filterStore.search"
          @click="filterStore.search = ''; showSuggestions = false"
          class="absolute right-2 text-slate-400 hover:text-slate-600 text-xs w-4 h-4 flex items-center justify-center cursor-pointer"
          title="Xóa từ khóa">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <!-- Autocomplete Suggestions Dropdown -->
      <div 
        v-if="showSuggestions && filterStore.search.trim().length > 0"
        class="absolute right-0 sm:right-auto sm:left-0 top-9 mt-1.5 w-[calc(100vw-28px)] sm:w-[440px] md:w-[480px] max-w-[500px] bg-white border border-slate-200/90 rounded-2xl shadow-xl z-50 overflow-hidden animate-in fade-in slide-in-from-top-1 duration-150">
        
        <!-- Header / Count -->
        <div class="px-3.5 py-2 bg-slate-50/90 border-b border-slate-100 flex items-center justify-between text-[11px] text-slate-500 font-medium">
          <div class="flex items-center gap-1.5">
            <i class="fa-solid fa-school text-slate-400 text-[10px]"></i>
            <span>Kết quả tìm kiếm <strong class="text-blue-700 font-semibold">({{ filteredSuggestions.length }})</strong></span>
          </div>
          <span class="text-[10px] text-slate-400">Nhấp để xem trên bản đồ</span>
        </div>

        <!-- Suggestions List -->
        <div v-if="filteredSuggestions.length > 0" class="max-h-72 sm:max-h-80 overflow-y-auto divide-y divide-slate-100/80">
          <div 
            v-for="school in filteredSuggestions.slice(0, 8)"
            :key="school.id"
            @click="selectSuggestion(school)"
            class="px-3.5 py-2.5 hover:bg-blue-50/70 cursor-pointer transition-colors flex items-center gap-3 group">
            
            <!-- Level Icon Badge -->
            <div 
              class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-105 border"
              :style="{ 
                backgroundColor: getLevelBg(school.education_level), 
                borderColor: getLevelBorder(school.education_level),
                color: getLevelColor(school.education_level) 
              }">
              <i :class="getLevelIcon(school.education_level)" class="text-xs"></i>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
              <p 
                class="font-semibold text-slate-800 text-xs sm:text-[13px] leading-snug group-hover:text-blue-700 transition-colors truncate" 
                :title="school.name">
                {{ school.name }}
              </p>
              
              <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500">
                <!-- Level Badge -->
                <span 
                  class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold flex-shrink-0"
                  :style="{ 
                    backgroundColor: getLevelBg(school.education_level), 
                    color: getLevelColor(school.education_level) 
                  }">
                  {{ getLevelLabel(school.education_level) }}
                </span>

                <span class="text-slate-300">•</span>

                <!-- Address -->
                <span 
                  class="truncate text-slate-500 text-[11px] flex items-center gap-1 min-w-0" 
                  :title="formatSchoolAddress(school)">
                  <i class="fa-solid fa-location-dot text-[9.5px] text-slate-400 flex-shrink-0"></i>
                  <span class="truncate">{{ formatSchoolAddress(school) }}</span>
                </span>
              </div>
            </div>

            <!-- Action indicator -->
            <div class="w-6 h-6 rounded-lg flex items-center justify-center text-slate-300 group-hover:text-blue-600 group-hover:bg-blue-100/60 flex-shrink-0 transition-all">
              <i class="fa-solid fa-chevron-right text-[10px] group-hover:translate-x-0.5 transition-transform"></i>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="px-4 py-6 text-center">
          <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
            <i class="fa-solid fa-magnifying-glass text-xs"></i>
          </div>
          <p class="text-xs font-semibold text-slate-700">Không tìm thấy trường nào</p>
          <p class="text-[11px] text-slate-400 mt-0.5">Không có cơ sở giáo dục nào khớp với "{{ filterStore.search }}"</p>
        </div>
      </div>
    </div>

    <!-- Desktop & Tablet: Searchable Ward Combobox (hidden on < md) -->
    <div class="hidden md:block relative w-36 lg:w-48 xl:w-56 flex-shrink-0" ref="wardContainer">
      <!-- Trigger Button -->
      <div 
        @click="toggleWardMenu"
        :class="[
          filterStore.ward !== 'all' ? 'border-blue-300 bg-blue-50/70 text-blue-900 ring-2 ring-blue-100' : 'border-slate-200/80 bg-slate-100/90 text-slate-700 hover:bg-slate-200/70',
          isWardMenuOpen ? 'ring-2 ring-blue-200 border-blue-400 bg-white' : ''
        ]"
        class="w-full pl-6 sm:pl-7 pr-6 sm:pr-7 py-1.5 text-xs font-medium rounded-xl border flex items-center justify-between cursor-pointer transition-all shadow-2xs select-none">
        
        <i class="fa-solid fa-location-dot absolute left-2 sm:left-2.5 top-1/2 -translate-y-1/2 text-blue-600 text-[11px]"></i>
        
        <span class="truncate pr-1">
          {{ selectedWardLabel }}
        </span>

        <!-- Clear ward or Chevron toggle -->
        <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
          <button 
            v-if="filterStore.ward !== 'all'"
            @click.stop="clearWardFilter"
            class="w-4 h-4 rounded-full text-slate-400 hover:text-rose-600 flex items-center justify-center cursor-pointer"
            title="Bỏ chọn xã/phường">
            <i class="fa-solid fa-xmark text-[10px]"></i>
          </button>
          <i 
            class="fa-solid fa-chevron-down text-[9px] transition-transform duration-200"
            :class="isWardMenuOpen ? 'rotate-180 text-blue-600' : 'text-slate-400'"></i>
        </div>
      </div>

      <!-- Floating Searchable Wards Panel -->
      <div 
        v-if="isWardMenuOpen"
        class="absolute right-0 top-9 mt-1 w-72 sm:w-80 max-w-[calc(100vw-24px)] bg-white border border-slate-200 rounded-2xl shadow-2xl p-2 z-50 animate-in fade-in slide-in-from-top-1">
        
        <!-- Search input for wards -->
        <div class="relative mb-2">
          <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
          <input 
            ref="wardSearchInputRef"
            v-model="wardSearchQuery"
            type="text"
            placeholder="Tìm tên xã, phường, thị trấn..."
            class="w-full pl-7 pr-7 py-1.5 bg-slate-100 hover:bg-slate-200/60 focus:bg-white text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-200 outline-none transition-all placeholder:text-slate-400"
          />
          <button 
            v-if="wardSearchQuery"
            @click="wardSearchQuery = ''"
            class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 w-4 h-4 flex items-center justify-center cursor-pointer">
            <i class="fa-solid fa-xmark text-[11px]"></i>
          </button>
        </div>

        <!-- Scrollable Wards List -->
        <div class="max-h-60 overflow-y-auto space-y-0.5 pr-1">
          <!-- All Wards Option -->
          <div 
            @click="selectWard('all')"
            :class="filterStore.ward === 'all' ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100'"
            class="px-2.5 py-1.5 rounded-lg text-xs cursor-pointer flex items-center justify-between transition-colors">
            <div class="flex items-center gap-2">
              <i class="fa-solid fa-map-location-dot text-blue-600 text-xs"></i>
              <span>Toàn tỉnh (Tất cả Xã/Phường)</span>
            </div>
            <i v-if="filterStore.ward === 'all'" class="fa-solid fa-check text-xs text-blue-600"></i>
          </div>

          <div class="h-px bg-slate-100 my-1"></div>

          <!-- Empty search result -->
          <div 
            v-if="filteredWardsList.length === 0" 
            class="text-center py-4 text-xs text-slate-400 italic">
            Không tìm thấy phường/xã nào khớp với "{{ wardSearchQuery }}"
          </div>

          <!-- Grouped filtered wards -->
          <template v-else>
            <div 
              v-for="w in filteredWardsList"
              :key="w.id"
              @click="selectWard(w.name)"
              :class="isWardSelected(w) ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-50'"
              class="px-2.5 py-1.5 rounded-lg text-xs cursor-pointer flex items-center justify-between transition-colors group">
              <div class="flex items-center gap-2 min-w-0">
                <span 
                  :class="getUnitBadgeClass(w.unit_type)"
                  class="text-[10px] px-1.5 py-0.5 rounded font-medium flex-shrink-0">
                  {{ w.unit_type || 'Xã' }}
                </span>
                <span class="truncate font-medium group-hover:text-blue-800">{{ w.full_name }}</span>
              </div>
              <i v-if="isWardSelected(w)" class="fa-solid fa-check text-xs text-blue-600 flex-shrink-0 ml-1"></i>
            </div>
          </template>
        </div>
      </div>
    </div>

    <!-- Mobile Search Trigger Button (< md only) -->
    <button 
      @click="openMobileSearch"
      class="md:hidden relative w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 transition-all cursor-pointer shadow-2xs"
      :class="filterStore.search ? 'bg-blue-800 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200/90'"
      title="Tìm kiếm trường học">
      <i class="fa-solid fa-magnifying-glass text-xs"></i>
      <span 
        v-if="filterStore.search" 
        class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-blue-500 rounded-full border-2 border-white">
      </span>
    </button>

    <!-- Mobile Filter Trigger Button (< md only) -->
    <button 
      @click="isMobileFilterOpen = true"
      :class="hasNonSearchFilter ? 'bg-blue-800 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200/90'"
      class="md:hidden relative w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 transition-all cursor-pointer shadow-2xs"
      title="Mở bộ lọc (Cấp học, chuyên ngành & Xã/Phường)">
      <i class="fa-solid fa-sliders text-xs"></i>
      <!-- Active badge dot -->
      <span 
        v-if="hasNonSearchFilter" 
        class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white">
      </span>
    </button>

    <!-- Reset Filters Button (Tablet & Desktop only) -->
    <button 
      v-if="filterStore.hasActiveFilter()"
      @click="filterStore.reset()"
      class="hidden md:flex text-[11px] text-blue-700 hover:text-blue-900 font-semibold px-2 py-1.5 rounded-lg hover:bg-blue-50 items-center gap-1 flex-shrink-0 transition-colors cursor-pointer"
      title="Xóa toàn bộ bộ lọc">
      <i class="fa-solid fa-rotate-left text-[10px]"></i>
      <span class="hidden md:inline">Đặt lại</span>
    </button>

    <!-- ============================================================== -->
    <!-- MOBILE FILTER BOTTOM SHEET / MODAL (< md)                      -->
    <!-- ============================================================== -->
    <Teleport to="body">
      <div
        v-if="isMobileFilterOpen"
        class="fixed inset-0 z-[3000] flex flex-col justify-end md:hidden animate-in fade-in select-none">

        <!-- Backdrop tap dismiss -->
        <button
          type="button"
          class="absolute inset-0 bg-slate-900/50 backdrop-blur-xs cursor-default"
          aria-label="Đóng bộ lọc"
          @click="isMobileFilterOpen = false">
        </button>

        <!-- Sheet Container -->
        <div class="relative z-10 w-full bg-white rounded-t-3xl shadow-2xl flex flex-col max-h-[90dvh] overflow-hidden animate-in slide-in-from-bottom duration-200 border-t border-slate-200">
        
        <!-- Sheet Header -->
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between flex-shrink-0 bg-slate-50/70">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center text-xs">
              <i class="fa-solid fa-sliders"></i>
            </div>
            <h3 class="font-bold text-slate-800 text-sm">Bộ lọc cơ sở giáo dục</h3>
          </div>
          <button 
            @click="isMobileFilterOpen = false"
            class="w-7 h-7 rounded-full bg-slate-200/70 hover:bg-slate-300 text-slate-600 flex items-center justify-center transition-colors cursor-pointer text-xs">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <!-- Scrollable Sheet Content -->
        <div class="flex-1 min-h-0 p-4 pb-6 overflow-y-auto overscroll-contain space-y-4 touch-pan-y">
          
          <!-- Section 1: Education Levels -->
          <div>
            <div class="text-xs font-bold text-slate-800 mb-2 flex items-center justify-between">
              <span class="flex items-center gap-1.5">
                <i class="fa-solid fa-graduation-cap text-blue-700"></i>
                <span>Cấp học</span>
              </span>
              <span v-if="filterStore.level !== 'all'" class="text-[10px] text-blue-700 font-semibold">
                Đã chọn 1
              </span>
            </div>
            <div class="grid grid-cols-2 gap-2">
              <button 
                v-for="lvl in levels"
                :key="lvl.id"
                @click="filterStore.level = lvl.id"
                :class="filterStore.level === lvl.id 
                  ? 'bg-blue-800 text-white font-semibold shadow-xs' 
                  : 'bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium'"
                class="px-2.5 py-2 rounded-xl text-xs flex items-center justify-between transition-all cursor-pointer">
                <span class="truncate">{{ lvl.label }}</span>
                <i v-if="filterStore.level === lvl.id" class="fa-solid fa-check text-[11px] ml-1"></i>
              </button>
            </div>
          </div>

          <div class="h-px bg-slate-100"></div>

          <!-- Section 2: One searchable training major -->
          <div>
            <div class="text-xs font-bold text-slate-800 mb-2 flex items-center justify-between">
              <span class="flex items-center gap-1.5">
                <i class="fa-solid fa-list-check text-blue-700"></i>
                <span>Chuyên ngành đào tạo</span>
              </span>
              <span v-if="filterStore.majorId !== 'all'" class="text-[10px] text-blue-700 font-semibold">Đã chọn 1</span>
            </div>
            <div class="relative mb-2">
              <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
              <input
                v-model="majorSearchQuery"
                type="text"
                placeholder="Tìm tên chuyên ngành..."
                class="w-full pl-8 pr-7 py-2 bg-slate-100 focus:bg-white text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-200 outline-none transition-all placeholder:text-slate-400"
              />
              <button
                v-if="majorSearchQuery"
                @click="majorSearchQuery = ''"
                class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 w-5 h-5 flex items-center justify-center cursor-pointer">
                <i class="fa-solid fa-xmark text-xs"></i>
              </button>
            </div>
            <div class="max-h-44 overflow-y-auto space-y-1 pr-1 border border-slate-100 rounded-xl p-1 bg-slate-50/50">
              <button
                @click="filterStore.majorId = 'all'"
                :class="filterStore.majorId === 'all' ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100'"
                class="w-full px-2.5 py-1.5 rounded-lg text-xs text-left flex items-center justify-between cursor-pointer">
                <span>Tất cả chuyên ngành</span>
                <i v-if="filterStore.majorId === 'all'" class="fa-solid fa-check text-xs"></i>
              </button>
              <button
                v-for="major in filteredMajors"
                :key="major.id"
                @click="filterStore.majorId = String(major.id)"
                :class="String(filterStore.majorId) === String(major.id) ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100'"
                class="w-full px-2.5 py-1.5 rounded-lg text-xs text-left flex items-center justify-between cursor-pointer">
                <span
                  class="min-w-0 flex-1 pr-2 leading-4 whitespace-normal break-words"
                  :title="major.name">
                  {{ major.name }}
                </span>
                <span class="flex items-center gap-1 flex-shrink-0 ml-1">
                  <span
                    :class="String(filterStore.majorId) === String(major.id) ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500'"
                    class="min-w-5 px-1.5 py-0.5 rounded-md text-[10px] font-mono text-center"
                    :title="`${major.schools_count || 0} trường đào tạo`">
                    {{ major.schools_count || 0 }}
                  </span>
                  <i v-if="String(filterStore.majorId) === String(major.id)" class="fa-solid fa-check text-xs flex-shrink-0"></i>
                </span>
              </button>
              <p v-if="filteredMajors.length === 0" class="text-center py-3 text-xs text-slate-400 italic">Không tìm thấy ngành phù hợp</p>
            </div>
          </div>

          <div class="h-px bg-slate-100"></div>

          <!-- Section 3: Ward / Commune (129 Wards) -->
          <div>
            <div class="text-xs font-bold text-slate-800 mb-2 flex items-center justify-between">
              <span class="flex items-center gap-1.5">
                <i class="fa-solid fa-location-dot text-blue-700"></i>
                <span>Địa bàn (Xã, Phường, Thị trấn)</span>
              </span>
              <span v-if="filterStore.ward !== 'all'" class="text-[10px] text-blue-700 font-semibold truncate max-w-[150px]">
                {{ selectedWardLabel }}
              </span>
            </div>

            <!-- Ward Search Input inside sheet -->
            <div class="relative mb-2">
              <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
              <input 
                v-model="wardSearchQuery"
                type="text"
                placeholder="Tìm xã, phường, thị trấn..."
                class="w-full pl-8 pr-7 py-2 bg-slate-100 focus:bg-white text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-200 outline-none transition-all placeholder:text-slate-400"
              />
              <button 
                v-if="wardSearchQuery"
                @click="wardSearchQuery = ''"
                class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 w-5 h-5 flex items-center justify-center cursor-pointer">
                <i class="fa-solid fa-xmark text-xs"></i>
              </button>
            </div>

            <!-- Scrollable List of Wards inside sheet -->
            <div class="max-h-48 overflow-y-auto space-y-1 pr-1 border border-slate-100 rounded-xl p-1 bg-slate-50/50">
              <!-- All Wards Option -->
              <div 
                @click="selectWard('all')"
                :class="filterStore.ward === 'all' ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100'"
                class="px-2.5 py-1.5 rounded-lg text-xs cursor-pointer flex items-center justify-between transition-colors">
                <div class="flex items-center gap-2">
                  <i class="fa-solid fa-map-location-dot text-blue-600 text-xs"></i>
                  <span>Toàn tỉnh (Tất cả Xã/Phường)</span>
                </div>
                <i v-if="filterStore.ward === 'all'" class="fa-solid fa-check text-xs text-blue-600"></i>
              </div>

              <!-- Filtered List -->
              <div 
                v-for="w in filteredWardsList"
                :key="w.id"
                @click="selectWard(w.name)"
                :class="isWardSelected(w) ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100'"
                class="px-2.5 py-1.5 rounded-lg text-xs cursor-pointer flex items-center justify-between transition-colors">
                <div class="flex items-center gap-2 min-w-0">
                  <span 
                    :class="getUnitBadgeClass(w.unit_type)"
                    class="text-[10px] px-1.5 py-0.5 rounded font-medium flex-shrink-0">
                    {{ w.unit_type || 'Xã' }}
                  </span>
                  <span class="truncate font-medium">{{ w.full_name }}</span>
                </div>
                <i v-if="isWardSelected(w)" class="fa-solid fa-check text-xs text-blue-600 flex-shrink-0 ml-1"></i>
              </div>
            </div>
          </div>

        </div>

        <!-- Sheet Footer Actions -->
        <div
          class="relative z-10 p-3 border-t border-slate-200 bg-slate-50 flex items-center gap-2 flex-shrink-0 shadow-[0_-4px_12px_rgba(15,23,42,0.06)]"
          style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
          <button 
            @click="filterStore.reset(); isMobileFilterOpen = false"
            class="flex-1 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 font-semibold text-xs hover:bg-slate-100 transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
            <i class="fa-solid fa-rotate-left text-[11px]"></i>
            <span>Đặt lại</span>
          </button>
          <button 
            @click="isMobileFilterOpen = false"
            class="flex-1 py-2.5 rounded-xl bg-blue-800 hover:bg-blue-900 text-white font-bold text-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer shadow-xs">
            <i class="fa-solid fa-check text-[11px]"></i>
            <span>Áp dụng</span>
          </button>
        </div>

        </div>
      </div>
    </Teleport>

    <!-- ============================================================== -->
    <!-- MOBILE FULL-SCREEN SEARCH OVERLAY (< md)                       -->
    <!-- ============================================================== -->
    <Teleport to="body">
      <div 
        v-if="isMobileSearchOpen" 
        class="fixed inset-0 z-[3100] bg-slate-900/40 backdrop-blur-xs flex flex-col md:hidden animate-in fade-in select-none">
        
        <!-- Top Search Bar Header -->
        <div 
          class="bg-white px-3 py-2 border-b border-slate-200 shadow-md flex items-center gap-2 flex-shrink-0"
          style="padding-top: max(0.5rem, env(safe-area-inset-top));">
          <button 
            @click="closeMobileSearch" 
            class="w-8 h-8 rounded-xl hover:bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0 cursor-pointer"
            title="Quay lại">
            <i class="fa-solid fa-arrow-left text-sm"></i>
          </button>
          
          <div class="flex-1 relative flex items-center">
            <i class="fa-solid fa-magnifying-glass absolute left-3 text-slate-400 text-xs pointer-events-none"></i>
            <input 
              ref="mobileSearchInputRef"
              v-model="filterStore.search"
              @keydown.enter="handleMobileEnter"
              type="text"
              placeholder="Tìm tên trường, địa chỉ..."
              class="w-full pl-8 pr-8 py-2 bg-slate-100 text-xs sm:text-sm font-medium text-slate-800 rounded-xl border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none"
            />
            <button 
              v-if="filterStore.search"
              @click="filterStore.search = ''"
              class="absolute right-2.5 text-slate-400 hover:text-slate-600 w-5 h-5 flex items-center justify-center cursor-pointer"
              title="Xóa">
              <i class="fa-solid fa-xmark text-xs"></i>
            </button>
          </div>

          <button 
            @click="closeMobileSearch"
            class="text-xs font-semibold text-blue-700 px-1 py-1 hover:text-blue-900 flex-shrink-0 cursor-pointer">
            Đóng
          </button>
        </div>

        <!-- Live Search Results Sheet -->
        <div class="bg-white flex-1 overflow-y-auto divide-y divide-slate-100">
          <div v-if="filteredSuggestions.length > 0">
            <div class="px-3.5 py-2 bg-slate-50 text-[11px] text-slate-500 font-semibold flex justify-between items-center">
              <span>Tìm thấy {{ filteredSuggestions.length }} cơ sở GDNN</span>
              <span class="text-[10px] text-slate-400">Chạm để xem</span>
            </div>
            <div 
              v-for="school in filteredSuggestions" 
              :key="school.id"
              @click="selectSuggestionAndClose(school)"
              class="px-3.5 py-2.5 hover:bg-blue-50/70 active:bg-blue-100 flex items-center gap-2.5 cursor-pointer">
              <div 
                class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 border"
                :style="{ 
                  backgroundColor: getLevelBg(school.education_level), 
                  borderColor: getLevelBorder(school.education_level),
                  color: getLevelColor(school.education_level) 
                }">
                <i :class="getLevelIcon(school.education_level)" class="text-xs"></i>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-slate-900 leading-snug line-clamp-1">{{ school.name }}</p>
                <p class="text-[11px] text-slate-500 truncate mt-0.5 flex items-center gap-1">
                  <i class="fa-solid fa-location-dot text-[9px] text-slate-400"></i>
                  <span>{{ formatSchoolAddress(school) }}</span>
                </p>
              </div>
              <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
            </div>
          </div>
          <div v-else-if="filterStore.search.trim().length > 0" class="p-8 text-center text-slate-500">
            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
              <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </div>
            <p class="text-xs font-bold text-slate-700">Không tìm thấy trường nào</p>
            <p class="text-[11px] text-slate-400 mt-1">Không có cơ sở giáo dục nào khớp với "{{ filterStore.search }}"</p>
          </div>
          <div v-else class="p-8 text-center text-slate-400 text-xs">
            <i class="fa-solid fa-magnifying-glass text-xl mb-2 text-slate-300 block"></i>
            Nhập tên trường, địa chỉ hoặc mã trường để tìm kiếm nhanh
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { filterStore, targetSchoolToFly } from '../../services/filterStore'
import { schoolService, LEVEL_MAP, liveSchools, liveTrainingMajors, EDUCATION_LEVELS } from '../../services/schoolService'

const wards = ref([])
const allSchools = liveSchools
const showSuggestions = ref(false)
const searchContainer = ref(null)

const levels = EDUCATION_LEVELS
const majors = liveTrainingMajors

// Mobile sheet state
const isMobileFilterOpen = ref(false)
const isMobileSearchOpen = ref(false)
const mobileSearchInputRef = ref(null)

function openMobileSearch() {
  isMobileSearchOpen.value = true
  nextTick(() => {
    mobileSearchInputRef.value?.focus()
  })
}

function closeMobileSearch() {
  isMobileSearchOpen.value = false
}

function selectSuggestionAndClose(school) {
  selectSuggestion(school)
  isMobileSearchOpen.value = false
}

function handleMobileEnter() {
  if (filteredSuggestions.value.length > 0) {
    selectSuggestionAndClose(filteredSuggestions.value[0])
  } else {
    closeMobileSearch()
  }
}

// Searchable Ward Combobox state
const isWardMenuOpen = ref(false)
const wardSearchQuery = ref('')
const wardContainer = ref(null)
const wardSearchInputRef = ref(null)
const majorSearchQuery = ref('')

const hasNonSearchFilter = computed(() => {
  return filterStore.level !== 'all' || filterStore.ward !== 'all' || filterStore.majorId !== 'all'
})

onMounted(async () => {
  // Load 129 wards directly from MySQL DB
  wards.value = await schoolService.getWards()

  // Sync live schools from MySQL
  await schoolService.syncFromApi()
  await schoolService.getTrainingMajors()

  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

function handleClickOutside(e) {
  if (searchContainer.value && !searchContainer.value.contains(e.target)) {
    showSuggestions.value = false
  }
  if (wardContainer.value && !wardContainer.value.contains(e.target)) {
    isWardMenuOpen.value = false
  }
}

function toggleWardMenu() {
  isWardMenuOpen.value = !isWardMenuOpen.value
  if (isWardMenuOpen.value) {
    nextTick(() => {
      wardSearchInputRef.value?.focus()
    })
  }
}

function selectWard(wardName) {
  filterStore.ward = wardName
  isWardMenuOpen.value = false
  wardSearchQuery.value = ''
}

function clearWardFilter() {
  filterStore.ward = 'all'
  wardSearchQuery.value = ''
}

function isWardSelected(w) {
  if (filterStore.ward === 'all') return false
  const selected = filterStore.ward.toLowerCase()
  return (w.name && w.name.toLowerCase() === selected) || 
         (w.full_name && w.full_name.toLowerCase() === selected)
}

const selectedWardLabel = computed(() => {
  if (filterStore.ward === 'all') return 'Toàn tỉnh'
  const matched = wards.value.find(w => 
    w.name.toLowerCase() === filterStore.ward.toLowerCase() ||
    w.full_name.toLowerCase() === filterStore.ward.toLowerCase()
  )
  return matched ? matched.full_name : filterStore.ward
})

const filteredWardsList = computed(() => {
  const q = wardSearchQuery.value.toLowerCase().trim()
  if (!q) return wards.value
  return wards.value.filter(w => {
    return (w.name && w.name.toLowerCase().includes(q)) ||
           (w.full_name && w.full_name.toLowerCase().includes(q))
  })
})

const filteredMajors = computed(() => {
  const q = majorSearchQuery.value.toLocaleLowerCase('vi').trim()
  if (!q) return majors.value
  return majors.value.filter(major => (major.name || '').toLocaleLowerCase('vi').includes(q))
})

function getUnitBadgeClass(unitType) {
  if (unitType === 'Phường') return 'bg-blue-100 text-blue-800'
  if (unitType === 'Thị trấn') return 'bg-purple-100 text-purple-800'
  return 'bg-emerald-100 text-emerald-800'
}

function handleEnterKey() {
  if (filteredSuggestions.value.length > 0) {
    selectSuggestion(filteredSuggestions.value[0])
  }
}

function removeVietnameseTones(str) {
  return (str || '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/đ/g, 'd')
    .replace(/Đ/g, 'D')
    .toLowerCase()
}

const filteredSuggestions = computed(() => {
  const rawQ = filterStore.search.trim().toLowerCase()
  if (!rawQ) return []
  const cleanQ = removeVietnameseTones(rawQ)

  return allSchools.value.filter(s => {
    const name = (s.name || '').toLowerCase()
    const cleanName = removeVietnameseTones(name)
    const code = (s.code || '').toLowerCase()
    const address = (s.address || '').toLowerCase()
    const cleanAddr = removeVietnameseTones(address)
    const ward = (s.ward || '').toLowerCase()
    const cleanWard = removeVietnameseTones(ward)
    const district = (s.district_name || '').toLowerCase()
    const cleanDistrict = removeVietnameseTones(district)

    return (
      name.includes(rawQ) ||
      cleanName.includes(cleanQ) ||
      code.includes(rawQ) ||
      address.includes(rawQ) ||
      cleanAddr.includes(cleanQ) ||
      ward.includes(rawQ) ||
      cleanWard.includes(cleanQ) ||
      district.includes(rawQ) ||
      cleanDistrict.includes(cleanQ)
    )
  })
})

function selectSuggestion(school) {
  targetSchoolToFly.value = school
  filterStore.search = school.name
  showSuggestions.value = false
}

function getLevelLabel(levelId) {
  const normalized = (levelId || '').toLowerCase().replace(/-/g, '_')
  return LEVEL_MAP[normalized]?.label || levelId || 'Khác'
}

function getLevelColor(levelId) {
  const normalized = (levelId || '').toLowerCase().replace(/-/g, '_')
  return LEVEL_MAP[normalized]?.color || '#2563eb'
}

function getLevelBg(levelId) {
  const normalized = (levelId || '').toLowerCase().replace(/-/g, '_')
  const bgMap = {
    gdtx: '#fef2f2',
    trung_cap: '#fffbeb',
    cao_dang: '#f0fdf4',
    dai_hoc: '#eff6ff',
  }
  return bgMap[normalized] || '#f8fafc'
}

function getLevelBorder(levelId) {
  const normalized = (levelId || '').toLowerCase().replace(/-/g, '_')
  const borderMap = {
    gdtx: '#fecaca',
    trung_cap: '#fde68a',
    cao_dang: '#bbf7d0',
    dai_hoc: '#bfdbfe',
  }
  return borderMap[normalized] || '#e2e8f0'
}

function getLevelIcon(levelId) {
  const normalized = (levelId || '').toLowerCase().replace(/-/g, '_')
  return LEVEL_MAP[normalized]?.icon || 'fa-solid fa-graduation-cap'
}

function formatSchoolAddress(school) {
  if (school.address && school.address.trim()) {
    return school.address.trim()
  }
  const parts = [school.ward, school.district_name].filter(p => p && p.trim())
  if (parts.length > 0) {
    return parts.join(', ')
  }
  return 'Tỉnh Ninh Bình'
}
</script>
