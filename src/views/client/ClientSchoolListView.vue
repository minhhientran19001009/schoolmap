<template>
  <div class="h-full overflow-y-auto bg-slate-50 selection:bg-blue-100 selection:text-blue-900" ref="pageContainer">
    <!-- 1. Top Hero Section (System-Cohesive Light Theme) -->
    <section class="relative overflow-hidden border-b border-slate-200/90 bg-gradient-to-b from-blue-50/70 via-indigo-50/30 to-slate-50">
      <div class="absolute inset-0 opacity-40 bg-[radial-gradient(#93c5fd_1px,transparent_1px)] [background-size:20px_20px] pointer-events-none"></div>
      
      <div class="relative mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
          <div class="max-w-2xl">
            <div class="mb-2.5 inline-flex items-center gap-2 rounded-full border border-blue-200/80 bg-white/90 px-3.5 py-1 text-xs font-bold text-blue-700 shadow-2xs backdrop-blur-xs">
              <i class="fa-solid fa-building-columns text-[11px] text-blue-600"></i>
              <span>Cổng tra cứu cơ sở GDNN Ninh Bình</span>
            </div>
            
            <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl lg:text-4xl leading-tight text-slate-900">
              Danh sách cơ sở GDNN Ninh Bình
            </h1>
            
            <p class="mt-2 text-xs sm:text-sm md:text-base text-slate-600 max-w-2xl leading-relaxed">
              Tra cứu thông tin cơ sở giáo dục nghề nghiệp, ngành/nghề đào tạo và chỉ tiêu tuyển sinh trên toàn địa bàn tỉnh Ninh Bình.
            </p>
          </div>

          <!-- Quick Navigation Actions -->
          <div class="flex flex-wrap items-center gap-2.5 flex-shrink-0">
            <router-link
              to="/map"
              class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-xs hover:shadow-soft hover:opacity-95 active:scale-95 transition-all">
              <i class="fa-solid fa-map-location-dot"></i>
              <span>Xem trên bản đồ</span>
            </router-link>
          </div>
        </div>

      </div>
    </section>

    <!-- 2. Main Content & Focused Filter Panel -->
    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 space-y-6">
      <!-- Filter Panel Container -->
      <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs transition-shadow hover:shadow-soft space-y-3.5">
        
        <!-- ROW 1: Search and Primary Controls Row -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
          <!-- Keyword Search Input with Clear Button -->
          <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm">
              <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <input 
              v-model="filters.q"
              @input="onSearchInput"
              @keyup.enter="applyFilters"
              type="text"
              placeholder="Tìm theo tên cơ sở, mã trường, ngành/nghề đào tạo, địa chỉ..."
              class="w-full h-11 pl-10 pr-9 text-xs sm:text-sm font-medium text-slate-800 placeholder:text-slate-400 bg-slate-50/70 hover:bg-slate-100/70 focus:bg-white rounded-xl border border-slate-200/90 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all shadow-2xs"
            />
            <button
              v-if="filters.q"
              @click="clearSearch"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 text-xs transition cursor-pointer"
              title="Xóa tìm kiếm">
              <i class="fa-solid fa-circle-xmark"></i>
            </button>
          </div>

          <!-- Right Controls: Sort Select + Reset Filter -->
          <div class="flex items-center gap-2 flex-shrink-0">
            <!-- Custom Styled Sort Popover Dropdown (No native <select> OS menu) -->
            <div ref="sortDropdownRef" class="relative min-w-[170px] sm:min-w-[195px]">
              <button
                type="button"
                @click="isSortMenuOpen = !isSortMenuOpen"
                class="w-full h-11 pl-10 pr-8 text-xs sm:text-sm font-medium rounded-xl border transition-all cursor-pointer flex items-center justify-between text-left shadow-2xs select-none"
                :class="isSortMenuOpen 
                  ? 'bg-white border-blue-400 ring-2 ring-blue-100 text-blue-700' 
                  : (filters.sort !== 'name' 
                      ? 'bg-blue-50/40 border-blue-200 font-bold text-blue-700' 
                      : 'bg-slate-50/70 hover:bg-slate-100/70 border-slate-200/90 text-slate-700')">
                
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-xs">
                  <i :class="selectedSortOption.icon"></i>
                </div>
                
                <span class="truncate block">
                  {{ selectedSortOption.label }}
                </span>

                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400 text-xs">
                  <i
                    class="fa-solid fa-chevron-down transition-transform duration-200"
                    :class="{ 'rotate-180 text-blue-600': isSortMenuOpen }"></i>
                </div>
              </button>

              <!-- Popover Menu -->
              <div
                v-if="isSortMenuOpen"
                class="absolute right-0 top-full mt-1.5 w-full sm:min-w-[200px] bg-white border border-slate-200 rounded-2xl shadow-xl p-1.5 z-50 animate-in fade-in select-none">
                <div class="space-y-0.5 text-xs">
                  <button
                    v-for="opt in sortOptions"
                    :key="opt.value"
                    type="button"
                    @click="selectSort(opt.value)"
                    :class="filters.sort === opt.value ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50'"
                    class="w-full px-2.5 py-2 rounded-xl cursor-pointer flex items-center justify-between transition-colors text-left">
                    <div class="flex items-center gap-2.5">
                      <i 
                        :class="[opt.icon, filters.sort === opt.value ? 'text-blue-600' : 'text-slate-400']" 
                        class="text-xs w-4 text-center"></i>
                      <span>{{ opt.label }}</span>
                    </div>
                    <i v-if="filters.sort === opt.value" class="fa-solid fa-check text-xs text-blue-600"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Reset Filter Button -->
            <button
              v-if="hasActiveFilter"
              @click="resetFilters"
              class="h-11 px-3 sm:px-3.5 inline-flex items-center justify-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 active:scale-95 transition-all text-xs font-bold cursor-pointer whitespace-nowrap shadow-2xs"
              title="Đặt lại bộ lọc về mặc định">
              <i class="fa-solid fa-rotate-left text-[11px]"></i>
              <span class="hidden sm:inline">Đặt lại</span>
            </button>
          </div>
        </div>

        <!-- ROW 2: Only 2 Core Filters Backed by DB: Tất cả xã / phường & Tất cả ngành nghề -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 sm:gap-3.5">
          <!-- 1. Searchable Ward Custom Popover Dropdown (Tất cả xã / phường) -->
          <div ref="wardDropdownRef" class="relative w-full">
            <button
              type="button"
              @click="isWardMenuOpen = !isWardMenuOpen"
              class="w-full h-11 pl-10 pr-8 text-xs sm:text-sm font-medium rounded-xl border transition-all cursor-pointer flex items-center justify-between text-left shadow-2xs select-none"
              :class="isWardMenuOpen 
                ? 'bg-white border-blue-400 ring-2 ring-blue-100 text-blue-700' 
                : (filters.ward !== 'all' 
                    ? 'bg-blue-50/40 border-blue-200 font-bold text-blue-700' 
                    : 'bg-slate-50/70 hover:bg-slate-100/70 border-slate-200/90 text-slate-700')">
              
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-blue-600 text-xs">
                <i class="fa-solid fa-map-pin"></i>
              </div>
              
              <span class="truncate block pr-2">
                {{ selectedWardLabel }}
              </span>

              <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5">
                <span
                  v-if="filters.ward !== 'all'"
                  @click.stop="clearWardFilter"
                  class="w-4 h-4 rounded-full text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors cursor-pointer"
                  title="Bỏ chọn xã/phường">
                  <i class="fa-solid fa-xmark text-[11px]"></i>
                </span>
                <i
                  class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200 pointer-events-none"
                  :class="{ 'rotate-180 text-blue-600': isWardMenuOpen }"></i>
              </div>
            </button>

            <!-- Searchable Popover Menu for 129 Wards -->
            <div
              v-if="isWardMenuOpen"
              class="absolute left-0 top-full mt-1.5 w-full bg-white border border-slate-200 rounded-2xl shadow-xl p-2 z-50 animate-in fade-in select-none">
              
              <!-- Search bar inside dropdown -->
              <div class="relative mb-2">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <input
                  ref="wardSearchInputRef"
                  v-model="wardSearchQuery"
                  type="text"
                  placeholder="Tìm tên xã, phường, thị trấn..."
                  class="w-full pl-8 pr-7 py-2 bg-slate-100/90 hover:bg-slate-100 focus:bg-white text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-200 outline-none transition-all placeholder:text-slate-400"
                />
                <button
                  v-if="wardSearchQuery"
                  @click="wardSearchQuery = ''"
                  class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 w-4 h-4 flex items-center justify-center cursor-pointer">
                  <i class="fa-solid fa-xmark text-[10px]"></i>
                </button>
              </div>

              <!-- Scrollable list of wards -->
              <div class="max-h-64 overflow-y-auto space-y-0.5 pr-1 text-xs">
                <!-- All Wards option -->
                <div
                  @click="selectWard('all')"
                  :class="filters.ward === 'all' ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50'"
                  class="px-2.5 py-2 rounded-xl cursor-pointer flex items-center justify-between transition-colors">
                  <div class="flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-blue-600 text-xs"></i>
                    <span>Tất cả xã / phường ({{ catalogs.wards.length }})</span>
                  </div>
                  <i v-if="filters.ward === 'all'" class="fa-solid fa-check text-xs text-blue-600"></i>
                </div>

                <div class="h-px bg-slate-100 my-1"></div>

                <!-- Empty search result -->
                <div v-if="filteredWardsList.length === 0" class="text-center py-4 text-xs text-slate-400 italic">
                  Không tìm thấy xã/phường khớp với "{{ wardSearchQuery }}"
                </div>

                <!-- Ward items -->
                <div
                  v-for="(w, idx) in filteredWardsList"
                  :key="typeof w === 'object' ? (w.id || w.code || idx) : idx"
                  @click="selectWard(w)"
                  :class="isWardSelected(w) ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50'"
                  class="px-2.5 py-2 rounded-xl cursor-pointer flex items-center justify-between transition-colors group">
                  <div class="flex items-center gap-2 min-w-0 pr-2">
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
              </div>
            </div>
          </div>

          <!-- 2. Searchable Major Custom Popover Dropdown (Tất cả ngành nghề) -->
          <div ref="majorDropdownRef" class="relative w-full">
            <!-- Trigger button matching the other select pixel-perfect -->
            <button
              type="button"
              @click="isMajorMenuOpen = !isMajorMenuOpen"
              class="w-full h-11 pl-10 pr-8 text-xs sm:text-sm font-medium rounded-xl border transition-all cursor-pointer flex items-center justify-between text-left shadow-2xs select-none"
              :class="isMajorMenuOpen 
                ? 'bg-white border-purple-400 ring-2 ring-purple-100 text-purple-700' 
                : (filters.major_id !== 'all' 
                    ? 'bg-purple-50/40 border-purple-200 font-bold text-purple-700' 
                    : 'bg-slate-50/70 hover:bg-slate-100/70 border-slate-200/90 text-slate-700')">
              
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-purple-600 text-xs">
                <i class="fa-solid fa-graduation-cap"></i>
              </div>
              
              <span class="truncate block pr-2">
                {{ selectedMajorLabel }}
              </span>

              <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5">
                <span
                  v-if="filters.major_id !== 'all'"
                  @click.stop="clearMajorFilter"
                  class="w-4 h-4 rounded-full text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors cursor-pointer"
                  title="Bỏ chọn ngành nghề">
                  <i class="fa-solid fa-xmark text-[11px]"></i>
                </span>
                <i
                  class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200 pointer-events-none"
                  :class="{ 'rotate-180 text-purple-600': isMajorMenuOpen }"></i>
              </div>
            </button>

            <!-- Searchable Popover Menu for 118+ Majors -->
            <div
              v-if="isMajorMenuOpen"
              class="absolute left-0 top-full mt-1.5 w-full bg-white border border-slate-200 rounded-2xl shadow-xl p-2 z-50 animate-in fade-in select-none">
              
              <!-- Search bar inside dropdown -->
              <div class="relative mb-2">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <input
                  ref="majorSearchInputRef"
                  v-model="majorSearchQuery"
                  type="text"
                  placeholder="Tìm tên hoặc mã ngành nghề..."
                  class="w-full pl-8 pr-7 py-2 bg-slate-100/90 hover:bg-slate-100 focus:bg-white text-xs font-medium text-slate-800 rounded-xl border border-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-200 outline-none transition-all placeholder:text-slate-400"
                />
                <button
                  v-if="majorSearchQuery"
                  @click="majorSearchQuery = ''"
                  class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 w-4 h-4 flex items-center justify-center cursor-pointer">
                  <i class="fa-solid fa-xmark text-[10px]"></i>
                </button>
              </div>

              <!-- Scrollable list of majors -->
              <div class="max-h-64 overflow-y-auto space-y-0.5 pr-1 text-xs">
                <!-- All Majors option -->
                <div
                  @click="selectMajor('all')"
                  :class="filters.major_id === 'all' ? 'bg-purple-50 text-purple-700 font-bold' : 'text-slate-700 hover:bg-slate-50'"
                  class="px-2.5 py-2 rounded-xl cursor-pointer flex items-center justify-between transition-colors">
                  <div class="flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-purple-600 text-xs"></i>
                    <span>Tất cả ngành nghề ({{ catalogs.training_majors.length }})</span>
                  </div>
                  <i v-if="filters.major_id === 'all'" class="fa-solid fa-check text-xs text-purple-600"></i>
                </div>

                <div class="h-px bg-slate-100 my-1"></div>

                <!-- Empty search result -->
                <div v-if="filteredMajorsList.length === 0" class="text-center py-4 text-xs text-slate-400 italic">
                  Không tìm thấy ngành nghề khớp với "{{ majorSearchQuery }}"
                </div>

                <!-- Major items -->
                <div
                  v-for="m in filteredMajorsList"
                  :key="m.id"
                  @click="selectMajor(m.id)"
                  :class="filters.major_id === m.id ? 'bg-purple-50 text-purple-700 font-bold' : 'text-slate-700 hover:bg-slate-50'"
                  class="px-2.5 py-2 rounded-xl cursor-pointer flex items-center justify-between transition-colors group">
                  <div class="min-w-0 pr-2">
                    <p class="font-medium truncate group-hover:text-purple-700" :title="m.name">
                      {{ m.name }}
                    </p>
                    <span v-if="m.code" class="text-[10px] text-slate-400 font-mono">
                      Mã: {{ m.code }}
                    </span>
                  </div>
                  <i v-if="filters.major_id === m.id" class="fa-solid fa-check text-xs text-purple-600 flex-shrink-0 ml-1"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ROW 3: Quick Results Indicator & Active Filter Badges -->
        <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
          <!-- Active filter badges or quick hint -->
          <div v-if="hasActiveFilter" class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
            <span class="text-[11px] font-medium text-slate-400">Đang lọc:</span>
            
            <span v-if="filters.q" class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-0.5 rounded-lg text-[11px]">
              Từ khóa: <strong>"{{ filters.q }}"</strong>
              <button @click="clearSearch" class="hover:text-blue-900 ml-0.5 cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
            </span>

            <span v-if="filters.ward !== 'all'" class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 border border-indigo-200 px-2.5 py-0.5 rounded-lg text-[11px]">
              Xã/phường: <strong>{{ filters.ward }}</strong>
              <button @click="filters.ward = 'all'; applyFilters()" class="hover:text-indigo-900 ml-0.5 cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
            </span>

            <span v-if="filters.major_id !== 'all'" class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 border border-purple-200 px-2.5 py-0.5 rounded-lg text-[11px]">
              Ngành: <strong>{{ getMajorName(filters.major_id) }}</strong>
              <button @click="clearMajorFilter" class="hover:text-purple-900 ml-0.5 cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
            </span>

            <button 
              @click="resetFilters" 
              class="text-[11px] text-rose-600 hover:text-rose-800 font-semibold underline ml-1 cursor-pointer">
              Xóa tất cả
            </button>
          </div>

          <div v-else class="text-xs text-slate-400 flex items-center gap-1.5">
            <i class="fa-solid fa-sliders text-[11px]"></i>
            <span>Chọn xã/phường hoặc ngành nghề để lọc danh sách cơ sở GDNN phù hợp</span>
          </div>

          <!-- Quick Results Indicator -->
          <div class="text-xs text-slate-500 font-medium flex items-center gap-1.5 flex-shrink-0 self-end sm:self-auto">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Đang tìm thấy <strong class="text-slate-800 font-bold">{{ meta.total || 0 }}</strong> cơ sở phù hợp</span>
          </div>
        </div>
      </div>

      <!-- 3. Results Header & Count -->
      <div class="flex items-center justify-between text-xs sm:text-sm text-slate-500 px-1">
        <div>
          <span>Hiển thị </span>
          <strong class="text-slate-800 font-bold">
            {{ meta.total === 0 ? 0 : `${meta.from || 1}–${meta.to || meta.total}` }}
          </strong>
          <span> trên tổng số </span>
          <strong class="text-blue-700 font-bold">{{ meta.total || 0 }}</strong>
          <span> cơ sở GDNN</span>
        </div>

        <div class="flex items-center gap-2 text-xs">
          <span class="hidden sm:inline text-slate-500 font-medium">Hiển thị mỗi trang:</span>
          <div ref="perPageDropdownRef" class="relative">
            <button
              type="button"
              @click="isPerPageMenuOpen = !isPerPageMenuOpen"
              class="h-8 pl-3 pr-7 text-xs text-slate-700 font-semibold bg-white rounded-lg border border-slate-200/90 hover:border-slate-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-100 outline-none cursor-pointer transition shadow-2xs flex items-center justify-between select-none"
              :class="{ 'border-blue-400 ring-1 ring-blue-100 text-blue-700': isPerPageMenuOpen }">
              <span>{{ filters.per_page }}</span>
              <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-slate-400 text-[10px]">
                <i class="fa-solid fa-chevron-down transition-transform duration-200" :class="{ 'rotate-180 text-blue-600': isPerPageMenuOpen }"></i>
              </div>
            </button>

            <!-- Popover Menu -->
            <div
              v-if="isPerPageMenuOpen"
              class="absolute right-0 bottom-full mb-1 sm:bottom-auto sm:top-full sm:mt-1 w-24 bg-white border border-slate-200 rounded-xl shadow-lg p-1 z-50 animate-in fade-in select-none">
              <div class="space-y-0.5 text-xs">
                <button
                  v-for="size in [9, 12, 18, 24]"
                  :key="size"
                  type="button"
                  @click="selectPerPage(size)"
                  :class="filters.per_page === size ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium'"
                  class="w-full px-2 py-1.5 rounded-lg cursor-pointer flex items-center justify-between transition-colors text-left">
                  <span>{{ size }}</span>
                  <i v-if="filters.per_page === size" class="fa-solid fa-check text-[10px] text-blue-600"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 4. Loading Skeleton Grid -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
        <div 
          v-for="i in 6" 
          :key="i"
          class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-4 animate-pulse">
          <div class="flex items-center justify-between">
            <div class="h-5 w-24 bg-slate-200 rounded-md"></div>
            <div class="h-4 w-16 bg-slate-100 rounded-md"></div>
          </div>
          <div class="space-y-2">
            <div class="h-5 w-3/4 bg-slate-200 rounded-md"></div>
            <div class="h-4 w-full bg-slate-100 rounded-md"></div>
          </div>
          <div class="h-16 bg-slate-50 rounded-xl"></div>
          <div class="h-9 w-full bg-slate-100 rounded-xl"></div>
        </div>
      </div>

      <!-- 5. Empty State -->
      <div 
        v-else-if="schools.length === 0"
        class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-xs">
        <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mb-3">
          <i class="fa-solid fa-building-circle-xmark"></i>
        </div>
        <h3 class="text-base font-bold text-slate-800">Không tìm thấy cơ sở GDNN phù hợp</h3>
        <p class="mt-1.5 text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
          Không có cơ sở giáo dục nghề nghiệp nào thỏa mãn điều kiện lọc của bạn. Vui lòng thử tìm kiếm với từ khóa khác hoặc đặt lại bộ lọc.
        </p>
        <button
          @click="resetFilters"
          class="mt-4 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-blue-700 transition cursor-pointer">
          <i class="fa-solid fa-rotate-left"></i>
          <span>Đặt lại toàn bộ bộ lọc</span>
        </button>
      </div>

      <!-- 6. School Cards Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
        <article
          v-for="school in schools"
          :key="school.id"
          class="group rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md flex flex-col justify-between relative overflow-hidden">
          
          <!-- Card Top Bar: Badges -->
          <div>
            <div class="flex items-start justify-between gap-2 mb-3">
              <!-- Education Level Badges -->
              <div class="flex flex-wrap items-center gap-1.5">
                <span
                  v-for="lvl in (school.education_levels || [])"
                  :key="lvl.id"
                  :style="{ backgroundColor: (lvl.color || '#16a34a') + '15', color: lvl.color || '#16a34a', borderColor: (lvl.color || '#16a34a') + '35' }"
                  class="inline-flex items-center gap-1 rounded-md border px-2 py-0.5 text-[11px] font-bold tracking-tight">
                  <i class="fa-solid fa-graduation-cap text-[10px]"></i>
                  <span>{{ lvl.name }}</span>
                </span>
              </div>

              <!-- School Code -->
              <span 
                v-if="school.code"
                class="font-mono text-[10px] font-semibold text-slate-400 bg-slate-50 border border-slate-100 px-1.5 py-0.5 rounded-md flex-shrink-0"
                title="Mã cơ sở đào tạo">
                #{{ school.code }}
              </span>
            </div>

            <!-- School Name -->
            <h2 
              @click="openSchoolDetail(school)"
              class="font-extrabold text-slate-900 text-base leading-snug line-clamp-2 group-hover:text-blue-700 transition-colors cursor-pointer"
              :title="school.name">
              {{ school.name }}
            </h2>

            <!-- Address / Ward -->
            <div class="mt-2.5 flex items-start gap-1.5 text-xs text-slate-500">
              <i class="fa-solid fa-location-dot text-slate-400 text-xs mt-0.5 flex-shrink-0"></i>
              <span class="line-clamp-2 leading-relaxed">
                {{ formatAddress(school) }}
              </span>
            </div>

            <!-- Training Majors Preview Section -->
            <div class="mt-4 pt-3.5 border-t border-slate-100">
              <div class="flex items-center justify-between text-xs mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                  Ngành nghề nổi bật
                </span>
                <span class="text-xs font-semibold text-blue-600">
                  {{ school.training_major_count || 0 }} ngành
                </span>
              </div>

              <!-- Top Majors List (Max 3) -->
              <div v-if="school.training_major_preview?.length > 0" class="space-y-1.5">
                <div
                  v-for="(major, idx) in school.training_major_preview"
                  :key="idx"
                  class="flex items-center justify-between gap-2 text-xs py-1 px-2 rounded-lg bg-slate-50/80 hover:bg-slate-100/80 transition-colors">
                  <div class="flex items-center gap-1.5 min-w-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 flex-shrink-0"></span>
                    <span class="truncate font-medium text-slate-700" :title="major.name">
                      {{ major.name }}
                    </span>
                  </div>

                  <!-- Quota if available -->
                  <span 
                    v-if="major.annual_quota > 0" 
                    class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-md flex-shrink-0"
                    title="Chỉ tiêu tuyển sinh">
                    {{ major.annual_quota }} chỉ tiêu
                  </span>
                </div>

                <!-- More majors count badge -->
                <p 
                  v-if="school.training_major_count > school.training_major_preview.length"
                  class="text-[11px] text-slate-400 italic pt-0.5 pl-1">
                  + Còn {{ school.training_major_count - school.training_major_preview.length }} ngành nghề khác...
                </p>
              </div>

              <!-- No majors fallback -->
              <div v-else class="text-[11px] text-slate-400 italic py-1">
                Đang cập nhật danh mục ngành nghề đào tạo
              </div>
            </div>
          </div>

          <!-- Card Actions -->
          <div class="mt-5 pt-3.5 border-t border-slate-100/90">
            <div class="grid grid-cols-2 gap-2">
              <!-- Action 1: View Detail Drawer -->
              <button
                @click="openSchoolDetail(school)"
                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200/80 py-2 px-3 text-xs font-bold transition-all active:scale-95 cursor-pointer"
                title="Mở hồ sơ chi tiết cơ sở">
                <i class="fa-solid fa-circle-info text-xs"></i>
                <span>Xem chi tiết</span>
              </button>

              <!-- Action 2: View on Map -->
              <router-link
                :to="{ path: '/map', query: { school: school.id } }"
                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 py-2 px-3 text-xs font-bold transition-all active:scale-95 cursor-pointer"
                title="Định vị cơ sở này trên bản đồ số GDNN">
                <i class="fa-solid fa-map-location-dot text-xs text-indigo-600"></i>
                <span>Xem bản đồ</span>
              </router-link>
            </div>
          </div>
        </article>
      </div>

      <!-- 7. Server-Side Pagination Controls -->
      <div 
        v-if="meta.last_page > 1" 
        class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200 pt-5">
        
        <!-- Record Range Info -->
        <p class="text-xs sm:text-sm text-slate-500 order-2 sm:order-1">
          Trang <strong class="text-slate-800">{{ meta.current_page }}</strong> / {{ meta.last_page }} 
          (Hiển thị {{ meta.from }}–{{ meta.to }} trong tổng {{ meta.total }} cơ sở)
        </p>

        <!-- Pagination Buttons -->
        <nav class="inline-flex items-center gap-1.5 order-1 sm:order-2" aria-label="Pagination">
          <!-- First Page -->
          <button
            @click="goToPage(1)"
            :disabled="meta.current_page === 1 || loading"
            class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center text-xs transition cursor-pointer"
            title="Trang đầu">
            <i class="fa-solid fa-angles-left"></i>
          </button>

          <!-- Prev Page -->
          <button
            @click="goToPage(meta.current_page - 1)"
            :disabled="meta.current_page === 1 || loading"
            class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center text-xs transition cursor-pointer"
            title="Trang trước">
            <i class="fa-solid fa-chevron-left"></i>
          </button>

          <!-- Page Numbers -->
          <button
            v-for="p in visiblePages"
            :key="p"
            @click="goToPage(p)"
            :disabled="loading"
            :class="p === meta.current_page ? 'bg-blue-600 text-white font-bold border-blue-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50 font-medium'"
            class="w-9 h-9 rounded-xl border flex items-center justify-center text-xs transition cursor-pointer">
            {{ p }}
          </button>

          <!-- Next Page -->
          <button
            @click="goToPage(meta.current_page + 1)"
            :disabled="meta.current_page === meta.last_page || loading"
            class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center text-xs transition cursor-pointer"
            title="Trang sau">
            <i class="fa-solid fa-chevron-right"></i>
          </button>

          <!-- Last Page -->
          <button
            @click="goToPage(meta.last_page)"
            :disabled="meta.current_page === meta.last_page || loading"
            class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center text-xs transition cursor-pointer"
            title="Trang cuối">
            <i class="fa-solid fa-angles-right"></i>
          </button>
        </nav>
      </div>
    </main>

    <!-- 8. School Detail Drawer Integration -->
    <SchoolDetailDrawer
      v-if="selectedSchool"
      :school="selectedSchool"
      @close="closeSchoolDetail"
      @buffer-analyze="handleBufferAnalyze"
      @view-related-campus="openRelatedCampus"
    />

    <ClientFooter class="mt-auto" />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { schoolService } from '../../services/schoolService'
