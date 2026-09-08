<template>
  <div class="flex items-center gap-1.5 sm:gap-2 flex-1 max-w-3xl justify-end md:justify-center min-w-0">
    <!-- Search Bar with Live Suggestions -->
    <div class="relative flex-1 min-w-0 sm:max-w-xs md:max-w-sm" ref="searchContainer">
      <div class="relative flex items-center">
        <i class="fa-solid fa-magnifying-glass absolute left-2.5 sm:left-3 text-slate-400 text-xs pointer-events-none"></i>
        <input 
          v-model="filterStore.search"
          @focus="showSuggestions = true"
          @keydown.esc="showSuggestions = false"
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
        v-if="showSuggestions && filteredSuggestions.length > 0"
        class="absolute left-0 right-0 sm:right-auto sm:w-80 md:w-96 top-9 mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl py-1 max-h-64 overflow-y-auto z-50">
        <div 
          v-for="school in filteredSuggestions.slice(0, 7)"
          :key="school.id"
          @click="selectSuggestion(school)"
          class="px-3 py-2 hover:bg-blue-50 cursor-pointer text-xs border-b border-slate-50 last:border-0 flex items-start gap-2 transition-colors">
          <span 
            class="w-2.5 h-2.5 rounded-full mt-1 flex-shrink-0"
            :style="{ backgroundColor: getLevelColor(school.education_level) }"></span>
          <div class="flex-1 min-w-0">
            <p class="font-semibold text-slate-800 truncate">{{ school.name }}</p>
            <p class="text-[11px] text-slate-500 truncate">{{ school.ward || '' }}, {{ school.district_name || '' }}</p>
          </div>
          <span class="text-[10px] font-medium text-slate-400 uppercase flex-shrink-0">{{ school.education_level }}</span>
        </div>
      </div>
    </div>

    <!-- Desktop & Tablet: Searchable Ward Combobox (hidden on < sm) -->
    <div class="hidden sm:block relative w-36 sm:w-44 md:w-56 lg:w-64 flex-shrink-0" ref="wardContainer">
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

    <!-- Mobile Filter Trigger Button (< sm only) -->
    <button 
      @click="isMobileFilterOpen = true"
      :class="hasNonSearchFilter ? 'bg-blue-800 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200/90'"
      class="sm:hidden relative w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 transition-all cursor-pointer shadow-2xs"
      title="Mở bộ lọc (Cấp học & Xã/Phường)">
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
      class="hidden sm:flex text-[11px] text-blue-700 hover:text-blue-900 font-semibold px-2 py-1.5 rounded-lg hover:bg-blue-50 items-center gap-1 flex-shrink-0 transition-colors cursor-pointer"
      title="Xóa toàn bộ bộ lọc">
      <i class="fa-solid fa-rotate-left text-[10px]"></i>
      <span class="hidden md:inline">Đặt lại</span>
    </button>

    <!-- ============================================================== -->
    <!-- MOBILE FILTER BOTTOM SHEET / MODAL (< sm)                      -->
    <!-- ============================================================== -->
    <div 
      v-if="isMobileFilterOpen"
      class="fixed inset-0 z-[1400] bg-slate-900/50 backdrop-blur-xs flex flex-col justify-end sm:hidden animate-in fade-in select-none">
      
      <!-- Backdrop tap dismiss -->
      <div class="flex-1" @click="isMobileFilterOpen = false"></div>

      <!-- Sheet Container -->
      <div class="bg-white rounded-t-3xl shadow-2xl flex flex-col max-h-[82vh] overflow-hidden animate-in slide-in-from-bottom duration-200 border-t border-slate-200">
        
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
        <div class="p-4 overflow-y-auto space-y-4">
          
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

          <!-- Section 2: Ward / Commune (129 Wards) -->
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
        <div class="p-3 border-t border-slate-100 bg-slate-50 flex items-center gap-2 flex-shrink-0">
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { filterStore, targetSchoolToFly } from '../../services/filterStore'
import { schoolService, LEVEL_MAP, liveSchools, EDUCATION_LEVELS } from '../../services/schoolService'

const wards = ref([])
const allSchools = liveSchools
const showSuggestions = ref(false)
const searchContainer = ref(null)

const levels = EDUCATION_LEVELS

// Mobile sheet state
const isMobileFilterOpen = ref(false)

// Searchable Ward Combobox state
const isWardMenuOpen = ref(false)
const wardSearchQuery = ref('')
const wardContainer = ref(null)
const wardSearchInputRef = ref(null)

const hasNonSearchFilter = computed(() => {
  return filterStore.level !== 'all' || filterStore.ward !== 'all'
})

onMounted(async () => {
  // Load 129 wards directly from MySQL DB
  wards.value = await schoolService.getWards()

  // Sync live schools from MySQL
  await schoolService.syncFromApi()

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

function getUnitBadgeClass(unitType) {
  if (unitType === 'Phường') return 'bg-blue-100 text-blue-800'
  if (unitType === 'Thị trấn') return 'bg-purple-100 text-purple-800'
  return 'bg-emerald-100 text-emerald-800'
}

const filteredSuggestions = computed(() => {
  const q = filterStore.search.toLowerCase().trim()
  if (!q) return []
  return allSchools.value.filter(s => {
    return s.name.toLowerCase().includes(q) ||
      (s.code && s.code.toLowerCase().includes(q)) ||
      (s.address && s.address.toLowerCase().includes(q)) ||
      (s.ward && s.ward.toLowerCase().includes(q))
  })
})

function selectSuggestion(school) {
  targetSchoolToFly.value = school
  filterStore.search = school.name
  showSuggestions.value = false
}

function getLevelColor(levelId) {
  return LEVEL_MAP[levelId]?.color || '#64748b'
}
</script>
