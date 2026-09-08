<template>
  <div 
    class="absolute bottom-6 left-3 z-[1000] glass-panel rounded-xl p-3 shadow-md border border-slate-200/80 text-xs select-none max-w-[240px] sm:max-w-[260px]">
    
    <div class="flex items-center justify-between font-bold text-slate-800 mb-2">
      <span class="flex items-center gap-1.5">
        <i class="fa-solid fa-layer-group text-blue-700"></i>
        <span>Phân loại cấp học</span>
      </span>
      <div class="flex items-center gap-1.5">
        <button 
          @click="isCollapsed = !isCollapsed" 
          class="text-slate-400 hover:text-slate-600 text-xs w-5 h-5 flex items-center justify-center cursor-pointer"
          :title="isCollapsed ? 'Mở rộng' : 'Thu gọn'">
          <i :class="isCollapsed ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'"></i>
        </button>
        <button 
          @click="$emit('close')"
          class="text-slate-400 hover:text-slate-600 w-5 h-5 rounded-full hover:bg-slate-200/60 flex items-center justify-center transition-colors cursor-pointer"
          title="Đóng bảng phân loại">
          <i class="fa-solid fa-xmark text-xs"></i>
        </button>
      </div>
    </div>

    <div v-show="!isCollapsed" class="space-y-1.5">
      <div 
        v-for="lvl in activeLevels" 
        :key="lvl.id"
        @click="$emit('select-level', lvl.id)"
        class="flex items-center justify-between py-0.5 px-1 rounded-md hover:bg-slate-100 cursor-pointer transition-colors group">
        <div class="flex items-center gap-2">
          <span 
            class="w-3 h-3 rounded-full flex-shrink-0 shadow-2xs border border-white"
            :style="{ backgroundColor: lvl.color }"></span>
          <span class="text-slate-700 text-[11px] group-hover:text-blue-800 font-medium">{{ lvl.label }}</span>
        </div>
        <span class="text-[10px] font-bold text-slate-400 group-hover:text-blue-700 bg-slate-100 px-1.5 py-0.2 rounded-full">
          {{ getCountByLevel(lvl.id) }}
        </span>
      </div>

      <div class="pt-2 mt-1 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between font-semibold">
        <span>Đang hiển thị:</span>
        <span class="text-blue-800 font-bold">{{ displayedCount }} trường</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { EDUCATION_LEVELS } from '../../services/schoolService'

const props = defineProps({
  schools: {
    type: Array,
    default: () => []
  },
  displayedCount: {
    type: Number,
    default: 0
  }
})

defineEmits(['select-level', 'close'])

const isCollapsed = ref(false)

// Exclude 'all' from legend items
const activeLevels = EDUCATION_LEVELS.filter(l => l.id !== 'all')

function getCountByLevel(levelId) {
  return props.schools.filter(s => s.education_level === levelId).length
}
</script>