import SchoolDetailDrawer from '../../components/school/SchoolDetailDrawer.vue'

const route = useRoute()
const router = useRouter()
const pageContainer = ref(null)

// State
const loading = ref(false)
const schools = ref([])
const meta = ref({
  current_page: 1,
  per_page: 12,
  total: 0,
  last_page: 1,
  from: 0,
  to: 0,
  summary: {
    school_count: 0,
    level_count: 0,
    major_count: 0,
    annual_quota_total: 0
  }
})

// Catalogs
const catalogs = ref({
  education_levels: [],
  districts: [],
  wards: [],
  school_types: [],
  training_majors: []
})

// Active Filters (Focused on DB-backed fields: q, ward, major_id, sort, pagination)
const filters = reactive({
  q: '',
  ward: 'all',
  major_id: 'all',
  sort: 'name',
  page: 1,
  per_page: 12
})

// Searchable Ward Dropdown State
const isWardMenuOpen = ref(false)
const wardDropdownRef = ref(null)
const wardSearchInputRef = ref(null)
const wardSearchQuery = ref('')

// Searchable Major Dropdown State
const isMajorMenuOpen = ref(false)
const majorDropdownRef = ref(null)
const majorSearchInputRef = ref(null)
const majorSearchQuery = ref('')

// Custom Sort Popover Dropdown State
const isSortMenuOpen = ref(false)
const sortDropdownRef = ref(null)
const sortOptions = [
  { value: 'name', label: 'Tên: A → Z', icon: 'fa-solid fa-arrow-down-a-z' },
  { value: 'name_desc', label: 'Tên: Z → A', icon: 'fa-solid fa-arrow-up-z-a' },
  { value: 'quota_desc', label: 'Chỉ tiêu cao nhất', icon: 'fa-solid fa-user-graduate' },
  { value: 'newest', label: 'Mới cập nhật', icon: 'fa-solid fa-clock-rotate-left' }
]

