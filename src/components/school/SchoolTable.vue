<template>
  <div class="h-full flex flex-col bg-white overflow-hidden p-4 sm:p-6 select-none">
    
    <!-- Top Controls: Search, Filters, Actions -->
    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 mb-4 flex-shrink-0">
      
      <div class="flex flex-wrap items-center gap-2 flex-1">
        <!-- Search -->
        <div class="relative min-w-[240px] flex-1 max-w-sm">
          <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Tìm theo tên, mã trường, địa chỉ..."
            class="w-full pl-8 pr-3 py-2 bg-slate-50 hover:bg-slate-100/80 focus:bg-white text-xs border border-slate-200 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all"
          />
        </div>

        <!-- Level Filter -->
        <select 
          v-model="selectedLevel"
          class="px-3 py-2 bg-slate-50 text-xs font-medium text-slate-700 border border-slate-200 rounded-xl outline-none focus:border-blue-500 cursor-pointer">
          <option value="all">Tất cả cấp học</option>
          <option v-for="l in levels" :key="l.id" :value="l.id">{{ l.label }}</option>
        </select>

        <!-- District Filter -->
        <select 
          v-model="selectedDistrict"
          class="px-3 py-2 bg-slate-50 text-xs font-medium text-slate-700 border border-slate-200 rounded-xl outline-none focus:border-blue-500 cursor-pointer">
          <option value="all">Tất cả 8 Huyện/TP</option>
          <option v-for="d in districts" :key="d.id" :value="d.id">{{ d.name }}</option>
        </select>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center gap-2 flex-shrink-0">
        <button 
          @click="$emit('open-excel-import')"
          class="px-3 py-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 text-xs font-semibold flex items-center gap-1.5 transition-all">
          <i class="fa-solid fa-file-excel text-emerald-600"></i>
          <span>Nhập Excel</span>
        </button>

        <button 
          @click="$emit('export-excel')"
          class="px-3 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-semibold flex items-center gap-1.5 transition-all">
          <i class="fa-solid fa-file-export text-slate-500"></i>
          <span>Xuất Excel</span>
        </button>

        <button 
          @click="$emit('add-school')"
          class="px-3.5 py-2 rounded-xl bg-blue-800 hover:bg-blue-900 text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all">
          <i class="fa-solid fa-plus"></i>
          <span>Thêm trường mới</span>
        </button>
      </div>

    </div>

    <!-- Data Table Container -->
    <div class="flex-1 overflow-auto border border-slate-200 rounded-2xl bg-white shadow-2xs">
      <table class="w-full text-left border-collapse text-xs">
        <thead class="bg-slate-50 text-slate-600 font-bold sticky top-0 border-b border-slate-200 z-10">
          <tr>
            <th class="py-3 px-3.5 text-center w-12">STT</th>
            <th class="py-3 px-3.5">Mã trường</th>
            <th class="py-3 px-3.5 min-w-[200px]">Tên cơ sở giáo dục</th>
            <th class="py-3 px-3.5">Cấp học</th>
            <th class="py-3 px-3.5">Địa bàn</th>
            <th class="py-3 px-3.5 text-center">Tọa độ GIS</th>
            <th class="py-3 px-3.5 text-right">Quy mô (HS/GV)</th>
            <th class="py-3 px-3.5 text-center">Trạng thái</th>
            <th class="py-3 px-3.5 text-center w-36">Thao tác</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
          <tr 
            v-for="(school, index) in paginatedSchools" 
            :key="school.id"
            class="hover:bg-blue-50/40 transition-colors">
            <td class="py-3 px-3.5 text-center text-slate-400 font-medium">
              {{ (currentPage - 1) * pageSize + index + 1 }}
            </td>
            <td class="py-3 px-3.5 font-mono font-semibold text-slate-700">
              {{ school.code }}
            </td>
            <td class="py-3 px-3.5">
              <div class="font-bold text-slate-800 hover:text-blue-700 cursor-pointer" @click="$emit('view-detail', school)">
                {{ school.name }}
              </div>
              <div class="text-[11px] text-slate-500 truncate max-w-xs">{{ school.address }}</div>
            </td>
            <td class="py-3 px-3.5">
              <span 
                class="px-2 py-0.5 rounded-full text-[10px] font-bold text-white whitespace-nowrap"
                :style="{ backgroundColor: getLevelColor(school.education_level) }">
                {{ getLevelLabel(school.education_level) }}
              </span>
            </td>
            <td class="py-3 px-3.5">
              <div class="font-medium text-slate-800">{{ school.district_name }}</div>
              <div class="text-[11px] text-slate-500">{{ school.ward }}</div>
            </td>
            <td class="py-3 px-3.5 text-center font-mono text-[11px] text-slate-600">
              <span v-if="school.lat && school.lng" class="bg-slate-100 px-1.5 py-0.5 rounded">
                {{ school.lat.toFixed(3) }}, {{ school.lng.toFixed(3) }}
              </span>
              <span v-else class="text-rose-500 font-semibold">Thiếu tọa độ</span>
            </td>
            <td class="py-3 px-3.5 text-right font-medium">
              <span class="text-blue-800 font-bold">{{ school.student_count?.toLocaleString('vi-VN') }}</span>
              <span class="text-slate-400 text-[10px] mx-1">/</span>
              <span class="text-slate-600">{{ school.teacher_count }}</span>
            </td>
            <td class="py-3 px-3.5 text-center">
              <span 
                class="px-2 py-0.5 rounded-md text-[10px] font-semibold border"
                :class="getStatusClass(school.status)">
                {{ getStatusLabel(school.status) }}
              </span>
            </td>
            <td class="py-3 px-3.5 text-center">
              <div class="flex items-center justify-center gap-1">
                <button 
                  @click="$emit('view-on-map', school)"
                  class="w-7 h-7 rounded-lg text-blue-700 hover:bg-blue-100 flex items-center justify-center transition-colors"
                  title="Xem trên bản đồ">
                  <i class="fa-solid fa-map-location-dot text-xs"></i>
                </button>
                <button 
                  @click="$emit('view-detail', school)"
                  class="w-7 h-7 rounded-lg text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors"
                  title="Xem hồ sơ">
                  <i class="fa-solid fa-eye text-xs"></i>
                </button>
                <button 
                  @click="$emit('edit-school', school)"
                  class="w-7 h-7 rounded-lg text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition-colors"
                  title="Chỉnh sửa">
                  <i class="fa-solid fa-pen text-xs"></i>
                </button>
                <button 
                  @click="$emit('delete-school', school)"
                  class="w-7 h-7 rounded-lg text-rose-700 hover:bg-rose-100 flex items-center justify-center transition-colors"
                  title="Xóa trường">
                  <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="filteredSchools.length === 0">
            <td colspan="9" class="py-12 text-center text-slate-400 italic">
              Không tìm thấy cơ sở giáo dục nào phù hợp với bộ lọc hiện tại.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination Footer -->
    <div class="mt-3 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500 flex-shrink-0">
      <div>
        Hiển thị <span class="font-bold text-slate-700">{{ paginatedSchools.length }}</span> / 
        <span class="font-bold text-slate-700">{{ filteredSchools.length }}</span> trường 
        (Tổng số: {{ schools.length }})
      </div>

      <div class="flex items-center gap-1">
        <button 
          @click="currentPage = 1"
          :disabled="currentPage === 1"
          class="px-2 py-1 rounded-md border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
          <i class="fa-solid fa-angles-left text-[10px]"></i>
        </button>
        <button 
          @click="currentPage--"
          :disabled="currentPage === 1"
          class="px-2 py-1 rounded-md border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
          <i class="fa-solid fa-angle-left text-[10px]"></i>
        </button>

        <span class="px-3 py-1 font-semibold text-slate-800">
          Trang {{ currentPage }} / {{ totalPages || 1 }}
        </span>

        <button 
          @click="currentPage++"
          :disabled="currentPage >= totalPages"
          class="px-2 py-1 rounded-md border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
          <i class="fa-solid fa-angle-right text-[10px]"></i>
        </button>
        <button 
          @click="currentPage = totalPages"
          :disabled="currentPage >= totalPages"
          class="px-2 py-1 rounded-md border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
          <i class="fa-solid fa-angles-right text-[10px]"></i>
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { EDUCATION_LEVELS, LEVEL_MAP, STATUS_MAP } from '../../services/schoolService'

