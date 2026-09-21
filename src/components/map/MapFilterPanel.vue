<template>
  <div 
    class="absolute top-3 left-3 z-[1000] w-48 sm:w-52 max-w-[calc(100vw-24px)] glass-panel rounded-2xl p-2.5 shadow-lg border border-slate-200/90 transition-all duration-200 select-none animate-in fade-in slide-in-from-top-1">
    
    <!-- Panel Header: Title + Close Button -->
    <div class="flex items-center justify-between text-xs px-1 pb-1.5 border-b border-slate-100">
      <span class="font-bold text-slate-800 flex items-center gap-1.5 text-xs">
        <i class="fa-solid fa-filter text-blue-600 text-[11px]"></i>
        <span>Bộ lọc bản đồ</span>
      </span>
      <button 
        @click="$emit('close')"
        class="text-slate-400 hover:text-slate-600 w-5 h-5 rounded-full hover:bg-slate-200/60 flex items-center justify-center transition-colors cursor-pointer"
        title="Đóng bộ lọc">
        <i class="fa-solid fa-xmark text-xs"></i>
      </button>
    </div>

    <!-- Active Ward Scope Indicator -->
    <div v-if="filterStore.ward !== 'all'" class="my-1.5 px-0.5">
      <div class="bg-blue-50/90 border border-blue-200/80 rounded-xl px-2 py-1 flex items-center justify-between text-[11px] shadow-2xs">
        <div class="flex items-center gap-1.5 min-w-0 text-blue-900 font-medium truncate">
          <i class="fa-solid fa-location-dot text-blue-600 text-[10px] flex-shrink-0"></i>
          <span class="truncate font-semibold">{{ activeWardDisplayName }}</span>
        </div>
        <button 
          @click="clearWardScope" 
          class="text-slate-400 hover:text-rose-600 ml-1 p-0.5 flex-shrink-0 cursor-pointer transition-colors"
          title="Bỏ lọc địa bàn, xem toàn tỉnh">
          <i class="fa-solid fa-xmark text-[11px]"></i>
        </button>
      </div>
    </div>

    <!-- Vertical List of Education Levels -->
    <div class="space-y-1 my-2">
      <button 
        v-for="lvl in levels"
        :key="lvl.id"
        @click="filterStore.level = lvl.id"
        :class="filterStore.level === lvl.id 
          ? 'bg-blue-800 text-white font-semibold shadow-xs' 
          : 'text-slate-700 hover:bg-slate-100/90 font-medium'"
        class="w-full px-2 py-1.5 rounded-xl text-xs transition-all flex items-center justify-between group cursor-pointer text-left">
        
        <div class="flex items-center gap-2 min-w-0">
          <i 
            :class="lvl.icon || 'fa-solid fa-graduation-cap'" 
            class="text-[11px] w-3.5 text-center flex-shrink-0 transition-transform group-hover:scale-110"
            :style="{ color: filterStore.level === lvl.id ? '#ffffff' : lvl.color }"></i>
          
          <span class="truncate text-[11px]">{{ lvl.label }}</span>
        </div>

        <!-- Count Badge -->
        <span 
          :class="filterStore.level === lvl.id 
            ? 'bg-white/20 text-white' 
            : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200'"
          class="text-[10px] px-1.5 py-0.5 rounded-md font-mono flex-shrink-0 ml-1">
          {{ getLevelCount(lvl.id) }}
        </span>
      </button>
    </div>

    <!-- One searchable training-major filter -->
    <div class="pt-2 border-t border-slate-100">
      <div class="flex items-center justify-between px-1 mb-1.5">
        <span class="text-[11px] font-bold text-slate-800 flex items-center gap-1.5">
          <i class="fa-solid fa-list-check text-blue-700"></i>
          <span>Chuyên ngành đào tạo</span>
        </span>
        <button
          v-if="filterStore.majorId !== 'all'"
          @click="filterStore.majorId = 'all'"
          class="text-[10px] text-slate-400 hover:text-rose-600 cursor-pointer"
          title="Bỏ lọc chuyên ngành">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <div class="relative mb-1.5">
        <i class="fa-solid fa-magnifying-glass absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
        <input
          v-model="majorSearchQuery"
          type="text"
          placeholder="Tìm chuyên ngành..."
          class="w-full pl-6 pr-2 py-1.5 bg-slate-100/80 rounded-lg border border-slate-200 text-[11px] outline-none focus:border-blue-400 focus:bg-white"
        />
      </div>
      <div class="max-h-32 overflow-y-auto space-y-0.5 pr-0.5">
        <button
          @click="filterStore.majorId = 'all'"
          :class="filterStore.majorId === 'all' ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100'"
          class="w-full px-2 py-1.5 rounded-lg text-[11px] text-left flex items-center justify-between cursor-pointer">
          <span>Tất cả chuyên ngành</span>
          <i v-if="filterStore.majorId === 'all'" class="fa-solid fa-check text-[10px]"></i>
        </button>
        <button
          v-for="major in filteredMajors"
          :key="major.id"
          @click="filterStore.majorId = String(major.id)"
          :class="String(filterStore.majorId) === String(major.id) ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100'"
          class="w-full px-2 py-1.5 rounded-lg text-[11px] text-left flex items-center justify-between cursor-pointer">
          <span
            class="min-w-0 flex-1 pr-1 leading-4 whitespace-normal break-words"
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
            <i v-if="String(filterStore.majorId) === String(major.id)" class="fa-solid fa-check text-[10px] flex-shrink-0"></i>
          </span>
        </button>
        <p v-if="filteredMajors.length === 0" class="text-[10px] text-slate-400 italic text-center py-2">Không tìm thấy ngành phù hợp</p>
      </div>
    </div>

    <!-- Summary Count Badge -->
    <div class="pt-1.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 px-1">
      <span>{{ filterStore.ward !== 'all' ? 'Trên địa bàn:' : 'Đang hiển thị:' }}</span>
      <span class="font-bold text-blue-800 font-mono">
        {{ filteredCount }}/{{ scopedSchools.length }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { EDUCATION_LEVELS, liveTrainingMajors, schoolService } from '../../services/schoolService'
import { filterStore } from '../../services/filterStore'

const props = defineProps({
  schools: {
    type: Array,
    default: () => []
  },
  filteredCount: {
    type: Number,
    default: 0
  },
  totalCount: {
    type: Number,
    default: 0
  }
})

defineEmits(['close'])

const levels = EDUCATION_LEVELS
const majors = liveTrainingMajors
const majorSearchQuery = ref('')

const filteredMajors = computed(() => {
  const query = majorSearchQuery.value.trim().toLocaleLowerCase('vi')
  if (!query) return majors.value
  return majors.value.filter(major => (major.name || '').toLocaleLowerCase('vi').includes(query))
})

onMounted(() => {
  schoolService.getTrainingMajors()
})

function normalizeWard(str) {
  return (str || '')
    .toLowerCase()
    .replace(/^(xã|phường|thị trấn)\s+/i, '')
    .trim()
}

// Scoped schools by current ward filter
const scopedSchools = computed(() => {
  if (!props.schools || props.schools.length === 0) return []
  if (!filterStore.ward || filterStore.ward === 'all') return props.schools

  const target = filterStore.ward.toLowerCase().trim()
  const targetNorm = normalizeWard(target)

  return props.schools.filter(s => {
    const sw = (s.ward || '').toLowerCase().trim()
    const swNorm = normalizeWard(sw)
    if (swNorm && targetNorm) {
      return swNorm === targetNorm
    }
    if (!s.ward && s.address) {
      const sa = (s.address || '').toLowerCase().trim()
      const regex = new RegExp(`(^|[,\\s])(xã|phường|thị trấn)\\s+${targetNorm}([,\\s]|$)`, 'i')
      return regex.test(sa)
    }
    return false
  })
})

// Once a major is selected, education-level counters must use the same
// major scope as the map results instead of counting every school in the ward.
const majorScopedSchools = computed(() => {
  if (filterStore.majorId === 'all') return scopedSchools.value

  const selectedMajorId = String(filterStore.majorId)

  return scopedSchools.value.filter(school => {
    const majorIds = Array.isArray(school.training_major_ids)
      ? school.training_major_ids.map(id => String(id))
      : []

    return majorIds.includes(selectedMajorId)
  })
})

const activeWardDisplayName = computed(() => {
  if (filterStore.ward === 'all') return ''
  const first = scopedSchools.value[0]
  if (first && first.ward) return first.ward
  return filterStore.ward
})

function clearWardScope() {
  filterStore.ward = 'all'
}

function getLevelCount(levelId) {
  if (levelId === 'all') return majorScopedSchools.value.length
  return majorScopedSchools.value.filter(s => {
    const ids = Array.isArray(s.education_level_ids) && s.education_level_ids.length > 0
      ? s.education_level_ids
      : [s.education_level]
    return ids.map(id => String(id).replace(/-/g, '_')).includes(levelId)
  }).length
}
</script>