const selectedSortOption = computed(() => {
  return sortOptions.find(opt => opt.value === filters.sort) || sortOptions[0]
})

function selectSort(val) {
  filters.sort = val
  isSortMenuOpen.value = false
  applyFilters()
}

// Custom Per-Page Dropdown State
const isPerPageMenuOpen = ref(false)
const perPageDropdownRef = ref(null)

function selectPerPage(count) {
  filters.per_page = count
  isPerPageMenuOpen.value = false
  applyFilters()
}

// Detail Drawer State
const selectedSchool = ref(null)

// Search debounce timer
let searchTimeout = null

// Computed: Check if any active filter is applied
const hasActiveFilter = computed(() => {
  return filters.q !== '' || 
         filters.ward !== 'all' || 
         filters.major_id !== 'all' || 
         filters.sort !== 'name'
})

// Ward Helper Functions
function getWardDisplayName(w) {
  if (typeof w === 'object' && w !== null) {
    return w.full_name || (w.unit_type ? `${w.unit_type} ${w.name}` : w.name)
  }
  return String(w || '')
}

function getWardFilterValue(w) {
  if (typeof w === 'object' && w !== null) {
    return w.full_name || w.name
  }
  return String(w || '')
}

const selectedWardLabel = computed(() => {
  if (filters.ward === 'all') {
    return `Tất cả xã / phường (${catalogs.value.wards.length || 0})`
  }
  return filters.ward
})

