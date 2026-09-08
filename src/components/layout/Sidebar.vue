<template>
  <aside 
    :class="isOpen ? 'w-64' : 'w-16 -translate-x-full md:translate-x-0 md:w-16'"
    class="fixed left-0 top-14 bottom-0 bg-white border-r border-slate-200 z-20 flex flex-col transition-all duration-300 ease-in-out select-none shadow-sm">
    
    <!-- Navigation Items -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden p-2 space-y-1">
      
      <!-- Bản đồ tương tác -->
      <button 
        @click="$emit('change-view', 'map')"
        :class="currentView === 'map' ? 'bg-blue-50 text-blue-800 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all group">
        <i class="fa-solid fa-map-location-dot w-5 text-center text-blue-700 text-base flex-shrink-0"></i>
        <span v-if="isOpen" class="truncate text-left flex-1">Bản đồ tương tác</span>
        <span v-if="isOpen && totalSchools" class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-bold rounded-full">
          {{ totalSchools }}
        </span>
      </button>

      <!-- Dashboard thống kê -->
      <button 
        @click="$emit('change-view', 'dashboard')"
        :class="currentView === 'dashboard' ? 'bg-blue-50 text-blue-800 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all group">
        <i class="fa-solid fa-chart-pie w-5 text-center text-blue-700 text-base flex-shrink-0"></i>
        <span v-if="isOpen" class="truncate text-left flex-1">Dashboard thống kê</span>
      </button>

      <!-- Quản lý trường học -->
      <button 
        @click="$emit('change-view', 'table')"
        :class="currentView === 'table' ? 'bg-blue-50 text-blue-800 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all group">
        <i class="fa-solid fa-school w-5 text-center text-blue-700 text-base flex-shrink-0"></i>
        <span v-if="isOpen" class="truncate text-left flex-1">Danh sách trường học</span>
      </button>

      <div class="border-t border-slate-100 my-2"></div>
      <div v-if="isOpen" class="px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">
        Nghiệp vụ GIS & Dữ liệu
      </div>

      <!-- Thêm trường mới -->
      <button 
        @click="$emit('open-create-school')"
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all">
        <i class="fa-solid fa-square-plus w-5 text-center text-emerald-600 text-base flex-shrink-0"></i>
        <span v-if="isOpen" class="truncate text-left flex-1">Thêm cơ sở giáo dục</span>
      </button>

      <!-- Nhập dữ liệu Excel -->
      <button 
        @click="$emit('open-excel-import')"
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all">
        <i class="fa-solid fa-file-excel w-5 text-center text-emerald-600 text-base flex-shrink-0"></i>
        <span v-if="isOpen" class="truncate text-left flex-1">Nhập dữ liệu Excel</span>
      </button>

      <!-- Xuất dữ liệu Excel -->
      <button 
        @click="$emit('export-excel')"
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all">
        <i class="fa-solid fa-file-export w-5 text-center text-amber-600 text-base flex-shrink-0"></i>
        <span v-if="isOpen" class="truncate text-left flex-1">Xuất dữ liệu Excel</span>
      </button>

      <!-- Công cụ GIS: Heatmap / Buffer -->
      <button 
        @click="$emit('trigger-gis-tool')"
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all">
        <i class="fa-solid fa-layer-group w-5 text-center text-purple-600 text-base flex-shrink-0"></i>
        <span v-if="isOpen" class="truncate text-left flex-1">Lớp phân tích GIS</span>
      </button>
    </nav>

    <!-- Bottom info card -->
    <div v-if="isOpen" class="p-3 border-t border-slate-200 bg-slate-50/80">
      <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs">
        <div class="flex items-center gap-2 mb-1">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          <span class="text-xs font-bold text-slate-700">Tỉnh Ninh Bình</span>
        </div>
        <p class="text-[11px] text-slate-500">Mạng lưới 8 huyện/TP</p>
        <p class="text-[11px] text-blue-700 font-semibold mt-1">Phiên bản V1.0 • GIS Mở</p>
      </div>
    </div>
  </aside>
</template>

<script setup>
defineProps({
  isOpen: {
    type: Boolean,
    default: true
  },
  currentView: {
    type: String,
    default: 'map'
  },
  totalSchools: {
    type: Number,
    default: 0
  }
})

defineEmits(['change-view', 'open-create-school', 'open-excel-import', 'export-excel', 'trigger-gis-tool'])
</script>