const props = defineProps({
  schools: {
    type: Array,
    default: () => []
  },
  districts: {
    type: Array,
    default: () => []
  }
})

defineEmits([
  'add-school',
  'edit-school',
  'delete-school',
  'view-detail',
  'view-on-map',
  'open-excel-import',
  'export-excel'
])

const levels = EDUCATION_LEVELS.filter(l => l.id !== 'all')

const searchQuery = ref('')
const selectedLevel = ref('all')
const selectedDistrict = ref('all')

const currentPage = ref(1)
const pageSize = 12

const filteredSchools = computed(() => {
  return props.schools.filter(s => {
    const matchSearch = !searchQuery.value || 
      s.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      s.code.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (s.address && s.address.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
      (s.ward && s.ward.toLowerCase().includes(searchQuery.value.toLowerCase()))

    const matchLevel = selectedLevel.value === 'all' || s.education_level === selectedLevel.value
    const matchDistrict = selectedDistrict.value === 'all' || s.district_id === selectedDistrict.value

    return matchSearch && matchLevel && matchDistrict
  })
})

const totalPages = computed(() => Math.ceil(filteredSchools.value.length / pageSize))

const paginatedSchools = computed(() => {
  const start = (currentPage.value - 1) * pageSize
  return filteredSchools.value.slice(start, start + pageSize)
})

function getLevelLabel(level) {
  return LEVEL_MAP[level]?.label || level
}

function getLevelColor(level) {
  return LEVEL_MAP[level]?.color || '#94a3b8'
}

function getStatusLabel(status) {
  return STATUS_MAP[status]?.label || 'Đã xác minh'
}

function getStatusClass(status) {
  return STATUS_MAP[status]?.color || 'bg-emerald-100 text-emerald-700 border-emerald-200'
}
</script>