const filteredWardsList = computed(() => {
  const query = wardSearchQuery.value.trim().toLowerCase()
  const list = catalogs.value.wards || []
  if (!query) return list
  return list.filter(w => {
    const name = getWardDisplayName(w).toLowerCase()
    return name.includes(query)
  })
})

function selectWard(ward) {
  if (ward === 'all') {
    filters.ward = 'all'
  } else {
    filters.ward = getWardFilterValue(ward)
  }
  isWardMenuOpen.value = false
  wardSearchQuery.value = ''
  applyFilters()
}

function clearWardFilter() {
  filters.ward = 'all'
  wardSearchQuery.value = ''
  applyFilters()
}

function isWardSelected(w) {
  if (filters.ward === 'all') return false
  const val = getWardFilterValue(w).toLowerCase()
  const selected = filters.ward.toLowerCase()
  return selected === val || (typeof w === 'object' && w.name && selected.includes(w.name.toLowerCase()))
}

// Major Dropdown Computed Label
const selectedMajorLabel = computed(() => {
  if (filters.major_id === 'all') {
    return `Tất cả ngành nghề (${catalogs.value.training_majors.length || 0})`
  }
  const m = catalogs.value.training_majors.find(item => String(item.id) === String(filters.major_id))
  if (m) {
    return m.code ? `${m.name} (${m.code})` : m.name
  }
  return 'Tất cả ngành nghề'
})

