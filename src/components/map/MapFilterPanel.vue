<template>
  <div 
    class="absolute top-3 left-3 z-[1000] w-48 sm:w-52 max-w-[calc(100vw-24px)] glass-panel rounded-2xl p-2.5 shadow-lg border border-slate-200/90 transition-all duration-200 select-none animate-in fade-in slide-in-from-top-1">
    
    <!-- Panel Header: Title + Close Button -->
    <div class="flex items-center justify-between text-xs px-1 pb-1.5 border-b border-slate-100">
      <span class="font-bold text-slate-800 flex items-center gap-1.5 text-xs">
        <i class="fa-solid fa-filter text-blue-600 text-[11px]"></i>
        <span>Lọc theo cấp học</span>
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
import { computed } from 'vue'
import { EDUCATION_LEVELS } from '../../services/schoolService'
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
  if (levelId === 'all') return scopedSchools.value.length
  return scopedSchools.value.filter(s => s.education_level === levelId).length
}
</script>