// Filtered major list based on user search inside popover
const filteredMajorsList = computed(() => {
  const query = majorSearchQuery.value.trim().toLowerCase()
  const majors = catalogs.value.training_majors || []
  if (!query) return majors
  return majors.filter(m => 
    (m.name && m.name.toLowerCase().includes(query)) ||
    (m.code && m.code.toLowerCase().includes(query))
  )
})

// Visible pagination numbers (max 5 buttons)
const visiblePages = computed(() => {
  const current = meta.value.current_page
  const total = meta.value.last_page
  const pages = []
  
  if (total <= 5) {
    for (let i = 1; i <= total; i++) pages.push(i)
  } else if (current <= 3) {
    pages.push(1, 2, 3, 4, 5)
  } else if (current >= total - 2) {
    pages.push(total - 4, total - 3, total - 2, total - 1, total)
  } else {
    pages.push(current - 2, current - 1, current, current + 1, current + 2)
  }
  return pages
})

// Helper functions
function formatAddress(school) {
  if (school.address) return school.address
  const parts = []
  if (school.ward) parts.push(school.ward)
  if (school.district_name) parts.push(school.district_name)
  parts.push('Tỉnh Ninh Bình')
  return parts.join(', ')
}

function getMajorName(majorId) {
  const m = catalogs.value.training_majors.find(item => String(item.id) === String(majorId))
  return m ? m.name : majorId
}

// Lifecycle
onMounted(async () => {
  document.addEventListener('click', handleClickOutside)
  await loadCatalogs()
  initFiltersFromQuery()
  await fetchSchools()

  // Handle URL ?school=ID to open drawer directly
  if (route.query.school) {
    openSchoolDetailById(route.query.school)
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

function handleClickOutside(e) {
  if (sortDropdownRef.value && !sortDropdownRef.value.contains(e.target)) {
    isSortMenuOpen.value = false
  }
  if (wardDropdownRef.value && !wardDropdownRef.value.contains(e.target)) {
    isWardMenuOpen.value = false
  }
  if (majorDropdownRef.value && !majorDropdownRef.value.contains(e.target)) {
    isMajorMenuOpen.value = false
  }
  if (perPageDropdownRef.value && !perPageDropdownRef.value.contains(e.target)) {
    isPerPageMenuOpen.value = false
  }
}

// Sync query params when route changes
watch(() => route.query, () => {
  initFiltersFromQuery()
  fetchSchools()
})

// Load Catalogs from Backend
async function loadCatalogs() {
  const data = await schoolService.getPublicCatalogs()
  if (data) {
    catalogs.value = {
      education_levels: data.education_levels || [],
      districts: data.districts || [],
      wards: data.wards || [],
      school_types: data.school_types || [],
      training_majors: data.training_majors || []
    }
  }
}

// Initialize state from URL Query Params
function initFiltersFromQuery() {
  const q = route.query
  filters.q = typeof q.q === 'string' ? q.q : ''
  filters.ward = typeof q.ward === 'string' && q.ward ? q.ward : 'all'
  filters.major_id = q.major_id ? (isNaN(q.major_id) ? 'all' : Number(q.major_id)) : 'all'
  filters.sort = typeof q.sort === 'string' && q.sort ? q.sort : 'name'
  filters.page = q.page ? Math.max(1, parseInt(q.page, 10)) : 1
  filters.per_page = q.per_page ? Math.max(6, parseInt(q.per_page, 10)) : 12
}

// Push state to URL
function syncQueryToUrl() {
  const query = {}
  if (filters.q) query.q = filters.q
  if (filters.ward !== 'all') query.ward = filters.ward
  if (filters.major_id !== 'all') query.major_id = String(filters.major_id)
  if (filters.sort !== 'name') query.sort = filters.sort
  if (filters.page > 1) query.page = String(filters.page)
  if (filters.per_page !== 12) query.per_page = String(filters.per_page)

  router.replace({ query }).catch(() => {})
}

// Fetch schools from backend API
async function fetchSchools() {
  loading.value = true
  try {
    const params = {
      q: filters.q,
      ward: filters.ward,
      major_id: filters.major_id === 'all' ? null : filters.major_id,
      sort: filters.sort,
      page: filters.page,
      per_page: filters.per_page
    }

    const res = await schoolService.getPublicSchools(params)
    if (res && res.data) {
      schools.value = res.data
      meta.value = res.meta || {
        current_page: filters.page,
        per_page: filters.per_page,
        total: res.data.length,
        last_page: 1,
        from: 1,
        to: res.data.length,
        summary: {}
      }
    }
  } catch (err) {
    console.error('Error fetching public schools:', err)
  } finally {
    loading.value = false
  }
}

// Actions
function onSearchInput() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    filters.page = 1
    syncQueryToUrl()
    fetchSchools()
  }, 350)
}

function clearSearch() {
  filters.q = ''
  filters.page = 1
  syncQueryToUrl()
  fetchSchools()
}

function selectMajor(majorId) {
  filters.major_id = majorId
  isMajorMenuOpen.value = false
  majorSearchQuery.value = ''
  applyFilters()
}

function clearMajorFilter() {
  filters.major_id = 'all'
  majorSearchQuery.value = ''
  applyFilters()
}

function applyFilters() {
  filters.page = 1
  syncQueryToUrl()
  fetchSchools()
}

function resetFilters() {
  filters.q = ''
  filters.ward = 'all'
  filters.major_id = 'all'
  filters.sort = 'name'
  filters.page = 1
  filters.per_page = 12
  wardSearchQuery.value = ''
  majorSearchQuery.value = ''
  isWardMenuOpen.value = false
  isMajorMenuOpen.value = false
  isSortMenuOpen.value = false
  isPerPageMenuOpen.value = false
  syncQueryToUrl()
  fetchSchools()
}

function goToPage(page) {
  if (page < 1 || page > meta.value.last_page || page === meta.value.current_page) return
  filters.page = page
  syncQueryToUrl()
  fetchSchools()
  
  // Smooth scroll up to top of list
  if (pageContainer.value) {
    pageContainer.value.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

// School Detail Drawer Logic
async function openSchoolDetail(school) {
  selectedSchool.value = school
  
  // Lazily load full detail fields
  const detail = await schoolService.getDetails(school.id || school.code)
  if (detail && selectedSchool.value?.id === school.id) {
    selectedSchool.value = detail
  }
}

async function openSchoolDetailById(id) {
  const detail = await schoolService.getDetails(id)
  if (detail) {
    selectedSchool.value = detail
  }
}

function closeSchoolDetail() {
  selectedSchool.value = null
}

function openRelatedCampus(campus) {
  openSchoolDetail(campus)
}

function handleBufferAnalyze(school) {
  // Navigate to map and trigger buffer analysis
  router.push({ path: '/map', query: { buffer: school.id } })
}
</script>

<style scoped>
/* Custom transitions and select styling */
select {
  background-image: none;
}
</style>
